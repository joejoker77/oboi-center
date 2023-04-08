<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    protected $errorBag = 'login';

    public function authorize():bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'email'    => 'required|string',
            'password' => 'required|string'
        ];
    }
}
