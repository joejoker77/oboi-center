<?php

namespace App\Http\Controllers\Blog;


use App\Http\Router\PostPath;
use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    public function index(PostPath $path)
    {
        if ($path->post) {
            $post = $path->post;
            return view('blog.show', compact('post'));
        } else if ($path->category) {
            $category = $path->category;
            return view('blog.index', compact('category'));
        }
    }
}
