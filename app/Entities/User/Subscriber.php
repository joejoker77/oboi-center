<?php

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $table = 'subscribers';

    protected $fillable = ['subscriber'];
}
