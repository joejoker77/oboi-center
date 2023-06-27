@php /** @var App\Entities\Shop\Order[] $orders */ @endphp

@extends('layouts.admin')

@section('content')
    <h1 class="py-4">Заказы</h1>
    <div class="d-flex flex-column">
        <div class="ms-auto">
            <form class="p-0 mb-3" method="POST" id="formActions" action="{{ route('admin.shop.orders.multi-delete') }}">
                @csrf
                <div class="btn-group" role="group" aria-label="control buttons">
                    <button type="submit" name="action" value="remove" class="btn btn-lg btn-danger js-confirm" data-confirm="multi" style="line-height: 0"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="Удалить выбранных">
                        <span data-feather="trash-2"></span>
                    </button>
                </div>
            </form>
        </div>
        <table class="table table-bordered table-striped" id="orderTable">
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
                    <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'user' ? '-user' : 'user']) }}">
                        Пользователь @if(request('sort') && request('sort') == 'user') <i data-feather="chevrons-up"></i> @endif
                        @if(request('sort') && request('sort') == '-user') <i data-feather="chevrons-down"></i> @endif
                    </a>
                </th>
                <th>
                    <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'current_status' ? '-current_status' : 'current_status']) }}">
                        Статус @if(request('sort') && request('sort') == 'current_status') <i data-feather="chevrons-up"></i> @endif
                        @if(request('sort') && request('sort') == '-current_status') <i data-feather="chevrons-down"></i> @endif
                    </a>
                </th>
                <th>
                    <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'payment_method' ? '-payment_method' : 'payment_method']) }}">
                        Способ оплаты @if(request('sort') && request('sort') == 'payment_method') <i data-feather="chevrons-up"></i> @endif
                        @if(request('sort') && request('sort') == '-payment_method') <i data-feather="chevrons-down"></i> @endif
                    </a>
                </th>
                <th>
                    <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'delivery_name' ? '-delivery_name' : 'delivery_name']) }}">
                        Способ доставки @if(request('sort') && request('sort') == 'delivery_name') <i data-feather="chevrons-up"></i> @endif
                        @if(request('sort') && request('sort') == '-delivery_name') <i data-feather="chevrons-down"></i> @endif
                    </a>
                </th>
                <th>Действия</th>
            </tr>
            <tr>
                <form action="?" name="search-orders" method="GET" id="searchOrders"></form>
                <th></th>
                <th><input form="searchOrders" type="text" name="id" class="form-control" aria-label="Искать по ID" value="{{ request('id') }}"></th>
                <th><input form="searchOrders" type="text" name="user" class="form-control" aria-label="Искать по имени пользователя" value="{{ request('user') }}"></th>
                <th>
                    <select name="status" id="selectStatus" class="js-choices" form="searchOrders">
                        <option value="">-= Выбрать статус =-</option>
                        @foreach(App\Entities\Shop\Order::statusesList() as $key => $status)
                            <option value="{{ $key }}"
                                @selected($key == request('status'))
                            >
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </th>
                <th>
                    <select name="payment_method" id="selectPaymentMethod" class="js-choices" form="searchOrders">
                        <option value="">-= Выбрать способ оплаты =-</option>
                        @foreach(App\Entities\Shop\Order::paymentList() as $key => $method)
                            <option value="{{ $key }}"
                                @selected($key == request('payment_method'))
                            >
                                {{ $method }}
                            </option>
                        @endforeach
                    </select>
                </th>
                <th>
                    <select name="delivery_name" id="selectDeliveryName" class="js-choices" form="searchOrders">
                        <option value="">-= Выбрать способ доставки =-</option>
                        @foreach(App\Entities\Shop\Order::deliveryList() as $delivery)
                            <option value="{{ $delivery }}"
                                @selected($delivery == request('delivery_name'))
                            >
                                {{ $delivery }}
                            </option>
                        @endforeach
                    </select>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @if(!$orders->isEmpty())
                @foreach($orders as $order)
                    <tr>
                        <td style="text-align: center">
                            <input form="formActions" type="checkbox" value="{{ $order->id }}" class="form-check-input" name="selected[]" style="cursor: pointer">
                        </td>
                        <td><a href="{{ route('admin.shop.orders.show', $order) }}">{{ $order->id }}</a></td>
                        <td><a href="{{ route('admin.users.show', $order->user) }}">{{ $order->user->name }}</a></td>
                        <td>{{ $order->getStatus($order->current_status) }}</td>
                        <td>{{ $order->paymentList()[$order->payment_method] }}</td>
                        <td>{{ $order->delivery_name }}</td>
                        <td>
                            <a href="{{ route('admin.shop.orders.edit', $order) }}" class="list-inline-item mx-1"
                               id="editCategory" data-bs-toggle="tooltip"
                               data-bs-placement="bottom"
                               data-bs-title="Редактировать"
                            >
                                <span data-feather="edit"></span>
                            </a>|<form method="POST" class="list-inline-item js-confirm ms-2"
                                       action="{{ route('admin.shop.orders.destroy', $order) }}"
                                       data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       data-bs-title="Удалить заказ"
                            >
                                @csrf
                                @method('DELETE')
                                <button class="btn p-0 align-baseline js-confirm text-danger" type="submit"><span data-feather="trash-2"></span></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
@endsection
