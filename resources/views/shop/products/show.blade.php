@php
/**
 * @var App\Cart\CartItem[] $cartAllItems
 * @var App\Entities\Shop\Product $product
 */
$quantity = $product->quantity;
@endphp
@extends('layouts.index')
@section('content')
    <div class="container" id="productPage">
        <div class="row mb-5">
        @if(!$product->photos->isEmpty())
            <div class="col-md-6">
                <main-gallery>
                    <div class="swiper full-swiper">
                        <div class="swiper-wrapper">
                            @php /** @var App\Entities\Shop\Photo $photo */ @endphp
                            @foreach($product->photos as $photo)
                                <div class="swiper-slide full">
                                    <img src="{{ $photo->getPhoto('large') }}" alt="{{ $photo->alt_tag }}">
                                </div>
                            @endforeach
                        </div>
                        @if($product->photos()->count() > 1)
                            <div class="swiper-button swiper-button-next"></div>
                            <div class="swiper-button swiper-button-prev"></div>
                        @endif
                        <div class="gallery-controls">
                            <div class="full-screen-button">
                                <span class="open material-symbols-outlined">fullscreen</span>
                                <span class="close material-symbols-outlined d-none">close_fullscreen</span>
                            </div>
                        </div>
                    </div>
                    @if($product->photos()->count() > 1)
                        <div class="swiper thumbs-swiper" thumbsSlider="">
                            <div class="swiper-wrapper">
                                @foreach($product->photos as $photo)
                                    <div class="swiper-slide thumb">
                                        <img src="{{ $photo->getPhoto('thumb') }}" alt="{{ $photo->alt_tag }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </main-gallery>
            </div>
        @endif
        <div class="col-md-6 product-offer">
            <h1 class="d-flex justify-content-between align-items-baseline">
                {{$product->name}}
                <span class="sku">Артикул: {{ $product->sku }}</span>
            </h1>

            <div class="props">
                <div class="country">
                    <div class="props-head">Страна производства:</div>
                    <div class="props-value">
                        <span
                            class="flag">{{ country_to_flag($product->country) }} <span>{{ $product->country }}</span></span>
                    </div>
                </div>
                <div class="price">
                    <div class="props-head">Цена:</div>
                    <div class="props-value">
                        @if($product->compare_at_price)
                            <span>@money($product->compare_at_price, 'RUB')</span>
                        @endif
                        @money($product->price, 'RUB')
                    </div>
                </div>
                @php $previewValues = $product->getPreviewValues() @endphp
                @if(!empty($previewValues))
                    <div class="base-values">
                        <div class="props-head text-start mb-2">Основные характеристики:</div>
                        <div class="props-value">
                            <ul>
                                @foreach($previewValues as $value)
                                    <li>
                                        <span class="value-name">{{ $value->attribute->name }}:</span>
                                        <span class="value-value">{{ $value->value }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
            @if(!empty($product->variants))
                <div class="variants">
                    @php /** @var App\Entities\Shop\Value[] $colors */ $colors = $product->getColors(); @endphp
                    @if(!$colors->isEmpty())
                        <div class="option">
                            <div class="option-head">Доступные цвета:</div>

                            @if($colors->count() > 10)
                            <div class="swiper scroll-content">
                                <div class="swiper-wrapper">
                            @endif

                            <div class="option-items @if($colors->count() > 10)swiper-slide @endif">
                                @foreach($colors as $color)
                                    @if($color->product->id != $product->id)
                                        <a class="option-item"
                                           href="{{ route('catalog.index',['product_path' => product_path($product->category, $color->product)]) }}"
                                           data-bs-toggle="tooltip" data-bs-placement="bottom"
                                           data-bs-custom-class="custom-tooltip" data-bs-title="{{ $color->value }}"
                                        >
                                    @else
                                        <div class="option-item active" data-bs-toggle="tooltip"
                                             data-bs-placement="bottom"
                                             data-bs-custom-class="custom-tooltip"
                                             data-bs-title="{{ $color->value }}">
                                    @endif
                                            <div class="option-image">
                                                @php $image = $color->product->photos()->first() @endphp
                                                <img src="{{ $image->getPhoto('small') }}" alt="{{ $image->alt_tag }}">
                                            </div>
                                            <div class="option-value">
                                                <span>{{ $color->value }}</span>
                                            </div>
                                    @if($color->product->id != $product->id)
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @if($colors->count() > 10)
                                    </div>
                                    <div class="swiper-scrollbar"></div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <product-form>
                    <form action="{{ route('cart.add', $product) }}" id="productForm" method="post">
                        @csrf
                        <input form="productForm" type="hidden" name="product_id" value="{{ $product->id }}">
                        <input form="productForm" type="hidden" name="product_quantity" value="">
                        <div class="form-controls">
                            <product-quantity>
                                <label for="elementQuantity">Укажите количество</label>
                                <div class="input-group">
                                    <button class="minus input-group-text">
                                        <i class="material-symbols-outlined">remove</i>
                                    </button>
                                    <input form="productForm" class="form-control" type="number" id="elementQuantity"
                                           name="quantity" value="{{ $quantity > 0 ? 1 : 0 }}"
                                           data-max-quantity="{{ $quantity }}"
                                           data-order-type="{{ $product->isCanBuy($quantity) ? 'checkout' : 'order' }}">
                                    <button class="plus input-group-text">
                                        <i class="material-symbols-outlined">add</i>
                                    </button>
                                </div>
                            </product-quantity>
                            <div class="buy-buttons">
                                @if(!$product->isCanBuy($quantity))
                                    <input type="hidden" name="type_order" value="order">
                                    <button type="submit" class="btn btn-blue-dark w-100">Создать заказ</button>
                                @else
                                    <input type="hidden" name="type_order" value="checkout">
                                    <button type="submit" class="btn btn-blue-dark w-100">В корзину</button>
                                @endif
                            </div>
                        </div>
                    </form>
                </product-form>
            @endif
            </div>
        </div>

    @php $dimensions = ['height'=> '', 'width' => ''];$materials  = ['up' => '', 'down' => ''];@endphp
    @foreach($product->values as $value)
        @if($value->attribute->name == 'Ширина рулона')
            @php $dimensions['height'] = $value->value @endphp
        @endif
        @if($value->attribute->name == 'Длина рулона')
            @php $dimensions['width'] = $value->value @endphp
        @endif
        @if ($value->attribute->name == "Материал покрытия")
            @php $materials['up'] = $value->value @endphp
        @endif
        @if ($value->attribute->name == "Материал основы")
            @php $materials['down'] = $value->value @endphp
        @endif
    @endforeach
    <div class="row">
        <div class="col-md-12">
            <div class="main-props">
                @if(!empty($dimensions['width']) && !empty($dimensions['height']))
                    <div class="props-item dimensions">
                        <span class="material-symbols-outlined">width_normal</span>
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
                        <div class="props-item material">
                            <span class="material-symbols-outlined">webhook</span>
                            <div class="prop-text">
                                <strong>Материал</strong> {{ $material }}
                            </div>
                        </div>
                    @endif
                @endif
                @foreach($product->values as $value)
                    @if($value->attribute->name == 'Тип обоев')
                        <div class="props-item type">
                            <span class="material-symbols-outlined">interests</span>
                            <div class="prop-text">
                                <strong>Тип обоев</strong> {{ $value->value }}
                            </div>
                        </div>
                    @elseif($value->attribute->name == 'Повтор рисунка')
                        <div class="props-item repeat">
                            <span class="material-symbols-outlined">compare</span>
                            <div class="prop-text">
                                <strong>Повтор</strong> {{ $value->value }}
                            </div>
                        </div>
                    @elseif($value->attribute->name == 'Фактура обоев')
                        <div class="props-item facture">
                            <span class="material-symbols-outlined">blur_on</span>
                            <div class="prop-text">
                                <strong>Фактура</strong> {{ $value->value }}
                            </div>
                        </div>
                    @endif
                @endforeach
                @php $files = $product->category->files()->doc() @endphp
                @if(!$files->isEmpty())
                    <div class="props-item documents">
                        <span class="material-symbols-outlined">picture_as_pdf</span>
                        <div class="prop-text">
                            <strong>{{ $product->category->name }}</strong>
                            @foreach($files as $document)
                                <a href="{{ $document->getFile() }}" target="_blank">Скачать PDF @if($files->count() > 1){{ $key+1 }}@endif</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
    @if(!$product->related->isEmpty())
        <div class="related-products">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="text-center py-3 mb-3">С этим товаром часто покупают</h2>
                        <div class="item-partners @if($product->related->count() < 3) without-swiper @endif">
                            @if($product->related->count() >= 3)
                                <div class="swiper swiperRelated">
                                    <div class="swiper-wrapper">
                                        @endif
                                        @php /** @var App\Entities\Shop\Product $related */ @endphp
                                        @foreach($product->related as $related)
                                            <div class="item-partner @if($product->related->count() >= 3)swiper-slide @endif" >
                                                <a class="stretched-link" href="{{ route('catalog.index',['product_path' => product_path($related->category, $related)]) }}">
                                                    <div class="related-media">
                                                        <img src="{{ $related->photos[0]->getPhoto('large') }}" alt="{{ $related->photos[0]->alt_tag }}">
                                                    </div>
                                                    <div class="related-props">
                                                        <div class="props-text">
                                                            <div class="h3">{{ $related->name }}</div>
                                                            <!-- TODO Переделать размеры и материал на связанные продукты -->
                                                            @if(!empty($dimensions['width']) && !empty($dimensions['height']))
                                                                <div class="props-item dimensions">
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
                                                                    <div class="props-item material">
                                                                        <div class="prop-text">
                                                                            <strong>Материал</strong> {{ $material }}
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <div class="props-price">
                                                            @money($related->price, 'RUB')
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                        @if($product->related->count() >= 3)
                                    </div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
