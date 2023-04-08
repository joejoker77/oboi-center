@extends('layouts.admin')
@php /** @var \App\Entities\Shop\Category $category */ @endphp
@section('content')
    <div class="my-4">
        <h3 class="mb-4 pb-4 border-bottom">Редактирование категории: "{{ $category->name }}"</h3>
        <form id="editForm" method="POST" action="{{ route('admin.shop.categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="hidden" name="published" value="{{ $category->published ? 1 : 0 }}">
            <div class="p-3 mb-3 bg-light border rounded-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name', $category) }}" placeholder="Название категории" required>
                            <label for="name">Название категории</label>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $category) }}" placeholder="Заголовок">
                            <label for="slug">Заголовок</label>
                            @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-3 mb-3 bg-light border rounded-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-text">
                            <label for="short_description">Короткое описание</label>
                            <textarea id="short_description" name="short_description"
                                      class="ckeditor form-control @error('short_description') is-invalid @enderror"
                                      placeholder="Короткое описание" rows="7">{{ trim(old('short_description', $category)) }}</textarea>

                            @error('short_description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-text">
                            <label for="description">Полное описание</label>
                            <textarea id="description" name="description" class="ckeditor form-control @error('description') is-invalid @enderror"
                                      placeholder="Полное описание" rows="7">{{ trim(old('description', $category)) }}</textarea>

                            @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-3 mb-3 bg-light border rounded-3">
                <h4 class="my-3 pb-3 border-bottom">Сео теги</h4>
                @include('layouts.partials.meta', ['meta' => $category->meta])
            </div>

            @if(!$categories->isEmpty() || count($category->allAttributes()) > 0)
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <div class="row">
                        @if(!$categories->isEmpty())
                            <div class=@if($category->allAttributes() > 0)"col-md-6"@else"col-md-12"@endif>
                                <h4 class="my-3 pb-3 border-bottom">Родительская категория</h4>
                                <div class="row">
                                    @error('parent_id')<div class="is-invalid"></div>@enderror
                                    <select name="parent_id" class="js-choices">
                                        <option value="">-=Привязать категорию=-</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" @if($cat->id === $category->parent_id) selected @endif>
                                                {{ html_entity_decode(str_repeat('&mdash;', (int)$cat->depth)) }}{{ $cat->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        @endif
                        @if(count($category->allAttributes()) > 0)
                            <div class=@if(!$categories->isEmpty())"col-md-6"@else"col-md-12"@endif>
                                <h4 class="my-3 pb-3 border-bottom">Привязать аттрибуты</h4>
                                @error('attributes[]')<div class="is-invalid"></div>@enderror
                                <select name="attributes[]" class="js-choices" multiple>
                                    <option value="">-=Выбрать аттрибуты=-</option>
                                    @foreach($category->allAttributes() as $attribute)
                                        <option value="{{ $attribute->id }}" selected>
                                            {{ $attribute->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('attributes[]')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </form>
        <div class="p-3 mb-3 bg-light border rounded-3">
            <up-images>
                <label for="photo" class="form-label">Изображения категории</label>
                <div class="images-container">
                    @foreach($category->photos as $image)
                        <div class="image-item">
                            <div class="wrapper-image" data-photo-id="{{ $image->id }}" data-photo-owner="category" data-category-id="{{ $category->id }}">
                                <img src="{{ asset($image->getPhoto('small')) }}" alt="{{ $image['alt'] }}">
                            </div>
                            <div class="image-control btn-group">
                                <form action="{{ route('admin.shop.categories.photo.up', [$category,$image]) }}" method="POST" class="btn btn-secondary">
                                    @csrf
                                    <button type="submit" class="btn p-0 text-white d-flex">
                                        <span data-feather="arrow-left-circle"></span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.shop.categories.photo.remove', [$category,$image]) }}" method="POST" class="btn btn-danger">
                                    @csrf
                                    <button type="submit" class="btn p-0 text-white d-flex js-confirm">
                                        <span data-feather="x-circle"></span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.shop.categories.photo.down', [$category,$image]) }}" method="POST" class="btn btn-secondary">
                                    @csrf
                                    <button type="submit" class="btn p-0 text-white d-flex">
                                        <span data-feather="arrow-right-circle"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <input form="editForm" class="form-control @error('photo') is-invalid @enderror" name="photo[]" type="file" id="photo" multiple>
                @error('photo')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </up-images>
        </div>
        <div class="p-3 mb-3 bg-light border rounded-3">
            <button form="editForm" type="submit" class="btn btn-success w-100">Сохранить</button>
        </div>
    </div>
@endsection
