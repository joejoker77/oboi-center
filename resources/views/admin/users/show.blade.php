@php /** @var App\Entities\User\User $user */ @endphp
@extends('layouts.admin')

@section('content')
    <h1 class="py-4">Пользователь {{ $user->name }} #ID {{ $user->id }}</h1>

    <div class="row">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary d-flex me-2" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Редактировать">
                <span data-feather="edit"></span>
            </a>
            <div>
                <form class="btn btn-danger" method="POST" action="{{ route('admin.users.destroy', $user) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Удалить">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn p-0 text-white d-flex js-confirm" style="line-height: 0">
                        <span data-feather="trash-2"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col">
        <table class="table table-striped table-bordered">
            <tr>
                <th>ID</th>
                <td>{{ $user->id }}</td>
            </tr>
            <tr>
                <th>Имя</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Фамилия</th>
                <td>{{ $user->userProfile->last_name }}</td>
            </tr>
            <tr>
                <th>E-mail</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Зарегистрирован</th>
                <td>{{ $user->created_at }}</td>
            </tr>
            <tr>
                <th>Статус</th>
                <td>
                    <span class="badge {{ $user::getBadgeStatus($user->status)['class'] }}">
                        {{ $user::getBadgeStatus($user->status)['name'] }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Роль</th>
                <td>
                    <span class="badge {{ $user->userProfile::getCurrentRole($user->userProfile->role)['class'] }}">
                        {{ $user->userProfile::getCurrentRole($user->userProfile->role)['name'] }}
                    </span>
                </td>
            </tr>
        </table>
    </div>
@endsection
