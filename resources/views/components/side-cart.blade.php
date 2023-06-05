@php /**
 * @var App\Cart\Cart $cart
 * @var App\Cart\CartItem $cartItem
 */ @endphp
<side-cart class="side-cart" data-count-items="{{ $cart->getAmount() }}">
    <div class="side-cart__wrapper">
        <div class="side-cart__inner">
            @if($cart->getAmount() > 0)
                <div class="side-cart__header">
                    <a href="{{ route('cart.index') }}">Ваши покупки ({{ $cart->getAmount() }})</a>
                    <span class="material-symbols-outlined close">close</span>
                </div>
                <div class="side-cart__content">
                    <div class="swiper side-cart-scroll-content">
                        <div class="swiper-wrapper">
                            <div class="cart-items swiper-slide">
                                <form id="checkoutForm" action="#" method="get">
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
                                            <div class="item-image">
                                                <img src="{{ $product->photos[0]->getPhoto('small') }}" alt="{{ $product->photos[0]->alt_tag }}">
                                            </div>
                                            <div class="item-content">
                                                <div class="item-content__head">
                                                    {{ $product->name }}
                                                </div>
                                                <div class="item-content__price d-none d-sm-block">
                                                    @if($product->compare_at_price)
                                                        <span>@money($product->compare_at_price, 'RUB')</span>
                                                    @endif
                                                    @money($product->price, 'RUB')
                                                </div>
                                                <div class="item-content__props d-none d-sm-block">
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
                                            <div class="item-actions">
                                                <span class="material-symbols-outlined">delete</span>

                                                <product-quantity data-change-quantity="{{ route('cart.change-quantity') }}" data-item-id="{{ $cartItem->getId() }}">
                                                    <div class="input-group">
                                                        <button class="minus input-group-text">
                                                            <i class="material-symbols-outlined">remove</i>
                                                        </button>
                                                        <input class="form-control" type="number"
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
                                            </div>
                                            <a class="stretched-link" href="{{ route('catalog.index', ['product_path' => product_path($product->category, $product)]) }}"></a>
                                        </div>
                                    @endforeach
                                </form>
                            </div>
                        </div>
                        <div class="side-cart-scrollbar swiper-scrollbar"></div>
                    </div>
                </div>
                <div class="side-cart__footer">
                    <div class="total">
                        <div class="total-amount">
                            Итого: <span>@money($cart->getCost()->getTotal(), 'RUB')</span>
                        </div>
                    </div>
                    <div class="buttons">
                        <a href="{{ route('cart.checkout') }}" class="btn btn-lg btn-blue-dark">Оформить заказ</a>
                        <div class="btn-group">
                            <button class="btn btn-link">Перейти в корзину</button>|
                            <button class="btn btn-link">Продолжить покупки</button>
                        </div>
                    </div>
                </div>
            @else
                <div class="side-cart__header">
                    Ваша корзина пуста
                    <span class="material-symbols-outlined close">close</span>
                </div>
                <div class="side-cart__content"></div>
                <div class="side-cart__footer"></div>
            @endif
        </div>
    </div>
</side-cart>

