<?php

namespace App\Entities\Blog;


use Throwable;
use Illuminate\Support\Str;
use App\Entities\Shop\Photo;
use Laravel\Scout\Searchable;
use Kalnoy\Nestedset\NodeTrait;
use App\Traits\WithMediaGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property int|null $parent_id
 * @property string $description
 * @property string $status
 * @property array $meta
 * @property int $depth
 *
 * @property Category $parent
 * @property Collection $children
 * @property Collection $photos
 * @property Collection $posts
 */
class Category extends Model
{
    use NodeTrait, WithMediaGallery;
    use Searchable {
        Searchable::usesSoftDelete insteadof NodeTrait;
    }

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const IMAGE_SIZE_SMALL = 150;

    public const IMAGE_SIZE_THUMB = 250;

    public const IMAGE_SIZE_MEDIUM = 480;

    public const IMAGE_SIZE_LARGE = 1024;

    public const IMAGE_SIZE_FULL = 1980;

    private const IMAGE_SIZES = [
        'small', 'thumb', 'medium', 'large', 'full'
    ];

    public static $searchable = ['name', 'title'];

    public const IMAGE_PATH = 'files/blog/categories/';


    protected $table = 'blog_categories';

    public $timestamps = false;

    protected $fillable = [
        'name', 'slug', 'title', 'parent_id', 'description', 'status', 'meta', 'photo'
    ];

    protected $casts = [
        'meta'      => 'array',
        'published' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });

//        static::deleting(function ($category) {
//            Event::dispatch(ShopCategoryOnDelete::class, $category);
//        });
    }

    public function toSearchableArray(): array
    {
        return [
            'description' => $this->description,
            'title'       => $this->title
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

    public function getStatuses():array
    {
        return [
            self::STATUS_ACTIVE => 'Опубликована',
            self::STATUS_DRAFT  => 'Черновик'
        ];
    }

    public function isActive():bool
    {
        return $this->status === $this::STATUS_ACTIVE;
    }

    public function getPath():string
    {
        return implode('/', array_merge($this->ancestors()->pluck('slug')->toArray(), [$this->slug]));
    }

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'blog_categories_photos','category_id', 'photo_id')->orderBy('sort');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function isPublished(): bool
    {
        return $this->published;
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
