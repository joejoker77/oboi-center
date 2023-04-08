<?php

namespace App\Cart\Cost\Calculator;

use App\Cart\CartItem;
use App\Cart\Cost\Cost;

interface CalculatorInterface
{
    /**
     * @param CartItem[] $items
     * @return Cost
     */
    public function getCost(array $items): Cost;
}
