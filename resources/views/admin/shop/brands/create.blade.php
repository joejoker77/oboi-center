@extends('layouts.admin')

@section('content')
    <form method="POST" action="{{ route('admin.shop.brands.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row mt-4">
            <h3 class="mb-4 pb-4 border-bottom">Создание нового бренда</h3>
            <div class="col-md-6">
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Основные</h4>
                    <div class="form-floating">
                        <input id="name" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" type="text" placeholder="Наименование бренда" required>
                        <label for="name" class="form-label">Наименование бренда</label>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <up-images data-destination="category">
                        <label for="photo" class="form-label">Логотип бренда</label>
                        <div class="images-container"></div>
                        <input class="form-control @error('photo') is-invalid @enderror" name="photo" type="file" id="photo">
                        @error('photo')<span class="invalid-feedback">{{ $message }}</span> @enderror
                    </up-images>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Сео текст</h4>
                    <div class="form-text">
                        <label for="short_description">SEO текст для бренда</label>
                        <textarea id="short_description" name="seo_text"
                                  class="ckeditor form-control @error('seo_text') is-invalid @enderror"
                                  placeholder="SEO текст для бренда" rows="7">{{ trim(old('seo_text')) }}</textarea>

                        @error('seo_text')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="p-3 mb-3 bg-light border rounded-3">
            @include('layouts.partials.meta')
        </div>
        <div class="p-3 mb-3 bg-light border rounded-3">
            <button type="submit" class="btn btn-success w-100">Сохранить</button>
        </div>
    </form>
@endsection
