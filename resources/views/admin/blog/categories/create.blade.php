@extends('layouts.admin')

@section('content')
    <div class="row my-4">
        <h3 class="mb-4 pb-4 border-bottom">Создание новой категории</h3>
        <form method="POST" action="{{ route('admin.blog.categories.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" value="{{ \App\Entities\Blog\Category::STATUS_DRAFT }}">
            <div class="p-3 mb-3 bg-light border rounded-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" placeholder="Название категории" required>
                            <label for="name">Название категории</label>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" placeholder="Заголовок">
                            <label for="slug">Заголовок</label>
                            @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        @if(!$categories->isEmpty())
                            <h4 class="my-3 pb-3 border-bottom">Родительская категория</h4>
                            @error('parent_id')<div class="is-invalid"></div>@enderror
                            <select name="parent_id" class="js-choices">
                                <option value="">-=Выбрать категорию=-</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->title ?: $category->name }}</option>
                                @endforeach
                            </select>
                            @error('parent_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-3 mb-3 bg-light border rounded-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-text">
                            <label for="description">Полное описание</label>
                            <textarea id="description" name="description"
                                      class="ckeditor form-control @error('description') is-invalid @enderror"
                                      placeholder="Полное описание" rows="7">{{ trim(old('description')) }}</textarea>

                            @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3 mb-3 bg-light border rounded-3">
                <up-images data-destination="category">
                    <label for="photo" class="form-label">Изображения категории</label>
                    <div class="images-container"></div>
                    <input class="form-control @error('photo') is-invalid @enderror" name="photo[]" type="file" id="photo" multiple>
                    @error('photo')<span class="invalid-feedback">{{ $message }}</span> @enderror
                </up-images>
            </div>

            <div class="p-3 mb-3 bg-light border rounded-3">
                <h4 class="my-3 pb-3 border-bottom">Сео теги</h4>
                @include('layouts.partials.meta')
            </div>

            <div class="p-3 mb-3 bg-light border rounded-3">
                <button type="submit" class="btn btn-success w-100">Сохранить</button>
            </div>
        </form>
    </div>

@endsection
