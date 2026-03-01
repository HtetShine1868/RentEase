<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    
    protected $fillable = [
        'booking_reference',
        'user_id',
        'property_id',
        'room_id',
        'check_in',
        'check_out',
        'duration_days',
        'room_price_per_day',
        'total_room_price',
        'commission_amount',
        'total_amount',
        'status',
        'cancellation_reason',
        'rejection_reason',
        'owner_notes',
        'approved_at',
        'rejected_at',
        'paid_at'
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const STATUS_PENDING = 'PENDING';           // Awaiting owner approval
    const STATUS_APPROVED = 'APPROVED';         // Owner approved, awaiting payment
    const STATUS_REJECTED = 'REJECTED';         // Owner rejected
    const STATUS_PAYMENT_PENDING = 'PAYMENT_PENDING'; // Approved but payment pending
    const STATUS_CONFIRMED = 'CONFIRMED';       // Payment received, booking confirmed
    const STATUS_CHECKED_IN = 'CHECKED_IN';     // Guest checked in
    const STATUS_CHECKED_OUT = 'CHECKED_OUT';   // Guest checked out
    const STATUS_CANCELLED = 'CANCELLED';       // Cancelled by user or system
    const STATUS_EXPIRED = 'EXPIRED';            // Payment not made within time limit

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'payable_id')->where('payable_type', 'BOOKING');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeOverlapping($query, $checkIn, $checkOut, $propertyId, $roomId = null)
    {
        return $query->where('property_id', $propertyId)
            ->where(function($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out', [$checkIn, $checkOut])
                  ->orWhere(function($q2) use ($checkIn, $checkOut) {
                      $q2->where('check_in', '<=', $checkIn)
                         ->where('check_out', '>=', $checkOut);
                  });
            })
            ->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN])
            ->when($roomId, function($q) use ($roomId) {
                $q->where('room_id', $roomId);
            });
    }

    // Check if property has any pending/approved bookings for the dates
    public static function hasConflictingRequest($propertyId, $checkIn, $checkOut, $excludeBookingId = null)
    {
        $query = self::where('property_id', $propertyId)
            ->where(function($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out', [$checkIn, $checkOut])
                  ->orWhere(function($q2) use ($checkIn, $checkOut) {
                      $q2->where('check_in', '<=', $checkIn)
                         ->where('check_out', '>=', $checkOut);
                  });
            })
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN]);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }
}