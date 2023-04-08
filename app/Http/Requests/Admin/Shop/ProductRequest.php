<?php

namespace App\Http\Requests\Admin\Shop;


use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules =  [
            "import_id"            => "nullable|string",
            "supplier"             => "nullable|string",
            "name"                 => "required|string|max:255",
            "description"          => "nullable|string",
            "sku"                  => "nullable|unique:App\Entities\Shop\Product,sku|string|max:255",
            "brand_id"             => "nullable|exists:App\Entities\Shop\Brand,id",
            "category_id"          => "nullable|exists:App\Entities\Shop\Category,id",
            "weight"               => "nullable|numeric",
            "quantity"             => "nullable|integer",
            "price"                => "nullable|integer",
            "compare_at_price"     => "nullable|integer",
            "photo"                => "nullable|array",
            "photo.*"              => "nullable|image|mimes:jpg,jpeg,png",
            "meta"                 => "nullable|array|min:2",
            "meta.*"               => "nullable|string|max:255",
            "product_categories"   => "nullable|array",
            "product_categories.*" => "nullable|exists:App\Entities\Shop\Category,id",
            "product_tags"         => "nullable|array",
            "product_tags.*"       => "nullable|exists:App\Entities\Shop\Tag,id",
            "product_attributes"   => "nullable|array",
            "volume"               => "nullable|integer",
            "packaging"            => "nullable|string|max:10",
            "unit"                 => "nullable|string|max:10",
            "amount_in_package"    => "nullable|string|max:10",
            "country"              => "nullable|string|max:20",
            "order_variants"       => "nullable|string|max:10",
            "product_type"         => "nullable|string|max:10",
        ];

        if ($this->isMethod('patch')) {
            $id = $this->product->id;
            $rules['sku'] = "nullable|unique:\App\Entities\Shop\Product,sku,".$id."|string|max:255";
        }

        return $rules;
    }
}
