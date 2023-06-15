<?php

namespace App\Http\Requests\Order;

use App\Entities\Shop\DeliveryMethod;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var DeliveryMethod $delivery */
        $delivery = DeliveryMethod::where(['name' => 'Самовывоз'])->first();

        return [
            'delivery_id'    => 'required|numeric',
            'customer_name'  => 'required|string|max:40',
            'customer_phone' => 'required|string',
            'payment_method' => 'required|string',
            'postal_code'    => 'exclude_if:delivery_id,'.$delivery->id.'|required|string|max:6|min:6',
            'city'           => 'string|max:25|nullable',
            'street'         => 'exclude_if:delivery_id,'.$delivery->id.'|required|string|max:70',
            'house'          => 'exclude_if:delivery_id,'.$delivery->id.'|required|string|max:6',
            'house_part'     => 'string|max:4|nullable',
            'flat'           => 'string|max:6|nullable'
        ];
    }
}
