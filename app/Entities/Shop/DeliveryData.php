<?php

namespace App\Entities\Shop;

class DeliveryData
{

    public $index;

    public $address;

    public function __construct($index, $address)
    {
        $this->address = $address;
        $this->index   = $index;
    }

}
