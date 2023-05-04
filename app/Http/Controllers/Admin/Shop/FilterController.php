<?php

namespace App\Http\Controllers\Admin\Shop;


use Illuminate\View\View;
use App\Entities\Shop\Tag;
use Illuminate\Http\Request;
use App\Entities\Shop\Filter;
use App\Entities\Shop\Category;
use App\Entities\Shop\Attribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\UseCases\Admin\Shop\FilterService;

class FilterController extends Controller
{

    private FilterService $service;

    public function __construct(FilterService $service)
    {
        $this->service = $service;
    }

    public function index():View
    {
        $filters = Filter::all();
        return view('admin.shop.filters.index', compact('filters'));
    }

    public function create():View
    {
        $filter     = null;
        $categories = Category::defaultOrder()->withDepth()->get();
        return view('admin.shop.filters.create', compact('categories', 'filter'));
    }

    public function edit(Filter $filter):View
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $tags       = Tag::all();
        $attributes = Attribute::all();
        return view('admin.shop.filters.edit', compact('filter', 'categories', 'tags', 'attributes'));
    }

    public function update(Request $request, Filter $filter):RedirectResponse
    {
        try {
            $filter = $this->service->update($request, $filter);
            return redirect()->route('admin.shop.filters.edit', compact('filter'))->with('success', 'Фильтр успешно отредактирован');
        } catch (\Exception $exception)
        {
            return redirect()->route('admin.shop.filters.edit', compact('filter'))->with('error', $exception->getMessage());
        }
    }

    public function store(Request $request):RedirectResponse
    {
        try {
            $filter = $this->service->create($request);
            return redirect()->route('admin.shop.filters.edit', compact('filter'))->with('success', 'Фильтр успешно создан');
        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function addGroup():View
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $tags       = Tag::all();
        $attributes = Attribute::all();
        return view('admin.shop.filters.partials.group-item', compact('categories', 'tags', 'attributes'));
    }

    public function destroy(Filter $filter):RedirectResponse
    {
        $filter->delete();
        return redirect('admin.shop.filters.index')->with('success', 'Фильтр успешно удален.');
    }

}
