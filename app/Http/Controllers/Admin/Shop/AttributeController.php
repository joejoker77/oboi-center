<?php

namespace App\Http\Controllers\Admin\Shop;

use Throwable;
use Illuminate\Http\Request;
use App\Entities\Shop\Category;
use App\Entities\Shop\Attribute;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\UseCases\Admin\Shop\AttributeService;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\Shop\AttributeRequest;

class AttributeController extends Controller
{

    private AttributeService $service;


    public function __construct(AttributeService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $attributes = Attribute::orderBy('sort')->get();
        return view('admin.shop.attributes.index', compact('attributes'));
    }

    public function create(): View
    {
        $types = Attribute::typesList();
        $categories = Category::defaultOrder()->withDepth()->get();

        return view('admin.shop.attributes.create', compact('types', 'categories'));
    }

    public function store(AttributeRequest $request): RedirectResponse
    {
        try {
            $attribute = $this->service->create($request);
            return redirect()->route('admin.shop.attributes.show', compact('attribute'));
        } catch (Throwable $e) {
            return redirect()->refresh()->with('error', $e->getMessage());
        }
    }

    public function show(Attribute $attribute): View
    {
        $categories = Category::defaultOrder()->withDepth()->get();

        return view('admin.shop.attributes.show', compact('attribute', 'categories'));
    }

    public function edit(Attribute $attribute): View
    {
        $types      = Attribute::typesList();
        $categories = Category::defaultOrder()->withDepth()->get();
        $variants   = implode("\r\n", $attribute->variants);

        return view('admin.shop.attributes.edit', compact('attribute', 'categories', 'types', 'variants'));
    }

    /**
     * @throws Throwable
     */
    public function update(Request $request, Attribute $attribute): RedirectResponse
    {
        $this->service->update($request, $attribute);
        return redirect()->route('admin.shop.attributes.show', $attribute)
            ->with('access', 'Атрибут успешно обновлен');
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $name = $attribute->name;
        $attribute->delete();
        return redirect()->route('admin.shop.attributes.index')->with('success', 'Атрибут ' . $name. ', успешно удален');
    }

    /**
     * @throws ValidationException
     */
    public function assignCategories(Request $request, Attribute $attribute): RedirectResponse
    {
        try {
            $this->validate($request, [
                'categories' => 'nullable|array|min:1',
                'categories*' => 'nullable|integer'
            ]);
            $attribute->categories()->detach();
            $attribute->categories()->attach($request['categories']);
            return redirect()->back()->with('success', 'Категории успешно привязаны');
        } catch (\DomainException|ValidationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * @throws ValidationException
     */
    public function unAssignCategory(Request $request, Attribute $attribute): RedirectResponse
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        try {
            $attribute->categories()->detach([$request['id']]);
            return redirect()->back()->with('success', 'Категория успешно отвязана от атрибута');
        } catch (\DomainException|ValidationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
