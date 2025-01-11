<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $table = 'membership';

    protected $fillable = [
        'name',
        'description',
        'duration',
        'price',
        'image_name',
    ];

    public function members()
    {
        return $this->belongsToMany(Member::class, 'member_membership', 'membership_id', 'member_id');
    }
}