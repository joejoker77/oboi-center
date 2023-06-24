<?php

namespace App\Http\Controllers\Admin\Shop;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Entities\Shop\Order;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request):View
    {
        $orders = Order::orderBy('created_at')->with('orderItems')->paginate(20);

        return view('admin.shop.orders.index', compact('orders'));
    }
}
