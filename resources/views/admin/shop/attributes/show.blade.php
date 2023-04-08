<?php /** @var \App\Entities\Shop\Attribute $attribute */ ?>
@extends('layouts.admin')

@section('content')
    <div class="pt-4 d-flex">
        <div class="ms-auto btn-group" role="group" aria-label="control buttons">
            <a href="{{ route('admin.shop.attributes.create') }}" class="btn btn-success d-flex"
               data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Создать">
                <span data-feather="plus-square"></span>
            </a>
            <a href="{{ route('admin.shop.attributes.edit', $attribute) }}" class="btn btn-primary d-flex"
               data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Редактировать">
                <span data-feather="edit"></span>
            </a>
            <form class="btn btn-danger" method="POST" action="{{ route('admin.shop.attributes.destroy', $attribute) }}"
                  data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Удалить">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn p-0 text-white d-flex js-confirm" style="line-height: 0">
                    <span data-feather="trash-2"></span>
                </button>
            </form>
        </div>
    </div>
    <div class="p-3 mb-4 bg-light">
        <div class="row">
            <div class="col-8">
                <table class="table-bordered table table-striped">
                    <tr>
                        <th>ID</th>
                        <td>{{ $attribute->id }}</td>
                    </tr>
                    <tr>
                        <th>Название</th>
                        <td>{{ $attribute->name }}</td>
                    </tr>
                    <tr>
                        <th>Тип</th>
                        <td>{{ $attribute::typesList()[$attribute->type] }}</td>
                    </tr>
                    <tr>
                        <th>Как опция</th>
                        <td>@if($attribute->as_option)
                                Да
                            @else
                                Нет
                            @endif</td>
                    </tr>
                    <tr>
                        <th>Еденица измерения</th>
                        <td>{{ $attribute->unit ?? "Не задано" }}</td>
                    </tr>
                    <tr>
                        <th>Значения</th>
                        <td>
                            @foreach($attribute->variants as $i => $variant)
                                @if(strpbrk($variant, '|'))
                                    @php
                                        $arrayValue = explode('|', $variant);
                                        $variant    = $arrayValue[0];
                                        $value      = $arrayValue[1];
                                    @endphp
                                    {{ $variant }} <span style="display: inline-block;background-color: {{ $value }};width: 16px;height: 16px"></span>@if((count($attribute->variants) - 1) !== $i),@endif
                                @else
                                    {{ $variant }}@if((count($attribute->variants) - 1) !== $i),@endif
                                @endif
                            @endforeach
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-4">

                <h4>Привязать категории</h4>
                @if(!$categories->isEmpty())
                    <form action="{{ route('admin.shop.attributes.assign-categories', $attribute) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            @error('categories[]')<div class="is-invalid"></div>@enderror
                            <select name="categories[]" class="js-choices" multiple
                                    data-action="{{ route('admin.shop.attributes.un-assign-category', $attribute) }}">
                                <option value="">-=Выбрать категории=-</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                    @foreach($attribute->categories as $cat)
                                        @selected($cat->id === $category->id)
                                    @endforeach
                                    >
                                        {{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->title ? : $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categories[]')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-0">
                            <button type="submit" class="btn btn-primary w-100">Привязать</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
