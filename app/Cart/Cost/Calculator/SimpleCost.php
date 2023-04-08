<?php

namespace App\Cart\Cost\Calculator;

use App\Cart\Cost\Cost;

class SimpleCost implements CalculatorInterface
{

    public function getCost(array $items): Cost
    {
        $cost = 0;
        foreach ($items as $item) {
            $cost += $item->getCost();
        }
        return new Cost($cost);
    }

}
