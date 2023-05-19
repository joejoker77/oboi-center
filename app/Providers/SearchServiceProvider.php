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
            return ClientBuilder::create()
		->setHosts(['https://localhost:9200'])
		->setCABundle('/var/www/oboi-center/certs/http_ca.crt')
		->setBasicAuthentication('elastic', 'oNsCMEpYLRYY6KqzjY8X')
		->setRetries($config['retries'])
		->build();
        });
    }
}
