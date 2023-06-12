@if(isset($result))
    @php
        /**
        * @var App\Http\Requests\Site\AjaxSearchResult $result
        * @var App\Entities\Shop\Product[] $products
        */
        $categories     = $result->categories;
        $posts          = $result->posts;
        $blogCategories = $result->blog_categories;
    @endphp

    @extends('layouts.index')

    @section('content')
        <div class="container pt-2 pt-md-3 pt-lg-5 search-page">
            <div class="row">
                <div class="col-12">
                    @if($products)
                        <div class="catalog-products mb-3 mb-lg-5">
                            <h5 class="mb-3 pb-3 border-bottom">Найденные товары ({{ $total }})</h5>
                            <div class="search-product-items">
                                @foreach($products as $product)
                                    @if($product->isActive())
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
                            {{ $products->appends(request()->input())->links() }}
                        </div>
                    @endif
                    @if($categories)
                        <div class="catalog-categories">
                            <h5 class="mb-3 pb-3 border-bottom">Найденные категории</h5>
                            @php /** @var App\Entities\Shop\Category $category */ @endphp
                            @foreach($categories as $category)
                                @if($category->isPublished())
                                    <div class="search-item mb-3 pb-3 border-bottom">
                                        <div class="search-desc d-flex">
                                            <div class="me-3 cat-image">
                                                @if(!$category->photos->isEmpty())
                                                    <img src="/{{ $category->getMainImage('small') }}" alt="{{ $category->photos[0]->alt_tag }}">
                                                @else
                                                    <span class="material-symbols-outlined">no_photography</span>
                                                @endif
                                            </div>
                                            <div class="text">
                                                <div class="h6">{{ $category->title ?? $category->name }}</div>
                                                {!! Str::limit($category->description, 330) !!}
                                            </div>
                                        </div>
                                        @if(!$products || $products->isEmpty())
                                            @php
                                                $catProducts   = $category->products;
                                                $countProducts = 0
                                            @endphp
                                            @if(!$catProducts->isEmpty())
                                                <div class="h6 mt-5 mb-2">Товары в категории</div>
                                                <div class="search-product-items">
                                                    @foreach($catProducts as $product)
                                                        @if($product->isActive())
                                                            @php $countProducts++ @endphp
                                                            @if($countProducts <= 10)
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
                                                </div>
                                            @endif
                                        @endif
                                        <div class="more text-end mt-3">
                                            <a class="btn btn-blue-dark" href="{{ route('catalog.index', product_path($category, null)) }}">Перейти в категорию</a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if($blogCategories || $posts)
                        <h5 class="mb-3 pb-3 border-bottom">Найденные статьи</h5>
                        <div class="catalog-categories">
                    @endif

                    @if($blogCategories)
                        @foreach($blogCategories as $blogCategory)
                            <div class="search-item mb-3 pb-3 border-bottom">
                                <div class="search-desc d-flex">
                                    <div class="me-3 cat-image">
                                        @if(!$blogCategory->photos->isEmpty())
                                            <img src="/{{ $blogCategory->getMainImage('small') }}" alt="{{ $blogCategory->photos[0]->alt_tag }}">
                                        @else
                                            <span class="material-symbols-outlined">no_photography</span>
                                        @endif
                                    </div>
                                    <div class="text">
                                        <div class="h6">{{ $blogCategory->title ?? $blogCategory->name }}</div>
                                        {!! Str::limit($blogCategory->description, 330) !!}
                                    </div>
                                </div>
                                <div class="more text-end mt-3">
                                    <a class="btn btn-blue-dark" href="{{ route('blog.index', post_path($blogCategory, null)) }}">Перейти в статью</a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    @if($posts)
                        @php /** @var App\Entities\Blog\Post $post */ @endphp
                        @foreach($posts as $post)
                            <div class="search-item mb-3 pb-3 border-bottom">
                                <div class="search-desc d-flex">
                                    <div class="me-3 cat-image">
                                        @if(!$post->photos->isEmpty())
                                            <img src="/{{ $post->photos[0]->getPhoto('small') }}" alt="{{ $post->photos[0]->alt_tag }}">
                                        @else
                                            <span class="material-symbols-outlined">no_photography</span>
                                        @endif
                                    </div>
                                    <div class="text">
                                        <div class="h6">{{ $post->title ?? $post->name }}</div>
                                        {!! Str::limit($post->content, 330) !!}
                                    </div>
                                </div>
                                <div class="more text-end mt-3">
                                    <a class="btn btn-blue-dark" href="{{ route('blog.index', post_path($post->category, $post)) }}">Перейти в статью</a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    @if($blogCategories || $posts)
                        </div>
                    @endif
                    @if(!$blogCategories && !$posts && !$products && !$categories)
                        <div class="not-found col-12">
                            <p>
                                <a href="tel:+74957205965">+7 (495) 720 59-65</a><br>
                                Если вы не нашли продукцию по вашему запросу, вы можете заказать ее по телефону.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endsection
@endif

