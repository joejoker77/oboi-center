<?php

namespace App\Entities\Site\Navigations;


use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $title
 * @property string $handler
 * @property bool $show_title
 * @property NavItem[] $navItems
 */
class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $fillable = ['title', 'handler', 'show_title'];

    protected $casts = ['show_title' => 'boolean'];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->attributes['handler'])) {
                $model->handler = Str::slug($model->title);
            }
        });
    }

    public function navItems():HasMany
    {
        return $this->hasMany(NavItem::class, 'menu_id', 'id');
    }
}
