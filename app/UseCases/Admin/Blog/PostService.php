<?php

namespace App\UseCases\Admin\Blog;


use App\Entities\Blog\Post;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\Blog\PostRequest;
use Illuminate\Http\Request;

class PostService
{
    public function create(PostRequest $request):Post
    {
        DB::beginTransaction();
        try {
            $post = Post::create([
                'title'             => $request['title'],
                'description'       => $request['description'],
                'content'           => $request['content'],
                'meta'              => $request['meta'],
                'category_id'       => $request['category_id'],
                'status'            => Post::STATUS_DRAFT,
                'sort'              => $request['sort']
            ]);

            if ($request->get('post_categories')) {
                $post->categories()->attach($request->get('post_categories'));
            }

            $this->checkImages($request, $post);

            DB::commit();
            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException('Сохранение сущности Post завершилось с ошибкой. Подробности: ' . $e->getMessage());
        }
    }

    private function checkImages(PostRequest $request, Post $post): void
    {
        if ($images = $request->file('photo')) {
            foreach ($images as $i => $image) {
                $info  = $image->getFileInfo();

                if ($post->photos()->where("name", '=', str_replace('.'.$info->getExtension(), '', $info->getFilename()) . '.webp')->first()) {
                    continue;
                }

                $photo = $post->photos()->create([
                    "name" => str_replace('.'.$info->getExtension(), '', $info->getFilename()) . '.webp',
                    "sort" => $i,
                    "path" => Post::getImageParams()['path'] . $post->id . '/images/'
                ]);
                save_image_to_disk($image, $photo, Post::getImageParams()['sizes']);
            }
        }
    }
}
