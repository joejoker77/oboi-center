<?php

namespace App\Http\Controllers\User;

use App\Entities\Shop\Order;
use App\Entities\Shop\Status;
use Davidnadejdin\LaravelAlfabank\Alfabank;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Entities\User\Subscriber;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Entities\User\DeliveryAddress;
use Illuminate\Support\Facades\Redirect;
use App\UseCases\Profile\ProfileService;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Profile\ProfileRequest;
use App\Http\Requests\Profile\DeliveryRequest;
use App\Http\Requests\Profile\PhoneVerifyRequest;
use Butschster\Head\Contracts\MetaTags\MetaInterface;

class ProfileController extends Controller
{
    public ProfileService $service;

    protected MetaInterface $meta;

    public function __construct(ProfileService $service, MetaInterface $meta)
    {
        $this->service = $service;
        $this->meta    = $meta;
    }

    public function showProfile(): View
    {
        $user = Auth::user();
        $this->meta->setRobots('nofollow, noindex');
        $subscriber = Subscriber::where(['subscriber' => $user->email])->first();

        return view('cabinet.profile.index', compact("user", 'subscriber'));
    }

    public function edit()
    {
        $user = Auth::user();

        return view('cabinet.profile.edit', compact('user'));
    }

    public function update(ProfileRequest $request): JsonResponse
    {
        try {
            $result = $this->service->edit(Auth::id(), $request);
        } catch (\DomainException|\Throwable $e) {
            $result = response()->json([
                'error' => $e->getMessage()
            ]);
        }
        return $result;
    }

    public function confirmPhone(PhoneVerifyRequest $request) {
        try {
            $result = $this->service->confirmPhone($request);
        } catch (\DomainException|\Throwable $e) {
            $result = response()->json([
                'error' => $e->getMessage()
            ]);
        }

        if (!$request->ajax()) {
            $arrayResponse = $result->getData(true);
            if (!empty($arrayResponse['error'])) {
                return redirect()->route('cabinet.profile.index')->with('error', $arrayResponse['error']);
            } elseif (!empty($arrayResponse['success'])) {
                return redirect()->route('cabinet.profile.index')->with('success', $arrayResponse['success']);
            }
        }
        return $result;
    }

    public function verifyPhone(Request $request)
    {
        try {

            $user   = Auth::user();
            $result = $this->service->requestCode($user->id);

            if (preg_match('/^\d{4}$/', $result)) {
                return view('cabinet.profile.phone-confirm', compact('user'));
            } else {
                return back()->with('error', 'Что то пошло не так');
            }
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getAddressForm(Request $request):View
    {
        $user    = Auth::user();
        $address = $request->get('address_id') ? DeliveryAddress::findOrFail($request->get('address_id')) : null;
        return view('cabinet.profile.add-address', compact('user', 'address'));
    }

    public function storeDeliveryAddress(DeliveryRequest $request)
    {
        try {
            $this->service->addAddress($request);
            return redirect()->route('cabinet.profile.index')->withFragment('#addresses-tab')
                ->with('success', 'Адрес успешно добавлен');
        } catch (\DomainException $e) {
            return back()->withFragment('#addresses-tab')->with('error', $e->getMessage());
        }
    }

    public function removeAddress(DeliveryAddress $address):RedirectResponse
    {
        $address->delete();
        return back()->withFragment('#addresses-tab')->with('success', 'Адрес успешно удален');
    }

    public function subscribe(Request $request):RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'subscriber' => 'required|email|unique:subscribers,subscriber'
        ]);

        if ($validator->fails()) {
            return Redirect::to(URL::previous().'#subscribeNews')->withErrors($validator);
        }

        try {
            Subscriber::create($request->all());
            return back()->with('success', 'Вы успешно подписались на нашу рассылку новостей');
        } catch (\Exception $exception) {
            return Redirect::to(URL::previous().'#subscribeNews')->with('error', $exception->getMessage());
        }
    }

    public function unSubscribe(Request $request): RedirectResponse
    {
        if (!$subscriber = Subscriber::where(['subscriber' => $request->get('subscriber')])->first()) {
            return back()->with('error', 'Пользователь с таким E-mail, не найден в подписке на новости');
        }

        try {
            $subscriber->delete();
            return back()->with('success', 'Вы успешно отписались от нашей рассылки');
        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
