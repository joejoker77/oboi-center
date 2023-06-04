<?php

namespace App\Entities\Site\Navigations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;


/**
 * @property int $id
 * @property string $title
 * @property string $route_name
 * @property string $item_path
 * @property int $parent_id
 * @property int $sort
 * @property int $front_id
 * @property int $front_parent
 * @property int $entity_id
 * @property string $entity_type
 * @property string $image
 *
 * @property Menu $menu
 * @property NavItem[] $children
 */
class NavItem extends Model
{
    use HasRecursiveRelationships;

    protected $table = 'navItems';

    protected $fillable = [
        'title',
        'route_name',
        'item_path',
        'parent_id',
        'sort',
        'front_id',
        'front_parent',
        'entity_id',
        'entity_type',
        'link_text',
        'image'
    ];

    public $timestamps = false;

    public function menu():BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public static function getType($type):string
    {
        $listTypes = [
            'post'          => 'Статья',
            'category'      => 'Категория',
            'tag'           => 'Тэг',
            'brand'         => 'Бренд',
            'product'       => 'Продукт',
            'separator'     => 'Разделитель',
            'external'      => 'Внешний',
            'blog_category' => 'Категория блога'
        ];
        return $listTypes[$type];
    }
}
