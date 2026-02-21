<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'laundry_order_id',
        'laundry_item_id',
        'quantity',
        'unit_price',
        'special_instructions'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    public function laundryOrder()
    {
        return $this->belongsTo(LaundryOrder::class);
    }

    public function laundryItem()
    {
        return $this->belongsTo(LaundryItem::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}