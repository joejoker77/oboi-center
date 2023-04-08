<?php

namespace App\View\Components;

use App\Cart\Cart;
use Illuminate\View\View;
use Illuminate\View\Component;

class SideCart extends Component
{
    public Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function render():View
    {
        return view('components.side-cart', ['cart' => $this->cart]);
    }
}
