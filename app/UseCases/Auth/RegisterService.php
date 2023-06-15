<?php

namespace App\UseCases\Auth;

use Throwable;
use Carbon\Carbon;
use App\Entities\User\User;
use App\Mail\Auth\VerifyMail;
use App\Services\Sms\SmsSender;
use Illuminate\Contracts\Mail\Mailer;
use App\Entities\User\DeliveryAddress;
use App\Http\Requests\Auth\RegisterRequest;


class RegisterService
{
    private Mailer $mailer;
    private SmsSender $smsSender;

    public function __construct(Mailer $mailer, SmsSender $smsSender)
    {
        $this->mailer    = $mailer;
        $this->smsSender = $smsSender;
    }

    /**
     * @param RegisterRequest $request
     * @param string|null $fromOrder
     * @param DeliveryAddress|null $address
     *
     * @return User
     * @throws Throwable
     */
    public function register(RegisterRequest $request, string $fromOrder = null, DeliveryAddress $address = null):User
    {
        $registerByEmail = (bool)filter_var($request['email'], FILTER_VALIDATE_EMAIL);
        $login           = "+".preg_replace("/[^0-9]/", '', $request['email']);

        $user = $registerByEmail ?
            User::register($request['name'], $request['email'], $request['password']) :
            User::register($request['name'], null, $request['password'], $login);

        $user->tmpPassword = $fromOrder ?? null;

        if ($registerByEmail) {
            $this->mailer->to($user->email)->send(new VerifyMail($user));
        } elseif ($fromOrder) {
            $this->smsSender->sendSms($user->userProfile->phone, 'Пароль для входа в личный кабинет: '. $fromOrder);
            if ($address) {
                $user->addresses()->create([
                    'postal_code' => $address->postal_code,
                    'city'        => $address->city,
                    'street'      => $address->street,
                    'house'       => $address->house,
                    'house_part'  => $address->house_part,
                    'flat'        => $address->flat
                ]);
            }

        } else {
            $response = $this->smsSender->send($user->userProfile->phone);
            $result   = json_decode((string)$response->getBody(), true);
            if ($result['success']) {
                $user->userProfile->requestVerify(Carbon::now(), $result['result']['code']);
            }
        }
        return $user;
    }

    public function verify($id):void
    {
        $user = User::findOrFail($id);
        $user->verify();
    }
}
