<?php

namespace App\Http\Controllers\User;

use App\Entities\User\DeliveryAddress;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\DeliveryRequest;
use App\Http\Requests\Profile\PhoneVerifyRequest;
use App\Http\Requests\Profile\ProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UseCases\Profile\ProfileService;

class ProfileController extends Controller
{
    public ProfileService $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    public function showProfile(): View
    {
        $user = Auth::user();

        return view('cabinet.profile.index', compact("user"));
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
}
