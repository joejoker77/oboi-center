<?php

namespace App\Http\Controllers\Auth;

use App\Entities\User\User;
use App\Http\Requests\Profile\PhoneVerifyRequest;
use App\UseCases\Profile\ProfileService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\UseCases\Auth\RegisterService;
use App\Http\Requests\Auth\RegisterRequest;
use Throwable;

class RegisterController extends Controller
{
    private RegisterService $service;
    private ProfileService $profileService;

    public function __construct(RegisterService $service, ProfileService $profileService)
    {
        $this->middleware('guest');
        $this->service        = $service;
        $this->profileService = $profileService;
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $confirmPhone = false;
        if (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            $request->validateWithBag('register', [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:32|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);
            $message = 'Для завершения регистрации, пройдите по ссылке, высланной вам в письме.';
        } else {
            $request->validateWithBag('register', [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|phone:RU|max:12|unique:user_profiles,phone',
                'password' => 'required|string|min:6|confirmed',
            ]);
            $confirmPhone = true;
            $message      = 'Для завершения регистрации, необходимо подтвердить номер телефона. Сейчас на указанный вами номер поступит звонок. Введите в поле подтверждения, последние 4 цифры данного номера.';
        }

        try {
            $this->service->register($request);
        } catch (Throwable|GuzzleException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('login', compact('confirmPhone'))
            ->with('success', $message);
    }

    public function verify($token): RedirectResponse
    {
        if (!$user = User::where('verify_token', $token)->first()) {
            return redirect()->route('login')->with('error', 'Извините, учетная запись не найдена.');
        }

        try {
            $this->service->verify($user->id);
            return redirect()->route('login')
                ->with('success', 'Ваш Email успешно подтвержден. Для входа на сайт, используйте свой пароль');
        } catch (\DomainException $e) {
            return redirect()->route('login')->with('error', $e->getMessage());
        }
    }


    public function verifyPhone(PhoneVerifyRequest $request)
    {
        $code = $request->get('phone_verify_token');
        if (preg_match('/^\d{4}$/', $code) !== 1) {
            return back()->with('error', 'Введенный код не соответствует формату 4-ех чисел');
        }
        try {
            $this->profileService->confirmPhone($request);
            return redirect()->route('login')
                ->with('success', 'Ваш телефонный номер успешно подтвержден. Можете выполнить вход на сайт, используя ваш пароль и в качестве логина, ваш номер телефона.');
        } catch (Throwable|GuzzleException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
