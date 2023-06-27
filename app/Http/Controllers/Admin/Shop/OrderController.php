<?php

namespace App\Http\Controllers\Admin\Shop;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Entities\Shop\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function index(Request $request):View
    {
        $query = Order::with(['orderItems', 'user']);
        $query = $this->queryParams($request, $query);
        $orders = $query->paginate(20);

        return view('admin.shop.orders.index', compact('orders'));
    }

    public function create()
    {
        abort(404);
    }

    public function show(Order $order) : View
    {
        return view('admin.shop.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        return view('admin.shop.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $order->update($request->only('current_status'));
        return redirect()->route('admin.shop.orders.show', compact('order'))->with('success', 'Статус заказа успешно изменен');
    }

    public function destroy (Order $order) : RedirectResponse
    {
        $order->delete();
        return redirect()->route('admin.shop.orders.index')->with('success', "Заказ успешно удален");
    }

    public function multiDelete(Request $request)
    {
        if (!empty($request->get('selected'))) {

            foreach ($request->get('selected') as $orderId) {
                $order = Order::find($orderId);
                $order->delete();
            }

            return back()->with('success', 'Все выбранные заказы были удалены');
        } else {
            return back()->with('warning', 'Внимание! Не выбран ни один заказ');
        }
    }

    private function queryParams(Request $request, $query)
    {
        if (!empty($value = $request->get('id'))) {
            $query->where('id', $value);
        }
        if (!empty($value = $request->get('name'))) {
            $query->where('name', $value);
        }
        if (!empty($value = $request->get('status'))) {
            $query->where('current_status', $value);
        }
        if (!empty($value = $request->get('payment_method'))) {
            $query->where('payment_method', $value);
        }
        if (!empty($value = $request->get('delivery_name'))) {
            $query->where('delivery_name', $value);
        }
        if(!empty($value = $request->get('sort'))) {

            if ($value == 'user') {
                $query->join('users', 'users.id', '=', 'shop_orders.user_id')->orderBy('users.name', 'ASC');
            } else if ($value == '-user') {
                $query->join('users', 'users.id', '=', 'shop_orders.user_id')->orderBy('users.name', 'DESC');
            } else if ($value[0] == '-') {
                $value = str_replace('-', '', $value);
                $query->orderBy($value, 'DESC');
            } else {
                $query->orderBy($value);
            }
        } else {
            $query->orderBy('id');
        }
        return $query;
    }
}
