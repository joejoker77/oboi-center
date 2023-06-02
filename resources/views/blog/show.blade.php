@php /** @var App\Entities\Blog\Post $post */ @endphp
@extends('layouts.index')

@section('content')
    <div class="container" id="postPage">
        <div class="row mb-5">
            <h1 class="h1">
                {{$post->title}}
            </h1>
            <div class="col-lg-12">
                {!! $post->content !!}
            </div>
        </div>
    </div>
@endsection
