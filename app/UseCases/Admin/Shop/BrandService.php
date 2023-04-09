<?php

namespace App\UseCases\Admin\Shop;

use Throwable;
use Carbon\Carbon;
use App\Entities\Shop\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BrandService
{
    /**
     * @throws Throwable
     */
    public function create(Request $request): Brand
    {
        DB::beginTransaction();
        try {
            $brand = Brand::create([
                'name'      => $request['name'],
                'seo_text'  => $request['seo_text'],
                'meta'      => $request['meta'],
                'import_id' => $request['import_id'] ?? null,
                'supplier'  => $request['supplier'] ?? null,
            ]);

            $this->checkImages($request, $brand);
            DB::commit();
            return $brand;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException('Сохранение сущности Brand завершилось с ошибкой. Подробности: ' . $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function update(Request $request, Brand $brand): void
    {
        DB::beginTransaction();
        try {
            $brand->update($request->only(['name', 'seo_text', 'meta', 'import_id', 'supplier']),[
                "name"      => $request['name'],
                "seo_text"  => $request['seo_text'],
                "meta"      => $request['meta'],
                'import_id' => $request['import_id'] ?? null,
                'supplier'  => $request['supplier'] ?? null,
            ]);

            $this->checkImages($request, $brand);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException('Обновление сущности Brand завершилось с ошибкой. Подробности: '. $e->getMessage());
        }

    }

    private function checkImages(Request $request, Brand $brand): void
    {
        if ($images = $request->file('photo')) {
            if ($brand->logo) {
                $this->removeLogo($brand);
            }
            if (is_array($images)) {
                foreach ($images as $image) {
                    $photo = $brand->logo()->create([
                        "name" => str_replace('.'.$image->getExtension(), '', $image->getFilename()) . '.webp',
                        "sort" => 0,
                        "path" => Brand::getImageParams()['path'] . $brand->name . '/'
                    ]);
                    save_image_to_disk($image, $photo, Brand::getImageParams()['sizes']);
                }
            } else {
                $image = $images;
                $photo = $brand->logo()->create([
                    "name" => str_replace('.'.$image->getExtension(), '', $image->getFilename()) . '.webp',
                    "sort" => 0,
                    "path" => Brand::getImageParams()['path'] . $brand->name . '/'
                ]);
                save_image_to_disk($image, $photo, Brand::getImageParams()['sizes']);
            }
        }
    }

    public function removePhoto(Brand $brand):void
    {
        $this->removeLogo($brand);
    }

    private function removeLogo(Brand $brand):void
    {
        $logo = $brand->logo;
        foreach (Brand::getImageParams()['sizes'] as $nameSize => $size) {
            $img = $logo->path . $nameSize . '_' . $logo->name;
            Storage::delete($img);
        }
        $brand->logo()->delete();
    }
}
