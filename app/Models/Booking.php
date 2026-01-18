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

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
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
}