<?php

namespace App\Providers;

use App\Cart\Cart;
use App\Cart\Storage\HybridStorage;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Cart\Cost\Calculator\SimpleCost;
use App\Cart\Cost\Calculator\DynamicCost;

class CartProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(Cart::class, function (Application $app) {
            return new Cart(
                new HybridStorage('cart', 3600 * 24),
                new DynamicCost(new SimpleCost()),
            );
        });
    }
}
