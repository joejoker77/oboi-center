<?php

namespace App\Listeners;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Alexusmai\LaravelFileManager\Events\FilesUploaded;

class ConvertImages
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param FilesUploaded $event
     * @return void
     */
    public function handle(FilesUploaded $event):void
    {
        foreach ($event->files() as $file) {
            $prefixPath = Storage::path('/');
            $originalName = str_replace($file["extension"], '', $file["name"]);
            $originalPath = str_replace($file["name"], '', $file["path"]);

            $path = $originalPath === '/' ? $prefixPath : $prefixPath.$originalPath;

            $img = Image::make($path.$file['name']);
            $img->encode('webp', 50);

            Storage::put($originalPath.$originalName.'webp', $img);
            Storage::delete($originalPath.$file['name']);
        }
    }
}
