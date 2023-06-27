@php /** @var App\Entities\Shop\Order $order */ @endphp
@extends('layouts.admin')

@section('content')
    <h1 class="my-4">Редактирование заказа №{{ $order->id }}</h1>
    <form action="{{ route('admin.shop.orders.update', $order) }}" method="post">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <h6 class="mb-3">Сменить статус заказа</h6>
            @error('current_status')<div class="is-invalid"></div>@enderror
            <select name="current_status" class="js-choices">
                <option value="">-=Статус заказа=-</option>
                @foreach($order::statusesList() as $key => $status)
                    <option value="{{ $key }}" @if($order->current_status === $key) selected @endif>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
            @error('current_status')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div>
            <button type="submit" class="btn btn-success w-100">Сохранить</button>
        </div>
    </form>
@endsection
