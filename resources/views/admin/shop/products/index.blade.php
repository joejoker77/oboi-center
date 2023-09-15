@php /** @var App\Entities\Shop\Product $product */ @endphp
@php /** @var App\Entities\Shop\Category[] $categories */ @endphp
@extends('layouts.admin')

@section('content')
    <div class="py-4 d-flex">
        <a href="{{ route('admin.shop.products.create') }}" class="btn btn-success">Добавить продукт</a>
        <div class="ms-auto">
            <form class="p-0 m-0" method="POST" id="formActions"
                  action="{{ route('admin.shop.products.set-status') }}">
                @csrf
                <div class="btn-group" role="group" aria-label="control buttons">
                    <button type="submit" name="action" value="published" class="btn btn-lg btn-primary" style="line-height: 0"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="Опубликовать">
                        <span data-feather="eye"></span>
                    </button>
                    <button type="submit" name="action" value="hit" class="btn btn-lg btn-warning" style="line-height: 0"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="Отметить как хит продаж">
                        <span data-feather="zap"></span>
                    </button>
                    <button type="submit" name="action" value="new" class="btn btn-lg btn-info" style="line-height: 0"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="Отметить как новинку">
                        <span data-feather="sunrise"></span>
                    </button>
                </div> |
                <div class="btn-group" role="group" aria-label="control buttons">
                    <button type="submit" name="action" value="un-published" class="btn btn-lg btn-primary" style="line-height: 0"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="Снять с публикации">
                        <span data-feather="eye-off"></span>
                    </button>
                    <button type="submit" name="action" value="revoke-hit" class="btn btn-lg btn-warning" style="line-height: 0"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="Убрать из хитов продаж">
                        <span data-feather="zap-off"></span>
                    </button>
                    <button type="submit" name="action" value="revoke-new" class="btn btn-lg btn-info" style="line-height: 0"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="убрать из новинок">
                        <span data-feather="sunset"></span>
                    </button>
                </div> |
                <div class="btn-group" role="group" aria-label="control buttons">
                    <button type="submit" name="action" value="remove" class="btn btn-lg btn-danger js-confirm" data-confirm="multi" style="line-height: 0"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="Удалить продукты">
                        <span data-feather="trash-2"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-bordered table-striped" id="productTable">
        <thead>
        <tr>
            <th style="text-align: center">
                <input type="checkbox" class="form-check-input" name="select-all" style="cursor: pointer">
            </th>
            <th>
                <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'id' ? '-id' : 'id']) }}">
                    ID @if(request('sort') && request('sort') == 'id') <i data-feather="chevrons-up"></i> @endif
                    @if(request('sort') && request('sort') == '-id') <i data-feather="chevrons-down"></i> @endif
                </a>
            </th>
            <th>Изображение</th>
            <th>
                <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'name' ? '-name' : 'name']) }}">
                    Наименование @if(request('sort') && request('sort') == 'name') <i data-feather="chevrons-up"></i> @endif
                    @if(request('sort') && request('sort') == '-name') <i data-feather="chevrons-down"></i> @endif
                </a>
            </th>
            <th>
                <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'sku' ? '-sku' : 'sku']) }}">
                    Артикул @if(request('sort') && request('sort') == 'sku') <i data-feather="chevrons-up"></i> @endif
                    @if(request('sort') && request('sort') == '-sku') <i data-feather="chevrons-down"></i> @endif
                </a>
            </th>
            <th>Категория</th>
            <th>Фабрика</th>
            <th>
                <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'quantity' ? '-quantity' : 'quantity']) }}">
                    Остаток @if(request('sort') && request('sort') == 'quantity') <i data-feather="chevrons-up"></i> @endif
                    @if(request('sort') && request('sort') == '-quantity') <i data-feather="chevrons-down"></i> @endif
                </a>
            </th>
            <th>
                <a class="link-secondary" href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'price' ? '-price' : 'price']) }}">
                    Цена @if(request('sort') && request('sort') == 'price') <i data-feather="chevrons-up"></i> @endif
                    @if(request('sort') && request('sort') == '-price') <i data-feather="chevrons-down"></i> @endif
                </a>
            </th>
            <th>Статус</th>
            <th>Хит продаж</th>
            <th>Новинка</th>
            <th>Действия</th>
        </tr>
        <tr>
            <form action="?" name="search-products" method="GET" id="searchProducts"></form>
            <td>&nbsp;</td>
            <td style="max-width: 50px;text-align: center">
                <input form="searchProducts" type="text" name="id" class="form-control" aria-label="Искать по ID" value="{{ request('id') }}">
            </td>
            <td>&nbsp;</td>
            <td style="max-width: 175px">
                <input type="text" form="searchProducts" name="name" class="form-control" aria-label="Искать по имени" value="{{ request('name') }}">
            </td>
            <td style="max-width: 175px">
                <input type="text" form="searchProducts" name="sku" class="form-control" aria-label="Искать по артикулу" value="{{ request('sku') }}">
            </td>
            <td style="width: 250px">
                <select name="category" id="selectCategory" class="js-choices" form="searchProducts">
                    <option value="">-= Выбрать категорию =-</option>
                    @if($categories)
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                            @selected($category->id == request('category'))
                            >
                                {{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </td>
            <td style="width: 220px">
                <select name="brand" id="selectBrand" class="js-choices" form="searchProducts">
                    <option value="">-= Выбрать фабрику =-</option>
                    @if ($brands)
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}"
                            @selected($brand->id == request('brand'))
                            >
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </td>
            <td style="max-width: 50px">
                <input type="text" name="quantity" class="form-control" form="searchProducts" value="{{ request('quantity') }}">
            </td>
            <td style="max-width: 75px">
                <input type="text" name="price" class="form-control" form="searchProducts" value="{{ request('price') }}">
            </td>
            <td style="width: 200px">
                <select name="status" form="searchProducts" class="js-choices" id="selectStatus">
                    <option value="">-= Выбрать статус =-</option>
                    @foreach(App\Entities\Shop\Product::productStatuses() as $status => $label)
                        <option value="{{ $status }}"
                        @selected($status == request('status'))
                        >{{ $label }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <label class="plus-minus-checkbox">
                    <input type="checkbox" name="hit" form="searchProducts" value="true" @checked(request('hit'))>
                    <span class="value"></span>
                </label>
            </td>
            <td>
                <label class="plus-minus-checkbox">
                    <input type="checkbox" name="new" form="searchProducts" value="true" @checked(request('new'))>
                    <span class="value"></span>
                </label>
            </td>
            <td>&nbsp;</td>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
            <tr>
                <td style="text-align: center">
                    <input form="formActions" type="checkbox" value="{{ $product->id }}" class="form-check-input" name="selected[]" style="cursor: pointer">
                </td>
                <td style="text-align: center">{{ $product->id }}</td>
                <td style="width: 100px;text-align: center">
                    @if(!$product->photos->isEmpty())
                        <img src="{{ asset($product->photos()->first()->getPhoto('small')) }}" alt="{{ $product->photos()->first()->alt_tag }}" height="40">
                    @else
                        <i data-feather="camera-off"></i>
                    @endif
                </td>
                <td><a href="{{ route('admin.shop.products.show', $product->slug) }}">{{ $product->name }}</a></td>
                <td style="white-space: nowrap">{{ $product->sku }}</td>
                <td><a href="{{ route('admin.shop.categories.show', $product->category) }}">{{ $product->category->title ?? $product->category->name }}</a></td>
                <td>@if($product->brand) <a href="{{ route('admin.shop.brands.show', $product->brand) }}">{{ $product->brand->name }} @endif</a></td>
                <td>{{ $product->quantity }} {{ $product->unit }}</td>
                <td style="white-space: nowrap">{{ $product->price }} руб.</td>
                <td style="text-align: center;"><span class="badge {{ $product::statusLabel($product->status) }}">{{ $product::statusName($product->status) }}</span></td>
                <td style="text-align: center;">
                    @if($product->hit)
                        <span class="badge {{ $product::statusLabel($product::STATUS_HIT) }}">{{ $product::statusName($product::STATUS_HIT) }}</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    @if($product->new)
                        <span class="badge {{ $product::statusLabel($product::STATUS_NEW) }}">{{ $product::statusName($product::STATUS_NEW) }}</span>
                    @endif
                </td>
                <td style="white-space: nowrap">
                    <a href="{{ route('admin.shop.products.edit', $product) }}" class="list-inline-item mx-1"
                       id="editCategory" data-bs-toggle="tooltip"
                       data-bs-placement="bottom"
                       data-bs-title="Редактировать"
                    >
                        <span data-feather="edit"></span>
                    </a>|<form method="POST" action="{{ route('admin.shop.products.set-status') }}" class="list-inline-item mx-1">
                        @csrf
                        <input type="hidden" name="action"
                               value=@if($product->status == $product::STATUS_ACTIVE)"un-published"@elseif($product->status == $product::STATUS_DRAFT)"published"@endif">
                        <input type="hidden" name="selected[]" value="{{ $product->id }}">
                        <button class="btn p-0 align-baseline text-primary" type="submit">
                            @if($product->status == $product::STATUS_ACTIVE)
                                <span data-feather="eye-off"></span>
                            @else
                                <span data-feather="eye"></span>
                            @endif
                        </button>
                    </form>|<form method="POST" action="{{ route('admin.shop.products.set-status') }}" class="list-inline-item mx-1">
                        @csrf
                        <input type="hidden" name="action"
                               value=@if($product->hit)"revoke-hit"@elseif(!$product->hit)"hit"@endif">
                        <input type="hidden" name="selected[]" value="{{ $product->id }}">
                        <button class="btn p-0 align-baseline text-primary" type="submit">
                            @if($product->hit)
                                <span data-feather="zap-off"></span>
                            @else
                                <span data-feather="zap"></span>
                            @endif
                        </button>
                    </form>|<form method="POST" action="{{ route('admin.shop.products.set-status') }}" class="list-inline-item mx-1">
                        @csrf
                        <input type="hidden" name="action"
                               value=@if($product->new)"revoke-new"@elseif(!$product->new)"new"@endif">
                        <input type="hidden" name="selected[]" value="{{ $product->id }}">
                        <button class="btn p-0 align-baseline text-primary" type="submit">
                            @if($product->new)
                                <span data-feather="sunset"></span>
                            @else
                                <span data-feather="sunrise"></span>
                            @endif
                        </button>
                    </form>
                    <form method="POST" class="list-inline-item js-confirm ms-2"
                          action="{{ route('admin.shop.products.destroy', $product) }}"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          data-bs-title="Удалить продукт"
                    >
                        @csrf
                        @method('DELETE')
                        <button class="btn p-0 align-baseline js-confirm text-danger" type="submit"><span data-feather="trash-2"></span></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $products->appends(request()->input())->links() }}
@endsection
