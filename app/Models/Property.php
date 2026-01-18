<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'type',
        'name',
        'description',
        'address',
        'city',
        'area',
        'latitude',
        'longitude',
        'status',
        'gender_policy',
        'unit_size',
        'bedrooms',
        'bathrooms',
        'furnishing_status',
        'min_stay_months',
        'deposit_months',
        'base_price',
        'commission_rate'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'base_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'unit_size' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer'
    ];

    protected $appends = ['total_price'];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function amenities()
    {
        return $this->hasMany(PropertyAmenity::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings()
    {
        return $this->hasMany(PropertyRating::class);
    }

    // Attributes
    public function getTotalPriceAttribute()
    {
        return $this->base_price + ($this->base_price * $this->commission_rate / 100);
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('overall_rating');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeHostel($query)
    {
        return $query->where('type', 'HOSTEL');
    }

    public function scopeApartment($query)
    {
        return $query->where('type', 'APARTMENT');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeWithinRadius($query, $lat, $lng, $radius = 10)
    {
        return $query->selectRaw("
            *,
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
        ", [$lat, $lng, $lat])
        ->having('distance', '<=', $radius)
        ->orderBy('distance');
    }
}