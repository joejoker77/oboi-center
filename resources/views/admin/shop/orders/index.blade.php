@php /** @var App\Entities\Shop\Order[] $orders */ @endphp
@extends('layouts.admin')

@section('content')
    <h1 class="py-4">Заказы</h1>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Пользователь</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>{{ $order->getStatus($order->current_status) }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
