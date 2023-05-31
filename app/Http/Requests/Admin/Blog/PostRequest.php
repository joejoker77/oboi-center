<?php

namespace App\Http\Requests\Admin\Blog;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            "title"             => "nullable|string|max:255",
            "category_id"       => "nullable|integer|exists:blog_categories,id",
            "description"       => "nullable|string",
            "content"           => "nullable|string",
            "photo"             => "nullable|array",
            "photo.*"           => "nullable|image|mimes:jpg,jpeg,png",
            "status"            => "required|string|min:5|max:6",
            "sort"              => "required|integer",
            "meta"              => "nullable|array|min:2",
            "meta.*"            => "nullable|string|max:255"
        ];
    }
}
