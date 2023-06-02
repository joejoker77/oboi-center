@php /** @var App\Entities\Blog\Category $category */ @endphp
@extends('layouts.index')

@section('content')
    <div class="container" id="blogCategoryPage">
        <div class="row">
            <h1 class="h1 my-3">{{ $category->title }}</h1>
            <div class="category-content">
                @if(!$category->photos->isEmpty())
                    <div class="float-start me-3 mb-3">
                        <img src="{{ $category->photos[0]->getPhoto('thumb') }}" alt="{{ $category->photos[0]->alt_tag }}" />
                    </div>
                @endif
                @if($category->description)
                     {!! $category->description !!}
                @endif
            </div>
            @if(!$category->children->isEmpty())
                <ul class="child-categories">
                    @foreach($category->children as $childCategory)
                        <li><a href="{{ route('blog.index', post_path($childCategory, null)) }}">{{ $childCategory->title ?? $childCategory->name }}</a></li>
                    @endforeach
                </ul>
            @endif
            @if(!$category->posts->isEmpty())
                <ul class="category-posts">
                    @foreach($category->posts as $post)
                        <li><a href="{{ route('blog.index', post_path($category, $post)) }}">{{ $post->title }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
