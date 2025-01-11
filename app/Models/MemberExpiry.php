<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberExpiry extends Model
{

    protected $table = 'member_expiry';

    protected $fillable = [
        'member_id',
        'expiry',
    ];

    public $timestamps = false;
}
