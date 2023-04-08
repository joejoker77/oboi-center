<?php

namespace App\UseCases\Admin\Shop;


use Throwable;
use App\Entities\Shop\Attribute;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\Shop\AttributeRequest;
use Illuminate\Http\Request;

class AttributeService
{
    /**
     * @throws Throwable
     */
    public function create(AttributeRequest $request): Attribute
    {
        DB::beginTransaction();
        try {
            $attribute = Attribute::create([
                'name'      => $request['name'],
                'type'      => $request['type'],
                'unit'      => $request['unit'],
                'mode'      => $request['mode'] ?? Attribute::MODE_SIMPLE,
                'variants'  => array_map('trim', preg_split('#[\r\n]+#', $request['variants'])),
                'sort'      => $request['sort'],
            ]);

            if ($request->get('categories')) {
                $attribute->categories()->attach($request['categories']);
            }
            DB::commit();
            return $attribute;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException('Сохранение сущности Attribute завершилось с ошибкой. Подробности: ' . $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function update(Request $request, Attribute $attribute):void
    {
        try {
            DB::beginTransaction();


            $variants = array_map('trim', preg_split('#[\r\n]+#', $request['variants']));

            $attribute->update($request->only([
                'name', 'type', 'unit', 'mode', 'sort'
            ]),[
                'name'     => $request['name'],
                'type'     => $request['type'],
                'unit'     => $request['unit'],
                'mode'     => $request['option'] ?? Attribute::MODE_SIMPLE,
                'sort'     => $request['sort']
            ]);

            $attribute->variants = $variants;
            $attribute->save();

            if ($request->get('categories')) {
                $attribute->categories()->detach();
                $attribute->categories()->attach($request['categories']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException('Обновление сущности Attribute завершилос с ошибкой. Подробности: ' . $e->getMessage());
        }
    }
}
