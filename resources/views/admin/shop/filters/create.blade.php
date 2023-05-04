@php
/**
 * @var App\Entities\Shop\Category[] $categories
 * @var App\Entities\Shop\Filter|null $filter
*/
@endphp
@extends('layouts.admin')

@section('content')
    <form method="POST" id="productForm" action="{{ route('admin.shop.filters.store') }}">
        @csrf
        <div class="row mt-4">
            <h3 class="mb-4 pb-4 border-bottom">Создание нового фильтра</h3>
            <div class=@if($filter)"col-md-6"@else"col-md-12"@endif>
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Основные</h4>
                    <div class="form-floating mb-3">
                        <input id="name" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" type="text" placeholder="Имя фильтра" required>
                        <label for="name" class="form-label">Имя фильтра</label>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-floating">
                        <h5>Видимость в категориях</h5>
                        <select name="categories[]" class="js-choices" multiple>
                            <option value="">-=Выбрать категорию=-</option>
                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}">{{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->title ?: $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Позиция</h4>
                    <div class="form-floating mb-3">
                        <select name="position" class="js-choices">
                            <option value="">-=Выбрать Позицию=-</option>
                            <option value="left">Слева</option>
                            <option value="top">Сверху</option>
                        </select>
                    </div>
                </div>
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <button type="submit" class="btn btn-success w-100">Сохранить</button>
                </div>
            </div>
            @if($filter)
                <div class="col-md-6">
                    <div class="p-3 mb-3 bg-light border rounded-3">

                    </div>
                </div>
            @endif
        </div>
    </form>
@endsection
