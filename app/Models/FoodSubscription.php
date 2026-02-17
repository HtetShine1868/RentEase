<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'service_provider_id',
        'meal_type_id',
        'start_date',
        'end_date',
        'delivery_time',
        'delivery_days',
        'status',
        'daily_price',
        'total_price',
        'discount_amount'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'delivery_time' => 'datetime',
        'delivery_days' => 'integer',
        'daily_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }
    
    public function mealType()
    {
        return $this->belongsTo(MealType::class);
    }
    
    public function orders()
    {
        return $this->hasMany(FoodOrder::class, 'subscription_id');
    }
    
    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}