<?php

namespace App\UseCases\Admin\Blog;


use Illuminate\Support\Str;
use App\Entities\Blog\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\Blog\CategoryRequest;

class CategoryService
{

    public function create(CategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $category = Category::make([
                "name"        => $request['name'],
                "title"       => $request['title'],
                "parent_id"   => $request['parent_id'],
                "description" => $request['description'],
                "status"      => $request['status'],
                "meta"        => $request['meta']
            ]);

            $category->save();
            $this->checkImages($request, $category);

            DB::commit();
            return $category;

        } catch (\Exception|\Throwable $exception) {
            DB::rollBack();
            throw new \DomainException('Сохранине категории для блога завершилось ошибкой. Error: '. $exception->getMessage());
        }
    }

    public function update(CategoryRequest $request, Category $category): void
    {
        $category->update($request->only([
            'name',
            'slug',
            'title',
            'parent_id',
            'description',
            'status',
            'meta']), [
            "name" => $request['name'],
            "slug" => $request['slug'],
            "title" => $request['title'],
            "parent_id" => $request['parent_id'],
            "description" => $request['description'],
            "status" => $request['published'],
            "meta" => $request['meta']
        ]);

        $this->checkImages($request, $category);
    }

    private function checkImages(CategoryRequest $request, Category $category):void
    {
        if ($images = $request->file('photo')) {

            foreach ($images as $i => $image) {
                if ($category->photos()->where('name', '=', str_replace('.'.$image->getExtension(), '', $image->getBasename()) . '.webp')->first()) {
                    continue;
                }
                $photo = $category->photos()->create([
                    "name" => str_replace('.'.$image->getExtension(), '', $image->getBasename()) . '.webp',
                    "sort" => $i,
                    "path" => Category::getImageParams()['path'] . Str::slug($category->name) . '/images/'
                ]);
                save_image_to_disk($image, $photo, Category::getImageParams()['sizes']);
            }
        }
    }
}
