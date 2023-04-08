<?php

namespace App\Entities\Shop;


use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use function Symfony\Component\Translation\t;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $path
 * @property int $sort
 *
 * @method Builder doc()
 * @method Builder video()
 */
class File extends Model
{
    use HasFactory;

    protected $table = 'shop_files';

    protected $fillable = ['name', 'path', 'sort', 'type'];

    public $timestamps = false;

    const TYPE_VIDEO = 'video';
    const TYPE_DOCUMENT = 'document';

    public static function typesList() : array
    {
        return [
            self::TYPE_VIDEO => 'Видеофайл',
            self::TYPE_DOCUMENT => 'Документ'
        ];
    }

    public function setSort($sort):void
    {
        $this->sort = $sort;
    }

    public function getFile():string
    {
        return '/storage/'.$this->path.$this->name;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'shop_categories_files', 'category_id', 'file_id');
    }
    public function scopeVideo(Builder $builder): Collection|array
    {
        return $builder->where('type', '=', 'video')->get();
    }
    public function scopeDoc(Builder $builder): Collection|array
    {
        return $builder->where('type', '=', 'document')->get();
    }

    /**
     * @throws \Exception
     */
    public function getThumb():string
    {
        $fileInfo       = pathinfo($this->getFile());
        $fileName       = str_replace($fileInfo['extension'], 'jpg', $this->name);
        $fileExportName = str_replace($fileInfo['extension'], 'webp', $this->name);

        if (!Storage::disk()->exists($this->path.$fileExportName)) {
            FFMpeg::fromDisk('local')
                ->open('public/'.$this->path.$this->name)
                ->getFrameFromSeconds(5)
                ->export()
                ->toDisk('local')
                ->save('public/'.$this->path.$fileName);

            $img       = Image::make(Storage::disk()->get($this->path.$fileName));
            $watermark = Image::make(Storage::disk()->get('files/button.png'));
            $img->insert($watermark, 'center');
            $img->encode('webp', 60);
            Storage::disk()->put($this->path.$fileExportName, $img);
            Storage::disk()->delete($this->path.$fileName);
        }

        return "/storage/".$this->path.$fileExportName;
    }
}
