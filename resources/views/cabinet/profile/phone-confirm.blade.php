@php /** @var App\Entities\User\User $user */ @endphp
@if(!empty($user->userProfile->phone_verify_token) && $user->userProfile->phone_verify_token_expire && $user->userProfile->phone_verify_token_expire->gt(\Carbon\Carbon::now()))
    <p>Мы сделали бесплатный звонок на ваш номер телефона. Введите в поле ниже 4 последних цифр номера, с которого поступит звонок</p>
    <form action="{{ route('cabinet.profile.confirm-phone') }}" method="post">
        @csrf
        <div class="form-floating mb-3">
            <input type="text" name="phone_verify_token" id="confirm-token" class="form-control{{ $errors->has('phone_verify_token') ? ' is-invalid' : '' }}" value="">
            <label for="confirm-token">Введите код подтверждения</label>
        </div>
        <div class="w-100">
            <button class="btn btn-blue-dark w-100" type="submit">Отправить код</button>
        </div>
    </form>
@endif
