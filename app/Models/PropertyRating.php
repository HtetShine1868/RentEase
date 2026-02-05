<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'booking_id',
        'cleanliness_rating',
        'location_rating',
        'value_rating',
        'service_rating',
        'overall_rating',
        'comment',
        'is_approved'
    ];

    protected $casts = [
        'overall_rating' => 'decimal:2',
        'is_approved' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}