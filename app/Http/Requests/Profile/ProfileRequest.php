<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'last_name'          => 'string|max:255|nullable',
            'phone'              => 'phone:RU|nullable',
            'phone_auth'         => 'boolean|nullable',
            'phone_verify_token' => 'string|max:4|nullable'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'phone_auth' => $this->toBoolean($this->phone_auth),
        ]);
    }

    private function toBoolean($booleable)
    {
        return filter_var($booleable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
