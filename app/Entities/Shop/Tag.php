<?php

namespace App\Entities\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $seo_text
 * @property array $meta
 *
 */
class Tag extends Model
{

    protected $table = 'shop_tags';

    public $timestamps = false;

    protected $fillable = [
        'name', 'seo_text', 'meta', 'slug'
    ];

    protected $casts = [
        'meta' => 'array'
    ];

    public static $searchable = ["name"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }
}
