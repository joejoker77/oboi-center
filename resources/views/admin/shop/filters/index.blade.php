@extends('layouts.admin')

@section('content')
    <div class="py-4 d-flex">
        <a href="{{ route('admin.shop.filters.create') }}" class="btn btn-success">Добавить фильтр</a>
        <div class="ms-auto">
            <form class="p-0 m-0" method="POST" id="formActions"
                  action="{{ route('admin.shop.filters.remove-batch') }}">
                @csrf
                <div class="btn-group" role="group" aria-label="control buttons">
                    <button type="submit" name="action" value="remove" class="btn btn-lg btn-danger js-confirm" data-confirm="multi" style="line-height: 0"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="Удалить">
                        <span data-feather="trash-2"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-bordered table-striped" id="productTable">
        <thead>
        <tr>
            <th>
                <input type="checkbox" class="form-check-input" name="select-all" style="cursor: pointer">
            </th>
            <th>
                <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'id' ? '-id' : 'id']) }}">
                    ID @if(request('sort') && request('sort') == 'id') <i data-feather="chevrons-up"></i> @endif
                    @if(request('sort') && request('sort') == '-id') <i data-feather="chevrons-down"></i> @endif
                </a>
            </th>
            <th>
                <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'name' ? '-name' : 'name']) }}">
                    Имя @if(request('sort') && request('sort') == 'name') <i data-feather="chevrons-up"></i> @endif
                    @if(request('sort') && request('sort') == '-name') <i data-feather="chevrons-down"></i> @endif
                </a>
            </th>
            <th>Видимость в категориях</th>
            <th>Действия</th>
        </tr>
        <tr>
            <form action="?" name="search-filters" method="GET" id="searchFilters"></form>
            <td>&nbsp;</td>
            <td>
                <input form="searchFilters" type="text" name="id" class="form-control" aria-label="Искать по ID" value="{{ request('id') }}">
            </td>
            <td>
                <input type="text" form="searchFilters" name="name" class="form-control" aria-label="Искать по имени" value="{{ request('name') }}">
            </td>
            <td></td>
        </tr>
        </thead>
        <tbody>
        @foreach($filters as $filter)
            <tr>
                <td>
                    <input form="formActions" type="checkbox" value="{{ $filter->id }}" class="form-check-input" name="selected[]" style="cursor: pointer">
                </td>
                <td class="text-center">{{ $filter->id }}</td>
                <td class="text-center">{{ $filter->name }}</td>
                <td>
                    @php $filterCategories = $filter->allCategories() @endphp
                    @if(!$filterCategories->isEmpty())
                        <div class="category_list">
                            @foreach($filterCategories as $category)
                                {{ $category->name }},
                            @endforeach
                        </div>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.shop.filters.edit', $filter) }}" class="list-inline-item"
                       id="editCategory" data-bs-toggle="tooltip"
                       data-bs-placement="bottom"
                       data-bs-title="Редактировать"
                    >
                        <span data-feather="edit"></span>
                    </a>|
                    <form method="POST" class="list-inline-item js-confirm"
                          action="{{ route('admin.shop.filters.destroy', $filter) }}"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          data-bs-title="Удалить фильтр"
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
