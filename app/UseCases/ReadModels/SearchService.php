<?php

namespace App\UseCases\ReadModels;

use App\Entities\Shop\Product;
use App\Entities\Shop\Category;
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
                                                    !empty($value['from']) ? ['range' => ['value.value_int' => ['gte' => $value['from']]]] : false,
                                                    !empty($value['to']) ? ['range' => ['values.value_int' => ['lte' => $value['to']]]]: false,
                                                ])),
                                            ],
                                        ],
                                    ],
                                ];
                            }, $values, array_keys($values))
                        ),
//                        'must_not' => [
//                            'bool' => [
//                                'must' => [
//                                    'match' => ['order_variants' => 'Вывод']
//                                ]
//                            ]
//                        ],
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
}
