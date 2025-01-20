<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

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

    /**
     * Define the many-to-many relationship with Membership model.
     */
    public function memberships()
    {
        return $this->belongsToMany(Membership::class, 'member_membership', 'member_id', 'membership_id');
    }

    public function expiryRecord()
    {
        return $this->hasOne(MemberExpiry::class, 'member_id');
    }

    public function coupon($couponCode)
    {
        return $this->hasOne(Coupon::class, 'member_id');
    }
    

    /**
     * Get the full name of the member (last_name, first_name, middle_initial).
     */
    public function getNameAttribute()
    {
        return "{$this->last_name}, {$this->first_name} {$this->middle_initial}";
    }

    /**
     * Calculate and return the remaining days until membership expiry.
     * Returns 'Expired' if the membership is expired.
     */
    public function getDaysRemainingAttribute()
    {
        $expiryRecord = $this->expiryRecord;
        
        $expiryDate = Carbon::parse($expiryRecord->expiry);
        
        $now = Carbon::now();
        
        if ($expiryDate->isFuture()) {
            return $now->diffInDays($expiryDate);
        }
        
        if ($expiryDate->isToday()) {
            return 0;
        }
        return 'Expired';
    }
    
    public static function boot()
    {
        parent::boot();
    
        // This event runs when the model is retrieved from the database
        static::retrieved(function ($member) {
            $member->updateStatusBasedOnExpiry();
            $member->deleteExpiredExpiryRecord();
        });
    
        static::creating(function ($member) {
            // Check if coupon_code is provided
            if (!empty($member->coupon_code)) {
                // Find the coupon by code
                $coupon = Coupon::where('code', $member->coupon_code)->first();
    
                // If coupon doesn't exist
                if (!$coupon) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'The coupon code does not exist.'
                    ]);
                }
    
                // If coupon has already been assigned to a member
                if ($coupon->member_id !== null) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'The coupon has already been used.'
                    ]);
                }
    
                // If coupon is expired
                if ($coupon->status == 'expired') {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'The coupon is expired.'
                    ]);
                }
    
                // If coupon is valid, assign the member_id to the coupon
                DB::transaction(function () use ($coupon, $member) {
                    $coupon->member_id = $member->id;
                    $coupon->status = 'used';  // Mark the coupon as used
                    $coupon->save();
                });
            }
        });
    
        static::saving(function ($member) {
            // Check if there is an active membership and calculate the expiry
            $membership = $member->memberships()->latest()->first();
    
            if ($membership) {
                $expiryDate = Carbon::now()->addDays($membership->duration)->toDateString();
    
                // Update the expiry record or create one
                $member->expiryRecord()->updateOrCreate(
                    ['member_id' => $member->id],
                    ['expiry' => $expiryDate]
                );
            }
        });
    
        static::created(function ($member) {
            // After a member is created, assign their ID to a coupon
            $coupon = Coupon::whereNull('member_id')->first(); // Find the first coupon with no member_id
    
            if ($coupon) {
                $coupon->member_id = $member->id; // Assign the member's ID to the coupon
                $coupon->save(); // Save the coupon with the updated member_id
            }
        });
    }
    

    /**
     * Update member status based on the expiry date.
     */
    public function updateStatusBasedOnExpiry()
    {
        // Check if the member has an expiry record
        $expiryRecord = $this->expiryRecord;
    
        // If the member has an expiry record, check if the expiry date has passed or is active
        if ($expiryRecord) {
            $expiryDate = Carbon::parse($expiryRecord->expiry);
            $now = Carbon::now();
    
            // If the expiry date is in the future or today, set status to 1 (active)
            if ($expiryDate->isFuture() || $expiryDate->isToday()) {
                $this->status = 1;  // Set status to active (1)
            } else {
                // If the expiry date is in the past, set status to 0 (inactive)
                $this->status = 0;  // Set status to inactive (0)
            }
        } else {
            // If no expiry record exists, set status to 0 (inactive)
            $this->status = 0;  // Set status to inactive (0)
        }
    
        // Save the updated status
        $this->save();
    }
    

    public function deleteExpiredExpiryRecord()
    {
        // Check if the member has an expiry record
        $expiryRecord = $this->expiryRecord;

        if ($expiryRecord) {
            // Parse the expiry date from the expiry record
            $expiryDate = Carbon::parse($expiryRecord->expiry);
            $now = Carbon::now();

            // If the expiry date is in the past, delete the expiry record and the related membership
            if ($expiryDate->isPast()) {
                // Delete the expiry record
                $expiryRecord->delete();

                // Delete the associated membership from the pivot table
                $this->memberships()->detach();
            }
        }
    }

}
