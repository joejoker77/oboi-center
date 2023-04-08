<?php

namespace App\UseCases\Admin\Shop;

use App\Entities\Shop\Photo;
use App\Entities\Shop\Product;
use App\Entities\Shop\Variant;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class VariantService
{

    public function updateVariant(Request $request)
    {
        try {
            DB::beginTransaction();
            $variant = Variant::find($request['variant']);
            $this->checkImages($request, $variant);

            if ($tags = $request->get('alt_tag')) {
                foreach ($tags as $key => $tag) {
                    if ($photo = Photo::find($key)) {
                        $photo->alt_tag = $tag;
                        $photo->save();
                    }
                }
            }

            if ($descriptions = $request->get('description')) {
                foreach ($descriptions as $key => $desc) {
                    if ($photo = Photo::find($key)) {
                        $photo->description = $desc;
                        $photo->save();
                    }
                }
            }

            if ($variantPhotos = $request->get('variantsPhoto')) {
                foreach ($variantPhotos as $variantPhoto) {
                    if ($photo = Photo::find($variantPhoto)) {
                        $photo->variant_id = $variant->id;
                        $photo->save();
                    }
                }
            }

            DB::commit();
        } catch (\Exception|\Throwable $exception) {
            DB::rollBack();
            throw new \DomainException($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param Variant $variant
     * @return void
     */
    private function checkImages(Request $request, Variant $variant): void
    {
        if ($images = $request->file('add-photos')) {
            /** @var UploadedFile $image  */
            foreach ($images as $i => $image) {

                $photo = $variant->product->photos()->make([
                    "name" => md5(microtime() . rand(0, 9999)) . '.webp',
                    "sort" => $i,
                    "path" => Product::getImageParams()['path'] . $variant->product->id . '/' . $variant->id . '/',
                ]);

                if ($tags = $request->get('alt_tag')) {
                    foreach ($tags as $key => $tag) {
                        if ($key == $image->getClientOriginalName()) {
                            $photo["alt_tag"] = $tag;
                        }
                    }
                }

                if ($descriptions = $request->get('description')) {
                    foreach ($descriptions as $key => $desc) {
                        if ($key == $image->getClientOriginalName()) {
                            $photo["description"] = $desc;
                        }
                    }
                }

                if ($variantPhotos = $request->get('variantsPhoto')) {
                    foreach ($variantPhotos as $key => $variantPhoto) {
                        if ($variantPhoto == $image->getClientOriginalName()) {
                            $photo['variant_id'] = $variant->id;
                        }
                    }
                }
                $photo->save();

                $this->saveImageToDisk($image, $photo);
            }
        }
    }

    private function saveImageToDisk($image, $photo):void
    {
        $img = Image::make($image->getRealPath());
        $img->backup();

        foreach (Product::getImageParams()['sizes'] as $nameSize => $size) {
            $img->resize($size,null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 60);

            Storage::put($photo->path . $nameSize . '_' . $photo->name, $img);

            $img->reset();
        }
    }
}
