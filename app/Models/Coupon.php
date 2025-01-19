<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Coupon extends Model
{
    protected $fillable = [
        'title',
        'code',
        'description',
        'discount',
        'status',
        'expiry',
    ];

    protected $casts = [
        'expiry' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($coupon) {
            if (empty($coupon->code)) {
                $coupon->code = Str::random(10);
            }
        });

        static::retrieved(function ($coupon) {
            if ($coupon->expiry && $coupon->expiry->isPast()) {
                $coupon->status = 'expired';
                $coupon->save();
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function validateAndUse($code)
    {
        $coupon = self::where('code', $code)->first();

        if (!$coupon) {
            throw new \Exception('Coupon not found.');
        }

        if ($coupon->status !== 'active') {
            throw new \Exception('Coupon is not active.');
        }

        $coupon->update(['status' => 'used']);
        return $coupon;
    }
}
