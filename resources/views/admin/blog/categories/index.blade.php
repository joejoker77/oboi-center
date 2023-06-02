@extends('layouts.admin')

@section('content')
    <p class="py-4"><a href="{{ route('admin.blog.categories.create') }}" class="btn btn-success">Добавить категорию</a></p>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Имя</th>
            <th>Псевдоним</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td><a href="{{ route('admin.blog.categories.show', $category) }}">{{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->title ?: $category->name }}</a></td>
                <td>{{ $category->slug }}</td>
                <td>
                    <form method="POST"  class="list-inline-item"
                          action="{{ route('admin.blog.categories.first', $category) }}"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          data-bs-title="В начало"
                    >
                        @csrf
                        <button class="btn p-0 align-baseline text-primary"><span data-feather="chevrons-up"></span></button>
                    </form>
                    <form method="POST" class="list-inline-item"
                          action="{{ route('admin.blog.categories.up', $category) }}"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          data-bs-title="На одну позицию вверх"
                    >
                        @csrf
                        <button class="btn p-0 align-baseline text-primary"><span data-feather="chevron-up"></span></button>
                    </form>
                    <form method="POST" class="list-inline-item"
                          action="{{ route('admin.blog.categories.down', $category) }}"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          data-bs-title="На одну позицию вниз"
                    >
                        @csrf
                        <button class="btn p-0 align-baseline text-primary"><span data-feather="chevron-down"></span></button>
                    </form>
                    <form method="POST" class="list-inline-item me-5"
                          action="{{ route('admin.blog.categories.last', $category) }}"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          data-bs-title="В конец"
                    >
                        @csrf
                        <button class="btn p-0 align-baseline text-primary"><span data-feather="chevrons-down"></span></button>
                    </form>

                    <a href="{{ route('admin.blog.categories.edit', $category) }}" class="list-inline-item"
                       id="editCategory" data-bs-toggle="tooltip"
                       data-bs-placement="bottom"
                       data-bs-title="Редактировать"
                    >
                        <span data-feather="edit"></span>
                    </a>|
                    <form class="list-inline-item" method="POST"
                          action="{{ route('admin.blog.category.toggle.status', [$category]) }}"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          data-bs-title="@if($category->isActive()){{"Снять с публикации"}}@else{{"Опубликовать"}}@endif"
                    >
                        @csrf
                        <button type="submit" class="btn p-0 align-baseline text-primary" style="line-height: 0">
                            <span data-feather="@if($category->isActive()){{"eye-off"}}@else{{"eye"}}@endif"></span>
                        </button>
                    </form>|
                    <form method="POST" class="list-inline-item js-confirm"
                          action="{{ route('admin.blog.categories.destroy', $category) }}"
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
    {{ $categories->links() }}
@endsection
