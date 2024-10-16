<?php

namespace App\Http\Requests\Admin\Shop;

use App\Rules\AttributeValue;
use Illuminate\Validation\Rule;
use App\Entities\Shop\Attribute;
use Illuminate\Foundation\Http\FormRequest;

class AttributeRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', 'max:255', Rule::in(array_keys(Attribute::typesList()))],
            'mode' => 'required|string|max:9',
            'variants' => ['nullable', new AttributeValue],
            'sort' => 'required|integer',
            'unit' => 'nullable|string|max:6'
        ];
    }
}
