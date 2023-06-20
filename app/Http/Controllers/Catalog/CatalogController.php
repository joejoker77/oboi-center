<?php

namespace App\Http\Controllers\Catalog;

use App\Cart\Cart;
use App\Entities\Shop\Product;
use App\Entities\User\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Entities\Shop\Category;
use App\Http\Router\ProductPath;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\UseCases\ReadModels\SearchService;
use App\Http\Requests\Products\SearchRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Butschster\Head\Contracts\MetaTags\MetaInterface;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class CatalogController extends Controller
{

    private SearchService $search;

    private Cart $cart;

    protected MetaInterface $meta;

    public function __construct(SearchService $search, Cart $cart, MetaInterface $meta)
    {
        $this->search            = $search;
        $this->cart              = $cart;
        $this->meta              = $meta;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function index(SearchRequest $request, ProductPath $path): View
    {
        $cartAllItems     = $this->cart->getAllItems();
        $category         = $path->category;
        $result           = $this->search->search($category, $request, 20, $request->get('page', 1));

        if ($result->products->isEmpty()) {
            abort(404);
        }

        $products         = $result->products;
        $categoriesCounts = $result->categoriesCounts;
        $query            = $category ? $category->children() : Category::whereIsRoot();
        $categories       = $query->defaultOrder()->withDepth()->getModels();
        $categories       = array_filter($categories, function (Category $category) use ($categoriesCounts) {
            return isset($categoriesCounts[$category->id]) && $categoriesCounts[$category->id] > 0;
        });

        if (!$path->product) {
            if ($category && !empty($category->meta)) {
                $this->meta->setTitle($category->meta['title']);
                $this->meta->setDescription($category->meta['description']);
            }
            return view('shop.products.index', compact('category', 'categories', 'categoriesCounts', 'products', 'cartAllItems'));
        } else {
            $product = $path->product;
            if (!empty($product->meta)) {
                $this->meta->setTitle($product->meta['title']);
                $this->meta->setDescription($product->meta['description']);
            }
            return view('shop.products.show', compact('category', 'categories', 'product', 'products', 'categoriesCounts', 'cartAllItems'));
        }
    }

    public function filter(Request $request):View
    {
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if($pageWasRefreshed ) {
            $request->query->remove('page');
        }

        $result         = $this->search->filter($request, 20, $request->get('page', 1));
        $cartAllItems   = $this->cart->getAllItems();
        $products       = $result->products;
        $restTags       = $result->tags;
        $restCategories = $result->categories;
        $restAttributes = $result->attributes;

        $this->meta->setTitle('Результат поиска по фильтру');
        $this->meta->setDescription('На данной странице отображаются товары к которым применен поисковый фильтр.');
        $this->meta->setRobots('noindex, nofollow');

        return view('shop.search.result', compact('cartAllItems', 'restCategories', 'restTags', 'products', 'restAttributes', 'request'));
    }

    public function ajaxSearch(Request $request): View
    {
        $result = $this->search->searchFromString($request, 200);
        return view('shop.search.ajax-result', compact('result'));
    }

    public function search(Request $request): View
    {
        $products = $total = null;
        if (!$request->get('query')) {
            Session::flash('error', 'Пустой параметр запроса');
            return view('shop.search.search-result');
        }
        Session::remove('error');
        $result = $this->search->searchFromString($request, 20);

        $this->meta->setTitle('Результат поиска');
        $this->meta->setDescription('На данной странице отображаются товары и статьи найденные по текстовому запросу.');
        $this->meta->setRobots('noindex, nofollow');

        if (!empty($result->products)) {
            $total = $this->search->getTotalProducts($request->get('query'));
            $products = new LengthAwarePaginator($result->products, $total, 20, $request->get('page'), ['path' => '/shop/search']);
        }

        return view('shop.search.search-result', compact('result', 'products', 'total'));
    }

    public function addFavorite(Request $request, Product $product):RedirectResponse
    {
        /** @var User|null $user */
        if (!$user = Auth::user()) {
            return back()->with('error', 'Пользователь не найден!');
        }
        try {
            $user->favorites()->create([
                'product_id' => $product->id
            ]);
        } catch (\Exception $e) {
            $message = stripos($e->getMessage(), 'Duplicate entry') ? 'Данный продукт уже находится у вас в избранном!':$e->getMessage();
            return back()->with('error', $message);
        }

        return back()->with('success', $product->name . ' успешно добавлен в избранное!');
    }

    public function removeFavorite(Request $request, Product $product):RedirectResponse
    {
        /** @var User|null $user */
        if (!$user = Auth::user()) {
            return back()->with('error', 'Пользователь не найден!');
        }
        try {
            $favorite = $user->favorites()->where(['product_id' => $product->id]);
            $favorite->delete();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $product->name . ' успешно удален из избранного!');
    }

}
