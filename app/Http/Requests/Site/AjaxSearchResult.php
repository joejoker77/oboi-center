<?php

namespace App\Http\Requests\Site;


class AjaxSearchResult
{
    public array $products;

    public array $posts;

    public array $blog_categories;

    public array $categories;


    public function __construct(?array $products,  ?array $posts, ?array $blog_categories, ?array $categories)
    {
        $this->products        = $products;
        $this->posts           = $posts;
        $this->blog_categories = $blog_categories;
        $this->categories      = $categories;
    }
}
