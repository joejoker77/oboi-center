<variant-list class="variants-container d-flex flex-column gap-3 overflow-auto py-1">
@foreach($variants as $variant)
    <div class="row position-relative flex-nowrap variant" data-variant-id="{{ $variant->id }}">
        <div class="col-auto align-self-center">
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
        <div class="col-2 align-self-center">
            <div class="form-text">
                <h6>{{ $variant->name }}</h6>
            </div>
        </div>
        <div class="col-auto align-self-center">
            <div class="form-floating">
                <input type="text" id="variantSku" class="form-control @error('variant.sku') is-invalid @enderror"
                       name="variant.sku" value="{{ $variant->sku }}" placeholder="Артикул продутка">
                <label for="variantSku">Артикул продукта</label>
                @error('variant.sku')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="col-auto align-self-center">
            <div class="form-floating">
                <input type="text" id="variantPrice" class="form-control @error('variant.price') is-invalid @enderror"
                       name="variant.price" value="{{ $variant->price }}" placeholder="Цена продутка">
                <label for="variantPrice">Цена продукта</label>
                @error('variant.price')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="col-auto align-self-center">
            <div class="form-floating">
                <input type="text" id="variantWeight" class="form-control @error('variant.weight') is-invalid @enderror"
                       name="variant.weight" value="{{ $variant->weight }}" placeholder="Вес продутка">
                <label for="variantWeight">Вес продукта</label>
                @error('variant.weight')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="col-auto align-self-center">
            <div class="form-floating">
                <input type="text" id="variantQuantity"
                       class="form-control @error('variant.quantity') is-invalid @enderror"
                       name="variant.quantity" value="{{ $variant->quantity }}" placeholder="Количество на складе">
                <label for="variantQuantity">Количество на складе</label>
                @error('variant.quantity')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="col-auto align-self-center">
            <div class="btn-group js-variantManage" role="group" aria-label="Manage variant">
                <button type="button" class="btn btn-outline-warning">
                    <i data-feather="edit"></i>
                </button>
                <button type="button" class="btn btn-outline-danger">
                    <i data-feather="x-circle"></i>
                </button>
            </div>
        </div>
    </div>
@endforeach
</variant-list>
