@php
 /**
 * @var App\Entities\Shop\Category $category
 * @var App\Cart\CartItem[] $cartItems
 */
    $country = null;
    if (!empty($products)) {
        foreach ($products as $product) {
            if ($product->country) {
                $country = $product->country;
                break;
            }
        }
    }
@endphp
@extends('layouts.index')
@section('content')
    <div class="container" id="categoryPage">
        @if($category)
            <div class="row">
                @if(!$category->photos->isEmpty())
                    @if ($category->photos->count() > 1)
                        <div class="col-md-5">
                            <main-gallery>
                                <div class="swiper full-swiper">
                                    <div class="swiper-wrapper">
                                        @php /** @var App\Entities\Shop\Photo $photo */ @endphp
                                        @foreach($category->photos as $photo)
                                            <div class="swiper-slide full">
                                                <img src="{{ $photo->getPhoto('large') }}" alt="{{ $photo->alt_tag }}">
                                            </div>
                                        @endforeach
                                        @if (!$category->files->isEmpty())
                                            @php /** @var App\Entities\Shop\File $file */ @endphp
                                            @foreach($category->files as $file)
                                                @if($file->type == App\Entities\Shop\File::TYPE_VIDEO)
                                                    <div class="swiper-slide full">
                                                        <div class="video-js">
                                                            <video src="{{ $file->getFile() }}"></video>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="swiper-button swiper-button-next"></div>
                                    <div class="swiper-button swiper-button-prev"></div>
                                    <div class="gallery-controls">
                                        <div class="full-screen-button">
                                            <span class="open material-symbols-outlined">fullscreen</span>
                                            <span class="close material-symbols-outlined d-none">close_fullscreen</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper thumbs-swiper" thumbsSlider="">
                                    <div class="swiper-wrapper">
                                        @foreach($category->photos as $photo)
                                            <div class="swiper-slide thumb">
                                                <img src="{{ $photo->getPhoto('thumb') }}" alt="{{ $photo->alt_tag }}">
                                            </div>
                                        @endforeach
                                        @if (!$category->files()->video()->isEmpty())
                                            @foreach($category->files()->video() as $file)
                                                <div class="swiper-slide thumb">
                                                    <img src="{{ $file->getThumb() }}">
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </main-gallery>
                        </div>
                    @endif
                @endif
                <div class=@if($category->photos->isEmpty() || $category->photos->count() == 1)"col-md-12"@else"col-md-7"@endif>
                <div class="d-flex flex-column justify-content-between h-100">
                    <div class="category-content">
                        <h1>{{ $category->title ?? $category->name }}
                            @if(!$category->products->isEmpty())
                                <span>{{ $category->products()->first()->brand->name }} @if($country)<span>{{ country_to_flag($country) }} {{ $country }}</span>@endif</span>
                            @endif
                        </h1>
                        @if($category->photos->count() == 1)
                            <div class="category-single-image">
                                <img src="{{ $category->photos()->first()->getPhoto('small') }}" alt="{{ $category->photos()->first()->alt_tag }}">
                            </div>
                        @endif
                        @if($category->description)
                            <div class="swiper scroll-content">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide h-auto">
                                        {!! $category->description !!}
                                    </div>
                                </div>
                                <div class="swiper-scrollbar"></div>
                            </div>
                        @endif
                    </div>
                    @if($products)
                        @php
                        /** @var $product App\Entities\Shop\Product */
                        $dimensions = ['height'=> [], 'width' => []];
                        $materials  = ['up' => [], 'down' => []];
                        @endphp
                        @foreach($products as $product)
                            @foreach($product->values as $value)
                                @if($value->attribute->name == 'Ширина рулона' && !in_array($value->value, $dimensions['height']))
                                    @php $dimensions['height'][] = $value->value @endphp
                                @endif
                                @if($value->attribute->name == 'Длина рулона' && !in_array($value->value, $dimensions['width']))
                                    @php $dimensions['width'][] = $value->value @endphp
                                @endif
                                @if ($value->attribute->name == "Материал покрытия" && !in_array($value->value, $materials['up']))
                                    @php $materials['up'][] = $value->value @endphp
                                @endif
                                @if ($value->attribute->name == "Материал основы" && !in_array($value->value, $materials['down']))
                                    @php $materials['down'][] = $value->value @endphp
                                @endif
                            @endforeach
                        @endforeach
                        @if($category->children->isEmpty())
                            <div class="category-props mt-auto">
                                @if(!empty($dimensions['width']) && !empty($dimensions['height']))
                                    <div class="dimensions">
                                        <span class="material-symbols-outlined">width_normal</span>
                                        <div class="prop-text">
                                            <strong>Размер рулона</strong> {{ $dimensions['height'][0] }} x {{ $dimensions['width'][0] }}
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($materials['up']) && !empty($materials['down']) )
                                    @if ($materials['up'][0] === $materials['down'][0])
                                        @php $material = $materials['up'][0] @endphp
                                    @elseif ($materials['up'][0] == 'Винил' && $materials['down'][0] == 'Флизелин')
                                        @php $material = 'Винил на флизелине' @endphp
                                    @endif
                                    @if(isset($material))
                                        <div class="material">
                                            <span class="material-symbols-outlined">webhook</span>
                                            <div class="prop-text">
                                                <strong>Материал</strong> {{ $material }}
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                @php $files = $category->files()->doc(); @endphp
                                @if(!$files->isEmpty())
                                    <div class="documents">
                                        <span class="material-symbols-outlined">picture_as_pdf</span>
                                        <div class="prop-text">
                                            <strong>Презентация</strong>
                                            @foreach($files as $key => $document)
                                                <a href="{{ $document->getFile() }}" target="_blank">Скачать PDF @if($files->count() > 1){{ $key+1 }}@endif</a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif
    <div class="row products">
        <div class="col-md-3">
            <aside>
                <h4>Подбор по параметрам</h4>
            </aside>
        </div>
        <div class="col-md-9">
            @if ($categories)
                <div class="categories">
                    <h2>@if($category->depth == 1)Коллекции производителя {{ $category->name }} <span class="country">({{ $country }})</span>@elseФабрики@endif</h2>
                    <div class="category-items">
                    @foreach (array_chunk($categories, 4) as $chunk)
                        @php /** @var App\Entities\Shop\Category $current */ @endphp
                        @foreach ($chunk as $current)
                            @if($current->published)
                                <div class="category-item">
                                    <a class="text-decoration-none" href="{{ route('catalog.index',array_merge(['product_path' => product_path($current, null)], request()->all())) }}">
                                        <div class="cat-media">
                                            <span class="position-absolute badge rounded-pill bg-primary">{{ $categoriesCounts[$current->id] ?? 0 }}</span>
                                            @if($catPhoto = $current->photos()->first())
                                                <img src="{{ $catPhoto->getPhoto('small') }}" alt="{{ $catPhoto->alt_tag }}">
                                            @else
                                                <span class="material-symbols-outlined">no_photography</span>
                                            @endif
                                        </div>
                                    </a>
                                    <a class="stretched-link" href="{{ route('catalog.index',array_merge(['product_path' => product_path($current, null)], request()->all())) }}">
                                        <strong>{{ $current->name }}</strong>
                                        <span class="count-products">Доступно {!! ru_plural($categoriesCounts[$current->id] ?? 0, ['позиция', 'позиции', 'позиций']) !!}</span>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                    </div>
                </div>
            @endif
            @if($products && $category->children->isEmpty())
                <h3>Образцы коллекции - {{ $products[0]->category->name }}</h3>
                <div class="product-items">
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
                <div id="getMoreProducts" class="btn btn-outline-dark invisible">More</div>
            @endif
            </div>
        </div>
    </div>
@endsection
