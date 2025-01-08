<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Member extends Model
{
    use HasFactory;

    protected $table = 'membership_user';

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_initial',
        'contact_number',
        'email',
        'address',
        'status',
    ];

    public function membership()
    {
        return $this->belongsToMany(Membership::class, 'member_membership', 'member_id', 'membership_id')
            ->withPivot('expiry');
    }

    public function getNameAttribute()
    {
        return "{$this->last_name}, {$this->first_name} {$this->middle_initial}";
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->status) {
            return 'Expired';
        }

        if (!$this->expiry) {
            return null;
        }

        $now = Carbon::now();
        $expiry = Carbon::parse($this->expiry);
        
        return $now->diffInDays($expiry);
    }
}
