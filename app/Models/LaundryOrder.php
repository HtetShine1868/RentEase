<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LaundryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_reference',
        'user_id',
        'service_provider_id',
        'booking_id',
        'service_mode',
        'is_rush',
        'rush_surcharge_percent',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'distance_km',
        'pickup_time',
        'pickup_instructions',
        'expected_return_date',
        'actual_return_date',
        'status',
        'base_amount',
        'rush_surcharge',
        'pickup_fee',
        'commission_amount',
        'total_amount',
        'cancellation_reason'
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'is_rush' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'PENDING';
    const STATUS_PICKUP_SCHEDULED = 'PICKUP_SCHEDULED';
    const STATUS_PICKED_UP = 'PICKED_UP';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_READY = 'READY';
    const STATUS_OUT_FOR_DELIVERY = 'OUT_FOR_DELIVERY';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_CANCELLED = 'CANCELLED';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function items()
    {
        return $this->hasMany(LaundryOrderItem::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the ratings for this order
     */
    public function ratings()
    {
        return $this->hasMany(ServiceRating::class, 'order_id')
            ->where('order_type', 'LAUNDRY');
    }

    /**
     * Alias for ratings() - if you prefer the name serviceRatings
     */
    public function serviceRatings()
    {
        return $this->ratings();
    }

    /**
     * Check if order has been rated
     */
    public function getHasBeenRatedAttribute()
    {
        return $this->ratings()->exists();
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        if (in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_CANCELLED])) {
            return false;
        }
        
        if ($this->status === self::STATUS_PENDING || $this->status === self::STATUS_PICKUP_SCHEDULED) {
            return Carbon::parse($this->pickup_time)->isPast();
        }
        
        return Carbon::parse($this->expected_return_date)->isPast();
    }

    public function getUrgencyLevelAttribute()
    {
        if ($this->is_overdue) {
            return 'critical';
        }

        if ($this->is_rush) {
            $hoursLeft = $this->getHoursLeftAttribute();
            if ($hoursLeft <= 6) return 'extremely_urgent';
            if ($hoursLeft <= 24) return 'urgent';
            return 'on_track';
        }

        // Normal orders
        if (in_array($this->status, [self::STATUS_PENDING, self::STATUS_PICKUP_SCHEDULED])) {
            $pickupTime = Carbon::parse($this->pickup_time);
            if ($pickupTime->isToday()) return 'urgent';
        }

        if (in_array($this->status, [self::STATUS_IN_PROGRESS, self::STATUS_READY, self::STATUS_OUT_FOR_DELIVERY])) {
            $returnDate = Carbon::parse($this->expected_return_date);
            if ($returnDate->isToday()) return 'urgent';
        }

        return 'on_track';
    }

    public function getHoursLeftAttribute()
    {
        if ($this->status === self::STATUS_PENDING || $this->status === self::STATUS_PICKUP_SCHEDULED) {
            return Carbon::now()->diffInHours(Carbon::parse($this->pickup_time), false);
        }
        
        return Carbon::now()->diffInHours(Carbon::parse($this->expected_return_date)->endOfDay(), false);
    }

    public function getProgressPercentageAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => 0,
            self::STATUS_PICKUP_SCHEDULED => 10,
            self::STATUS_PICKED_UP => 25,
            self::STATUS_IN_PROGRESS => 50,
            self::STATUS_READY => 75,
            self::STATUS_OUT_FOR_DELIVERY => 90,
            self::STATUS_DELIVERED => 100
        ];

        return $statuses[$this->status] ?? 0;
    }
}