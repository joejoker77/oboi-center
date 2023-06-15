<?php

namespace App\Http\Controllers\Auth;



use App\Entities\User\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\Sms\SmsSender;
use Illuminate\Http\JsonResponse;
use App\Entities\User\UserProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{

    private SmsSender $smsSender;

    public function __construct(SmsSender $smsSender)
    {
        $this->middleware('guest');
        $this->smsSender = $smsSender;
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $confirmPhone = false;
        if (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            $this->validateEmail($request);
        } else {
            $confirmPhone = true;
        }

        if (!$confirmPhone) {
            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );

            return $response == Password::RESET_LINK_SENT
                ? $this->sendResetLinkResponse($request, $response)
                : $this->sendResetLinkFailedResponse($request, $response);
        } else {
            $this->validatePhone($request);
            $login = "+".preg_replace("/[^0-9]/", '', $request->get('email'));
            if (!$userProfile = UserProfile::where(['phone' => $login])->first()) {
                return back()->with('error', 'Пользователь с таким номером телефона не найден');
            } else {
                $password = Str::random(8);
                $userProfile->user->update(['status' => User::STATUS_WAIT, 'password' => bcrypt($password)]);
                $userProfile->update(['phone_verified' => false]);
                $this->smsSender->sendSms($userProfile->phone, 'Пароль для входа в личный кабинет: '. $password);
                return redirect()->route('login')->with('success', 'Ваш пароль успешно сброшен. Новый пароль выслан вам в смс сообщении.');
            }
        }
    }

    /**
     * Validate the email for the given request.
     *
     * @param Request $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }

    protected function validatePhone(Request $request)
    {
        $request->validate(['email' => 'required|string|phone:RU|max:16|unique:user_profiles,phone']);
    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email');
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param Request $request
     * @param  string  $response
     * @return RedirectResponse|JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $request->wantsJson()
            ? new JsonResponse(['message' => trans($response)], 200)
            : back()->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param Request $request
     * @param  string  $response
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }
}
