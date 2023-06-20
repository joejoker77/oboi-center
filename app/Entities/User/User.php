<?php

namespace App\Entities\User;

use App\Entities\Shop\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $status
 * @property string $verify_token
 * @property string|null email_verified_at
 *
 * @property UserProfile $userProfile
 * @property DeliveryAddress[] $addresses
 * @property Order[] $orders
 * @property UserWishlist[] $favorites
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public const STATUS_WAIT   = 'wait';
    public const STATUS_ACTIVE = 'active';
    public mixed $tmpPassword  = null;

    protected $fillable = [
        'name', 'email', 'password', 'status', 'verify_token', 'email_verified_at'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function register(string $name, mixed $email, string $password, $phone = null): self
    {
        if (!$email && !$phone) {
            throw new \DomainException('Вы должны заполнить поле Email или номер телефона');
        }

        $user = static::create([
            'name'         => $name,
            'email'        => $email ?? null,
            'password'     => bcrypt($password),
            'status'       => self::STATUS_WAIT,
            'verify_token' => $email ? \Str::uuid() : null,
        ]);

        $user->userProfile()->create([
            'last_name'                 => null,
            'phone'                     => $phone ?? null,
            'phone_auth'                => false,
            'phone_verified'            => false,
            'phone_verify_token'        => null,
            'phone_verify_token_expire' => null,
            'role'                      => UserProfile::ROLE_USER
        ]);
        return $user;
    }

    public function isVerifyEmail():bool
    {
        return (bool)$this->email_verified_at;
    }

    public function isWait():bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive():bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function verify():void
    {
        if (!$this->isWait() && $this->isVerifyEmail()) {
            throw new \DomainException('Данная учетная запись уже подтверждена');
        }

        $this->update([
            'status'            => self::STATUS_ACTIVE,
            'email_verified_at' => Carbon::now(),
            'verify_token'      => null,
        ]);
    }

    public function verifyByPhone():void
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
        $this->userProfile->update([
            'phone_verified' => 1
        ]);
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function addresses():HasMany
    {
        return $this->hasMany(DeliveryAddress::class, 'user_id', 'id');
    }

    public function orders():HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id')->with(['orderItems']);
    }

    public function favorites():HasMany
    {
        return $this->hasMany(UserWishlist::class, 'user_id', 'id');
    }
}
