<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Entities\Shop\Tag;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{

    public function index(): View
    {
        $tags = Tag::orderBy('name')->get();
        return view('admin.shop.tags.index', compact('tags'));
    }

    public function create():View
    {
        return view('admin.shop.tags.create');
    }

    public function ajaxCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:25',
            'meta_title' => 'required|string|max:120',
            'meta_description' => 'required|string|max:120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()->all()
            ]);
        }

        $tag = Tag::create([
            'name' => $request['name'],
            'meta'=> ['title' => $request['meta_title'], 'description' => $request['meta_description']],
            'seo_text' => ''
        ]);

        return response()->json(['success' => 'Тэг успешно создан', 'id' => $tag->id]);
    }

    public function show(Tag $tag):View
    {
        return view('admin.shop.tags.show', compact('tag'));
    }

    public function edit(Tag $tag):View
    {
        return view('admin.shop.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag):RedirectResponse
    {
        $tag->update($request->all());
        return redirect()->route('admin.shop.tags.show', compact('tag'));
    }

    public function store(Request $request):RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:32',
            'seo_text' => 'nullable|string',
            'meta' => 'nullable|array|min:2',
            'meta*' => 'nullable|string|max:255'
        ]);

        $tag = Tag::create([
            'name' => $request['name'],
            'seo_text' => $request['seo_text'],
            'meta' => $request['meta']
        ]);

        return redirect()->route('admin.shop.tags.show', compact('tag'));
    }

    public function destroy(Tag $tag):RedirectResponse
    {
        $tag->delete();
        return redirect()->route('admin.shop.tags.index');
    }
}
