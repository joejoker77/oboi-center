<?php

namespace App\Http\Requests\Products;

use Illuminate\Contracts\Pagination\Paginator;

class SearchResult
{
    public Paginator $products;

    public array $categoriesCounts;


    public function __construct(Paginator $products, array $categoriesCounts)
    {
        $this->products         = $products;
        $this->categoriesCounts = $categoriesCounts;
    }
}
