@extends('layouts.index')

@section('content')
    <div class="container mb-3 mb-xl-5">
        <div class="h1">Сброс пароля</div>
        <form method="POST" action="{{ route('password.request') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-floating mb-3">
                <input id="email" type="email" placeholder="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email }}" required>
                <label for="email">E-Mail адрес</label>
                @if ($errors->has('email'))<div class="invalid-feedback">{{ $errors->first('email') }}</div>@endif
            </div>
            <div class="form-floating mb-3">
                <input id="password" type="password" placeholder="Пароль" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                <label for="password">Пароль</label>
                @if ($errors->has('password'))<div class="invalid-feedback">{{ $errors->first('password') }}</div>@endif
            </div>

            <div class="form-floating mb-3">
                <input id="password-confirm" placeholder="Подтверждение пароля" type="password" class="form-control" name="password_confirmation" required>
                <label for="password-confirm">Потдверждение пароля</label>
            </div>
            <button type="submit" class="btn btn-blue-dark w-100">Сохранить новый пароль</button>
        </form>
    </div>
@endsection
