<?php

namespace App\Entities\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $attribute_id
 * @property int $product_id
 * @property string $value
 *
 * @property Attribute $attribute
 * @property Product $product
 *
 */
class Value extends Model
{
    public $timestamps = false;

    protected $table = 'shop_values';

    protected $fillable = [
        'attribute_id', 'product_id', 'value'
    ];

    public static function boot()
    {
        parent::boot();
        Model::preventLazyLoading();
    }

    public static function blank($attribute_id):self
    {
        $object = new static();
        $object->attribute_id = $attribute_id;
        return $object;
    }

    public function isForAttribute($id):bool
    {
        return $this->attribute_id === $id;
    }

    public function change($value):void
    {
        $this->update(['value' => $value]);
    }

    public function attribute():HasOne
    {
        return $this->hasOne(Attribute::class, 'id', 'attribute_id');
    }

    public function product():HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
