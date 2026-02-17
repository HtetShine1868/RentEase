<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRating extends Model
{
    protected $fillable = [
        'user_id',
        'service_provider_id',
        'order_id',
        'order_type',
        'quality_rating',
        'delivery_rating',
        'value_rating',
        'overall_rating',
        'comment'
    ];

    protected $casts = [
        'quality_rating' => 'integer',
        'delivery_rating' => 'integer',
        'value_rating' => 'integer',
        'overall_rating' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function order()
    {
        if ($this->order_type === 'FOOD') {
            return $this->belongsTo(FoodOrder::class, 'order_id');
        }
        return $this->belongsTo(LaundryOrder::class, 'order_id');
    }
}