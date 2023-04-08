@extends('layouts.admin')

@section('content')
    <p class="py-4"><a href="{{ route('admin.shop.tags.create') }}" class="btn btn-success">Добавить тег</a></p>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Наименование тега</th>
                <th>Псевдоним тега</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tags as $tag)
                <tr>
                    <td><a href="{{ route('admin.shop.tags.show', $tag) }}">{{ $tag->name }}</a></td>
                    <td>{{ $tag->slug }}</td>
                    <td>
                        <a href="{{ route('admin.shop.tags.edit', $tag) }}" class="list-inline-item"
                           id="editCategory" data-bs-toggle="tooltip"
                           data-bs-placement="bottom"
                           data-bs-title="Редактировать"
                        >
                            <span data-feather="edit"></span>
                        </a>|
                        <form method="POST" class="list-inline-item js-confirm"
                              action="{{ route('admin.shop.tags.destroy', $tag) }}"
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
