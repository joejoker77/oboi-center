<?php

namespace App\Console\Commands\Search;

use App\Entities\Shop\Product;
use App\Services\Search\ProductIndexer;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Console\Command;

class ReindexCommand extends Command
{
    protected $signature = 'search:reindex';

    private ProductIndexer $products;

    public function __construct(ProductIndexer $products)
    {
        parent::__construct();
        $this->products = $products;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function handle():bool
    {
        $this->products->clear();

        foreach (Product::active()->orderBy('id')->cursor() as $product) {
            $this->products->index($product);
            echo 'Product: '.$product->name.' indexing'.PHP_EOL;
        }
        return true;
    }
}
