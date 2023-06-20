<?php
/** @var \App\Entities\User\User $user */
use App\Entities\Shop\Product;
?>
@extends('layouts.index')

@section('content')
    @if($errors->any())
        <div class="container">
            {!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
        </div>
    @endif
    <div class="container mb-5">
        <ul class="nav nav-pills" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button"
                        role="tab"
                        aria-controls="profile-tab-pane" aria-selected="true" class="nav-link nav-link-dark active">
                    Профиль
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button id="wishlist-tab" data-bs-toggle="tab" data-bs-target="#wishlist-tab-pane" type="button"
                        role="tab"
                        aria-controls="wishlist-tab-pane" aria-selected="false" class="nav-link nav-link-dark">
                    Избранное
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders-tab-pane" type="button" role="tab"
                        aria-controls="orders-tab-pane" aria-selected="false" class="nav-link nav-link-dark">
                    Заказы
                </button>
            </li>
            @if(!$user->addresses->isEmpty())
                <li class="nav-item" role="presentation">
                    <button id="addresses-tab" data-bs-toggle="tab" data-bs-target="#addresses-tab-pane" type="button"
                            role="tab"
                            aria-controls="addresses-tab-pane" aria-selected="false" class="nav-link nav-link-dark">
                        Адреса доставки
                    </button>
                </li>
            @endif
        </ul>
        <div class="tab-content" id="profileTabsContent">
            <div class="tab-pane fade show active" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
                 tabindex="0">
                <div class="p-5 mb-4 bg-light rounded-3">
                    <div class="container-fluid">
                        <h1 class="display-5 fw-bold">Данные профиля</h1>
                        <table class="table table-responsive table-striped table-bordered">
                            <tbody>
                            <tr>
                                <th>Имя</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Фамилия</th>
                                <td>{{ $user->userProfile->last_name }}</td>
                            </tr>
                            <tr>
                                <th>E-mail</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Номер телефона</th>
                                <td>
                                    <p class="d-flex align-items-center justify-content-between mb-0">{{ $user->userProfile->phone }}
                                        @if($user->userProfile->tokenExpired())
                                            Ваш телефонный номер не подтвержден.
                                            <button type="button" id="getConfirmPhone" class="btn btn-outline-dark">
                                                Подтвердить номер телефона
                                            </button>
                                        @endif
                                        @if($user->userProfile->isPhoneVerified())
                                            <span class="badge text-white text-bg-success">Номер подтвержден</span>
                                        @elseif($user->userProfile->phone)
                                            <span class="badge text-white text-bg-danger">Номер не подтвержден</span>
                                        @endif
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th>Двухфакторная аутентификация</th>
                                <td>@if($user->userProfile->phone_auth)
                                        Включена
                                    @else
                                        Выключена
                                    @endif</td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-lg btn-blue-dark me-2" data-js-action="getFromDeliveryAddress">
                                Добавить адрес доставки
                            </button>
                            <button id="getFormProfile" class="btn btn-blue-dark btn-lg" type="button">Редактировать
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="wishlist-tab-pane" role="tabpanel" aria-labelledby="wishlist-tab"
                 tabindex="0">
                <div class="p-5 mb-4 bg-light rounded-3">
                    <div class="container-fluid" id="categoryPage">
                        <div class="h1">Избранное</div>
                        @if($user->favorites)
                            <div class="product-items">
                                @php $favorites = Product::whereIn('id', $user->favorites->pluck('product_id')->toArray())->with(['photos','category', 'values'])->get() @endphp
                                @php /** @var Product $favorite */ @endphp
                                @foreach($favorites as $favorite)
                                    <div class="product-item">
                                        <a href="{{ route('catalog.index',['product_path' => product_path($favorite->category, $favorite)]) }}">
                                            <div class="product-media">
                                                @if(!$favorite->isCanBuy())
                                                    <span class="material-symbols-outlined" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="custom-tooltip" data-bs-title="Доступно только для заказа">info</span>
                                                @else
                                                    <span class="position-absolute badge rounded-pill bg-primary">{{ $favorite->quantity }}</span>
                                                @endif
                                                @if(!$favorite->photos->isEmpty())
                                                    <img class="position-relative" src="{{ $favorite->photos()->first()->getPhoto('medium') }}" alt="{{ $favorite->photos()->first()->alt_tag }}">
                                                @else
                                                    <span class="position-relative material-symbols-outlined">no_photography</span>
                                                @endif
                                                @auth
                                                    @if(in_array($favorite->id, $user->favorites->pluck('product_id')->toArray()))
                                                        <form class="favorite" action="{{ route('shop.remove-favorite', $favorite) }}" method="post">
                                                            @csrf
                                                            <button type="submit">
                                                                <span class="material-symbols-outlined selected" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-custom-class="custom-tooltip" data-bs-title="Удалить из избранного">favorite</span>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form class="favorite" action="{{ route('shop.add-favorite', $favorite) }}" method="post">
                                                            @csrf
                                                            <button type="submit">
                                                                <span class="material-symbols-outlined" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-custom-class="custom-tooltip" data-bs-title="Добавить в избранное">favorite</span>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endauth
                                            </div>
                                            <div class="product-props">
                                                <span class="product-name">{{ $favorite->name }}</span>
                                                <span class="product-price d-flex">
                                        <strong>Цена:</strong>&nbsp;
                                        <span class="prop-value">@money($favorite->price, 'RUB')</span>
                                    </span>
                                                @foreach($favorite->values as $value)
                                                    @if($value->attribute->name == 'Ширина рулона')
                                                        @php $dmns['height'] = $value->value @endphp
                                                    @endif
                                                    @if($value->attribute->name == 'Длина рулона')
                                                        @php $dmns['width'] = $value->value @endphp
                                                    @endif
                                                    @if ($value->attribute->name == "Материал покрытия")
                                                        @php $mat['up'] = $value->value @endphp
                                                    @endif
                                                    @if ($value->attribute->name == "Материал основы")
                                                        @php $mat['down'] = $value->value @endphp
                                                    @endif
                                                @endforeach
                                                @if(isset($mat))
                                                    <span class="product-materials d-none d-lg-block">
                                            <strong>Материал:</strong>
                                            <span class="prop-value">{{ $mat['up'] }} x {{ $mat['down'] }}</span>
                                        </span>
                                                @endif
                                                @if(isset($dmns) and !empty($dmns['height']) and !empty($dmns['width']))
                                                    <span class="product-dimensions d-none d-lg-block">
                                            <strong>Размер:</strong>
                                            <span class="prop-value">{{ $dmns['height'] }} x {{ $dmns['width'] }}</span>
                                        </span>
                                                @endif
                                                <product-form>
                                                    <form action="{{ route('cart.add', $favorite) }}" id="productForm-{{ $favorite->id }}" class="w-100" novalidate>
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $favorite->id }}">
                                                        <input type="hidden" name="product_quantity" value="">
                                                        <product-quantity>
                                                            <label for="elementQuantity-{{ $favorite->id }}">Укажите количество</label>
                                                            <div class="input-group">
                                                                <button class="minus input-group-text">
                                                                    <i class="material-symbols-outlined">remove</i>
                                                                </button>
                                                                <input class="form-control" type="number"
                                                                       id="elementQuantity-{{ $favorite->id }}"
                                                                       name="quantity" value="1"
                                                                       data-max-quantity="{{ $favorite }}"
                                                                       data-order-type="{{ $favorite->isCanBuy($favorite->quantity) ? 'checkout' : 'order' }}"
                                                                >
                                                                <button class="plus input-group-text">
                                                                    <i class="material-symbols-outlined">add</i>
                                                                </button>
                                                            </div>
                                                        </product-quantity>
                                                        @if(!$favorite->isCanBuy())
                                                            <input type="hidden" name="type_order" value="order">
                                                            <button type="submit" class="btn btn-blue-dark w-100">Создать заказ</button>
                                                        @else
                                                            <input type="hidden" name="type_order" value="checkout">
                                                            <button type="submit" class="btn btn-blue-dark w-100">В корзину</button>
                                                        @endif
                                                    </form>
                                                </product-form>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="orders-tab-pane" role="tabpanel" aria-labelledby="orders-tab" tabindex="0">
                <div class="p-5 mb-4 bg-light rounded-3">
                    <div class="container-fluid">
                        <div class="h1">Заказы</div>
                        <div class="accordion" id="accordionOrders">
                            @foreach($user->orders as $key => $order)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{$key}}">
                                        <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse-{{$key}}"
                                                aria-expanded="true" aria-controls="collapseOne">
                                            ID Заказа #{{ $order->id }} от {{ $order->created_at }}
                                        </button>
                                    </h2>
                                    <div id="collapse-{{$key}}" class="accordion-collapse collapse"
                                         aria-labelledby="heading-{{$key}}" data-bs-parent="#accordionOrders">
                                        <div class="accordion-body">
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
                                                                        class="{{ get_order_label((int)$order->statuses)['class'] }}">{{ get_order_label((int)$order->statuses)['label'] }}</div>
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
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @if(!$user->addresses->isEmpty())
                <div class="tab-pane fade" id="addresses-tab-pane" role="tabpanel" aria-labelledby="addresses-tab"
                     tabindex="0">
                    <div class="p-5 mb-4 bg-light rounded-3">
                        <div class="container-fluid">
                            <div class="h1">Адреса доставки</div>
                            <div class="addresses d-flex gap-3">
                                @foreach($user->addresses as $address)
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <form id="removeAddress-{{ $address->id }}"
                                                  action="{{ route('cabinet.profile.remove-delivery-address', $address) }}"
                                                  method="post">@csrf</form>
                                            <p><strong>{{ $address->postal_code }}</strong>, {{ $address->city }}
                                                , {{ $address->street }} {{ $address->house }} {{ $address->house_part }}
                                                , {{ $address->flat }}</p>
                                            <div class="d-flex">
                                                <div class="btn-group ms-auto">
                                                    <button form="removeAddress-{{$address->id}}" type="submit"
                                                            class="btn btn-sm btn-outline-danger">Удалить
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                                            data-js-action="editDeliveryAddress"
                                                            data-address-id="{{ $address->id }}">Редактировать
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <button class="btn btn-lg" data-js-action="getFromDeliveryAddress"
                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        data-bs-custom-class="custom-tooltip" data-bs-title="Добавть адрес доставки">
                                    <span class="material-symbols-outlined">add_box</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
