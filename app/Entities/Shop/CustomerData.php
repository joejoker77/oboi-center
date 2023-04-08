<?php

namespace App\Entities\Shop;

class CustomerData
{
    public $phone;

    public $name;

    public function __construct($phone, $name)
    {
        $this->name  = $name;
        $this->phone = $phone;
    }
}
