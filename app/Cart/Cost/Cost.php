<?php

namespace App\Cart\Cost;

final class Cost
{
    private float $value;

    private array $discounts = [];


    public function __construct(float $value, array $discounts = [])
    {
        $this->value = $value;
        $this->discounts = $discounts;
    }

    public function withDiscount(Discount $discount):self
    {
        return new Cost($this->value, array_merge($this->discounts, [$discount]));
    }

    public function getOrigin():float
    {
        return $this->value;
    }

    public function getTotal(): float
    {
        return $this->value - array_sum(array_map(function (Discount $discount) {
            return $discount->getValue();
            }, $this->discounts));
    }

    public function getDiscounts():array
    {
        return $this->discounts;
    }
}
