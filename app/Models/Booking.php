<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'user_id',
        'property_id',
        'room_id',
        'check_in',
        'check_out',
        'room_price_per_day',
        'commission_amount',
        'total_amount',
        'status',
        'cancellation_reason'
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'room_price_per_day' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    protected $appends = ['duration_days', 'total_room_price'];

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
        return $this->morphMany(Payment::class, 'payable');
    }
        public function latestPayment()
    {
        return $this->morphOne(Payment::class, 'payable')->latestOfMany();
    }
       public function isPaid()
    {
        return $this->payments()
            ->where('status', 'COMPLETED')
            ->exists();
    }

    public function rating()
    {
        return $this->hasOne(PropertyRating::class);
    }

    public function foodOrders()
    {
        return $this->hasMany(FoodOrder::class);
    }

    public function laundryOrders()
    {
        return $this->hasMany(LaundryOrder::class);
    }

    // Attributes
    public function getDurationDaysAttribute()
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    public function getTotalRoomPriceAttribute()
    {
        return $this->room_price_per_day * $this->duration_days;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'CONFIRMED');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['CONFIRMED', 'CHECKED_IN']);
    }

    public function scopeForDateRange($query, $checkIn, $checkOut)
    {
        return $query->where(function ($q) use ($checkIn, $checkOut) {
            $q->whereBetween('check_in', [$checkIn, $checkOut])
              ->orWhereBetween('check_out', [$checkIn, $checkOut])
              ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                  $q2->where('check_in', '<', $checkIn)
                     ->where('check_out', '>', $checkOut);
              });
        });
    }

    // Methods
    public function canBeCancelled()
    {
        return in_array($this->status, ['PENDING', 'CONFIRMED']) 
            && $this->check_in->isFuture();
    }

    public function calculateCommission()
    {
        $commissionRate = CommissionConfig::where('service_type', $this->property->type)->first()->rate;
        return ($this->total_room_price * $commissionRate) / 100;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_reference = 'BK' . strtoupper(uniqid());
            $booking->commission_amount = $booking->calculateCommission();
        });
    }
    public function canBeReviewed($userId = null)
    {
        if (!$userId) {
            // If no user ID provided, check if user is authenticated
            if (!auth()->check()) {
                return false;
            }
            $userId = auth()->id();
        }
        
        // Can review if booking is completed (checked out) and not already reviewed
        $hasReview = $this->property->reviews()
            ->where('user_id', $userId)
            ->where('booking_id', $this->id)
            ->exists();
        
        return $this->status === 'CHECKED_OUT' && 
               !$hasReview &&
               Carbon::parse($this->check_out)->diffInDays(Carbon::now()) <= 30;
    }

}