<?php  /** @var \App\Entities\Shop\Attribute $attribute */ ?>
@extends('layouts.admin')

@section('content')
    <p class="py-4"><a href="{{ route('admin.shop.attributes.create') }}" class="btn btn-success">Добавить аттрибут</a></p>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Наименование атрибута</th>
                <th>Тип атрибута</th>
                <th>Отображение в категориях</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        @foreach($attributes as $attribute)
            @php($allCategories = $attribute->allCategories())
            <tr>
                <td><a href="{{ route('admin.shop.attributes.show', $attribute) }}">{{ $attribute->name }}</a></td>
                <td>{{ \App\Entities\Shop\Attribute::typesList()[$attribute->type] }}</td>
                <td>
                    @if(!empty($allCategories))
                        @foreach($allCategories as $i => $category)
                            <a href="{{ route('admin.shop.categories.show', $category) }}">{{$category->name}}</a>@if($i + 1 !== count($allCategories)),@endif
                        @endforeach
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.shop.attributes.edit', $attribute) }}" class="list-inline-item"
                       id="editCategory" data-bs-toggle="tooltip"
                       data-bs-placement="bottom"
                       data-bs-title="Редактировать"
                    >
                        <span data-feather="edit"></span>
                    </a>|
                    <form method="POST" class="list-inline-item js-confirm"
                          action="{{ route('admin.shop.attributes.destroy', $attribute) }}"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          data-bs-title="Удалить категорию"
                    >
                        @csrf
                        @method('DELETE')
                        <button class="btn p-0 align-baseline js-confirm text-danger" type="submit"><span data-feather="trash-2"></span></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
