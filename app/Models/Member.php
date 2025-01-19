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
            if (!empty($member->coupon_code)) {
                Coupon::validateAndUse($member->coupon_code);
            }
        });
    }

    public function useCoupon($couponCode)
    {
        // Find the coupon with the given code
        $coupon = Coupon::where('code', $couponCode)->first();
    
        // If the coupon is not found, return a descriptive message
        if (!$coupon) {
            return ['success' => false, 'message' => 'Coupon not found.'];
        }
    
        // Check if the coupon is expired
        if ($coupon->expiry->isPast()) {
            return ['success' => false, 'message' => 'Coupon has expired.'];
        }
    
        // Check if the coupon is already used
        if ($coupon->status === 'used') {
            return ['success' => false, 'message' => 'Coupon has already been used.'];
        }
    
        // Retrieve the user's membership
        $membership = $this->memberships()->first();
    
        // If no membership exists, return an error
        if (!$membership) {
            return ['success' => false, 'message' => 'No membership found for the user.'];
        }
    
        // Use a database transaction to ensure atomicity
        return \DB::transaction(function () use ($coupon) {
            // Mark the coupon as used
            $coupon->status = 'used';
            $coupon->save();
    
            return ['success' => true, 'message' => 'Coupon applied successfully.'];
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
