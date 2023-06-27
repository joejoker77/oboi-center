@php /** @var App\Entities\User\User $user */ @endphp
@extends('layouts.admin')

@section('content')
    <h1 class="my-4">Редактирование пользователя "#{{ $user->id }}" ({{ $user->name }})</h1>
    <form method="POST" id="userForm" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <h6 class="mb-3">Сменить роль пользователя</h6>
            @error('role')<div class="is-invalid"></div>@enderror
            <select name="role" class="js-choices">
                <option value="">-=Роль пользователя=-</option>
                @foreach($user->userProfile::roleList() as $key => $role)
                    <option value="{{ $key }}" @if($user->userProfile->role === $key) selected @endif>
                        {{ $role }}
                    </option>
                @endforeach
            </select>
            @error('role')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="mb-3">
            <h6 class="mb-3">Сменить статус пользователя</h6>
            @error('status')<div class="is-invalid"></div>@enderror
            <select name="status" class="js-choices">
                <option value="">-=Статус пользователя=-</option>
                @foreach($user::statusesList() as $key => $status)
                    <option value="{{ $key }}" @if($user->status === $key) selected @endif>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
            @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div>
            <button type="submit" class="btn btn-success w-100">Сохранить</button>
        </div>
    </form>
@endsection
