<?php

namespace App\Http\Controllers\Catalog;

use App\Entities\Shop\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Entities\Shop\Status;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Davidnadejdin\LaravelAlfabank\Alfabank;

class PaymentController extends Controller
{
    public function paymentOrder(Order $order):View
    {
        $resp = Alfabank::register([
            'orderNumber' => $order->id,
            'amount'      => $order->getTotalCost() * 100,
            'returnUrl'   => env('ALFABANK_RETURN_URL'),
            'failUrl'     => env('ALFABANK_ERROR_URL'),
            'sessionTimeoutSecs' => 3600 * 12
        ]);

        if (isset($resp['orderId'])) {
            $order->payment_id  = $resp['orderId'];
            $order->payment_url = $resp['formUrl'];
            $order->save();
        }
        return view('shop.payment.order-payment', compact('order', 'resp'));
    }

    public function paymentResult(Request $request): View|RedirectResponse
    {
        /** @var Order $order */
        if (!$request->get('orderId') || !$order = Order::where('payment_id', $request->get('orderId'))->first()) {
            return redirect()->route('cart.index')->with('error', 'Заказ не найден');
        }
        $statusOrder = Alfabank::getOrderStatus([
            'orderId' => $order->payment_id
        ]);
        if ($order->current_status !== Status::PAID && $statusOrder['OrderStatus'] == 2 && $statusOrder['depositAmount'] == $order->getTotalCost()*100) {
            $order->current_status = Status::PAID;
            $order->payment_url    = null;
            $order->save();
        }

        if ($statusOrder['depositAmount'] < $order->getTotalCost()*100) {
            return back()->with('error', 'Заказ оплачен не полностью');
        }

        if ($statusOrder['depositAmount'] === 0) {
            return back()->with('error', 'Заказ не оплачен');
        }

        return view('shop.payment.payment-result', compact('order'));
    }

    public function paymentError(Request $request) : View|RedirectResponse
    {
        if (!$request->get('orderId') || !$order = Order::where('payment_id', $request->get('orderId'))->first()) {
            return redirect()->route('cart.index')->with('error', 'Заказ не найден');
        }
        return view('shop.payment.payment-error', $order);
    }
}
