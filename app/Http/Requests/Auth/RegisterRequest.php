<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    protected $errorBag = 'register';

    public function authorize():bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|max:32|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
