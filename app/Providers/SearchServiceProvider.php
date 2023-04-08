<?php

namespace App\Providers;

use Carbon\Laravel\ServiceProvider;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Foundation\Application;

class SearchServiceProvider extends ServiceProvider
{
    public function register()
    {
        parent::register();
        $this->app->singleton(Client::class, function (Application $app) {
            $config = $app->make('config')->get('elasticsearch');
            return ClientBuilder::create()->setHosts($config['hosts'])->setRetries($config['retries'])->build();
        });
    }
}
