<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodItem extends Model
{
    protected $fillable = [
        'service_provider_id',
        'name',
        'description',
        'meal_type_id',
        'base_price',
        'commission_rate',
        'is_available',
        'daily_quantity',
        'sold_today',
        'dietary_tags',
        'calories'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'is_available' => 'boolean',
        'daily_quantity' => 'integer',
        'sold_today' => 'integer',
        'calories' => 'integer',
        'dietary_tags' => 'array'
    ];

    /**
     * Get the service provider.
     */
    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    /**
     * Get the meal type.
     */
    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return '₹' . number_format($this->base_price, 2);
    }
}