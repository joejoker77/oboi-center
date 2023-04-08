<?php

namespace App\Entities\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $seo_text
 * @property array $meta
 *
 * @property Photo $logo
 */
class Brand extends Model
{
    use HasFactory;

    protected $table = 'shop_brands';

    public $timestamps = false;

    protected $fillable = ['name', 'import_id', 'supplier', 'photo', 'seo_text', 'meta'];

    protected $casts = [
        'meta' => 'array'
    ];

    public static $searchable = ['name'];

    public const IMAGE_PATH = 'files/brands/images/';

    public const IMAGE_SIZE_LARGE = 300;

    public const IMAGE_SIZE_MEDIUM = 150;

    public const IMAGE_SIZE_SMALL = 75;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    public static function getImageParams():array
    {
        return [
            'sizes' => [
                'small' => self::IMAGE_SIZE_SMALL,
                'medium' => self::IMAGE_SIZE_MEDIUM,
                'large' => self::IMAGE_SIZE_LARGE
            ],
            'path' => self::IMAGE_PATH
        ];
    }

    public function logo(): HasOne
    {
        return $this->hasOne(Photo::class);
    }
}
