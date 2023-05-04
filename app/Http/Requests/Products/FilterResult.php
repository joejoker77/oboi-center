<?php

namespace App\Http\Requests\Products;

use Illuminate\Contracts\Pagination\Paginator;

class FilterResult
{
    public Paginator $products;

    public array $categories;

    public array $tags;
    public array $attributes;


    public function __construct(Paginator $products, array $categories, array $tags, array $attributes)
    {
        $this->products   = $products;
        $this->categories = $categories;
        $this->tags       = $tags;
        $this->attributes = $attributes;
    }
}
