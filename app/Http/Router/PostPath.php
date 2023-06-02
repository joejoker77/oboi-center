<?php

namespace App\Http\Router;

use App\Entities\Blog\Post;
use App\Entities\Blog\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Routing\UrlRoutable;

class PostPath implements UrlRoutable
{
    public Post|null $post = null;
    public Category|null $category = null;


    public function withPost(?Post $post):self
    {
        $clone = clone $this;
        $clone->post = $post;
        return $clone;
    }

    public function withCategory(?Category $category):self
    {
        $clone = clone $this;
        $clone->category = $category;
        return $clone;
    }

    public function getRouteKey()
    {
        $segments = [];

        if ($this->category) {
            $segments[] = Cache::tags(Category::class)->rememberForever('blog_category_path_'. $this->category->id, function () {
                return $this->category->getPath();
            });
        }

        if ($this->post) {
            $segments[] = Cache::tags(Post::class)->rememberForever('blog_post_path_'.$this->post->id, function () {
                return $this->post->slug;
            });
        }

        return implode('/', $segments);
    }

    public function getRouteKeyName():string
    {
        return 'post_path';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $chunks   = explode('/', $value);
        $category = null;

        do{
            $slug = reset($chunks);
            if ($slug && $next = Category::withDepth()->where('slug', $slug)->where('parent_id', $category?->id)->first()) {
                $category = $next;
                array_shift($chunks);
            }
        } while (!empty($slug) && !empty($next));

        $post = null;

        do {
            $slug = reset($chunks);
            if ($slug && $next = Post::where('slug', $slug)->first()) {
                $post = $next;
                array_shift($chunks);
            }
        } while (!empty($slug) && !empty($next));

        if (
            !empty($chunks) ||
            ($category && $category->status == Category::STATUS_DRAFT) ||
            ($post && $post->status == Post::STATUS_DRAFT) ||
            ($category && $post && $category->id !== $post->category_id)
        ) {
            abort(404);
        }

        return $post ? $this->withCategory($category)->withPost($post) : $this->withCategory($category);
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }
}
