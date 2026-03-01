<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodServiceConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_provider_id',
        'supports_subscription',
        'supports_pay_per_eat',
        'opening_time',
        'closing_time',
        'avg_preparation_minutes',
        'delivery_buffer_minutes',
        'subscription_discount_percent',
    ];

    protected $casts = [
        'supports_subscription' => 'boolean',
        'supports_pay_per_eat' => 'boolean',
    ];

    // Relationship
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }
        /**
     * Get the meal types for this food service
     */
    public function mealTypes()
    {
        return $this->belongsToMany(MealType::class, 'food_service_meal_types', 'service_provider_id', 'meal_type_id');
    }
}