<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    use HasFactory;

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
        'dietary_tags' => 'json',
        'calories' => 'integer'
    ];

    protected $appends = ['total_price'];

    // Relationships
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function mealType()
    {
        return $this->belongsTo(MealType::class);
    }

    public function orderItems()
    {
        return $this->hasMany(FoodOrderItem::class);
    }

    // Attributes
    public function getTotalPriceAttribute()
    {
        return $this->base_price + ($this->base_price * $this->commission_rate / 100);
    }

    // Methods
    public function isAvailable()
    {
        if (!$this->is_available) {
            return false;
        }

        if ($this->daily_quantity !== null) {
            return $this->sold_today < $this->daily_quantity;
        }

        return true;
    }

    public function incrementSoldToday($quantity = 1)
    {
        $this->increment('sold_today', $quantity);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            if ($item->daily_quantity !== null && $item->sold_today >= $item->daily_quantity) {
                $item->is_available = false;
            }
        });
    }
}