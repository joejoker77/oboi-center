<?php

namespace App\UseCases\Admin\Shop;

use App\Cart\Cart;
use App\Cart\CartItem;
use App\Entities\Shop\Product;

class CartService
{
    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function add($product_id, $quantity, $type_order):void
    {
        $product = Product::find($product_id);
        $this->cart->add(new CartItem($product, $quantity, $type_order));
    }

    public function set($id, $quantity):void
    {
        $this->cart->set($id, $quantity);
    }

    public function remove($id):void
    {
        $this->cart->remove($id);
    }

    public function clear():void
    {
        $this->cart->clear();
    }
}
