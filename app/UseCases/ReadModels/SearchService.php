<?php

namespace App\UseCases\ReadModels;

use App\Http\Requests\Products\FilterResult;
use Illuminate\Http\Request;
use App\Entities\Shop\Product;
use App\Entities\Shop\Category;
use App\Entities\Shop\Attribute;
use Elastic\Elasticsearch\Client;
use Illuminate\Database\Query\Expression;
use App\Http\Requests\Products\SearchResult;
use App\Http\Requests\Products\SearchRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class SearchService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function search(?Category $category, SearchRequest $request, int $perPage, int $page): SearchResult
    {
        $values = array_filter((array)$request->input('attrs'), function ($value) {
            return !empty($value['equals']) || !empty($value['from']) || !empty($value['to']);
        });

        $response = $this->client->search([
            'index' => 'products',
            'body' => [
                '_source' => ['id'],
                'from' => ($page - 1) * $perPage,
                'size' => $perPage,
                'aggs' => [
                    'group_by_category' => [
                        'terms' => [
                            'field' => 'categories',
                            'size' => 1000
                        ],
                    ],
                ],
                'query' => [
                    'bool' => [
                        'must' => array_merge(
                            [
                                ['term' => ['status' => Product::STATUS_ACTIVE]],
                            ],
                            array_filter([
                                $category ? ['term' => ['categories' => $category->id]] : false,
                                !empty($request['text']) ? ['multi_match' => [
                                    'query' => $request['text'],
                                    'fields' => ['name', 'description']
                                ]] : false,
                            ]),
                            array_map(function ($value, $id) {
                                return [
                                    'nested' => [
                                        'path' => 'values',
                                        'query' => [
                                            'bool' => [
                                                'must' => array_values(array_filter([
                                                    ['match' => ['values.attribute' => $id]],
                                                    !empty($value['equals']) ? ['match' => ['values.value_string' => $value['equals']]] : false,
                                                    !empty($value['from']) ? ['range' => ['values.value_int' => ['gte' => $value['from']]]] : false,
                                                    !empty($value['to']) ? ['range' => ['values.value_int' => ['lte' => $value['to']]]]: false,
                                                ])),
                                            ],
                                        ],
                                    ],
                                ];
                            }, $values, array_keys($values))
                        ),
                    ]
                ]
            ]
        ]);

        $ids = array_column($response['hits']['hits'], '_id');

        if ($ids) {
            $items = Product::active()->with(['category', 'values', 'photos'])->whereIn('id', $ids)->orderBy(new Expression('FIELD(id,' . implode(',', $ids) . ')'))->get();
            $pagination = new LengthAwarePaginator($items, $response['hits']['total']['value'], $perPage, $page);
        } else {
            $pagination = new LengthAwarePaginator([], 0, $perPage, $page);
        }

        return new SearchResult(
            $pagination,
            array_column($response['aggregations']['group_by_category']['buckets'], 'doc_count', 'key')
        );
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function filter(Request $request, int $perPage, int $page)
    {
        $values = array_filter((array)$request->input('attributes'), function ($value) {
            return !empty($value['equals']) || !empty($value['min']) || !empty($value['max']);
        });

        if (!empty($request->get('price'))) {
            $minPrice = (int)str_replace(" ₽", "", str_replace('"', '', $request->input('price')['min']));
            $maxPrice = (int)str_replace(" ₽", "", str_replace('"', '', $request->input('price')['max']));
        }

        $body = [
            'index' => 'products',
            'body' => [
                '_source' => ['id', 'values', 'tags', 'categories'],
                'from' => ($page - 1) * $perPage,
                'size' => $perPage,
                'query' => [
                    'bool' => [
                        'must' => array_merge(
                            [
                                ['term' => ['status' => Product::STATUS_ACTIVE]],
                            ],
                            array_filter([
                                !empty($request['text']) ? ['multi_match' => [
                                    'query' => $request['text'],
                                    'fields' => ['name', 'description']
                                ]] : false,
                                !empty($request->get('colors')) ? ['terms' => ['tags' => $request->get('colors')]] : false,
                                !empty($request->get('tags')) ? ['terms' => ['tags' => $request->get('tags')]] : false,
                                !empty($request->get('categories')) ? ['terms' => ['categories' => $request->get('categories')]] : false,
                                !empty($request->get('price') && isset($minPrice) && isset($maxPrice)) ?
                                    ['range' => ['price' => ['gte' => $minPrice, 'lte' => $maxPrice]]] : false,
                                !empty($request->get('brand')) ? ['term' => ['brand' => $request->get('brand')]] : false,
                            ]),
                            array_map(function ($value, $id) {
                                $attribute = Attribute::findOrFail($id);
                                return [
                                    'nested' => [
                                        'path' => 'values',
                                        'query' => [
                                            'bool' => [
                                                'must' => array_values(array_filter([
                                                    !empty($value['equals']) ? ['bool' => [
                                                        'should' => array_filter(array_map(function ($equal) {
                                                            return ['match' => ['values.value_string' => $equal]];
                                                        }, $value['equals'])),]] : false,
                                                    ['match' => ['values.attribute' => $id]],
                                                    !empty($value['min']) ? ['range' => ['values.value_int' => ['gte' => (int)trim(str_replace($attribute->unit, '', $value['min']))]]] : false,
                                                    !empty($value['max']) ? ['range' => ['values.value_int' => ['lte' => (int)trim(str_replace($attribute->unit, '', $value['max']))]]]: false,
                                                ])),
                                            ],
                                        ],
                                    ],
                                ];
                            }, $values, array_keys($values))
                        ),
                    ]
                ]
            ]
        ];

        $response = $this->client->search($body);

        $body['body']['from'] = 0;
        $body['body']['size'] = 10000;

        $responseAll = $this->client->search($body);

        $attributes       = $tags = $categories = [];
        $ids              = array_column($response['hits']['hits'], '_id');
        $attributeValues  = array_column(array_column($responseAll['hits']['hits'], '_source'), 'values');
        $tagsValues       = array_column(array_column($responseAll['hits']['hits'], '_source'), 'tags');
        $categoriesValues = array_column(array_column($responseAll['hits']['hits'], '_source'), 'categories');
//dump($response);
        foreach ($attributeValues as $values) {
            array_map(function ($val) use (&$attributes) {
                if (!array_key_exists($val['attribute'], $attributes)) {
                    $attributes[$val['attribute']] = [];
                }
                if (isset($attributes[$val['attribute']]) && !in_array($val['value_string'], $attributes[$val['attribute']])) {
                    $attributes[$val['attribute']][] = $val['value_string'];
                }
            }, $values);
        }

        $categories = $categoriesValues[0]??[];

        foreach ($tagsValues as $tagsArray) {
            $tags = array_merge($tags, $tagsArray);
        }

        if ($ids) {
            $items = Product::active()->with(['category', 'values', 'photos'])->whereIn('id', $ids)->orderBy(new Expression('FIELD(id,' . implode(',', $ids) . ')'))->get();
            $pagination = new LengthAwarePaginator($items, $response['hits']['total']['value'], $perPage, $page);
        } else {
            $pagination = new LengthAwarePaginator([], 0, $perPage, $page);
        }

        return new FilterResult($pagination, $categories, $tags, $attributes);
    }
}
