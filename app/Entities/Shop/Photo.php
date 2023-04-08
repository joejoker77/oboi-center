<?php

namespace App\Entities\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $alt_tag
 * @property string $description
 * @property string $path
 * @property int $sort
 */
class Photo extends Model
{
    use HasFactory;

    protected $table = 'shop_photos';

    protected $fillable = ['name', 'path', 'sort'];

    public $timestamps = false;

    public function setSort($sort):void
    {
        $this->sort = $sort;
    }

    public function getPhoto($size):string
    {
        return '/storage/'.$this->path.$size.'_'.$this->name;
    }

    public function isIdEqualTo($id): bool
    {
        return $this->id == $id;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'shop_categories_photos', 'category_id', 'photo_id');
    }

    public function variant(): BelongsToMany
    {
        return $this->belongsToMany(Variant::class, 'shop_variants_photos', 'photo_id', 'variant_id');
    }
}
