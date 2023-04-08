@extends('layouts.admin')

@section('content')
    <form method="POST" action="{{ route('admin.shop.tags.update', $tag) }}">
        @csrf
        @method('PATCH')
        <div class="row mt-4">
            <h3 class="mb-4 pb-4 border-bottom">Редактирование тега: "{{ $tag->name }}"</h3>
            <div class="col-md-6">
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Основные</h4>
                    <div class="form-floating">
                        <input id="name" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name', $tag) }}" type="text" placeholder="Наименование тега" required>
                        <label for="name" class="form-label">Наименование тега</label>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Сео текст</h4>
                    <div class="form-text">
                    <textarea id="short_description" name="seo_text" aria-label="Сео текст тега: {{ $tag->name }}"
                              class="ckeditor form-control @error('seo_text') is-invalid @enderror"
                              placeholder="SEO текст для бренда" rows="7">{{ trim(old('seo_text', $tag)) }}</textarea>

                        @error('seo_text')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="p-3 mb-3 bg-light border rounded-3">
            @include('layouts.partials.meta', ['meta' => $tag->meta])
        </div>
        <div class="p-3 mb-3 bg-light border rounded-3">
            <button type="submit" class="btn btn-success w-100">Сохранить</button>
        </div>
    </form>
@endsection
