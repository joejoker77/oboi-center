@php /** @var App\Entities\Shop\DeliveryMethod $delivery_method */ @endphp
@extends('layouts.admin')

@section('content')
    <form method="POST" id="productForm" action="{{ route('admin.shop.delivery-methods.update', $delivery_method) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="row mt-4">
            <h3 class="mb-4 pb-4 border-bottom">Редактирование метода доставки "{{ $delivery_method->name }}"</h3>
            <div class="col-md-6">
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Основные</h4>
                    <div class="form-floating mb-3">
                        <input id="name" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name', $delivery_method) }}" type="text" placeholder="Наименование метода" required>
                        <label for="name" class="form-label">Наименование метода</label>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-floating">
                        <input id="cost" class="form-control @error('cost') is-invalid @enderror"
                               name="cost" value="{{ old('cost', $delivery_method) }}" type="number" placeholder="Стоимость доставки" required>
                        <label for="cost" class="form-label">Стоимость доставки</label>
                        @error('cost')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Сортировка</h4>
                    <div class="form-floating mb-3">
                        <input id="sort" class="form-control @error('sort') is-invalid @enderror"
                               name="sort" value="{{ old('sort', $delivery_method) }}" type="number" placeholder="Сортировка" required>
                        <label for="sort" class="form-label">Сортировка</label>
                        @error('sort')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <h4>Параметры доставки</h4>
                    <div class="form-floating mb-3">
                        <input id="min-weight" class="form-control @error('min_weight') is-invalid @enderror"
                               name="min_weight" value="{{ old('min_weight', $delivery_method) }}" type="number" placeholder="Минимальный вес">
                        <label for="min-weight" class="form-label">Минимальный вес</label>
                        @error('min_weight')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input id="max-weight" class="form-control @error('max_weight') is-invalid @enderror"
                               name="max_weight" value="{{ old('max_weight', $delivery_method) }}" type="number" placeholder="Максимальный вес">
                        <label for="max-weight" class="form-label">Максимальный вес</label>
                        @error('max_weight')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <div class="form-floating mb-3">
                        <input id="min-amount" class="form-control @error('min_amount') is-invalid @enderror"
                               name="min_amount" value="{{ old('min_amount', $delivery_method) }}" type="number" placeholder="Минимальное количество">
                        <label for="min-amount" class="form-label">Минимальное количество</label>
                        @error('min_amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input id="max-amount" class="form-control @error('max_amount') is-invalid @enderror"
                               name="max_amount" value="{{ old('max_amount', $delivery_method) }}" type="number" placeholder="Максимальное количество">
                        <label for="max-amount" class="form-label">Максимальное количество</label>
                        @error('max_amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="p-3 mb-3 bg-light border rounded-3">
                    <div class="form-floating mb-3">
                        <input id="min-dimensions" class="form-control @error('min_dimensions') is-invalid @enderror"
                               name="min_dimensions" value="{{ old('min_dimensions', $delivery_method) }}" type="number" placeholder="Максимальный размер">
                        <label for="min-dimensions" class="form-label">Минимальный размер (м<sup>3</sup>)</label>
                        @error('min_dimensions')<span class="invalid-feedback">{{ $delivery_method }}</span>@enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input id="max-dimensions" class="form-control @error('max_dimensions') is-invalid @enderror"
                               name="max_dimensions" value="{{ old('max_dimensions', $delivery_method) }}" type="number" placeholder="Максимальный размер">
                        <label for="max-dimensions" class="form-label">Максимальный размер (м<sup>3</sup>)</label>
                        @error('max_dimensions')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="p-3 mb-3 bg-light border rounded-3">
            <button type="submit" class="btn btn-success w-100">Сохранить</button>
        </div>
    </form>
@endsection
