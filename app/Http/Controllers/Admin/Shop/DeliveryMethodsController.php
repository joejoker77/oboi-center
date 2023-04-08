<?php

namespace App\Http\Controllers\Admin\Shop;


use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\Shop\DeliveryMethod;

class DeliveryMethodsController extends Controller
{

    public function index():View
    {
        $methods = DeliveryMethod::orderBy('sort')->get();
        return view('admin.shop.delivery.index', compact('methods'));
    }

    public function show(DeliveryMethod $method):View
    {
        return view('admin.shop.delivery.show', compact('method'));
    }

    public function create():View
    {
        return view('admin.shop.delivery.create');
    }

    public function store(Request $request):RedirectResponse
    {
        try {

            $request->validate([
                'name'           => 'required|string|max:32',
                'cost'           => 'required|integer',
                'min_weight'     => 'nullable|integer',
                'max_weight'     => 'nullable|integer',
                'min_amount'     => 'nullable|integer',
                'max_amount'     => 'nullable|integer',
                'min_dimensions' => 'nullable|integer',
                'max_dimensions' => 'nullable|integer',
                'sort'           => 'required|integer'
            ]);

            $delivery_method = DeliveryMethod::create([
                'name' => $request->get('name'),
                'cost' => $request->get('cost'),
                'min_weight' => $request->get('min_weight') ?? 0,
                'max_weight' => $request->get('max_weight') ?? 0,
                'min_amount' => $request->get('min_amount') ?? 0,
                'max_amount' => $request->get('max_amount') ?? 0,
                'min_dimensions' => $request->get('min_dimensions') ?? 0,
                'max_dimensions' => $request->get('max_dimensions') ?? 0,
                'sort' => $request->get('sort')
            ]);

            return redirect()->route('admin.shop.delivery-methods.index', compact('delivery_method'))
                ->with('success', 'Метод успешно создан');
        } catch (\Throwable $e) {
            echo $e->getMessage().PHP_EOL;
            return back()->with('error', 'Во время выполнения запроса, произошла следующая ошибка: '. $e->getMessage());
        }
    }
}
