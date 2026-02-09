<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealType extends Model
{
    protected $fillable = ['name', 'display_order'];

    /**
     * Get food items for this meal type.
     */
    public function foodItems()
    {
        return $this->hasMany(FoodItem::class);
    }

    /**
     * Get food orders for this meal type.
     */
    public function foodOrders()
    {
        return $this->hasMany(FoodOrder::class);
    }
}