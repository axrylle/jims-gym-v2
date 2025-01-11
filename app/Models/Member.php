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
            // Check if the expiry date has passed
            $expiryDate = Carbon::parse($expiryRecord->expiry);
            $now = Carbon::now();

            // If the expiry date is in the past, delete the expiry record
            if ($expiryDate->isPast()) {
                $expiryRecord->delete();
            }
        }
    }
}
