<?php

namespace App\Entities\Shop;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @property string $name
 * @property int $id
 * @property array $visible_in_categories
 * @property string $position
 */
class Filter extends Model
{
    protected $table    = 'shop_filters';

    protected $fillable = ['name', 'visible_in_categories', 'position'];

    protected $casts    = ['visible_in_categories' => 'array'];

    public $timestamps = false;

    public function groups():HasMany
    {
        return $this->hasMany(FilterGroup::class, 'filter_id', 'id');
    }

}
