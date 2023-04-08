<?php

namespace App\Http\Router;

use App\Entities\Shop\Product;
use App\Entities\Shop\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Routing\UrlRoutable;

class ProductPath implements UrlRoutable
{
    public Category|null $category = null;
    public Product|null $product   = null;

    public function withCategory(?Category $category):self
    {
        $clone           = clone $this;
        $clone->category = $category;

        return $clone;
    }

    public function withProduct(?Product $product):self
    {
        $clone = clone $this;
        $clone->product = $product;

        return $clone;
    }

    public function getRouteKey()
    {
        $segments = [];

        if ($this->category) {
            $segments[] = Cache::tags(Category::class)->rememberForever('category_path_'.$this->category->id, function () {
                return $this->category->getPath();
            });
        }

        if ($this->product) {
            $segments[] = Cache::tags(Product::class)->rememberForever('product_path_'.$this->product->id,function () {
                return $this->product->slug;
            });
        }

        return implode('/', $segments);
    }

    public function getRouteKeyName(): string
    {
        return 'product_path';
    }

    public function resolveRouteBinding($value, $field = null): Model|ProductPath|null
    {
        $chunks = explode('/', $value);

        $category = null;
        do{
            $slug = reset($chunks);
            if ($slug && $next = Category::withDepth()->where('slug', $slug)->where('parent_id', $category?->id)->first()) {
                $category = $next;
                array_shift($chunks);
            }
        } while (!empty($slug) && !empty($next));

        $product = null;
        do {
            $slug = reset($chunks);
            if ($slug && $next = Product::where('slug', $slug)->first()) {
                $product = $next;
                array_shift($chunks);
            }
        } while (!empty($slug) && !empty($next));

        if (
            !empty($chunks) ||
            ($category && !$category->published) ||
            ($product && !$product->isActive()) ||
            ($product && $category && $product->category_id !== $category->id)
        ) {
            abort(404);
        }

        return $product ? $this->withCategory($category)->withProduct($product) : $this->withCategory($category);
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }
}
