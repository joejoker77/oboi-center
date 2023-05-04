<?php

namespace App\Entities\Shop;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\Collection;
use Kalnoy\Nestedset\QueryBuilder;

/**
 *
 * @property string $name
 * @property int $id
 * @property string $position
 *
 * @property FilterGroup[] $groups
 * @property Category[] $categories
 */
class Filter extends Model
{
    protected $table    = 'shop_filters';

    protected $fillable = ['name', 'position'];

    public $timestamps  = false;

    public function groups():HasMany
    {
        return $this->hasMany(FilterGroup::class, 'filter_id', 'id');
    }

    public function categories():BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'shop_filters_categories', 'filter_id','category_id');
    }

    public function allCategories(): Collection|Category|array|QueryBuilder
    {
        $result = [];
        $categories = $this->categories()->with('attributes')->get();
        foreach ($categories as $category) {
            $result = $category->descendantsAndSelf($category->id);
        }
        return $result;
    }
}
