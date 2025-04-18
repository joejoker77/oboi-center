<?php

namespace App\Http\Controllers\Catalog;

use App\Cart\Cart;
use App\Entities\Shop\Order;
use Illuminate\View\View;
use App\UseCases\Shop\OrderService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Entities\Shop\DeliveryMethod;
use Illuminate\Http\RedirectResponse;
use App\UseCases\Auth\RegisterService;
use App\Http\Requests\Order\OrderRequest;
use Butschster\Head\Contracts\MetaTags\MetaInterface;

class CheckoutController extends Controller
{
    private OrderService $service;
    private Cart $cart;
    private RegisterService $registerService;

    protected MetaInterface $meta;

    public function __construct(OrderService $service, Cart $cart, RegisterService $registerService, MetaInterface $meta)
    {
       $this->service         = $service;
       $this->cart            = $cart;
       $this->registerService = $registerService;
       $this->meta            = $meta;
    }

    public function index():View|RedirectResponse
    {
        $user    = Auth::user();
        $methods = DeliveryMethod::all();
        $cart    = $this->cart;

        $this->meta->setTitle('Оформление заказа - Обои Центр');
        $this->meta->setRobots('noindex, nofollow');

        if ($cart->getAmount() === 0) {
            return back()->with('error', 'Ваша корзина пуста. Нет товаров для оформления заказа');
        }

        return view('shop.cart.checkout', compact('user', 'methods', 'cart'));
    }

    public function store(OrderRequest $request): RedirectResponse
    {
        $user = Auth::user();
        try {
            $order = $this->service->checkout($request, $this->cart, $this->registerService);

            if (!$user) {
                return redirect()->route('login')->with('success', 'Ваш заказ создан. Если хотите оплатить заказ онлайн или посмотреть детали заказа, пожалуйста авторизуйтесь. На указанный вами телефон, мы выслали SMS с временным паролем.');
            } else {
                if ($order->payment_method === Order::PAYMENT_CARD) {
                    return redirect()->route('shop.order-payment', $order)->with('success', 'Вы выбрали оплату онлайн. Пожалуйста оплатите заказ удобным для вас способом');
                }
                return redirect()->route('cabinet.profile.index')->withFragment('#orders-tab-pane')->with('success', 'Ваш заказ успешно создан. Наш менеджер свяжется с вами в ближайшее время, для уточнения деталей заказа.');
            }

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
