<?php

namespace App\UseCases\Shop;

use Throwable;
use App\Cart\Cart;
use App\Cart\CartItem;
use Illuminate\Support\Str;
use App\Entities\Shop\Order;
use App\Entities\Shop\OrderItem;
use App\Entities\User\UserProfile;
use Illuminate\Support\Facades\DB;
use App\Entities\Shop\CustomerData;
use App\Entities\Shop\DeliveryData;
use Illuminate\Support\Facades\Auth;
use App\Entities\Shop\DeliveryMethod;
use App\Entities\User\DeliveryAddress;
use App\UseCases\Auth\RegisterService;
use App\Http\Requests\Order\OrderRequest;
use GuzzleHttp\Exception\GuzzleException;
use App\Http\Requests\Auth\RegisterRequest;

class OrderService
{
    /**
     * @throws GuzzleException
     * @throws Throwable
     */
    public function checkout(OrderRequest $request, Cart $cart, RegisterService $registerService): Order
    {
        $products       = [];
        $user           = Auth::user();
        $deliveryMethod = $this->getDelivery($request->get('delivery_id'));

        if (!$user && !$request->get('customer_phone')) {
            throw new \DomainException('Пользователь не найден или не указан номер телефона');
        } elseif(!$user && $this->existsUser("+".preg_replace("/[^0-9]/", '',$request->get('customer_phone')))) {
            throw new \DomainException('Пользователь с таким номером уже существует. Пожалуйста, войдите в систему, прежде чем оформлять покупку. Для входа на сайт используйте свой номер в качестве логина и пароль, высланный вам по смс, либо придуманный вами, если вы регистрировались через форму регистрации. Если вы не помните свой пароль, то можете воспользоваться <a href="/password/reset">страницей сброса пароля</a>');
        }

        DB::beginTransaction();
        try {
            $temp_password = Str::random(8);
            if (!$user && $request) {
                $user = $registerService->register(new RegisterRequest([
                    'name'                  => $request->get('customer_name'),
                    'email'                 => $request->get('customer_phone'),
                    'password'              => $temp_password,
                    'password_confirmation' => $temp_password
                ]), $temp_password, $deliveryMethod->name != 'Самовывоз' ? new DeliveryAddress([
                    'postal_code' => $request->get('postal_code'),
                    'city'        => $request->get('city'),
                    'street'      => $request->get('street'),
                    'house'       => $request->get('house'),
                    'house_part'  => $request->get('house_part'),
                    'flat'        => $request->get('flat')
                ]) : null);
            }

            $order = Order::create(
                $user->id,
                new CustomerData($request->get('customer_phone'), $request->get('customer_name')),
                $cart->getCost()->getTotal(),
                $request->get('note') ?? null,
                $request->get('payment_method')
            );

            $addressArray   = [
                'г. '.$request->get('city'),
                'ул. '.$request->get('street'),
                'д. '.$request->get('house'),
                $request->get('house_part') ? 'корпус/литера'. $request->get('house_part') : null,
                $request->get('flat') ? 'кв. '. $request->get('flat') : null
            ];

            $order->setDeliveryInfo(
                $deliveryMethod,
                new DeliveryData(
                    $request->get('postal_code'),
                    implode(', ',array_filter($addressArray))
                )
            );
            $order->saveOrFail();

            $items = array_map(function (CartItem $item) use (&$products, $order) {
                $product    = $item->getProduct();
                $products[] = $product;

                $product->checkout($item->getQuantity());

                $orderItem = OrderItem::create(
                    $product,
                    $item->getPrice(),
                    $item->getQuantity()
                );
                $orderItem->order_id = $order->id;
                return $orderItem;
            }, $cart->getItems());

            $order->orderItems()->saveMany($items);

            DB::commit();

            foreach ($products as $product) {
                $product->save();
            }
            $cart->clear();

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException('Создание заказа завершилось с ошибкой. Подробности: ' . $e->getMessage());
        }
    }

    private function getDelivery($id):DeliveryMethod
    {
        return DeliveryMethod::findOrFail($id);
    }

    private function existsUser($phone): bool
    {
        return (bool)UserProfile::where('phone', $phone)->first();
    }
}
