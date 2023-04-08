<?php

namespace App\Entities\Shop;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $percent
 * @property string $name
 * @property string $from_date
 * @property string $to_date
 * @property bool $active
 * @property integer $sort
 */
class Discount extends Model
{
    protected $table = 'shop_discounts';

    public $timestamps = false;

    protected $fillable = [
        'percent', 'name', 'from_date', 'to_date', 'active', 'sort'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];
}
