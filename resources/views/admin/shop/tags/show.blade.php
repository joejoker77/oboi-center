<?php /** @var \App\Entities\Shop\Tag $tag */ ?>
@extends('layouts.admin')

@section('content')
    <div class="pt-4 d-flex">
        <div class="ms-auto btn-group" role="group" aria-label="control buttons">
            <a href="{{ route('admin.shop.tags.create') }}" class="btn btn-success d-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Создать">
                <span data-feather="plus-square"></span>
            </a>
            <a href="{{ route('admin.shop.tags.edit', $tag) }}" class="btn btn-primary d-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Редактировать">
                <span data-feather="edit"></span>
            </a>

            <form class="btn btn-danger" method="POST" action="{{ route('admin.shop.tags.destroy', $tag) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Удалить">
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
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr><th>ID:</th><td>{{ $tag->id }}</td></tr>
                    <tr><th>Имя:</th><td>{{ $tag->name }}</td></tr>
                    <tr><th>Псевдоним:</th><td>{{ $tag->slug }}</td></tr>
                    @if($tag->meta)
                        <tr><th>Мета тег Title:</th><td>{{ $tag->meta['title'] }}</td></tr>
                        <tr><th>Мета тег Description:</th><td>{{ $tag->meta['description'] }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
