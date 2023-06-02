@php /** @var App\Entities\Blog\Post[] $posts */ @endphp
@php /** @var App\Entities\Blog\Category $categories */ @endphp
@extends('layouts.admin')

@section('content')
    <div class="py-4 d-flex">
        <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-success">Добавить статью</a>
    </div>
    <table class="table table-bordered table-striped" id="postsTable">
        <thead>
        <tr>
            <th>ID</th>
            <th>Изображение</th>
            <th>Наименование</th>
            <th>Категория</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($posts as $post)
            <tr>
                <td>{{ $post->id }}</td>
                <td>
                    @if(!$post->photos->isEmpty())
                        <img src="{{ asset($post->photos()->first()->getPhoto('small')) }}" alt="{{ $post->photos()->first()->alt_tag }}" height="40">
                    @else
                        <i data-feather="camera-off"></i>
                    @endif
                </td>
                <td><a href="{{ route('admin.blog.posts.show', $post->slug) }}">{{ $post->title }}</a></td>
                <td><a href="{{ route('admin.blog.categories.show', $post->category) }}">{{ $post->category->title ?? $post->category->name }}</a></td>
                <td><span class="badge {{ $post::statusLabel($post->status) }}">{{ $post::statusName($post->status) }}</span></td>
                <td>
                    <a href="{{ route('admin.blog.posts.edit', $post) }}" class="list-inline-item mx-1"
                       id="editCategory" data-bs-toggle="tooltip"
                       data-bs-placement="bottom"
                       data-bs-title="Редактировать"
                    >
                        <span data-feather="edit"></span>
                    </a>|<form method="POST" action="{{ route('admin.blog.posts.set-status', $post) }}" class="list-inline-item mx-1">
                        @csrf
                        <input type="hidden" name="action"
                               value=@if($post->status == $post::STATUS_ACTIVE)"un-published"@elseif($post->status == $post::STATUS_DRAFT)"published"@endif">
                        <input type="hidden" name="selected[]" value="{{ $post->id }}">
                        <button class="btn p-0 align-baseline text-primary" type="submit">
                            @if($post->status == $post::STATUS_ACTIVE)
                                <span data-feather="eye-off"></span>
                            @else
                                <span data-feather="eye"></span>
                            @endif
                        </button>
                    </form>|
                    <form method="POST" class="list-inline-item js-confirm ms-2"
                          action="{{ route('admin.blog.posts.destroy', $post) }}"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          data-bs-title="Удалить статью"
                    >
                        @csrf
                        @method('DELETE')
                        <button class="btn p-0 align-baseline js-confirm text-danger" type="submit"><span data-feather="trash-2"></span></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $posts->appends(request()->input())->links() }}
@endsection

