<?php

namespace App\Entities\User;

use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $user_id
 * @property string $last_name
 * @property string $phone
 * @property boolean $phone_auth
 * @property boolean $phone_verified
 * @property string $phone_verify_token
 * @property string $phone_verify_token_expire
 * @property string $role
 *
 * @property User $user
 *
 */
class UserProfile extends Model
{
    public const ROLE_USER = 'user';
    public const ROLE_MODERATOR = 'moderator';
    public const ROLE_ADMIN = 'admin';

    public $timestamps = false;

    protected $table = 'user_profiles';

    protected $fillable = [
        'last_name', 'phone', 'phone_auth', 'phone_verified', 'phone_verify_token', 'phone_verify_token_expire', 'role'
    ];

    protected $casts = [
        'phone_verified' => 'boolean',
        'phone_verify_token_expire' => 'datetime',
        'phone_auth' => 'boolean',
    ];

    public static function roleList(): array
    {
        return [
            self::ROLE_USER => 'User',
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_MODERATOR => 'Moderator'
        ];
    }

    public function edit(string $last_name, string $phone)
    {
        $this->update(['last_name' => $last_name, 'phone' => $phone]);
    }

    public function isModerator():bool
    {
        return $this->role === self::ROLE_MODERATOR;
    }

    public function isAdmin():bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPhoneVerified():bool
    {
        return $this->phone_verified;
    }

    public function isPhoneAuthEnabled():bool
    {
        return $this->phone_auth;
    }

    public function isFilledProfile(): bool
    {
        $user = Auth::user();
        return !empty($user->name) && !empty($this->last_name) && $this->isPhoneVerified();
    }

    public function changeRole($role):void
    {
        if (!array_key_exists($role, self::roleList())) {
            throw new \InvalidArgumentException('Неизвестная роль "' . $role . '"');
        }
        if ($this->role === $role) {
            throw new \DomainException('Данная роль уже назначена пользователю');
        }
        $this->update(['role' => $role]);
    }

    /**
     * @throws Throwable
     */
    public function unVerify():void
    {
        $this->phone_verified            = false;
        $this->phone_verify_token        = null;
        $this->phone_verify_token_expire = null;
        $this->phone_auth                = false;
        $this->saveOrFail();
    }

    /**
     * @throws Throwable
     */
    public function requestVerify(Carbon $now, $token):string
    {
        if (empty($this->phone)) {
            throw new \DomainException('Телефонный номер пуст.');
        }

        if (!empty($this->phone_verify_token) && $this->phone_verify_token_expire && $this->phone_verify_token_expire->gt($now)) {
            throw new \DomainException('Код уже запрошен.');
        }
        $salt                            = 'WRrnsZAg';
        $this->phone_verified            = false;
        $this->phone_verify_token        = $salt.md5($salt.$token);
        $this->phone_verify_token_expire = $now->copy()->addSeconds(300);
        $this->saveOrFail();

        return $this->phone_verify_token;
    }

    /**
     * @throws Throwable
     */
    public function verify(string $token, Carbon $now):void
    {
        if ($token !== $this->phone_verify_token) {
            throw new \DomainException('Неверный код.');
        }

        if ($this->phone_verify_token_expire->lt($now)) {
            throw new \DomainException('Код просрочен.');
        }

        $this->phone_verified            = true;
        $this->phone_verify_token        = null;
        $this->phone_verify_token_expire = null;

        $this->user->verifyByPhone();
        $this->saveOrFail();
    }

    /**
     * @throws Throwable
     */
    public function enablePhoneAuth():void
    {
        if (!empty($this->phone) && !$this->isPhoneVerified()) {
            throw new \DomainException('Телефонный номер пуст.');
        }
        $this->phone_auth = true;
        $this->saveOrFail();
    }

    /**
     * @throws Throwable
     */
    public function disablePhoneAuth():void
    {
        $this->phone_auth = false;
        $this->saveOrFail();
    }

    public function phoneIsVerified():bool
    {
        return $this->phone_verified;
    }

    public function tokenExpired():bool
    {
        return !$this->phoneIsVerified() && $this->phone_verify_token && $this->phone_verify_token_expire->lt(Carbon::now());
    }

    public function user():HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
