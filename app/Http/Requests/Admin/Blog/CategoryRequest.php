<?php

namespace App\Http\Requests\Admin\Blog;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            "name"              => "required|string|max:255",
            "title"             => "nullable|string|max:255",
            "parent_id"         => "nullable|integer|exists:blog_categories,id",
            "description"       => "nullable|string",
            "photo"             => "nullable|array",
            "photo.*"           => "nullable|image|mimes:jpg,jpeg,png",
            "status"            => "required|string|min:5|max:6",
            "meta"              => "nullable|array|min:2",
            "meta.*"            => "nullable|string|max:255"
        ];
    }
}
