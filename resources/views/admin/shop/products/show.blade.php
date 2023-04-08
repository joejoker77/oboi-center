@php /** @var \App\Entities\Shop\Product $product */ @endphp
@extends('layouts.admin')

@section('content')
    <div class="pt-4 d-flex">
        <div class="ms-auto btn-group" role="group" aria-label="control buttons">
            <a href="{{ route('admin.shop.products.create') }}" class="btn btn-success d-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Создать">
                <span data-feather="plus-square"></span>
            </a>
            <a href="{{ route('admin.shop.products.edit', $product) }}" class="btn btn-primary d-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Редактировать">
                <span data-feather="edit"></span>
            </a>

            <form class="btn btn-primary" method="POST"
                  action="{{ route('admin.shop.product.set-active', [$product]) }}"
                  data-bs-toggle="tooltip" data-bs-placement="bottom"
                  data-bs-title="@if(!$product->isActive()){{"Сделать активным"}}@else{{"Продутк активен"}}@endif"
            >
                @csrf
                <button type="submit" class="btn p-0 text-white d-flex" style="line-height: 0" @if($product->isActive()) disabled @endif>
                    <span data-feather="@if(!$product->isActive()){{"eye-off"}}@else{{"eye"}}@endif"></span>
                </button>
            </form>

            <form class="btn btn-danger" method="POST" action="{{ route('admin.shop.products.destroy', $product) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Удалить">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn p-0 text-white d-flex js-confirm" style="line-height: 0">
                    <span data-feather="trash-2"></span>
                </button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <h6>Основные</h6>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>ID</th>
                    <td>{{ $product->id }}</td>
                </tr>
                <tr>
                    <th>Статус</th>
                    <td><span class="badge {{ $product::statusLabel($product->status) }}">{{ $product::statusName($product->status) }}</span></td>
                </tr>
                @if($product->brand)
                    <tr>
                        <th>Бренд</th>
                        <td>{{ $product->brand->name }}</td>
                    </tr>
                @endif
                <tr>
                    <th>Артикул</th>
                    <td>{{ $product->sku }}</td>
                </tr>
                <tr>
                    <th>Наименование</th>
                    <td>{{ $product->name }}</td>
                </tr>
                <tr>
                    <th>Страна производства</th>
                    <td>{{ $product->country }}</td>
                </tr>
                <tr>
                    <th>Цена</th>
                    <td>{{ $product->price }}</td>
                </tr>
                @if($product->category)
                    <tr>
                        <th>Основная категория</th>
                        <td>{{ $product->category->title ?? $product->category->name }}</td>
                    </tr>
                @endif
                @if(!$product->categories->isEmpty())
                    <tr>
                        <th>Дополнительные категории</th>
                        <td>
                            @foreach($product->categories as $category)
                                {{ $category->title ?? $category->name }},
                            @endforeach
                        </td>
                    </tr>
                @endif
                @if(!$product->tags->isEmpty())
                    <tr>
                        <th>Теги</th>
                        <td>
                            @foreach($product->tags as $i => $tag)
                                {{ $tag->name }}@if(count($product->tags) !== $i+1),@endif
                            @endforeach
                        </td>
                    </tr>
                @endif
            </table>
            @if(!$product->photos->isEmpty())
                <h3>Изображения продукта</h3>
                <up-images>
                    <div class="images-container">
                        @php /** @var App\Entities\Shop\Photo $image */ @endphp
                        @foreach($product->photos as $image)
                            <div class="image-item">
                                <div class="wrapper-image" data-photo-id="{{ $image->id }}" data-photo-owner="product" data-product-id="{{ $product->id }}">
                                    <img src="{{ asset($image->getPhoto('small')) }}" alt="{{ $image['alt'] }}">
                                </div>
                                <div class="image-control btn-group">
                                    <form action="{{ route('admin.shop.products.photo.up', [$product,$image]) }}" method="POST" class="btn btn-secondary">
                                        @csrf
                                        <button type="submit" class="btn p-0 text-white d-flex">
                                            <span data-feather="arrow-left-circle"></span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.shop.products.photo.remove', [$product,$image]) }}" method="POST" class="btn btn-danger">
                                        @csrf
                                        <button type="submit" class="btn p-0 text-white d-flex js-confirm">
                                            <span data-feather="x-circle"></span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.shop.products.photo.down', [$product,$image]) }}" method="POST" class="btn btn-secondary">
                                        @csrf
                                        <button type="submit" class="btn p-0 text-white d-flex">
                                            <span data-feather="arrow-right-circle"></span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </up-images>
            @endif
        </div>
        <div class="col-md-4">
            <h6>Атрибуты продукта</h6>
            <table class="table table-striped table-bordered">
                @foreach($product->values as $value)
                    <tr>
                        <th>{{ $value->attribute->name }}</th>
                        <td>
                            @if(strpbrk($value->value, '|'))
                                @php
                                    $arrayValue = explode('|', $value->value);
                                    $variant    = $arrayValue[0];
                                    $valueStr   = $arrayValue[1];
                                @endphp
                                {{ $variant }} <span style="display: inline-block;background-color: {{ $valueStr }};width: 16px;height: 16px"></span>
                            @else
                                {{ $value->value }}
                            @endif
                        </td>
                    </tr>

                @endforeach
            </table>
        </div>
    </div>
@endsection
