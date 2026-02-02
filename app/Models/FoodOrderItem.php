<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodOrderItem extends Model
{
    protected $fillable = [
        'food_order_id',
        'food_item_id',
        'quantity',
        'unit_price',
        'special_instructions'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2'
    ];

    /**
     * Get the food order.
     */
    public function foodOrder(): BelongsTo
    {
        return $this->belongsTo(FoodOrder::class);
    }

    /**
     * Get the food item.
     */
    public function foodItem(): BelongsTo
    {
        return $this->belongsTo(FoodItem::class);
    }

    /**
     * Get total price for this item.
     */
    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}