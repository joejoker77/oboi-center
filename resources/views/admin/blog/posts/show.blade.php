@php /** @var App\Entities\Blog\Post $post */ @endphp
@extends('layouts.admin')

@section('content')
    <div class="pt-4 d-flex">
        <div class="ms-auto btn-group" role="group" aria-label="control buttons">
            <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-success d-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Создать">
                <span data-feather="plus-square"></span>
            </a>
            <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-primary d-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Редактировать">
                <span data-feather="edit"></span>
            </a>

            <form class="btn btn-primary" method="POST"
                  action="{{ route('admin.blog.post.set-status', [$post]) }}"
                  data-bs-toggle="tooltip" data-bs-placement="bottom"
                  data-bs-title="@if(!$post->isActive()){{"Сделать активным"}}@else{{"Продутк активен"}}@endif"
            >
                @csrf
                <button type="submit" class="btn p-0 text-white d-flex" style="line-height: 0" @if($post->isActive()) disabled @endif>
                    <span data-feather="@if(!$post->isActive()){{"eye-off"}}@else{{"eye"}}@endif"></span>
                </button>
            </form>

            <form class="btn btn-danger" method="POST" action="{{ route('admin.blog.posts.destroy', $post) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Удалить">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn p-0 text-white d-flex js-confirm" style="line-height: 0">
                    <span data-feather="trash-2"></span>
                </button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <h6>Основные</h6>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>ID</th>
                    <td>{{ $post->id }}</td>
                </tr>
                <tr>
                    <th>Статус</th>
                    <td><span class="badge {{ $post::statusLabel($post->status) }}">{{ $post::statusName($post->status) }}</span></td>
                </tr>
                <tr>
                    <th>Наименование</th>
                    <td>{{ $post->title }}</td>
                </tr>
                @if($post->category)
                    <tr>
                        <th>Основная категория</th>
                        <td>{{ $post->category->title ?? $post->category->name }}</td>
                    </tr>
                @endif
                @if(!$post->categories->isEmpty())
                    <tr>
                        <th>Дополнительные категории</th>
                        <td>
                            @foreach($post->categories as $category)
                                {{ $category->title ?? $category->name }},
                            @endforeach
                        </td>
                    </tr>
                @endif
            </table>
            @if(!$post->photos->isEmpty())
                <h3>Изображения продукта</h3>
                <up-images>
                    <div class="images-container">
                        @php /** @var App\Entities\Shop\Photo $image */ @endphp
                        @foreach($post->photos as $image)
                            <div class="image-item">
                                <div class="wrapper-image" data-photo-id="{{ $image->id }}" data-photo-owner="product" data-product-id="{{ $post->id }}">
                                    <img src="{{ asset($image->getPhoto('small')) }}" alt="{{ $image['alt'] }}">
                                </div>
                                <div class="image-control btn-group">
                                    <form action="{{ route('admin.shop.products.photo.up', [$post,$image]) }}" method="POST" class="btn btn-secondary">
                                        @csrf
                                        <button type="submit" class="btn p-0 text-white d-flex">
                                            <span data-feather="arrow-left-circle"></span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.shop.products.photo.remove', [$post,$image]) }}" method="POST" class="btn btn-danger">
                                        @csrf
                                        <button type="submit" class="btn p-0 text-white d-flex js-confirm">
                                            <span data-feather="x-circle"></span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.shop.products.photo.down', [$post,$image]) }}" method="POST" class="btn btn-secondary">
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
    </div>
@endsection
