@php
    /** @var App\Http\Requests\Site\AjaxSearchResult $result */
    $products       = $result->products;
    $categories     = $result->categories;
    $posts          = $result->posts;
    $blogCategories = $result->blog_categories;
@endphp
<div class="search-result-container">
    <div class="container">
        <div class="row border-bottom-0">
            <aside class="col-12 col-lg-2">
                <div class="swiper mySwiperSearchContent">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="h5">Категории каталога</div>
                            <ul>
                                @if($categories)
                                    @php /** @var App\Entities\Shop\Category $category */ @endphp
                                    @foreach($categories as $category)
                                        @if($category->isPublished())
                                            <li>
                                                <a href="{{ route('catalog.index', product_path($category, null)) }}">
                                                    {{ $category->title ?? $category->name }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                @else
                                    <li class="text-danger">Не найдено</li>
                                @endif
                            </ul>
                            <div class="h5">Статьи</div>
                            <ul>
                                @if(!$blogCategories && !$posts)
                                    <li class="text-danger">Не найдено</li>
                                @else
                                    @php /** @var App\Entities\Blog\Category $blogCategory */ @endphp
                                    @foreach($blogCategories as $blogCategory)
                                        <li><a href="{{ route('blog.index', post_path($blogCategory, null)) }}">{{ $blogCategory->title ?? $blogCategory->name }}</a></li>
                                    @endforeach
                                    @php /** @var App\Entities\Blog\Post $post */ @endphp
                                    @foreach($posts as $post)
                                        <li><a href="{{ route('blog.index', post_path($post->category, $post)) }}">{{ $post->title }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="swiper-scrollbar"></div>
                </div>
            </aside>
                @if(!empty($products))
                <div class="find-products col-12 col-lg-10">
                    <div class="product-items">
                        @php /** @var App\Entities\Shop\Product $product */ @endphp
                        @foreach($products as $key => $product)
                            @if($key <= 15)
                                <div class="product-item">
                                    <a href="{{ route('catalog.index',['product_path' => product_path($product->category, $product)]) }}">
                                        <div class="product-media">
                                            @if(!$product->photos->isEmpty())
                                                <img src="{{ $product->photos()->first()->getPhoto('small') }}" alt="{{ $product->photos()->first()->alt_tag }}">
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
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="show-all">
                        <button type="button" class="d-block btn btn-blue-dark mx-auto mt-5">Смотреть весь результат</button>
                    </div>
                </div>
                @elseif(!empty($categories))
                <div class="find-products col-12 col-lg-10">
                    <div class="product-items">
                        @php $countAllProducts = 0 @endphp
                        @foreach($categories as $category)
                            @if(!$category->products->isEmpty())
                                @php $products = $category->products @endphp
                                @php /** @var App\Entities\Shop\Product $product */ @endphp
                                @php $countProducts = 0 @endphp
                                @foreach($products as $key => $product)
                                    @if($countAllProducts <= 15)
                                        @if($countProducts <= 7)
                                            @php $countProducts ++ ; $countAllProducts ++ @endphp
                                            <div class="product-item">
                                                <a href="{{ route('catalog.index',['product_path' => product_path($category, $product)]) }}">
                                                    <div class="product-media">
                                                        @if(!$product->photos->isEmpty())
                                                            <img src="{{ $product->photos()->first()->getPhoto('small') }}" alt="{{ $product->photos()->first()->alt_tag }}">
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
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                    <div class="show-all">
                        <button type="button" class="d-block btn btn-blue-dark mx-auto mt-5">Смотреть весь результат</button>
                    </div>
                </div>
                @else
                <div class="find-products col-12 col-lg-10">
                    <p>Мы не нашли ни одного продукта по вашему запросу. Но это не значит что у нас его нет в наличии. Рекомендуем вам обратиться к нашим консультантам по телефону:
                        <a href="tel:+74957205965">+7 (495) 720 59-65</a>. Вам оперативно ответят на все интересующие вас вопросы, по нашей продукции.
                    </p>
                </div>
                @endif

        </div>
    </div>
</div>
