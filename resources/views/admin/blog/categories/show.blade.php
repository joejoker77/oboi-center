<?php /** @var \App\Entities\Blog\Category $category */ ?>
@extends('layouts.admin')

@section('content')
    <div class="pt-4 d-flex">
        <div class="ms-auto btn-group" role="group" aria-label="control buttons">
            <a href="{{ route('admin.blog.categories.create') }}" class="btn btn-success d-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Создать">
                <span data-feather="plus-square"></span>
            </a>
            <a href="{{ route('admin.blog.categories.edit', $category) }}" class="btn btn-primary d-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Редактировать">
                <span data-feather="edit"></span>
            </a>

            <form class="btn btn-primary" method="POST"
                  action="{{ route('admin.blog.category.toggle.status', [$category]) }}"
                  data-bs-toggle="tooltip" data-bs-placement="bottom"
                  data-bs-title="@if($category->status == $category::STATUS_ACTIVE){{"Снять с публикации"}}@else{{"Опубликовать"}}@endif"
            >
                @csrf
                <button type="submit" class="btn p-0 text-white d-flex" style="line-height: 0">
                    <span data-feather="@if($category->status == $category::STATUS_ACTIVE){{"eye-off"}}@else{{"eye"}}@endif"></span>
                </button>
            </form>

            <form class="btn btn-danger" method="POST" action="{{ route('admin.blog.categories.destroy', $category) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Удалить">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn p-0 text-white d-flex js-confirm" style="line-height: 0">
                    <span data-feather="trash-2"></span>
                </button>
            </form>
        </div>
    </div>
    <div class="p-3 mb-4 bg-light">
        <div class="row">
            <div class="col-8">
                <table class="table table-striped table-bordered">
                    <tr><th>ID:</th><td>{{ $category->id }}</td></tr>
                    <tr><th>Имя:</th><td>{{ $category->name }}</td></tr>
                    <tr><th>Псевдоним:</th><td>{{ $category->slug }}</td></tr>
                    <tr><th>Заголовок:</th><td>{{ $category->title }}</td></tr>
                    <tr><th>Полное описание:</th><td>{!! $category->description !!}</td></tr>
                    <tr><th>Мета тег Title:</th><td>{{ $category->meta['title'] }}</td></tr>
                    <tr><th>Мета тег Description:</th><td>{{ $category->meta['description'] }}</td></tr>
                    <tr>
                        <th>Статус:</th>
                        <td>@if($category->status == $category::STATUS_ACTIVE)
                                <div class="badge bg-success text-uppercase">Опубликована</div>
                            @else
                                <div class="badge bg-secondary text-uppercase">Черновик</div>
                            @endif
                        </td>
                    </tr>
                    @if($category->parent)
                        <tr>
                            <th>Родительская категория:</th>
                            <td>
                                <a href="{{ route('admin.blog.categories.show', $category->parent) }}">{{ $category->parent->name }}</a>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="col-4">
                <div class="image-container">
                    @if(!$category->photos->isEmpty())
                        <img src="{{ asset($category->getMainImage('medium')) }}" alt="Главное изображение категории">
                    @endif
                </div>
            </div>
        </div>

        @if(!$category->photos->isEmpty())
            <h3>Изображения категории</h3>
            <up-images>
                <div class="images-container">
                    @php /** @var \App\Entities\Shop\Photo $image */ @endphp
                    @foreach($category->photos as $image)
                        <div class="image-item">
                            <div class="wrapper-image" data-photo-id="{{ $image->id }}" data-photo-owner="category" data-category-id="{{ $category->id }}">
                                <img src="{{ asset($image->getPhoto('small')) }}" alt="{{ $image['alt'] }}">
                            </div>
                            <div class="image-control btn-group">
                                <form action="{{ route('admin.blog.categories.photo.up', [$category,$image]) }}" method="POST" class="btn btn-secondary">
                                    @csrf
                                    <button type="submit" class="btn p-0 text-white d-flex">
                                        <span data-feather="arrow-left-circle"></span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.blog.categories.photo.remove', [$category,$image]) }}" method="POST" class="btn btn-danger">
                                    @csrf
                                    <button type="submit" class="btn p-0 text-white d-flex js-confirm">
                                        <span data-feather="x-circle"></span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.blog.categories.photo.down', [$category,$image]) }}" method="POST" class="btn btn-secondary">
                                    @csrf
                                    <button type="submit" class="btn p-0 text-white d-flex">
                                        <span data-feather="arrow-right-circle"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </up-images>
        @endif
    </div>
@endsection
