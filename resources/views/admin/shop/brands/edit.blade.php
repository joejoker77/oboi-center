@extends('layouts.admin')

@section('content')
    <form method="POST" action="{{ route('admin.shop.brands.update', $brand) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="row mt-4">
            <h3 class="mb-4 pb-4 border-bottom">Редактирование бренда: {{ $brand->name }}</h3>
            <div class="col-md-6">
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Основные</h4>
                    <div class="form-floating">
                        <input id="name" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name', $brand) }}" type="text" placeholder="Наименование бренда" required>
                        <label for="name" class="form-label">Наименование бренда</label>
                        @error('name')<span class="invalid-feedback">{{ $$message }}</span>@enderror
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
                    <textarea id="short_description" name="seo_text"
                              class="ckeditor form-control @error('seo_text') is-invalid @enderror"
                              placeholder="SEO текст для бренда" rows="7">{{ trim(old('seo_text', $brand)) }}</textarea>

                        @error('seo_text')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="p-3 mb-3 bg-light border rounded-3">
            @include('layouts.partials.meta', ['meta' => $brand->meta])
        </div>

        <div class="p-3 mb-3 bg-light border rounded-3">
            <button type="submit" class="btn btn-success w-100">Сохранить</button>
        </div>
    </form>
    @if($brand->logo)
        <div class="p-3 mb-3 bg-light border rounded-3">
            <up-images data-destination="category">
                <label for="photo" class="form-label">Логотип бренда</label>
                <div class="images-container">
                    <div class="image-item">
                        <div class="wrapper-image">
                            <img src="{{ asset($brand->logo->getPhoto('medium')) }}" alt="{{ $brand->logo->alt_tag }}">
                        </div>
                        <div class="image-control btn-group">
                            <form action="{{ route('admin.shop.brands.logo.remove', [$brand, $brand->logo]) }}" method="POST" class="btn btn-danger">
                                @csrf
                                <button type="submit" class="btn p-0 text-white d-flex js-confirm">
                                    <span data-feather="x-circle"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </up-images>
        </div>
    @endif
@endsection
