<?php

namespace App\Entities\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property int $id
 * @property int $filter_id
 * @property string $name
 * @property array $categories
 * @property array $tags
 * @property array $attributes
 * @property boolean $display_header
 *
 * @property Filter $filter
 */
class FilterGroup extends Model
{
    protected $table    = 'shop_filter_groups';

    protected $fillable = ['filter_id', 'name', 'categories', 'tags', 'attributes', 'display_header'];

    protected $casts    = ['categories' => 'array', 'tags' => 'array', 'attributes' => 'array'];

    public $timestamps  = false;


    public function filter(): BelongsTo
    {
        return $this->belongsTo(Filter::class, 'filter_id', 'id');
    }
}
