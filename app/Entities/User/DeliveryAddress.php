<?php

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $postal_code
 * @property string $city
 * @property string $street
 * @property string $house
 * @property string $house_part
 * @property string $flat
 */
class DeliveryAddress extends Model
{
    protected $table   = 'delivery_addresses';

    public $timestamps = false;

    protected $fillable = [
        'postal_code', 'city', 'street', 'house', 'house_part', 'flat', 'user_id'
    ];

    public function user():HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
