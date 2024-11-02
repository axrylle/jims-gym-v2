<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Members extends Model
{
    use HasFactory;

    protected $table = 'membership_user';

    protected $fillable = [
        'membership_id',
        'last_name',
        'first_name',
        'middle_initial',
        'contact_number',
        'email',
        'address',
        'status',
        'expiry',
    ];

    protected $dates = ['expiry'];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            $member->setExpiryDate();
        });

        static::updating(function ($member) {
            $member->setExpiryDate();
        });

        static::retrieved(function ($member) {
            $member->checkExpiry();
        });
    }

    public function setExpiryDate()
    {
        if ($this->membership) {
            $this->expiry = Carbon::now()->addDays($this->membership->duration);
        }
    }

    // Automatically update status if expiry date has passed
    public function checkExpiry()
    {
        if ($this->expiry && Carbon::now()->greaterThan($this->expiry)) {
            $this->status = false;
            $this->save();
        }
    }

    // Accessor for full name
    public function getNameAttribute()
    {
        return "{$this->last_name}, {$this->first_name} {$this->middle_initial}";
    }
}
