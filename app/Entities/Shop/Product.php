<?php

namespace App\Entities\Shop;


use Illuminate\Support\Str;
use App\Traits\WithMediaGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id
 * @property string $sku
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property integer $category_id
 * @property integer $brand_id
 * @property integer $price
 * @property integer $compare_at_price
 * @property integer $amount_in_package
 * @property string $status
 * @property string $order_variants
 * @property string $packaging
 * @property string $product_type
 * @property string $unit
 * @property string $country
 * @property float $weight
 * @property float $volume
 * @property boolean $hit
 * @property boolean $new
 * @property integer $quantity
 * @property array $meta
 * @property string $import_id
 *
 * @property Brand $brand
 * @property Category $category
 * @property Category[] $categories
 * @property Value[] $values
 * @property Product[] $related
 * @property Product[] $variants
 * @property Photo[] $photos
 *
 * @method Builder active()
 * @method Builder catBuy()
 *
 */
class Product extends Model
{

    use HasFactory, WithMediaGallery;

    protected $table = 'shop_products';

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category_id',
        'brand_id',
        'price',
        'compare_at_price',
        'status',
        'weight',
        'quantity',
        'meta',
        'product_type',
        'volume',
        'packaging',
        'unit',
        'amount_in_package',
        'country',
        'order_variants',
        'import_id',
        'supplier'
    ];

    protected $casts = [
        'meta' => 'array',
        'hit' => 'boolean',
        'new' => 'boolean'
    ];

    public static $searchable = ['name', 'sku'];

    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_NEW = 'new';
    const STATUS_HIT = 'hit';

    const TYPE_MODULE = 'module';
    const TYPE_COMPANION = 'companion';
    const TYPE_MAIN = 'main';

    public const IMAGE_SIZE_SMALL = 150;

    public const IMAGE_SIZE_THUMB = 250;

    public const IMAGE_SIZE_MEDIUM = 480;

    public const IMAGE_SIZE_LARGE = 1024;

    public const IMAGE_SIZE_FULL = 1980;

    private const IMAGE_SIZES = [
        'small', 'thumb', 'medium', 'large', 'full'
    ];

    public const IMAGE_PATH = 'files/products/';

    public static function boot()
    {
        parent::boot();

        Model::preventLazyLoading();
        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    public static function types():array
    {
        return [
            'Компаньон' => self::TYPE_COMPANION,
            'Модуль'    => self::TYPE_MODULE,
            'Обычный'   => self::TYPE_MAIN
        ];
    }

    public static function statusList(): array
    {
        return [
            self::STATUS_DRAFT  => 'Черновик',
            self::STATUS_ACTIVE => 'Опубликованный',
            self::STATUS_HIT    => 'Лидер продаж',
            self::STATUS_NEW    => 'Новинка'
        ];
    }

    public static function productStatuses(): array
    {
        return [
            self::STATUS_DRAFT  => 'Черновик',
            self::STATUS_ACTIVE => 'Опубликованный'
        ];
    }

    public static function statusLabel($status): string
    {
        return match ($status) {
            self::STATUS_DRAFT => 'text-bg-secondary',
            self::STATUS_ACTIVE => 'text-bg-success',
            self::STATUS_NEW => 'text-bg-info',
            self::STATUS_HIT => 'text-bg-warning',
            default => 'text-bg-light',
        };
    }

    public static function statusName($status): string
    {
        return self::statusList()[$status];
    }

    public static function getImageParams():array
    {
        return [
            'sizes' => [
                'small'  => self::IMAGE_SIZE_SMALL,
                'thumb'  => self::IMAGE_SIZE_THUMB,
                'medium' => self::IMAGE_SIZE_MEDIUM,
                'large'  => self::IMAGE_SIZE_LARGE,
                'full'   => self::IMAGE_SIZE_FULL,
            ],
            'path' => self::IMAGE_PATH
        ];
    }

    public function isCanBuy($quantity = null):bool
    {
        $q = $quantity !== null ? $quantity : $this->quantity;
        return ($this->order_variants != 'Вывод' && $q > 0) || ($this->order_variants == 'Вывод' && $q > 0);
    }

    public function isActive():bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isDraft():bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isNew():bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isHit():bool
    {
        return $this->status === self::STATUS_HIT;
    }

    public function setHit():void
    {
        dd($this);
        $this->update(['hit'=>1]);
    }

    public function revokeHit():void
    {
        $this->update(['hit'=>0]);
    }

    public function setNew():void
    {
        $this->update(['new' => 1]);
    }

    public function revokeNew():void
    {
        $this->update(['new'=>0]);
    }

    public function published():void
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    public function unPublished():void
    {
        $this->update(['status' => self::STATUS_DRAFT]);
    }

    public function setPrice($price): void
    {
        $this->update(['price' => $price]);
    }

    public function checkout($quantity):void
    {
        $this->setQuantity($this->quantity - $quantity);
    }

    private function setQuantity($quantity):void
    {
        if ($this->quantity <= 0) {
            $this->quantity = 0;
            //TODO Add Event empty stock
        } else {
            $this->quantity = $quantity;
        }
    }

    public function changeQuantity($quantity):void
    {
        $this->update(['quantity'=>$quantity]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPreviewValues():array
    {
        $result = [];
        $attributes = [
            'Тип обоев',
            'Материал основы',
            'Материал покрытия',
            'Длина рулона',
            'Ширина рулона',
            'Цвет'
        ];
        foreach ($this->values as $value) {
            if (!in_array($value->attribute->name, $attributes)) {
                continue;
            }
            $result[] = $value;
        }
        return $result;
    }

    public function getDiffAttributes(): array
    {
        $result = [];
        if (!$this->variants) {
            return  $result;
        }
        foreach ($this->variants as $variant) {
            foreach ($variant->values as $varValue) {
                foreach ($this->values as $value) {
                    if ($varValue->attribute->name == $value->attribute->name && $value->value != $varValue->value) {
                        $result[] = $value->attribute->name;
                    }
                }
            }
        }
        return array_unique($result);
    }

    public function getColors()
    {
        $varIds = [];
        foreach ($this->variants as $variant) {
            $varIds[] = $variant->id;
        }
        $varIds[] = $this->id;
        sort($varIds);

        return Value::where('attribute_id',function($query) {
            $query->select('id')->from('shop_attributes')->where('name', 'Цвет');
        })->whereIn('product_id', $varIds)->with(['product'])->get();
    }

    // Relationships
    public function brand():HasOne
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function category():HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function categories():BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'shop_category_product', 'product_id', 'category_id');
    }

    public function tags():BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'shop_product_tag', 'product_id', 'tag_id');
    }

    public function values():HasMany
    {
        return $this->hasMany(Value::class, 'product_id', 'id')->with(['attribute', 'product.photos']);
    }

    public function photos():BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'shop_products_photos','product_id', 'photo_id')->orderBy('sort');
    }

    public function variants():BelongsToMany
    {
        return $this->belongsToMany(self::class, 'shop_variants','product_id', 'variant_id')->with(['photos', 'values']);
    }

    public function related():BelongsToMany
    {
        return $this->belongsToMany(self::class, 'shop_related','product_id', 'related_id')->with(['photos','category']);
    }

    public function scopeCanBuy(Builder $query):Builder
    {
        return $query->where('order_variants', '!=', 'Вывод');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
