@php use App\Entities\Shop\Order; @endphp
@php /** @var App\Entities\Shop\Order $order */ @endphp
@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1 class="my-4">Заказ №{{ $order->id }}</h1>
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.shop.orders.edit', $order) }}" class="btn btn-primary btn-lg d-flex me-2"
               data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Редактировать">
                <span data-feather="edit"></span>
            </a>
            <div>
                <form class="btn btn-danger btn-lg" method="POST"
                      action="{{ route('admin.shop.orders.destroy', $order) }}" data-bs-toggle="tooltip"
                      data-bs-placement="bottom" data-bs-title="Удалить">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn p-0 text-white d-flex js-confirm" style="line-height: 0">
                        <span data-feather="trash-2"></span>
                    </button>
                </form>
            </div>
        </div>
        <div class="col-md-9">
            <h5 class="mb-3">Данные пользователя</h5>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>ID</th>
                    <td>{{ $order->id }}</td>
                </tr>
                <tr>
                    <th>Дата создания</th>
                    <td>{{ $order->created_at }}</td>
                </tr>
                <tr>
                    <th>Пользователь</th>
                    <td>{{ $order->user->name }}</td>
                </tr>
                <tr>
                    <th>E-mail пользователя</th>
                    <td>{{ $order->user->email }}</td>
                </tr>
                <tr>
                    <th>Телефон пользователя</th>
                    <td>{{ $order->user->userProfile->phone }}</td>
                </tr>
                <tr>
                    <th>Покупатель</th>
                    <td>{{ $order->customer_name }}</td>
                </tr>
                <tr>
                    <th>Телефон покупателя</th>
                    <td>{{ $order->customer_phone }}</td>
                </tr>
                <tr>
                    <th>Геолокация пользователя</th>
                    <td>{{ $order->customer_ip }}</td>
                </tr>
            </table>
            <h5 class="mb-3">Информация о доставке</h5>
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Способ доставки</th>
                    <td>{{ $order->delivery_name }}</td>
                </tr>
                @if($order->delivery_name !== 'Самовывоз')
                    <tr>
                        <th>Адрес доставки</th>
                        <td>{{ $order->delivery_address }}</td>
                    </tr>
                @endif
            </table>
            <h5 class="mb-3">Информация об оплате</h5>
            <table class="table-bordered table-bordered table">
                <tr>
                    <th>Способ оплаты</th>
                    <td>{{ $order::paymentList()[$order->payment_method] }}</td>
                </tr>
                @if($order->payment_method === Order::PAYMENT_CARD)
                    <tr>
                        <th>Статус оплаты</th>
                        <td>
                            <div class="{{ get_order_label($order->current_status)['class'] }}">
                                {{ get_order_label($order->current_status)['label'] }}
                            </div>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
        <div class="col-md-3">
            <h5 class="mb-3">Текущий статус заказа</h5>
            <ul class="list-group mb-4">
                <li class="list-group-item">{{ $order::statusesList()[$order->current_status] }}</li>
            </ul>
            <h5 class="mb-3">Содержимое заказа</h5>
            <ul class="list-group">
                @php /** @var App\Entities\Shop\OrderItem $orderItem */ @endphp
                @foreach($order->orderItems as $orderItem)
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('admin.shop.products.show', $orderItem->products[0]) }}">
                            {{ $orderItem->product_name }}
                        </a>
                        <div class="prices">
                            @money($orderItem->price, 'RUB') x {{ $orderItem->quantity }}
                        </div>
                        <strong>@money($orderItem->price * $orderItem->quantity, 'RUB')</strong>
                    </li>
                @endforeach
                @if($order->delivery_name !== 'Самовывоз')
                    <li class="list-group-item d-flex justify-content-end">
                        Цена доставки: @money($order->delivery_cost, 'RUB')
                    </li>
                @endif
                <li class="list-group-item d-flex justify-content-end">
                    Итого:&nbsp;&nbsp;<strong>@money($order->cost + $order->delivery_cost, 'RUB')</strong>
                </li>
            </ul>
        </div>
    </div>
@endsection
