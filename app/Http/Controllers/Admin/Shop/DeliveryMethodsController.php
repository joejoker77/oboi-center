<?php

namespace App\Http\Controllers\Admin\Shop;


use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Entities\Shop\DeliveryMethod;

class DeliveryMethodsController extends Controller
{

    public function index(Request $request):View
    {
        $query = DeliveryMethod::orderBy('sort');
        $this->queryParams($request, $query);
        $methods = $query->paginate(20);

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

    public function destroy(DeliveryMethod $delivery_method):RedirectResponse
    {
        $delivery_method->delete();
        return redirect()->route('admin.shop.delivery-methods.index');
    }

    public function edit(DeliveryMethod $delivery_method):View
    {
        return view('admin.shop.delivery.edit', compact('delivery_method'));
    }

    public function update(Request $request, DeliveryMethod $delivery_method)
    {
        try {
            $delivery_method->update($request->all());
            return redirect()->route('admin.shop.delivery-methods.index', $delivery_method)->with('success', 'Метод успешно обновлен');
        } catch (\Exception|\DomainException $e) {
            echo $e->getMessage();
            return redirect()->route('admin.shop.delivery-methods.edit', $delivery_method)->with('error', $e->getMessage());
        }
    }

    public function remove(Request $request)
    {
        if (empty($selected = $request->get('selected'))) {
            return back()->with('error', 'Не выбран ни один метод');
        }
        $methods = DeliveryMethod::find($selected);
        foreach ($methods as $method) {
            $method->delete();
        }
        return back()->with('success', 'Выбранные методы успешно удалены');
    }

    private function queryParams(Request $request, $query)
    {
        if (!empty($value = $request->get('id'))) {
            $query->where('id', $value);
        }

        if (!empty($value = $request->get('name'))) {
            $query->where('name', 'like', '%' . $value . '%');
        }

        if (!empty($value = $request->get('cost'))) {
            $query->where('cost', '>=', $value);
        }
        if (!empty($value = $request->get('min_weight'))) {
            $query->where('min_weight', '>=', $value);
        }
        if (!empty($value = $request->get('min_amount'))) {
            $query->where('min_amount', '>=', $value);
        }
        if (!empty($value = $request->get('min_dimensions'))) {
            $query->where('min_dimensions', '>=', $value);
        }

        return $query;
    }
}
