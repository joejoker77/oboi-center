<?php /** @var \App\Entities\Shop\Brand $brand */ ?>
@extends('layouts.admin')

@section('content')
    <div class="pt-4 d-flex">
        <div class="ms-auto btn-group" role="group" aria-label="control buttons">
            <a href="{{ route('admin.shop.brands.create') }}" class="btn btn-success d-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Создать">
                <span data-feather="plus-square"></span>
            </a>
            <a href="{{ route('admin.shop.brands.edit', $brand) }}" class="btn btn-primary d-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Редактировать">
                <span data-feather="edit"></span>
            </a>

            <form class="btn btn-danger" method="POST" action="{{ route('admin.shop.brands.destroy', $brand) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Удалить">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn p-0 text-white d-flex js-confirm" style="line-height: 0">
                    <span data-feather="trash-2"></span>
                </button>
            </form>
        </div>
    </div>
    <div class="p-3 mb-4 bg-light">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-striped table-bordered">
                    <tr><th>ID:</th><td>{{ $brand->id }}</td></tr>
                    <tr><th>Имя:</th><td>{{ $brand->name }}</td></tr>
                    <tr><th>Псевдоним:</th><td>{{ $brand->slug }}</td></tr>
                    <tr><th>Мета тег Title:</th><td>{{ $brand->meta['title'] }}</td></tr>
                    <tr><th>Мета тег Description:</th><td>{{ $brand->meta['description'] }}</td></tr>
                </table>
            </div>
            <div class="col-md-4">
                <div class="image-container">
                    @if(!empty($brand->logo))
                        <img src="{{ asset($brand->logo->getPhoto('large')) }}" alt="logo_{{ $brand->name }}">
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
