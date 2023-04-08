<?php

namespace App\Entities\Shop;

use Kalnoy\Nestedset\Collection;
use Kalnoy\Nestedset\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use function Symfony\Component\String\s;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $type
 * @property string $unit
 * @property string $mode
 * @property array $variants
 * @property int $sort
 *
 * @property Collection $categories
 */
class Attribute extends Model
{

    public const TYPE_STRING = 'string';

    public const TYPE_INTEGER = 'integer';

    public const TYPE_FLOAT = 'float';

    const MODE_MULTIPLE  = 'multiple';
    const MODE_SIMPLE    = 'simple';
    const MODE_AS_OPTION = 'as_option';

    protected $table = 'shop_attributes';

    public $timestamps = false;

    protected $fillable = ['name', 'type', 'mode', 'unit', 'variants', 'sort'];

    protected $casts = [
        'variants' => 'array'
    ];

    public static function boot()
    {
        parent::boot();
        Model::preventLazyLoading();
    }

    public static function typesList():array
    {
        return [
            self::TYPE_STRING => 'Строка',
            self::TYPE_INTEGER => 'Число',
            self::TYPE_FLOAT => 'Дробное число'
        ];
    }

    public static function modeList():array
    {
        return [
            self::MODE_MULTIPLE  => "Множественный",
            self::MODE_SIMPLE    => "Обычный",
            self::MODE_AS_OPTION => "Как опция",
        ];
    }

    public function isString():bool
    {
        return $this->type === self::TYPE_STRING;
    }

    public function isInteger(): bool
    {
        return $this->type === self::TYPE_INTEGER;
    }

    public function isFloat():bool
    {
        return $this->type === self::TYPE_FLOAT;
    }

    public function isNumber():bool
    {
        return $this->isInteger() || $this->isFloat();
    }

    public function isSelect():bool
    {
        return count($this->variants) > 0;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'shop_attribute_category')->with('attributes');
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
