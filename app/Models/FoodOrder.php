<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodOrder extends Model
{
    protected $fillable = [
        'order_reference',
        'user_id',
        'service_provider_id',
        'subscription_id',
        'booking_id',
        'order_type',
        'meal_date',
        'meal_type_id',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'distance_km',
        'delivery_instructions',
        'status',
        'estimated_delivery_time',
        'actual_delivery_time',
        'base_amount',
        'delivery_fee',
        'commission_amount',
        'total_amount',
        'accepted_at',
        'preparing_at',
        'out_for_delivery_at',
        'delivered_at',
        'cancelled_at'
    ];

    protected $casts = [
        'meal_date' => 'date',
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
        'accepted_at' => 'datetime',
        'preparing_at' => 'datetime',
        'out_for_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'base_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'distance_km' => 'decimal:2'
    ];

    /**
     * Get the user who placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service provider.
     */
    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    /**
     * Get the subscription (if any).
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(FoodSubscription::class);
    }

    /**
     * Get the booking (if any).
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the meal type.
     */
    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }

    /**
     * Get the order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(FoodOrderItem::class, 'food_order_id');
    }

    /**
     * Get the payment for this order.
     */
    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Scope a query to only include delivered orders.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'DELIVERED');
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'CANCELLED');
    }

    /**
     * Scope a query to only include orders for today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope a query to only include orders for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Get the provider's earnings for this order.
     */
    public function getProviderEarningsAttribute()
    {
        return $this->total_amount - $this->commission_amount;
    }

    /**
     * Get the commission percentage for this order.
     */
    public function getCommissionPercentageAttribute()
    {
        return $this->total_amount > 0 ? 
            ($this->commission_amount / $this->total_amount) * 100 : 0;
    }

    /**
     * Check if order is delayed.
     */
    public function getIsDelayedAttribute()
    {
        if (in_array($this->status, ['DELIVERED', 'CANCELLED'])) {
            return false;
        }

        return $this->estimated_delivery_time && 
               $this->estimated_delivery_time->lt(now());
    }

    /**
     * Get order status color.
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'PENDING' => 'bg-blue-100 text-blue-800',
            'ACCEPTED' => 'bg-yellow-100 text-yellow-800',
            'PREPARING' => 'bg-yellow-100 text-yellow-800',
            'OUT_FOR_DELIVERY' => 'bg-purple-100 text-purple-800',
            'DELIVERED' => 'bg-green-100 text-green-800',
            'CANCELLED' => 'bg-red-100 text-red-800'
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get order status icon.
     */
    public function getStatusIconAttribute()
    {
        $icons = [
            'PENDING' => 'fas fa-clock',
            'ACCEPTED' => 'fas fa-check',
            'PREPARING' => 'fas fa-utensils',
            'OUT_FOR_DELIVERY' => 'fas fa-shipping-fast',
            'DELIVERED' => 'fas fa-flag-checkered',
            'CANCELLED' => 'fas fa-times-circle'
        ];

        return $icons[$this->status] ?? 'fas fa-question-circle';
    }

    /**
     * Get formatted order amount.
     */
    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted provider earnings.
     */
    public function getFormattedEarningsAttribute()
    {
        return '₹' . number_format($this->provider_earnings, 2);
    }
}