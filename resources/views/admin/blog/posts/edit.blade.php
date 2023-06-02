@php
    /** @var Post $post */
    use App\Entities\Blog\Post;
@endphp
@extends('layouts.admin')

@section('content')
    <form method="POST" id="productForm" action="{{ route('admin.blog.posts.update', $post) }}" enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        <div class="row mt-4">
            <h3 class="mb-4 pb-4 border-bottom">Создание новой статьи</h3>
            <div class="col-md-9 base-form">
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <div class="form-floating mb-3">
                        <input type="text" id="title" class="form-control @error('title') is-invalid @enderror"
                               name="title" value="{{ $post->title }}" placeholder="Наименование статьи" required>
                        <label for="title">Наименование статьи</label>
                        @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Анонс статьи</h6>
                            <div class="form-text">
                                <textarea id="description" name="description"
                                          class="ckeditor form-control @error('description') is-invalid @enderror"
                                          placeholder="Описание товара" rows="7">{{ trim($post->description) }}</textarea>
                                @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6>Полный текст статьи</h6>
                            <div class="form-text">
                                <textarea id="content" name="content"
                                          class="ckeditor form-control @error('content') is-invalid @enderror"
                                          placeholder="Полный текст" rows="7">{{ trim($post->content) }}</textarea>
                                @error('content')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="p-3 mb-3 bg-light border rounded-3">
                        <up-images data-destination="category">
                            <label for="photo" class="form-label">Изображения статьи</label>
                            <div class="images-container"></div>
                            <input class="form-control @error('photo') is-invalid @enderror" name="photo[]" type="file" id="photo" multiple>
                            @error('photo')<span class="invalid-feedback">{{ $message }}</span> @enderror
                        </up-images>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="p-3 mb-3 bg-light border rounded-3">
                        @include('layouts.partials.meta', ['meta' => $post->meta])
                    </div>
                </div>

                <div class="p-3 mb-3 bg-light border rounded-3">
                    <button type="submit" class="btn btn-success w-100">Сохранить</button>
                </div>
            </div>
            <div class="col-md-3 adding-forms">
                @if(!$categories->isEmpty())
                    <div class="p-3 mb-3 bg-light border rounded-3">
                        <h6 class="my-3 pb-3 border-bottom">Основная категория</h6>
                        @error('category_id')<div class="is-invalid"></div>@enderror
                        <select name="category_id" class="js-choices">
                            <option value="">-=Выбрать категорию=-</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected($category->id == $post->category->id)>
                                    {{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->title ?: $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<span class="invalid-feedback">{{ $message }}</span>@enderror

                        <h6 class="my-3 pb-3 border-bottom">Дополнительные категории</h6>
                        @error('post.categories')
                        <div class="is-invalid"></div>@enderror
                        <select name="post.categories[]" class="js-choices" multiple>
                            <option value="">-=Выбрать категорию=-</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected($post->categories->contains($category))>
                                    {{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->title ?: $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('post.categories')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                @endif
                    <div class="p-3 mb-3 bg-light border rounded-3">
                        <h6 class="my-3 pb-3 border-bottom">Настройки категории</h6>
                        <select name="status" id="status" class="js-choices">
                            @foreach($post::statusList() as $status => $label)
                                <option value="{{ $status }}" @selected($status == $post->status)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="form-floating mb-3">
                            <input type="number" min="0" step="1" id="sort" class="form-control @error('title') is-invalid @enderror"
                                   name="sort" value="{{ $post->sort }}" placeholder="Сортировка" required>
                            <label for="title">Сортировка</label>
                            @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
            </div>
        </div>
    </form>
@endsection
