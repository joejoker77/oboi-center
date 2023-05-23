<?php

namespace App\Http\Controllers\Catalog;

use App\Cart\Cart;
use App\Http\Requests\Products\FilterResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Entities\Shop\Category;
use App\Http\Router\ProductPath;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\UseCases\ReadModels\SearchService;
use App\Http\Requests\Products\SearchRequest;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Support\Facades\Session;
use function PHPUnit\Framework\isEmpty;

class CatalogController extends Controller
{

    private SearchService $search;

    private Cart $cart;

    public function __construct(SearchService $search, Cart $cart)
    {
        $this->search = $search;
        $this->cart   = $cart;
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
            return view('shop.products.index', compact('category', 'categories', 'categoriesCounts', 'products', 'cartAllItems'));
        } else {
            $product = $path->product;
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

        return view('shop.search.result', compact('cartAllItems', 'restCategories', 'restTags', 'products', 'restAttributes', 'request'));
    }

}
