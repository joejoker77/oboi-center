<?php

namespace App\Entities\Shop;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property integer $cost
 * @property integer $min_weight
 * @property integer $max_weight
 * @property integer $min_amount
 * @property integer $max_amount
 * @property integer $min_dimensions
 * @property integer $max_dimensions
 * @property integer $sort
 */
class DeliveryMethod extends Model
{
    protected $table = 'shop_delivery_methods';

    public $timestamps = false;

    protected $fillable = [
        'name', 'cost', 'min_weight', 'max_weight', 'min_amount', 'max_amount', 'min_dimensions', 'max_dimensions', 'sort'
    ];
}
