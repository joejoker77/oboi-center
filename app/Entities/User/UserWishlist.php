<?php

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property int $product_id
 */
class UserWishlist extends Model
{
    protected $table    = 'user_wishlist';

    public $timestamps  = false;

    protected $fillable = ['user_id', 'product_id'];


}
