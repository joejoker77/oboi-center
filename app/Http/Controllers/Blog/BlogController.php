<?php

namespace App\Http\Controllers\Blog;


use App\Http\Router\PostPath;
use App\Http\Controllers\Controller;
use Butschster\Head\Contracts\MetaTags\MetaInterface;

class BlogController extends Controller
{
    protected MetaInterface $meta;

    public function __construct(MetaInterface $meta)
    {
        $this->meta = $meta;
    }

    public function index(PostPath $path)
    {
        if ($path->post) {
            $post = $path->post;
            $this->meta->setTitle($post->meta['title']);
            $this->meta->setDescription($post->meta['description']);
            return view('blog.show', compact('post'));
        } else if ($path->category) {
            $category = $path->category;
            $this->meta->setTitle($category->meta['title']);
            $this->meta->setDescription($category->meta['description']);
            return view('blog.index', compact('category'));
        }
    }
}
