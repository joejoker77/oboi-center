<?php
namespace App\Traits;

use Illuminate\Http\Request;
use App\Entities\Shop\Category;

trait QueryParams {

    public function queryParams(Request $request, $query)
    {
        if (!empty($value = $request->get('id'))) {
            $query->where('id', $value);
        }
        if (!empty($value = $request->get('sku'))) {
            $query->where('sku', $value);
        }
        if (!empty($value = $request->get('brand'))) {
            $query->where('brand_id', $value);
        }
        if (!empty($value = $request->get('name'))) {
            $query->where('name', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('category'))) {
            $catIds = Category::whereDescendantOrSelf($value)->pluck('id');
            $query->whereIn('category_id', $catIds);
        }
        if (!empty($value = $request->get('quantity'))) {
            $query->where('quantity', '<=', $value);
        }
        if (!empty($value = $request->get('price'))) {
            $query->where('price', '<=', $value);
        }
        if (!empty($value = $request->get('status'))) {
            $query->where('status', '=', $value);
        }
        if ($request->get('hit') == 'true') {
            $query->where('hit', true);
        }
        if($request->get('new') == 'true') {
            $query->where('new', true);
        }
        if(!empty($value = $request->get('sort'))) {
            if ($value[0] == '-') {
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
