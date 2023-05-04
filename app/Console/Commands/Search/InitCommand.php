<?php

namespace App\Console\Commands\Search;

use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;
use Elastic\Elasticsearch\Exception\ElasticsearchException;

class InitCommand extends Command
{
    protected $signature = 'search:init';

    private Client $client;

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle():bool
    {
        $this->initProducts();

        return true;
    }

    public function initProducts():void
    {
        try {
            $this->client->indices()->delete([
                'index' => 'products'
            ]);

        } catch (ElasticsearchException $exception) {
            echo $exception->getMessage().PHP_EOL;
        }

        $this->client->indices()->create([
            'index' => 'products',
            'body'  => [
                'mappings' => [
                    '_source' => [
                        'enabled' => true,
                    ],
                    'properties' => [
                        'id' => [
                            'type' => 'integer',
                        ],
                        'name' => [
                            'type' => 'text',
                        ],
                        'description' => [
                            'type' => 'text',
                        ],
                        'price' => [
                            'type' => 'integer',
                        ],
                        'status' => [
                            'type' => 'keyword',
                        ],
                        'brand_id' => [
                            'type' => 'integer'
                        ],
                        'sku' => [
                            'type' => 'keyword',
                        ],
                        'categories' => [
                            'type' => 'integer',
                        ],
                        'tags' => [
                            'type' => 'long',
                        ],
                        'values' => [
                            'type' => 'nested',
                            'properties' => [
                                'attribute' => [
                                    'type' => 'integer',
                                ],
                                'value_string' => [
                                    'type' => 'text',
                                ],
                                'value_int' => [
                                    'type' => 'integer'
                                ]
                            ]
                        ]
                    ]
                ],
                'settings' => [
                    'analysis' => [
                        'char_filter' => [
                            'replace' => [
                                'type' => 'mapping',
                                'mappings' => [
                                    '&=> and '
                                ],
                            ],
                        ],
                        'filter' => [
                            'word_delimiter' => [
                                'type' => 'word_delimiter',
                                'split_on_numerics' => false,
                                'split_on_case_change' => true,
                                'generate_word_parts' => true,
                                'generate_number_parts' => true,
                                'catenate_all' => true,
                                'preserve_original' => true,
                                'catenate_numbers' => true,
                            ],
                            'trigrams' => [
                                'type' => 'ngram',
                                'min_gram' => 2,
                                'max_gram' => 5,
                            ],
                        ],
                        'analyzer' => [
                            'default' => [
                                'char_filter' => [
                                    'html_strip',
                                    'replace',
                                ],
                                'tokenizer' => 'whitespace',
                                'filter' => [
                                    'lowercase',
                                    'word_delimiter',
                                    'trigrams'
                                ]
                            ]
                        ]
                    ],
                    'max_ngram_diff' => 3,
                ]
            ]
        ]);
    }
}
