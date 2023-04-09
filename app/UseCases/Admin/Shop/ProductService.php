<?php

namespace App\UseCases\Admin\Shop;


use App\Entities\Shop\Value;
use Illuminate\Http\Request;
use Throwable;
use App\Entities\Shop\Product;
use App\Entities\Shop\Variant;
use App\Entities\Shop\Attribute;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\Shop\ProductRequest;

class ProductService
{

    /**
     * @throws Throwable
     */
    public function create(ProductRequest $request):Product
    {
        DB::beginTransaction();
        try {
            $product = Product::create([
                'name'              => $request['name'],
                'sku'               => $request['sku'],
                'price'             => $request['price'] ?? 0,
                'weight'            => $request['weight'],
                'quantity'          => $request['quantity'],
                'description'       => $request['description'],
                'product_type'      => $request['type'],
                'meta'              => $request['meta'],
                'category_id'       => $request['category_id'],
                'brand_id'          => $request['brand_id'],
                'status'            => Product::STATUS_DRAFT,
                'volume'            => $request['volume'] ?? null,
                'packaging'         => $request['packaging'] ?? null,
                'unit'              => $request['unit'] ?? null,
                'amount_in_package' => $request['amount_in_package'] ?? null,
                'country'           => $request['country'] ?? null,
                'order_variants'    => $request['order_variants'] ?? null,
                'import_id'         => $request['import_id'] ?? null,
                'supplier'          => $request['supplier'] ?? 'Обои-центр',
                'compare_at_price'  => $request['compare_at_price'] ?? null
            ]);

            if ($request->get('product_attributes')) {
                $this->assignAttributes($request->get('product_attributes'), $product);
            }

            if ($request->get('product_categories')) {
                $product->categories()->attach($request->get('product_categories'));
            }

            if ($request->get('product_tags')) {
                $product->tags()->attach($request->get('product_tags'));
            }

            $this->checkImages($request, $product);

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException('Сохранение сущности Product завершилось с ошибкой. Подробности: ' . $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function update(ProductRequest $request, Product $product)
    {
        DB::beginTransaction();
        try {
            $product->update($request->only([
                'name',
                'sku',
                'price',
                'weight',
                'quantity',
                'description',
                'product_type',
                'meta',
                'category_id',
                'brand_id',
                'status',
                'volume',
                'packaging',
                'unit',
                'amount_in_package',
                'country',
                'order_variants',
                'import_id',
                'supplier',
                'compare_at_price'
            ]), [
                'name'              => $request['name'],
                'sku'               => $request['sku'],
                'price'             => $request['price'] ?? 0,
                'weight'            => $request['weight'],
                'quantity'          => $request['quantity'],
                'description'       => $request['description'],
                'product_type'      => $request['product_type'],
                'meta'              => $request['meta'],
                'category_id'       => $request['category_id'],
                'brand_id'          => $request['brand_id'],
                'status'            => Product::STATUS_DRAFT,
                'volume'            => $request['volume'] ?? null,
                'packaging'         => $request['packaging'] ?? null,
                'unit'              => $request['unit'] ?? null,
                'amount_in_package' => $request['amount_in_package'] ?? null,
                'country'           => $request['country'] ?? null,
                'order_variants'    => $request['order_variants'] ?? null,
                'import_id'         => $request['import_id'] ?? null,
                'supplier'          => $request['supplier'] ?? 'Обои-центр',
                'compare_at_price'  => $request['compare_at_price'] ?? null
            ]);

            if ($request->get('product_attributes')) {
                $this->assignAttributes($request->get('product_attributes'), $product);
            }

            if ($request->get('product_categories')) {
                $product->categories()->detach();
                $product->categories()->attach($request->get('product_categories'));
            }

            if ($request->get('product_tags')) {
                $product->tags()->detach();
                $product->tags()->attach($request->get('product_tags'));
            }

            $this->checkImages($request, $product);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException('Сохранение сущности Product завершилось с ошибкой. Подробности: ' . $e->getMessage());
        }
    }

    public function setStatus(Request $request):string
    {
        $action = $request->get('action');
        $answer = '';
        if (empty($selected = $request->get('selected'))) {
            throw new \DomainException('Не выбран ни один товар');
        }
        $products = Product::find($selected);

        /** @var Product $product */
        foreach ($products as $product) {
            switch ($action) {
                case 'hit': $product->setHit(); $answer = 'Выбранные товары успешно добавлены в хиты продаж';break;
                case 'new': $product->setNew(); $answer = 'Выбранные товары успешно добавлены в новинки';break;
                case 'revoke-hit': $product->revokeHit(); $answer = 'Выбранные товары успешно удалены из хитов продаж';break;
                case 'revoke-new': $product->revokeNew(); $answer = 'Выбранные товары успешно удалены из новинок';break;
                case 'published': $product->published(); $answer = 'Выбранные товары успешно опубликованы';break;
                case 'un-published': $product->unPublished(); $answer = 'Выбранные товары успешно сняты с бубликации';break;
                case 'remove': $product->delete(); $answer = 'Выбранные товары успешно удалены'; break;
            }
        }
        return $answer;
    }

    /**
     * @param array $attributes
     * @param Product $product
     * @return void
     */
    private function assignAttributes(array $attributes, Product $product):void
    {
        foreach ($attributes as $id => $options) {
            if ($attribute = Attribute::find($id)) {

                if ($attribute->mode == Attribute::MODE_SIMPLE && count($options) == 1) {
                    if (!Value::where('attribute_id', $id)->where('product_id', $product->id)->where('value', $options[0])->first()) {
                        $product->values()->create([
                            'attribute_id' => $id,
                            'value' => $options[0]
                        ]);
                    }
                } elseif ($attribute->mode == Attribute::MODE_MULTIPLE) {
                    if (!Value::where('attribute_id', $id)->where('product_id', $product->id)->where('value', trim(implode(', ', $options), ', '))->first()) {
                        $product->values()->create([
                            'attribute_id' => $id,
                            'value' => trim(implode(', ', $options), ', ')
                        ]);
                    }
                }
            }
        }
    }

    private function checkImages(ProductRequest $request, Product $product): void
    {
        if ($images = $request->file('photo')) {
            foreach ($images as $i => $image) {
                $info  = $image->getFileInfo();

                if ($product->photos()->where("name", '=', str_replace('.'.$info->getExtension(), '', $info->getFilename()) . '.webp')->first()) {
                    continue;
                }

                $photo = $product->photos()->create([
                    "name" => str_replace('.'.$info->getExtension(), '', $info->getFilename()) . '.webp',
                    "sort" => $i,
                    "path" => Product::getImageParams()['path'] . $product->id . '/images/'
                ]);
                save_image_to_disk($image, $photo, Product::getImageParams()['sizes']);
            }
        }
    }

    /**
     * @param ProductRequest $request
     * @param Product $product
     * @return void
     */
    private function createVariants(ProductRequest $request, Product $product): void
    {
        $arrays = [];
        $keys   = [];
        $requestValues = $request->get('product_attributes');
        foreach ($requestValues as $id => $value) {
            $attribute = Attribute::find($id);
            if (is_array($value) && $attribute->as_option) {
                $arrays[] = $value;
                $keys[]   = $id;
            }
        }
        if (count($arrays) < 2) {
            return;
        }
        $variants = $this->generateVariants($arrays);

        foreach ($variants as $i => $variant) {
            if (count($variant) > 1) {
                $variants[$i] = array_combine($keys, $variant);
                $name = implode('/', $variant);
            } else {
                $name = $variant[0];
            }
            foreach ($variants[$i] as $var) {
                if (strpbrk($var,'|')) {
                    $varArray = explode('|', $var);
                    $name = str_replace('|'.$varArray[1], '', $name);
                }
            }
            /** @var Variant $productVariant */
            $productVariant = $product->variants()->create([
                'name'     => $name,
                'sku'      => null,
                'price'    => $product->price,
                'weight'   => $product->weight,
                'quantity' => 0
            ]);
            foreach ($variants[$i] as $id => $value) {
                $productVariant->values()->create([
                    'attribute_id' => $id,
                    'value'        => $value
                ]);
            }
        }
    }

    private function generateVariants($arrays, $N=-1, $count=FALSE, $weight=FALSE): array
    {
        if ($N == -1) {
            $arrays = array_values($arrays);
            $count = count($arrays);
            $weight = array_fill(-1, $count+1, 1);
            $Q = 1;
            foreach ($arrays as $i=>$array) {
                $size = count($array);
                $Q = $Q * $size;
                $weight[$i] = $weight[$i-1] * $size;
            }

            $result = [];
            for ($n=0; $n<$Q; $n++)
                $result[] = $this->generateVariants($arrays, $n, $count, $weight);

        } else {
            $StateArr = array_fill(0, $count, 0);

            for ($i=$count-1; $i>=0; $i--)
            {
                $StateArr[$i] = floor($N/$weight[$i-1]);
                $N = $N - $StateArr[$i] * $weight[$i-1];
            }

            $result = [];
            for ($i=0; $i<$count; $i++)
                $result[$i] = $arrays[$i][$StateArr[$i]];
        }
        return $result;
    }
}
