@extends('layouts.admin')

@section('content')
    <div class="py-4 d-flex">
        <a href="{{ route('admin.shop.delivery-methods.create') }}" class="btn btn-success">Добавить метод доставки</a>
        <div class="ms-auto">
            <form class="p-0 m-0" method="POST" id="formActions"
                  action="{{ route('admin.shop.delivery-methods.remove') }}">
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
            <th style="text-align: center">
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
                    Наименование @if(request('sort') && request('sort') == 'name') <i data-feather="chevrons-up"></i> @endif
                    @if(request('sort') && request('sort') == '-name') <i data-feather="chevrons-down"></i> @endif
                </a>
            </th>
            <th>
                <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'sku' ? '-cost' : 'cost']) }}">
                    Стоимость @if(request('sort') && request('sort') == 'cost') <i data-feather="chevrons-up"></i> @endif
                    @if(request('sort') && request('sort') == '-cost') <i data-feather="chevrons-down"></i> @endif
                </a>
            </th>
            <th>Минимальный вес</th>
            <th>Минимальное количество</th>
            <th>Минимальный объем</th>
        </tr>
        <tr>
            <form action="?" name="search-products" method="GET" id="searchDelivery"></form>
            <td>&nbsp;</td>
            <td style="max-width: 50px;text-align: center">
                <input form="searchProducts" type="text" name="id" class="form-control" aria-label="Искать по ID" value="{{ request('id') }}">
            </td>
            <td style="max-width: 175px">
                <input type="text" form="searchProducts" name="name" class="form-control" aria-label="Искать по имени" value="{{ request('name') }}">
            </td>
            <td style="max-width: 175px">
                <input type="text" form="searchProducts" name="sku" class="form-control" aria-label="Искать стоимости" value="{{ request('cost') }}">
            </td>
            <td style="width: 250px">
                <input type="text" name="min_weight" class="form-control" form="searchProducts" value="{{ request('min_weight') }}">
            </td>
            <td style="width: 220px">
                <input type="text" name="min_amount" class="form-control" form="searchProducts" value="{{ request('min_amount') }}">
            </td>
            <td style="max-width: 50px">
                <input type="text" name="min_dimensions" class="form-control" form="searchProducts" value="{{ request('min_dimensions') }}">
            </td>
        </tr>
        </thead>
        <tbody>
        @foreach($methods as $method)
            <tr>
                <td style="text-align: center">
                    <input form="formActions" type="checkbox" value="{{ $method->id }}" class="form-check-input" name="selected[]" style="cursor: pointer">
                </td>
                <td style="text-align: center">{{ $method->id }}</td>
                <td style="width: 100px;text-align: center">{{ $method->name }}</td>
                <td>{{ $method->cost }}</td>
                <td style="white-space: nowrap">{{ $method->min_weight }} - {{ $method->max_weight }}</td>
                <td>{{ $method->min_amount }} - {{ $method->max_amount }}</td>
                <td>{{ $method->min_dimensions }} - {{ $method->max_dimensions }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
