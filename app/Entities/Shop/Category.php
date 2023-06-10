<?php

namespace App\Entities\Shop;


use Throwable;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Kalnoy\Nestedset\NodeTrait;
use App\Traits\WithMediaGallery;
use App\Events\ShopCategoryOnDelete;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $import_id
 * @property string $supplier
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property int|null $parent_id
 * @property string $short_description
 * @property string $description
 * @property int $published
 * @property array $meta
 * @property int $depth
 *
 * @property Category $parent
 * @property Collection $children
 * @property Collection $attributes
 * @property Collection $photos
 * @property Collection $files
 * @property Collection $products
 * @property Collection $filters
 */
class Category extends Model
{
    use NodeTrait, WithMediaGallery;
    use Searchable {
        Searchable::usesSoftDelete insteadof NodeTrait;
    }

    public const IMAGE_SIZE_SMALL = 150;

    public const IMAGE_SIZE_THUMB = 250;

    public const IMAGE_SIZE_MEDIUM = 480;

    public const IMAGE_SIZE_LARGE = 1024;

    public const IMAGE_SIZE_FULL = 1980;

    private const IMAGE_SIZES = [
        'small', 'thumb', 'medium', 'large', 'full'
    ];

    public static $searchable = ['name', 'title', 'description'];

    public const IMAGE_PATH = 'files/categories/';


    protected $table = 'shop_categories';

    public $timestamps = false;

    protected $fillable = [
        'name', 'slug', 'title', 'parent_id', 'short_description', 'description', 'published', 'meta', 'photo', 'files', 'import_id', 'supplier'
    ];

    protected $casts = [
        'meta' => 'array',
        'published' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });

        static::deleting(function ($category) {
            Event::dispatch(ShopCategoryOnDelete::class, $category);
        });
    }

    public function toSearchableArray(): array
    {
        return [
            'description' => $this->description,
            'short_description' => $this->short_description
        ];
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

    public function getMainImage($size, $index = 0):null|string
    {
        /** @var Photo $photo */
        $photos = $this->photos->toArray();
        if (!empty($photos[$index])) {
            $photo = $photos[$index];
            return 'storage/' . $photo['path'] . $size . '_' . $photo['name'];
        }
        return null;
    }

    public function getPath():string
    {
        return implode('/', array_merge($this->ancestors()->pluck('slug')->toArray(), [$this->slug]));
    }

    public function parentAttributes():array
    {
        return $this->parent ? $this->parent->allAttributes() : [];
    }

    public function allAttributes():array
    {
        return array_merge($this->parentAttributes(), $this->attributes()->orderBy('sort')->getModels());
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'shop_attribute_category');
    }

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'shop_categories_photos','category_id', 'photo_id')->orderBy('sort');
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'shop_categories_files','category_id', 'file_id')->orderBy('sort');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function filters():BelongsToMany
    {
        return $this->belongsToMany(Filter::class, 'shop_filters_categories', 'category_id', 'filter_id');
    }

    public function isPublished(): bool
    {
        return (bool)$this->published;
    }

    /**
     * @throws Throwable
     */
    public function publised():void
    {
        $this->published = true;
        $this->saveOrFail();
    }

    /**
     * @throws Throwable
     */
    public function unPublished():void
    {
        $this->published = false;
        $this->saveOrFail();
    }

}
