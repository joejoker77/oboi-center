@extends('layouts.index')

@section('content')
    <div class="container">
        <div class="h1">Запрос на сброс пароля</div>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-floating mb-3">
                <input id="email" type="email" placeholder="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                <label for="email">E-Mail адрес</label>
                @if ($errors->has('email'))<div class="invalid-feedback">{{ $errors->first('email') }}</div>@endif
            </div>
            <button type="submit" class="btn btn-primary w-100">Отправить запрос</button>
        </form>
    </div>
@endsection
