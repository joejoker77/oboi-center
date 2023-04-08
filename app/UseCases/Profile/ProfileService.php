<?php

namespace App\UseCases\Profile;

use Throwable;
use Carbon\Carbon;
use Illuminate\Mail\Mailer;
use App\Entities\User\User;
use Illuminate\Http\Request;
use App\Mail\Auth\VerifyMail;
use App\Services\Sms\SmsSender;
use Illuminate\Http\JsonResponse;
use App\Entities\User\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Entities\User\DeliveryAddress;
use http\Exception\InvalidArgumentException;
use App\Http\Requests\Profile\ProfileRequest;
use App\Http\Requests\Profile\PhoneVerifyRequest;

class ProfileService
{
    private SmsSender $sms;
    private Mailer $mailer;

    public function __construct(SmsSender $sms, Mailer $mailer)
    {
        $this->sms    = $sms;
        $this->mailer = $mailer;
    }

    /**
     * @throws Throwable
     */
    public function edit($id, ProfileRequest $request): JsonResponse
    {
        $user     = $this->getUser($id);
        $oldPhone = $user->userProfile->phone;

        if ($user->name !== $request->get('name')) {
            DB::transaction(function () use ($request, $user) {
                $user->userProfile->edit($request->get('last_name'), $request->get('phone'));
                $user->update($request->only(['name']));
            });
        } else {
            $user->userProfile->edit($request->get('last_name'), $request->get('phone'));
        }

        if ($user->email !== $request->get('email')) {
            $user->update([
                'email'        => $request->get('email'),
                'verify_token' => \Str::uuid(),
            ]);
            $this->mailer->to($user->email)->send(new VerifyMail($user));
        }

        if ($request->get('phone') !== $oldPhone) {
            $user->userProfile->unVerify();
            $response = $this->sms->send($user->userProfile->phone, 'Код подтверждения:');
            $result   = json_decode((string)$response->getBody(), true);
            if ($result['success']) {
                $user->userProfile->requestVerify(Carbon::now(), $result['result']['code']);
                return response()->json([
                    'success' => 'Запрошен код подтверждения.',
                    'action' => 'need-confirm'
                ]);
            } else {
                return response()->json([
                    'error' => $result['error'],
                    'action' => 'bad'
                ]);
            }
        }
        return response()->json([
            'success' => 'Данные профиля, успешно обновлены',
            'action' => 'modal-close'
        ]);
    }


    /**
     * @throws Throwable
     */
    public function requestCode($id) : string
    {
        $user = $this->getUser($id);

        $user->userProfile->unVerify();

        $response = $this->sms->send($user->userProfile->phone, 'Код подтверждения:');
        $result   = json_decode((string)$response->getBody(), true);

        if ($result['success']) {
            $user->userProfile->requestVerify(Carbon::now(), $result['result']['code']);

            return $result['result']['code'];
        }
        return 'error';
    }


    /**
     * @throws Throwable
     */
    public function confirmPhone(PhoneVerifyRequest $request): JsonResponse
    {
        $code = $request->get('phone_verify_token');

        if (!$code) {
            throw new InvalidArgumentException('Поле токен должно содержать 4 цифры');
        }
        $salt  = 'WRrnsZAg';
        $token = $salt.md5($salt.$code);

        /** @var User $user */
        $user = $this->getUserByPhoneToken($token);

        $user->userProfile->verify($token, Carbon::now());

        return response()->json([
            'success' => 'Номер телефона успешно подтвержден. Спасибо!',
            'action'  => 'confirm-done'
        ]);
    }

    private function getUser($id):User
    {
        return User::findOrFail($id);
    }

    private function getUserByPhoneToken($token): object|null
    {
        $userProfile = UserProfile::where('phone_verify_token', $token)->first();
        if (!$userProfile) {
            throw new \DomainException('Пользователь не найден');
        }
        return $userProfile->user;
    }

    public function addAddress(Request $request): void
    {
        /** @var User $user */
        $user = Auth::user();

        DeliveryAddress::create([
            'user_id'     => $user->id,
            'postal_code' => $request->get('postal_code'),
            'city'        => $request->get('city') ?? 'Москва',
            'street'      => $request->get('street'),
            'house'       => $request->get('house'),
            'house_part'  => $request->get('house_part') ?? null,
            'flat'        => $request->get('flat') ?? null
        ]);
    }
}
