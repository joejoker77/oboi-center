<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryRequest extends FormRequest
{

    protected $redirect           = '/cabinet/profile#addresses-tab';
    protected $stopOnFirstFailure = false;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postal_code' => 'required|numeric|max:6|min:6',
            'city'        => 'string|max:25|nullable',
            'street'      => 'required|string|max:70',
            'house'       => 'required|string|max:6',
            'house_part'  => 'string|max:4|nullable',
            'flat'        => 'string|max:6|nullable'
        ];
    }
}
