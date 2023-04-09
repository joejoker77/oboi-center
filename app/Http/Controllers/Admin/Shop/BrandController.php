<?php

namespace App\Http\Controllers\Admin\Shop;

use Throwable;
use App\Entities\Shop\Brand;
use Illuminate\Http\Request;
use App\Traits\WithMediaGallery;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\UseCases\Admin\Shop\BrandService;

class BrandController extends Controller
{

    private $service;


    public function __construct(BrandService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $brands = Brand::orderBy('name')->get();
        return view('admin.shop.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.shop.brands.create');
    }

    /**
     * @throws Throwable
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $brand = $this->service->create($request);
            return redirect()->route('admin.shop.brands.show', compact('brand'));
        } catch (\Exception $e) {
            return redirect()->refresh()->with('error', $e->getMessage());
        }

    }

    public function show(Brand $brand): View
    {
        return view('admin.shop.brands.show', compact('brand'));
    }

    public function edit(Brand $brand): View
    {
        return view('admin.shop.brands.edit', compact('brand'));
    }

    /**
     * @throws Throwable
     */
    public function update(Request $request, Brand $brand): RedirectResponse
    {
        try {
            $this->service->update($request, $brand);
            return redirect()->route('admin.shop.brands.show', compact('brand'))
                ->with('success', 'Бренд '. $brand->name . ' успешно обновлен');
        } catch (\Exception $e){
            echo $e->getMessage().PHP_EOL;
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();
        return redirect()->route('admin.shop.brands.index')->with('success', 'Брэнд, успешно удален.');
    }

    public function photoRemove(Brand $brand):RedirectResponse
    {
        $this->service->removePhoto($brand);
        return redirect()->route('admin.shop.brands.show', compact('brand'))
            ->with('success', 'Лого бренда '. $brand->name . ' успешно удалено');
    }
}
