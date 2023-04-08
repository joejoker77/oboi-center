<?php

namespace App\Http\Requests\Admin\Shop;

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
            "import_id"         => "nullable|string",
            "supplier"          => "nullable|string",
            "name"              => "required|string|max:255",
            "title"             => "nullable|string|max:255",
            "parent_id"         => "nullable|integer|exists:shop_categories,id",
            "short_description" => "nullable|string",
            "description"       => "nullable|string",
            "files"             => "nullable|array",
            "files.*"           => "nullable|file|mimes:mov,mp4,doc,docx,pdf,ppt,pptm,pptx",
            "photo"             => "nullable|array",
            "photo.*"           => "nullable|image|mimes:jpg,jpeg,png",
            "published"         => "required|integer|min:0|max:1",
            "meta"              => "nullable|array|min:2",
            "meta.*"            => "nullable|string|max:255"
        ];
    }
}
