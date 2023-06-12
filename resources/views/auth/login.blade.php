@extends('layouts.index')

@section('content')
    <div class="container mb-5">
        <div class="row">
            <div class="col d-flex flex-column justify-content-between mb-5">
                <div class="h1 text-nowrap">Войти на сайт</div>
                <form method="POST" action="{{ route('login') }}" class="d-flex flex-column justify-content-between h-100">
                    @csrf
                    <div class="form-floating mb-3">
                        <input id="emailLogin" type="text" placeholder="email" class="form-control @error('email', 'login') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                        <label for="emailLogin">E-Mail адрес или телефон</label>
                        @error('email', 'login')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input id="passwordLogin" type="password" placeholder="Пароль" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        <label for="passwordLogin">Пароль</label>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row mb-3 mt-auto">
                        <div class="col">
                            <div class="form-check p-0 pt-2">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember" class="form-check-label">Запомнить меня</label>
                            </div>
                        </div>
                        <div class="col text-end">
                            <a class="btn btn-link" href="{{ route('password.request') }}">Забыли пароль?</a>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-lg btn-blue-dark w-100">Войти</button>
                    </div>
                </form>
            </div>
            <div class="col mb-5">
                <div class="h1">Зарегистрироваться</div>
                @if(!request()->get('confirmPhone'))
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-floating mb-3">
                            <input id="name" type="text" placeholder="Имя" class="form-control @error('name', 'register') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                            <label for="name">Имя</label>
                            @error('name', 'register')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="emailRegistration" type="text" placeholder="text" class="form-control @error('email', 'register') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                            <label for="emailRegistration">E-Mail адрес или телефон</label>
                            @error('email', 'register')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="passwordRegistration" type="password" placeholder="Пароль" class="form-control @error('password', 'register') is-invalid @enderror" name="password" required>
                            <label for="passwordRegistration">Пароль</label>
                            @error('password', 'register')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="passwordConfirm" placeholder="Подтверждение пароля" type="password" class="form-control" name="password_confirmation" required>
                            <label for="passwordConfirm">Потдверждение пароля</label>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-lg btn-blue-dark w-100">Регистрация</button>
                        </div>
                    </form>
                @else
                    <form action="{{ route('register.verify-phone') }}" method="post">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="text" name="phone_verify_token" id="confirm-token" class="form-control{{ $errors->has('phone_verify_token') ? ' is-invalid' : '' }}" value="">
                            <label for="confirm-token">Введите код подтверждения</label>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-lg btn-blue-dark w-100">Подтвердить номер</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
