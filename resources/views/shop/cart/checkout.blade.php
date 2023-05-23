@php
/**
 * @var App\Entities\User\User|null $user
 * @var App\Entities\Shop\DeliveryMethod[] $methods
 * @var App\Cart\Cart $cart
 */
@endphp
@extends('layouts.index')

@section('content')
    @if($errors->any())
        <div class="container">
            {!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
        </div>
    @endif
    <div class="container checkout">
        <div class="row mb-5 flex-column-reverse flex-lg-row">
            <div class="col-md-9">
                <form id="orderForm" action="{{ route('cart.create-order') }}" method="post">
                    @csrf
                    @if($user)
                        <input type="hidden" name="customer_name" value="{{ $user->name }}">
                        <input type="hidden" name="customer_phone" value="{{ $user->userProfile->phone }}">
                    @else
                        <h1>
                            Контактная информация
                            <span>У вас уже есть аккаунт? <a href="{{ route('login') }}">Вход</a></span>
                        </h1>
                        <div class="form-floating mb-3">
                            <input type="text" id="customerName" class="form-control @error('customer_name') is-invalid @enderror"
                                   name="customer_name" placeholder="Ваше Имя" required>
                            <label for="customerName">Ваше Имя</label>
                            @error('customer_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-floating">
                            <input type="text" id="customerPhone" class="form-control @error('customer_phone') is-invalid @enderror"
                                   name="customer_phone" placeholder="Ваш Номер телефона" required>
                            <label for="customerPame">Ваш Номер Телефона</label>
                            @error('customer_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="annotation mb-5">
                            <span class="material-symbols-outlined">info</span> Данные нужны нам, для связи с вами по уточнению деталей заказа.
                        </div>
                    @endif
                    @if($methods)
                        <div class="delivery-block mb-5">
                            <h4>Выберите метод доставки</h4>
                            <select class="form-select mb-3" name="delivery_id" id="deliveryMethods">
                                @foreach($methods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="delivery-address-block">
                        <h4>@if($user && !$user->addresses->isEmpty())Ваш @elseУкажите@endif адрес доставки</h4>
                        @if($user && !$user->addresses->isEmpty())
                            <div class="col-md-12 customer-address mb-5">
                                @php $addressArray = [
                                    'Индекс: ' . $user->addresses[0]->postal_code,
                                    'г. '. $user->addresses[0]->city,
                                    'ул. '.$user->addresses[0]->street,
                                    'д. '.$user->addresses[0]->house,
                                    $user->addresses[0]->house_part ? 'корпус/литера '. $user->addresses[0]->house_part : null,
                                    $user->addresses[0]->flat ? 'кв. '. $user->addresses[0]->flat : null
                                ]
                                @endphp
                                <div class="text">{{ implode(', ', array_filter($addressArray)) }}</div>
                                <div class="button-other-address">
                                    <button type="button" class="btn btn-link" id="otherAddress">Указать другой адрес доставки</button>
                                </div>
                            </div>
                        @endif
                        <div class="row g-2 align-items-center @if($user && !$user->addresses->isEmpty())d-none @endif">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input id="postIndex" type="text" placeholder="Почтовый индекс" class="form-control{{ $errors->has('postal_code') ? ' is-invalid' : '' }}" name="postal_code" value="@if($user && !$user->addresses->isEmpty()){{ $user->addresses[0]->postal_code }}@endif" required>
                                    <label for="postIndex">Почтовый индекс</label>
                                    @if ($errors->has('postal_code'))<div class="invalid-feedback">{{ $errors->first('postal_code') }}</div>@endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input id="city" type="text" placeholder="Город" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" name="city" value="@if($user && !$user->addresses->isEmpty()){{ $user->addresses[0]->city }}@endif">
                                    <label for="city">Город</label>
                                    @if ($errors->has('city'))<div class="invalid-feedback">{{ $errors->first('city') }}</div>@endif
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-3 @if($user && !$user->addresses->isEmpty())d-none @endif"">
                            <input id="street" type="text" placeholder="Улица" class="form-control{{ $errors->has('street') ? ' is-invalid' : '' }}" name="street" value="@if($user && !$user->addresses->isEmpty()){{ $user->addresses[0]->street }}@endif" required>
                            <label for="street">Улица</label>
                            @if ($errors->has('street'))<div class="invalid-feedback">{{ $errors->first('street') }}</div>@endif
                        </div>
                        <div class="row g-2 align-items-center @if($user && !$user->addresses->isEmpty())d-none @endif"">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input id="house" type="text" placeholder="Дом" class="form-control{{ $errors->has('house') ? ' is-invalid' : '' }}" name="house" value="@if($user && !$user->addresses->isEmpty()){{ $user->addresses[0]->house }}@endif" required>
                                    <label for="house">Дом</label>
                                    @if ($errors->has('house'))<div class="invalid-feedback">{{ $errors->first('house') }}</div>@endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input id="housePart" type="text" placeholder="Корпус" class="form-control{{ $errors->has('house_part') ? ' is-invalid' : '' }}" name="house_part" value="@if($user && !$user->addresses->isEmpty()){{ $user->addresses[0]->house_part }}@endif" >
                                    <label for="housePart">Корпус</label>
                                    @if ($errors->has('house_part'))<div class="invalid-feedback">{{ $errors->first('house_part') }}</div>@endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input id="flat" type="text" placeholder="Квартира" class="form-control{{ $errors->has('flat') ? ' is-invalid' : '' }}" name="flat" value="@if($user && !$user->addresses->isEmpty()){{ $user->addresses[0]->flat }}@endif">
                                    <label for="flat">Квартира</label>
                                    @if ($errors->has('flat'))<div class="invalid-feedback">{{ $errors->first('flat') }}</div>@endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="delivery-block">
                        <h4>Выберите метод оплаты</h4>
                        <select class="form-select mb-3" name="payment_method" id="paymentMethod">
                            @foreach(App\Entities\Shop\Order::paymentList() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-100">
                        <button class="btn btn-lg btn-blue-dark w-100 my-3" type="submit">Создать заказ</button>
                    </div>
                </form>
            </div>
            <div class="col-md-3">
                <ul class="cart-items list-group mb-5 mb-lg-0">
                    @foreach($cart->getItems() as $item)
                        <li class="cart-item list-group-item">
                            <div class="cart-item__media">
                                @php $product = $item->getProduct() @endphp
                                <img src="{{ $product->photos[0]->getPhoto('small') }}" alt="{{ $product->photos[0]->alt_tag }}">
                            </div>
                            <div class="cart-item__info">
                                <div class="cart-item__info_head">
                                    <strong>{{ $product->name }}</strong> х {{ $item->getQuantity() }}
                                </div>
                                <div class="cart-item__info_price">
                                    @money($item->getCost(), 'RUB')
                                </div>
                            </div>
                        </li>
                    @endforeach
                    <li class="total-cost list-group-item d-flex">
                        <span class="ms-auto">Итого: </span>
                        <strong>@money($cart->getCost()->getTotal(), 'RUB')</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
