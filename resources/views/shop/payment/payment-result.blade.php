@extends('layouts.index')

@section('content')
    <div class="container">
        <h1>Результат оплаты</h1>
        <p>Заказ № {{ $order->id }} успешно оплачен</p>
        <div class="row">
            <div class="col-md-9">
                <div class="p-5 bg-light rounded-3">
                    <table class="table table-bordered table-striped">
                        <tbody>
                        <tr>
                            <th>ID Заказа:</th>
                            <td>{{ $order->id }}</td>
                        </tr>
                        <tr>
                            <th>Дата создания:</th>
                            <td>{{ $order->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Текущий статус:</th>
                            <td>
                                <div
                                    class="{{ get_order_label((int)$order->current_status)['class'] }}">{{ get_order_label((int)$order->current_status)['label'] }}</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Способ доставки:</th>
                            <td>{{ $order->delivery_name }}</td>
                        </tr>
                        <tr>
                            <th>Почтовый индекс:</th>
                            <td>{{ $order->delivery_index }}</td>
                        </tr>
                        <tr>
                            <th>Адрес доставки:</th>
                            <td>{{ $order->delivery_address }}</td>
                        </tr>
                        <tr>
                            <th>Стоимость заказа:</th>
                            <td>@money($order->cost, 'RUB')</td>
                        </tr>
                        <th>Стоимость доставки:</th>
                        <td>@money($order->delivery_cost, 'RUB')</td>
                        <tr>
                            <th>Итого к оплате:</th>
                            <td>@money($order->getTotalCost(), 'RUB')</td>
                        </tr>
                        @if($order->note)
                            <th>Примечание к заказу:</th>
                            <td>{{ $order->note }}</td>
                        @endif
                        </tbody>
                    </table>
                    @if(isset($resp['formUrl']))
                        <a href="{{ $resp['formUrl'] }}" class="btn btn-danger" target="_blank">Оплатить заказ</a>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <h4>Позиции в заказе</h4>
                <ul class="cart-items list-group">
                    @foreach($order->orderItems as $item)
                        <li class="cart-item list-group-item">
                            <div class="cart-item__media">
                                @php $product = $item->products->filter(function ($product) use($item) {return $item->product_id == $product->id;})->first() @endphp
                                <img
                                    src="{{ $product->photos[0]->getPhoto('small') }}"
                                    alt="{{ $product->photos[0]->alt_tag }}">
                            </div>
                            <div class="cart-item__info">
                                <div class="cart-item__info_head">
                                    <strong>{{ $product->name }}</strong>
                                    х {{ $item->quantity }}
                                </div>
                                <div class="cart-item__info_price">
                                    @money($item->getCost(), 'RUB')
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
