@extends('layouts.index')
@section('content')
    @php /** * @var App\Cart\Cart $cart
         * @var App\Cart\CartItem $cartItem
         */
        @endphp
    <div class="container cart">
        <div class="cart__wrapper">
            <div class="cart__inner">
                @if($cart->getAmount() > 0)
                    <h1 class="cart__header mb-lg-5">Ваши покупки</h1>
                    <div class="cart__content">
                        <div class="cart-items">
                            <div class="items-head d-none d-lg-block">
                                <table class="w-100">
                                    <tr>
                                        <td>Наименование</td>
                                        <td>Цена</td>
                                        <td>Количество</td>
                                        <td>Итог</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <form id="checkoutForm" action="{{ route('cart.checkout') }}" method="get">
                                @csrf
                                @foreach($cart->getItems() as $cartItem)
                                    @php
                                        /** @var App\Entities\Shop\Product $product */
                                        /** @var App\Entities\Shop\Value $value */
                                        $product    = $cartItem->getProduct();
                                        $dimensions = ['height'=> '', 'width' => ''];
                                        $materials  = ['up' => '', 'down' => ''];
                                        $type       = null;
                                        foreach($product->values as $value):
                                            if($value->attribute->name == 'Ширина рулона'):
                                               $dimensions['height'] = $value->value;
                                            endif;
                                            if($value->attribute->name == 'Длина рулона'):
                                                $dimensions['width'] = $value->value;
                                            endif;
                                            if ($value->attribute->name == "Материал покрытия"):
                                                $materials['up'] = $value->value;
                                            endif;
                                            if ($value->attribute->name == "Материал основы"):
                                                $materials['down'] = $value->value;
                                            endif;
                                            if ($value->attribute->name == 'Тип обоев'):
                                                $type = $value->value;
                                            endif;
                                        endforeach
                                    @endphp

                                    <div class="cart-item">
                                        <div class="item-name">
                                            <div class="item-image">
                                                <img src="{{ $product->photos[0]->getPhoto('small') }}" alt="{{ $product->photos[0]->alt_tag }}">
                                            </div>
                                            <div class="item-head">
                                                <a href="{{ route('catalog.index', ['product_path' => product_path($product->category, $product)]) }}">{{ $product->name }}</a>
                                            </div>
                                        </div>
                                        <div class="item-content d-none d-lg-block">
                                            <div class="item-content__price">
                                                @if($product->compare_at_price)
                                                    <span>@money($product->compare_at_price, 'RUB')</span>
                                                @endif
                                                @money($product->price, 'RUB')
                                            </div>
                                            <div class="item-content__props">
                                                @if(!empty($dimensions['width']) && !empty($dimensions['height']))
                                                    <div class="prop-item dimensions">
                                                        <div class="prop-text">
                                                            <strong>Размер рулона</strong> {{ $dimensions['height'] }} x {{ $dimensions['width'] }}
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(!empty($materials['up']) && !empty($materials['down']) )
                                                    @if ($materials['up'] === $materials['down'])
                                                        @php $material = $materials['up'] @endphp
                                                    @elseif ($materials['up'] == 'Винил' && $materials['down'] == 'Флизелин')
                                                        @php $material = 'Винил на флизелине' @endphp
                                                    @endif
                                                    @if(isset($material))
                                                        <div class="prop-item material">
                                                            <div class="prop-text">
                                                                <strong>Материал</strong> {{ $material }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif

                                                @if($type)
                                                    <div class="prop-item type">
                                                        <div class="prop-text">
                                                            <strong>Тип</strong> {{ $type }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <product-quantity data-change-quantity="{{ route('cart.change-quantity') }}" data-item-id="{{ $cartItem->getId() }}">
                                            <div class="input-group">
                                                <button class="minus input-group-text">
                                                    <i class="material-symbols-outlined">remove</i>
                                                </button>
                                                <input form="checkoutForm" class="form-control" type="number" id="elementQuantity"
                                                       aria-label="Количество"
                                                       name="quantity" value="{{ $cartItem->getQuantity() }}"
                                                       data-max-quantity="{{ $product->quantity }}"
                                                       data-order-type="{{ $product->isCanBuy($cartItem->getQuantity()) ? 'checkout' : 'order' }}">
                                                <button class="plus input-group-text">
                                                    <i class="material-symbols-outlined">add</i>
                                                </button>
                                            </div>
                                        </product-quantity>
                                        <div class="subtotal-price">
                                            @money($product->price * $cartItem->getQuantity(), 'RUB')
                                        </div>
                                        <div class="item-actions">
                                            <span class="delete-item d-none d-lg-inline">Удалить</span>
                                            <span class="material-symbols-outlined text-danger d-inline d-lg-none">delete</span>
                                        </div>
                                    </div>
                                @endforeach
                            </form>
                        </div>
                    </div>
                    <div class="cart__footer">
                        <div class="total">
                            <div class="total-amount">
                                Итого: <span>@money($cart->getCost()->getTotal(), 'RUB')</span>
                            </div>
                        </div>
                        <div class="buttons">
                            <button type="submit" form="checkoutForm" class="btn btn-blue-dark w-100">Оформить заказ</button>
                        </div>
                    </div>
                @else
                    <div class="cart__header">
                        <h1>Ваша корзина пуста</h1>
                    </div>
                    <div class="cart__content"></div>
                    <div class="cart__footer"></div>
                @endif
            </div>
        </div>
    </div>
@endsection
