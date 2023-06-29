<variant-list class="variants-container d-flex flex-column gap-3 overflow-auto py-1" data-product-id="{{ $product->id }}" data-type="{{ $type }}">
    <h4 class="mb-3 pb-3 border-bottom d-flex justify-content-between align-items-center">
        @if($type == 'variants')Варианты продукта @else Связанные продукты@endif
        <button class="js-addVariant btn btn-success fs-5" type="button">
            <i data-feather="plus-square"></i>
        </button>
    </h4>
@foreach($variants as $variant)
    <div class="d-flex justify-content-start position-relative flex-nowrap variant" data-variant-id="{{ $variant->id }}">
        <div class="col-auto align-self-center me-2">
            <div class="variant-image" data-variant-id="{{ $variant->id }}">
                <button type="button" class="btn btn-outline-primary js-variantImg" data-variant-id="{{ $variant->id }}">
                    @if(!$variant->photos->isEmpty())
                        @php $photo = $variant->photos[0] @endphp
                        <img src="{{ asset($photo->getPhoto('small')) }}" alt="{{ $photo->alt_tag }}">
                    @else
                        <i data-feather="camera-off"></i>
                    @endif
                </button>
            </div>
        </div>
        <div class="col-2 align-self-center me-2">
            <div class="form-text text-center">
                <h6>{{ $variant->name }}</h6>
            </div>
        </div>
        <div class="col-auto align-self-center me-2">
            <div class="form-floating">
                <input type="text" id="variantSku-{{ $variant->id }}" class="form-control @error('variant.sku') is-invalid @enderror"
                       name="variant.sku" value="{{ $variant->sku }}" placeholder="Артикул продутка" disabled>
                <label for="variantSku-{{ $variant->id }}">Артикул продукта</label>
                @error('variant.sku')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="col-auto align-self-center me-2">
            <div class="form-floating">
                <input type="text" id="variantPrice-{{ $variant->id }}" class="form-control @error('variant.price') is-invalid @enderror"
                       name="variant.price" value="{{ $variant->price }}" placeholder="Цена продутка" disabled>
                <label for="variantPrice-{{ $variant->id }}">Цена продукта</label>
                @error('variant.price')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="col-auto align-self-center me-2">
            <div class="form-floating">
                <input type="text" id="variantQuantity-{{ $variant->id }}"
                       class="form-control @error('variant.quantity') is-invalid @enderror"
                       name="variant.quantity" value="{{ $variant->quantity }}" placeholder="Количество на складе" disabled>
                <label for="variantQuantity-{{ $variant->id }}">Количество на складе</label>
                @error('variant.quantity')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="col-auto align-self-center ms-auto">
            <div class="btn-group js-variantManage" role="group" aria-label="Manage variant">
                <a href="{{ route('admin.shop.products.edit', $variant) }}" type="button" target="_blank" class="btn btn-outline-warning d-flex align-items-center">
                    <i data-feather="edit"></i>
                </a>
                <a href="{{ route('admin.shop.products.show', $variant) }}" type="button" target="_blank" class="btn btn-outline-primary d-flex align-items-center">
                    <i data-feather="eye"></i>
                </a>
                <button type="button" class="btn btn-outline-danger" js-deleteVariant="{{ $variant->id }}" data-current-product="{{ $product->id }}">
                    <i data-feather="x-circle"></i>
                </button>
            </div>
        </div>
    </div>
@endforeach
</variant-list>
