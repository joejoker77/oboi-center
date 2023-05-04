@php
/**
 * @var App\Entities\Shop\Category[] $categories
 * @var App\Entities\Shop\Tag[] $tags
 * @var App\Entities\Shop\Attribute[] $attributes
 * @var App\Entities\Shop\Filter|null $filter
*/
@endphp
@extends('layouts.admin')

@section('content')
    <form method="POST" id="productForm" action="{{ route('admin.shop.filters.update', $filter) }}">
        @csrf
        @method('PATCH')
        <div class="row mt-4">
            <h3 class="mb-4 pb-4 border-bottom">Создание нового фильтра</h3>
            <div class="col-md-6">
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Основные</h4>
                    <div class="form-floating mb-3">
                        <input id="name" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ $filter->name }}" type="text" placeholder="Имя фильтра" required>
                        <label for="name" class="form-label">Имя фильтра</label>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-floating">
                        <h5>Видимость в категориях</h5>
                        <select name="categories[]" class="js-choices" multiple>
                            <option value="">-=Выбрать категорию=-</option>
                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @foreach($filter->categories as $cat)
                                        @selected($cat->id === $category->id)
                                    @endforeach
                                >
                                    {{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->title ?: $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Позиция</h4>
                    <div class="form-floating mb-3">
                        <select name="position" class="js-choices">
                            <option value="">-=Выбрать Позицию=-</option>
                            <option value="left" @selected($filter->position == 'left')>Слева</option>
                            <option value="top" @selected($filter->position == 'top')>Сверху</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h5>Группы для фильтра</h5>
                    <div class="group accordion mb-3" id="accordionGroups">
                        @foreach($filter->groups as $key => $group)
                            @include('admin.shop.filters.partials.group-item', compact('group', 'key'))
                        @endforeach
                    </div>
                    <div class="bg-light text-end">
                        <button id="addFilterGroup" type="button" class="btn btn-success">Добавить группу</button>
                    </div>
                </div>
            </div>
            <div class="p-3 mb-3 bg-light border rounded-3">
                <button type="submit" class="btn btn-success w-100">Сохранить</button>
            </div>
        </div>
    </form>
@endsection
