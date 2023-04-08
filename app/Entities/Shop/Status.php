<?php

namespace App\Entities\Shop;

class Status
{
    const NEW = 1;

    const PAID = 2;

    const SENT = 3;

    const COMPLETED = 4;

    const CANCELLED = 5;

    const CANCELLED_BY_CUSTOMER = 6;

    public $value = null;

    public $created_at = null;


    public function __construct($value, $created_at)
    {
        $this->value      = $value;
        $this->created_at = $created_at;
    }
}
