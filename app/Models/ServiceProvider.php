<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_type',
        'business_name',
        'description',
        'contact_email',
        'contact_phone',
        'address',
        'city',
        'latitude',
        'longitude',
        'service_radius_km',
        'status',
        'rating',
        'total_orders'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'service_radius_km' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_orders' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function foodServiceConfig()
    {

        return $this->hasOne(FoodServiceConfig::class, 'service_provider_id');
    }
    public function laundryConfig()
    {
        return $this->hasOne(LaundryServiceConfig::class);
    }

    public function foodItems()
    {
        return $this->hasMany(FoodItem::class);
    }

    public function laundryItems()
    {
        return $this->hasMany(LaundryItem::class);
    }

// In ServiceProvider.php model
    public function foodOrders()
    {
        return $this->hasMany(FoodOrder::class, 'service_provider_id');
    }

    public function laundryOrders()
    {
        return $this->hasMany(LaundryOrder::class);
    }

    // Scopes
    public function scopeFoodService($query)
    {
        return $query->where('service_type', 'FOOD');
    }

    public function scopeLaundryService($query)
    {
        return $query->where('service_type', 'LAUNDRY');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeWithinCoverage($query, $lat, $lng)
    {
        return $query->selectRaw("
            *,
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
        ", [$lat, $lng, $lat])
        ->whereRaw("
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= service_radius_km
        ", [$lat, $lng, $lat]);
    }
        public function mealTypes()
    {

        return $this->belongsToMany(MealType::class, 'food_service_meal_types', 'service_provider_id', 'meal_type_id');
    }
}