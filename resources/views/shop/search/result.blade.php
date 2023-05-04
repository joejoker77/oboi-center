@extends('layouts.index')

@section('content')
    <div class="container" id="categoryPage">
        <div class="row products">
            <div class="col-md-3">
                <aside>
                    <h4>Подбор по параметрам</h4>
                    <x-filter :request="$request" :restAttributes="$restAttributes" :restCategories="$restCategories" :restTags="$restTags" position="left" />
                </aside>
            </div>
            <div class="col-md-9">
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
                                        @if(isset($material))
                                            <span class="product-materials">Материал:
                                            <span class="prop-value">{{ $material }}</span>
                                        </span>
                                        @endif
                                        @if(isset($dimensions) and !empty($dimensions['height']) and !empty($dimensions['width']))
                                            <span class="product-dimensions">Размер:
                                            <span class="prop-value">{{ $dimensions['height'][0] }} x {{ $dimensions['width'][0] }}</span>
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
