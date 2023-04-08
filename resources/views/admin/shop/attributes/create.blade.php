@extends('layouts.admin')

@section('content')
    <div class="my-4">
        <h3 class="mb-4 pb-4 border-bottom">Создание нового атрибута</h3>
        <form method="POST" action="{{ route('admin.shop.attributes.store') }}">
            @csrf
            <div class="p-3 mb-3 bg-light border rounded-3">
                <h4>Основное</h4>
                <div class="form-floating mb-3">
                    <input id="name" class="form-control @error('name') is-invalid @enderror"
                           name="name" value="{{ old('name') }}" type="text" placeholder="Наименование атрибута" required>
                    <label for="name" class="form-label">Наименование атрибута</label>
                    @error('name')<span class="invalid-feedback">{{ $$message }}</span>@enderror
                </div>

                <div class="form-floating mb-3">
                    <input id="sort" class="form-control @error('sort') is-invalid @enderror"
                           name="sort" value="{{ old('sort') }}" type="text" placeholder="Сортировка">
                    <label for="sort" class="form-label">Сортировка</label>
                    @error('sort')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <select id="type" class="js-choices @error('type') is-invalid @enderror" name="type" required>
                        <option>-=Выбрать тип значения=-</option>
                        @foreach($types as $type => $label)
                            <option value="{{ $type }}"{{ $type === old('type') ? ' selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-floating mb-3">
                    <input id="unit" class="form-control @error('unit') is-invalid @enderror"
                           name="unit" value="{{ old('unit') }}" type="text" placeholder="Еденица измерения">
                    <label for="unit" class="form-label">Еденица измерения</label>
                    @error('default')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-floating mb-3">
                    <textarea name="variants" id="variants" placeholder="Опции атрибута" class="form-control @error('variants') is-invalid @enderror">{{ old('variants') }}</textarea>
                    <label for="variants" class="form-label">Опции атрибута</label>
                    @error('variants')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-0">
                    <select id="mode" class="js-choices @error('mode') is-invalid @enderror" name="type" required>
                        <option>-=Выбрать тип атрибута=-</option>
                        @foreach(\App\Entities\Shop\Attribute::modeList() as $mode => $label)
                            <option value="{{ $mode }}"{{ $mode === old('type') ? ' selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('mode')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="p-3 mb-3 bg-light border rounded-3">
                <h3>Дополнительно: Привязать категории</h3>
                <div class="row mb-3">
                    @error('categories[]')<div class="is-invalid"></div>@enderror
                    <select name="categories[]" class="js-choices" multiple>
                        <option value="">-=Выбрать категории=-</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->title ? : $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('categories[]')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="p-3 mb-3 bg-light border rounded-3">
                <div class="form-text">
                    <button type="submit" class="btn btn-success w-100">Сохранить</button>
                </div>
            </div>
        </form>
    </div>
@endsection
