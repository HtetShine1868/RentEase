<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryItem extends Model
{
    use HasFactory;

    protected $table = 'laundry_items';
    
    protected $fillable = [
        'service_provider_id',
        'item_name',
        'item_type',
        'description',
        'base_price',
        'rush_surcharge_percent',
        'commission_rate',
        'is_active'
    ];
    
    // DO NOT include 'total_price' in fillable - it's generated

    protected $casts = [
        'base_price' => 'decimal:2',
        'rush_surcharge_percent' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            // Get commission rate from config if not set
            if (!$item->commission_rate) {
                $config = \App\Models\CommissionConfig::where('service_type', 'LAUNDRY')->first();
                $item->commission_rate = $config ? $config->rate : 10.00;
            }
            
            // DO NOT set total_price - it's a generated column
            // MySQL will calculate it automatically
        });
    }

    /**
     * Get the service provider that owns the item
     */
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    /**
     * Get the order items for this laundry item
     */
    public function orderItems()
    {
        return $this->hasMany(LaundryOrderItem::class, 'laundry_item_id');
    }

    /**
     * Get the orders through order items
     */
    public function orders()
    {
        return $this->belongsToMany(LaundryOrder::class, 'laundry_order_items', 'laundry_item_id', 'laundry_order_id')
                    ->withPivot('quantity', 'unit_price', 'total_price', 'special_instructions')
                    ->withTimestamps();
    }

    /**
     * Scope active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('item_type', $type);
    }

    /**
     * Accessor for formatted total price (read-only)
     */
    public function getFormattedTotalPriceAttribute()
    {
        return '৳' . number_format($this->total_price, 2);
    }

    /**
     * Accessor for formatted base price
     */
    public function getFormattedBasePriceAttribute()
    {
        return '৳' . number_format($this->base_price, 2);
    }

    /**
     * Get item type label
     */
    public function getItemTypeLabelAttribute()
    {
        $labels = [
            'CLOTHING' => 'Clothing',
            'BEDDING' => 'Bedding',
            'CURTAIN' => 'Curtain',
            'OTHER' => 'Other'
        ];
        
        return $labels[$this->item_type] ?? $this->item_type;
    }
}