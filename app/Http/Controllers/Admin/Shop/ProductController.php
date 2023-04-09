<?php

namespace App\Http\Controllers\Admin\Shop;


use Throwable;
use App\Entities\Shop\Tag;
use App\Traits\QueryParams;
use App\Entities\Shop\Brand;
use App\Entities\Shop\Photo;
use Illuminate\Http\Request;
use App\Entities\Shop\Product;
use App\Entities\Shop\Category;
use App\Entities\Shop\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Doctrine\DBAL\Cache\CacheException;
use App\UseCases\Admin\Shop\ProductService;
use App\Http\Requests\Admin\Shop\ProductRequest;

class ProductController extends Controller
{
    use QueryParams;

    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request):View
    {
        $query = Product::with(['category', 'photos', 'brand']);
        $this->queryParams($request, $query);

        $products   = $query->paginate(20);
        $categories = Category::defaultOrder()->withDepth()->get();
        $brands     = Brand::orderBy('name')->get();
        return view('admin.shop.products.index', compact('products', 'categories', 'brands'));
    }

    public function create():View
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $brands     = Brand::orderBy('id')->get();
        $tags       = Tag::orderBy('name')->get();
        return view('admin.shop.products.create', compact('categories', 'brands', 'tags'));
    }

    public function store(ProductRequest $request):RedirectResponse
    {
        try {
            $product = $this->service->create($request);
            return redirect()->route('admin.shop.products.show', compact('product'))
                ->with('success', 'Продукт успешно создан');
        } catch (CacheException|Throwable $e) {
            return back()->with('error', 'Во время выполнения запроса, произошла следующая ошибка: '. $e->getMessage());
        }
    }

    public function edit(Product $product):View
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $brands     = Brand::orderBy('id')->get();
        $tags       = Tag::orderBy('name')->get();
        return view('admin.shop.products.edit', compact('product', 'brands', 'tags', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            $this->service->update($request, $product);
            return redirect()->route('admin.shop.products.show', $product)->with('success', 'Товар успешно обновлен');
        } catch (\Exception|\DomainException $e) {
            return redirect()->route('admin.shop.products.edit', $product)->with('error', $e->getMessage());
        }
    }

    public function show(Product $product):View
    {
        return view('admin.shop.products.show', compact('product'));
    }

    public function destroy(Product $product):RedirectResponse
    {
        $product->delete();
        return redirect()->route('admin.shop.products.index');
    }

    public function getAttributesForm(Request $request): View
    {
        $category   = Category::ancestorsAndSelf($request["id"])->first();
        $attributes = $category->allAttributes();
        return view('admin.shop.products.partials.attributes', compact('attributes'));
    }

    public function getVariantsForm(Request $request): View|JsonResponse
    {
        if (!$request['attributeIds']) {
            return response()->json(['error' => 'Отсутствуют ID атрибутов']);
        }
        $attributes = Attribute::whereIn('id', $request['attributeIds'])->get();
        if (!$attributes) {
            return response()->json(['error' => 'Атрибуты не найдены']);
        }
        return view('admin.shop.products.partials.variants', compact('attributes'));
    }

    /**
     * @throws Throwable
     */
    public function photoUp (Product $product, Photo $photo): RedirectResponse
    {
        try {
            $product->movePhotoUp($photo->id);
            return redirect()->back()->with('success', 'Фото успешно перемещено.');
        } catch (\DomainException $e) {
            return redirect()->route('admin.shop.categories.show', compact('product'))
                ->with('error', $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function photoDown (Product $product, Photo $photo): RedirectResponse
    {
        try {
            $product->movePhotoDown($photo->id);
            return redirect()->back()->with('success', 'Фото успешно перемещено.');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function photoRemove (Product $product, Photo $photo): RedirectResponse
    {
        try {
            $product->removePhoto($photo->id);
            return redirect()->back()->with('success', 'Фото успешно удалено.');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function setStatus(Request $request): RedirectResponse
    {
        try {
            $answer = $this->service->setStatus($request);
            return back()->with('success', $answer)->withInput();
        } catch (\DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
