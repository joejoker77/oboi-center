<?php

namespace App\Listeners;

use App\Entities\Shop\Category;
use App\Entities\Shop\Photo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Filesystem\Filesystem;

/**
 *
 */
class ClearShopCategoryFiles implements ShouldQueue
{
    use InteractsWithQueue;


    /**
     * @param Category $category
     * @return void
     */
    public function handle(Category $category):void
    {
        /** @var Photo $photo */
        $src          = [];
        $doc          = new \DOMDocument();
        $categoryText = $category->description.$category->short_description;

        if ($categoryText) {
            $doc->loadHTML($categoryText);
            $images = $doc->getElementsByTagName('img');

            foreach ($images as $img) {
                $withoutBaseUrl = str_replace(URL::to('/'), '', $img->getAttribute('src'));
                $src[]          = str_replace('/storage/', '', $withoutBaseUrl);
            }

        }

        foreach ($category->photos as $photo) {
            $src[] = $photo->path . 'full_' . $photo->name;
            $src[] = $photo->path . 'large_' . $photo->name;
            $src[] = $photo->path . 'medium_' . $photo->name;
            $src[] = $photo->path . 'thumb_' . $photo->name;
            $src[] = $photo->path . 'small_' . $photo->name;

            $photo->delete();
        }

        if (!empty($src)) {
            $fileSystem = new Filesystem();
            foreach ($src as $filePath) {
                if (\Storage::exists($filePath)) {
                    $searchedDesc  = Category::whereFullText('description', $fileSystem->name($filePath))->get('id')->toArray();
                    $searchedShort = Category::whereFullText('short_description', $fileSystem->name($filePath))->get('id')->toArray();
                    $count         = count($searchedShort) + count($searchedDesc);

                    $findArray     = array_merge($searchedDesc, $searchedShort);

                    if ($category->photos()
                        ->where('name', $fileSystem->name($filePath). '.' . $fileSystem->extension($filePath))
                        ->exists() && !empty($findArray)
                    ) {
                        $count++;
                    }
                    if ($count < 2) {
                        \Storage::delete($filePath);
                        $directory = Storage::path('/').dirname($filePath);
                        $files = $fileSystem->files($directory);
                        $dirs  = $fileSystem->directories($directory);
                        if(empty($files) && empty($dirs)) {
                            $fileSystem->deleteDirectory($directory);
                        }
                    }
                }
            }
        }
    }
}
