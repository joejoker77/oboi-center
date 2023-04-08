<?php

namespace App\Cart\Cost\Calculator;

use App\Cart\Cost\Cost;
use App\Cart\Cost\Discount as CartDiscount;
use App\Entities\Shop\Discount as DiscountEntity;

class DynamicCost implements CalculatorInterface
{
    private CalculatorInterface $next;

    public function __construct(CalculatorInterface $next)
    {
        $this->next = $next;
    }

    public function getCost(array $items): Cost
    {
        /** @var DiscountEntity $discounts */
        $discounts = DiscountEntity::where('active', true)->orderBy('sort')->get();

        $cost = $this->next->getCost($items);

        foreach ($discounts as $discount) {
            if ($discount->isEnabled()) {
                $new = new CartDiscount($cost->getOrigin() * $discount->percent / 100, $discount->name);
                $cost = $cost->withDiscount($new);
            }
        }

        return $cost;
    }
}
