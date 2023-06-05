@extends('layouts.index')

@section('content')
    <div class="container" id="categoryPage">
        <div class="row products">
            <h1>Результат поиска</h1>
            <div class="d-lg-none pb-3">
                <button class="btn btn-blue-dark w-100" id="showFilter">
                    <span class="material-symbols-outlined">filter_list</span>
                    Подобрать по параметрам
                </button>
            </div>
            <x-filter :request="$request" :restAttributes="$restAttributes" :restCategories="$restCategories" :restTags="$restTags" position="left" />
            <div class="col-lg-9 mx-auto">
                @if($products)
                    <div class="product-items">
                        @php /** @var App\Entities\Shop\Product $product */ @endphp
                        @foreach($products as $key => $product)
                            @php
                                $quantity = $product->quantity;
                                foreach ($cartAllItems as $cartItem) {
                                    if ($product->id == $cartItem->getProductId()) {
                                        $quantity -= $cartItem->getQuantity();
                                    }
                                }
                                if ($quantity < 0) {$quantity = 0;}
                            @endphp
                            <div class="product-item">
                                <a href="{{ route('catalog.index',['product_path' => product_path($product->category, $product)]) }}">
                                    <div class="product-media">
                                        @if(!$product->isCanBuy($quantity))
                                            <span class="material-symbols-outlined" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="custom-tooltip" data-bs-title="Доступно только для заказа">info</span>
                                        @else
                                            <span class="position-absolute badge rounded-pill bg-primary">{{ $quantity }}</span>
                                        @endif
                                        @if(!$product->photos->isEmpty())
                                            <img src="{{ $product->photos()->first()->getPhoto('medium') }}" alt="{{ $product->photos()->first()->alt_tag }}">
                                        @else
                                            <span class="material-symbols-outlined">no_photography</span>
                                        @endif
                                    </div>
                                    <div class="product-props">
                                        <span class="product-name">{{ $product->name }}</span>
                                        <span class="product-price d-flex">
                                        <strong>Цена:</strong>&nbsp;
                                        <span class="prop-value">@money($product->price, 'RUB')</span>
                                    </span>
                                        @foreach($product->values as $value)
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
                                            <form action="#" id="productForm-{{ $key }}" class="w-100" novalidate>
                                                @csrf
                                                <input form="productForm-{{ $key }}" type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input form="productForm-{{ $key }}" type="hidden" name="product_quantity" value="">
                                                <product-quantity>
                                                    <label for="elementQuantity-{{ $key }}">Укажите количество</label>
                                                    <div class="input-group">
                                                        <button class="minus input-group-text">
                                                            <i class="material-symbols-outlined">remove</i>
                                                        </button>
                                                        <input form="productForm-{{ $key }}" class="form-control" type="number"
                                                               id="elementQuantity-{{ $key }}"
                                                               name="quantity" value="{{ $quantity > 0 ? 1 : 0 }}"
                                                               data-max-quantity="{{ $quantity }}"
                                                               data-order-type="{{ $product->isCanBuy($quantity) ? 'checkout' : 'order' }}"
                                                        >
                                                        <button class="plus input-group-text">
                                                            <i class="material-symbols-outlined">add</i>
                                                        </button>
                                                    </div>
                                                </product-quantity>
                                                @if(!$product->isCanBuy($quantity))
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
                    <div id="productsLoader" class="w-100 text-center my-3 invisible">
                        <div class="spinner-border text-secondary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="getMoreProducts" class="btn btn-outline-dark invisible" data-url="/shop/filter?{{ http_build_query($request->input()) }}">More</div>
                @endif
            </div>
        </div>
    </div>
@endsection
