<?php

namespace App\View\Components;

use App\Cart\Cart;
use Illuminate\View\View;
use Illuminate\View\Component;

class CountCart extends Component
{
    public Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function render():View
    {
        return view('components.count-cart', ['count' => $this->cart->getAmount()]);
    }
}
