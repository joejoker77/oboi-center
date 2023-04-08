<?php

namespace App\Entities\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $price
 * @property int $quantity
 * @property string $product_name
 * @property string $product_sku
 *
 * @property Order $order
 * @property Product[] $products
 */
class OrderItem extends Model
{
    protected $table = 'shop_order_items';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'price', 'quantity', 'product_name', 'product_sku'
    ];

    public static function create(Product $product, $price, $quantity):self
    {
        $item = new static();
        $item->product_id   = $product->id;
        $item->product_name = $product->name;
        $item->product_sku  = $product->sku;
        $item->price        = $price;
        $item->quantity     = $quantity;

        return $item;
    }

    public function getCost():int
    {
        return $this->quantity * $this->price;
    }

    public function products():HasMany
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }

    public function order():HasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
