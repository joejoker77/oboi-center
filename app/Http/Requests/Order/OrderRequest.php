<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_name'  => 'required|string|max:40',
            'customer_phone' => 'required|string',
            'payment_method' => 'required|string',
            'postal_code'    => 'required|string|max:6|min:6',
            'city'           => 'string|max:25|nullable',
            'street'         => 'required|string|max:70',
            'house'          => 'required|string|max:6',
            'house_part'     => 'string|max:4|nullable',
            'flat'           => 'string|max:6|nullable'
        ];
    }
}
