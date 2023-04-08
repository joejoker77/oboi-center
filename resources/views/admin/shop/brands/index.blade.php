@extends('layouts.admin')

@section('content')
    <p class="py-4"><a href="{{ route('admin.shop.brands.create') }}" class="btn btn-success">Добавить бренд</a></p>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Логотип бренда</th>
                <th>Наименование бренда</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        @foreach($brands as $brand)
            <tr>
                <td>
                    @if($brand->logo)
                        <img src="{{ asset($brand->logo->getPhoto('small')) }}" alt="">
                    @else
                        <i data-feather="camera-off"></i>
                    @endif
                </td>
                <td><a href="{{ route('admin.shop.brands.show', $brand) }}">{{ $brand->name }}</a></td>
                <td>
                    <a href="{{ route('admin.shop.brands.edit', $brand) }}" class="list-inline-item"
                       id="editCategory" data-bs-toggle="tooltip"
                       data-bs-placement="bottom"
                       data-bs-title="Редактировать"
                    >
                        <span data-feather="edit"></span>
                    </a>|
                    <form method="POST" class="list-inline-item js-confirm"
                          action="{{ route('admin.shop.brands.destroy', $brand) }}"
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
