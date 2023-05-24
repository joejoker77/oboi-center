<?php

namespace App\Http\Controllers\Site;

use App\Entities\Shop\Product;
use App\Entities\Shop\Category;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\UseCases\ReadModels\HomeService;
use Butschster\Head\Contracts\MetaTags\MetaInterface;

class HomeController extends Controller
{
    private HomeService $service;

    protected MetaInterface $meta;

    public function __construct(HomeService $service, MetaInterface $meta)
    {
        $this->service = $service;
        $this->meta    = $meta;
    }

    public function index(): View
    {
        try {
            $categories     = Category::whereDescendantOf(1)->hasChildren()->get();
            $user           = Auth::user();
            $reviews        = $this->service->getReviews();
            $newCollections = Category::with('photos', 'parent')
                ->whereIn('id', array_unique(Product::where('order_variants', 'Новинка')
                    ->pluck('category_id')->toArray()))->get();

            $this->meta->setTitle('Интернет магазин обоев - "Обои Центр"');
            $this->meta->setDescription('Купть обои в Москве. Обои под заказ. Большой выбор обоев мировых брендов.');

            return view('home', compact('categories', 'user', 'newCollections', 'reviews'));
        } catch (\Throwable $e) {
            return view('home')->with('error', $e->getMessage());
        }
    }
}
