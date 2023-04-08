<?php
/** @var App\Entities\User\User $user */
?>
@if(!empty($user->userProfile->phone_verify_token) && $user->userProfile->phone_verify_token_expire && $user->userProfile->phone_verify_token_expire->gt(\Carbon\Carbon::now()))
    <p>Мы сделали бесплатный звонок на ваш номер телефона. Введите в поле ниже 4 последних цифр номера, с которого поступит звонок</p>
    <form action="{{ route('cabinet.profile.confirm-phone') }}">
        <div class="form-floating mb-3">
            <input type="text" name="phone_verify_token" id="confirm-token" class="form-control{{ $errors->has('phone_verify_token') ? ' is-invalid' : '' }}" value="">
            <label for="confirm-token">Введите код подтверждения</label>
        </div>
    </form>
@else
    <form method="post" action="{{ route('cabinet.profile.update') }}">
        @csrf
        <div class="form-floating mb-3">
            <input id="name" type="text" placeholder="Имя" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ $user->name }}" required>
            <label for="name">Имя</label>
            @if ($errors->has('name'))<div class="invalid-feedback">{{ $errors->first('name') }}</div>@endif
        </div>

        <div class="form-floating mb-3">
            <input id="last-name" type="text" placeholder="Фамилия" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ $user->userProfile->last_name }}">
            <label for="last-name">Фамилия</label>
            @if ($errors->has('last_name'))<div class="invalid-feedback">{{ $errors->first('last_name') }}</div>@endif
        </div>

        <div class="form-floating mb-3">
            <input id="email" type="email" placeholder="Email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $user->email }}">
            <label for="email">Email</label>
            @if ($errors->has('email'))<div class="invalid-feedback">{{ $errors->first('email') }}</div>@endif
        </div>

        <div class="form-floating mb-3">
            <input id="phone" type="tel" placeholder="Номер телефона" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ $user->userProfile->phone }}">
            <label for="phone">Номер телефона</label>
            @if ($errors->has('phone'))<div class="invalid-feedback">{{ $errors->first('phone') }}</div>@endif
        </div>
    </form>
@endif
