<?php

namespace App\Entities\Shop;

use App\Entities\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $delivery_id
 * @property string $payment_method
 * @property integer $cost
 * @property string $note
 * @property integer $current_status
 * @property string $cancel_reason
 * @property array $statuses
 * @property string $customer_phone
 * @property string $customer_name
 * @property string $delivery_index
 * @property string $delivery_address
 * @property integer $payment_id
 * @property string $delivery_name
 * @property integer $delivery_cost
 *
 * @property OrderItem[] $orderItems
 * @property User $user
 * @property DeliveryMethod $deliveryMethod
 *
 */
class Order extends Model
{

    const PAYMENT_CASH = 'cash';

    const PAYMENT_CARD = 'online';

    protected $table = 'shop_orders';

    protected $fillable = [
        'user_id', 'delivery_id', 'payment_method', 'cost', 'note', 'current_status', 'cancel_reason', 'statuses', 'customer_phone', 'customer_name',
        'delivery_index', 'delivery_address', 'payment_id'
    ];

    protected $casts = [
        'statuses' => 'array'
    ];


    public static function create($userId, CustomerData $customerData, $cost, $note, $paymentMethod):self
    {
        $order                 = new static();
        $order->user_id        = $userId;
        $order->customer_name  = $customerData->name;
        $order->customer_phone = $customerData->phone;
        $order->cost           = $cost;
        $order->note           = $note;
        $order->payment_method = $paymentMethod;

        $order->addStatus(Status::NEW);

        return $order;
    }

    public static function paymentList():array
    {
        return [
            self::PAYMENT_CASH => 'Оплата наличными',
            self::PAYMENT_CARD => 'Оплата онлайн'
        ];
    }

    public function setDeliveryInfo(DeliveryMethod $method, DeliveryData $deliveryData): void
    {
        $this->delivery_id      = $method->id;
        $this->delivery_name    = $method->name;
        $this->delivery_cost    = $method->cost;
        $this->delivery_index   = $deliveryData->index;
        $this->delivery_address = $deliveryData->address;
    }

    public function pay($method):void
    {
        if ($this->isPaid()) {
            throw new \DomainException('Заказ уже оплачен.');
        }
        $this->payment_method = $method;
        $this->addStatus(Status::PAID);
    }

    public function send(): void
    {
        if($this->isSent()) {
            throw new \DomainException('Заказ уже отправлен.');
        }
        $this->addStatus(Status::SENT);
    }

    public function complete(): void
    {
        if ($this->isCompleted()) {
            throw new \DomainException('Заказ уже завершен.');
        }
        $this->addStatus(Status::COMPLETED);
    }

    public function cancel($reason):void
    {
        if ($this->isCanceled()) {
            throw new \DomainException('Заказ уже отменен.');
        }
        $this->cancel_reason = $reason;
        $this->addStatus(Status::CANCELLED);
    }

    public function getTotalCost():int
    {
        return $this->cost + $this->delivery_cost;
    }

    public function canBePaid():bool
    {
        return $this->isNew();
    }

    public function isNew():bool
    {
        return $this->current_status == Status::NEW;
    }

    public function isPaid():bool
    {
        return $this->current_status == Status::PAID;
    }

    public function isSent():bool
    {
        return $this->current_status == Status::SENT;
    }

    public function isCompleted():bool
    {
        return $this->current_status == Status::COMPLETED;
    }

    public function isCanceled():bool
    {
        return $this->current_status == Status::CANCELLED;
    }

    private function addStatus($value):void
    {
        $statuses       = $this->statuses;
        $statuses[]     = new Status($value, time());
        $this->statuses = $statuses;

//        if ($value == Status::NEW) {
//            // TODO Add Event new Order
//        }
        $this->current_status = $value;
    }

    public function user():HasOne
    {
        return $this->hasOne(User::class, 'id','user_id')->with('userProfile');
    }

    public function deliveryMethod():HasOne
    {
        return $this->hasOne(DeliveryMethod::class, 'id', 'delivery_id');
    }

    public function orderItems():HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id')->with(['products','products.photos']);
    }
}
