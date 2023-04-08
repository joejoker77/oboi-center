<?php

namespace App\Http\Controllers\Auth;

use App\Entities\User\User;
use App\Entities\User\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class LoginController extends Controller
{
    use ThrottlesLogins;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm($confirmPhone = false): View
    {
        return view('auth.login', compact('confirmPhone'));
    }

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request)
    {
        /** @var User $user */
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse($request);
        }

        $login = $request->get('email');

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $request->validateWithBag('login', [
                'email'    => 'required|string|email|max:32',
                'password' => 'required|string|min:6',
            ]);
            $authenticate = Auth::attempt($request->only(['email', 'password']), $request->filled('remember'));
        } else {

            $request->validateWithBag('login', [
                'email'    => 'required|string|phone:RU|max:16',
                'password' => 'required|string|min:6',
            ]);
            $login = "+".preg_replace("/[^0-9]/", '', $login);

            $userProfile = UserProfile::where('phone', $login)->first();
            if (!$userProfile || !$userProfile->user) {
                return back()->with('error', 'Пользователь с таким номером телефона не найден');
            }

            $authenticate = Hash::check($request->get('password'), $userProfile->user->password) &&
                Auth::loginUsingId($userProfile->user->id, $request->filled('remember'));
        }

        if ($authenticate) {
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            $user = Auth::user();

            if (!filter_var($login, FILTER_VALIDATE_EMAIL) && !$user->userProfile->isPhoneVerified()) {
                $user->verifyByPhone();
            }

            if ($user->isWait() || (filter_var($login, FILTER_VALIDATE_EMAIL) && $user->email && !$user->isVerifyEmail())) {
                Auth::logout();
                return back()->with('error', 'Ваш email не подтвержден. На указанный вами адрес, мы выслали письмо со ссылкой на подтверждение почтового ящика. Пожалуйста, перейдите по этой ссылке.');
            }

            return redirect()->intended(route('home'));
        }

        $this->incrementLoginAttempts($request);

        if (!$user = User::where('email', $request->get('email'))) {
            throw ValidationException::withMessages(['email' => [trans('auth.failed')]]);
        } else {
            throw ValidationException::withMessages(['password' => [trans('passwords.invalid')]]);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        return redirect()->route('home');
    }

    protected function username(): string
    {
        return 'email';
    }
}
