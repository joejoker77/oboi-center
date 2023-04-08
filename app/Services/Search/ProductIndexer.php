<?php

namespace App\Services\Search;

use App\Entities\Shop\Value;
use App\Entities\Shop\Product;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;

class ProductIndexer
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function clear():void
    {
        $this->client->deleteByQuery([
            'index' => 'products',
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ]
            ],
        ]);
    }

    public function index(Product $product):void
    {
        $this->client->index([
            'index' => 'products',
            'id'    => $product->id,
            'body'  => [
                'id'             => $product->id,
                'name'           => $product->name,
                'description'    => $product->description,
                'price'          => $product->price,
                'status'         => $product->status,
                'brand_id'       => $product->brand_id,
                'sku'            => $product->sku,
                'order_variants' => $product->order_variants,
                'categories'     => array_merge(
                    [$product->category_id],
                    $product->category->ancestors()->pluck('id')->toArray()
                ),
                'values' => array_map(function (Value $value) {
                    return [
                        'attribute'    => $value->attribute_id,
                        'value_string' => (string)$value->value,
                        'value_int'    => (int) $value->value,
                    ];
                }, $product->values()->getModels()),
            ]
        ]);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function remove(Product $product):void
    {
        $this->client->delete([
            'index' => 'products',
            'id'    => $product->id,
        ]);
    }
}
