<?php

namespace App\Http\Controllers\Catalog;

use App\Cart\Cart;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Entities\Shop\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\UseCases\Admin\Shop\CartService;
use Butschster\Head\Contracts\MetaTags\MetaInterface;

class CartController extends Controller
{
    private CartService $service;

    private Cart $cart;

    protected MetaInterface $meta;

    public function __construct(CartService $service, Cart $cart, MetaInterface $meta)
    {
        $this->service = $service;
        $this->cart    = $cart;
        $this->meta    = $meta;
    }

    public function index()
    {
        $cart = $this->cart;
        $this->meta->setTitle('Корзина пользователя - Обои Центр');
        $this->meta->setRobots('noindex, nofollow');

        return view('shop.cart.index', compact('cart'));
    }

    public function add(Request $request, Product $product): View|RedirectResponse
    {
        if ($request->get("quantity") == 0) {
            return back()->with('error', 'Количество не может быть нулевым');
        }

        $this->service->add($product->id, $request->get("quantity"), $request->get('type_order'));

        return view('components.side-cart', ['cart' => $this->cart]);
    }

    public function changeQuantity(Request $request): View
    {
        $this->cart->set($request->get('item_id'), $request->get('quantity'));
        return view('components.side-cart', ['cart' => $this->cart]);
    }

    public function deleteItem(Request $request): JsonResponse
    {
        $this->cart->remove($request->get('item_id'));
        return response()->json(['status'=>'success', 'message'=>'Товар удален из корзины']);
    }
}
