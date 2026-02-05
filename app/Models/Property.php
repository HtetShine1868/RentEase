<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory;

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
        'commission_rate',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'base_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'unit_size' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'min_stay_months' => 'integer',
        'deposit_months' => 'integer',
    ];

    protected $appends = [
        'total_price',
        'formatted_price',
        'status_badge',
        'type_name',
    ];

    // ========== RELATIONSHIPS ==========
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function amenities()
    {
        return $this->hasMany(PropertyAmenity::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // ADD THESE TWO METHODS HERE:
    public function images()
    {
        return $this->hasMany(PropertyImage::class)->orderBy('display_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }
    // END OF ADDITIONS

    public function availableRooms()
    {
        return $this->hasMany(Room::class)->where('status', 'AVAILABLE');
    }

    public function reviews()
    {
        return $this->hasMany(PropertyRating::class);
    }

    // ========== ACCESSORS ==========
    public function averageRating()
    {
        return $this->reviews()->avg('overall_rating');
    }

    public function totalReviews()
    {
        return $this->reviews()->count();
    }

    public function getTotalPriceAttribute()
    {
        return $this->base_price + ($this->base_price * $this->commission_rate / 100);
    }

    public function getFormattedPriceAttribute()
    {
        return '৳' . number_format($this->total_price, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'DRAFT' => 'bg-gray-100 text-gray-800',
            'PENDING' => 'bg-yellow-100 text-yellow-800',
            'ACTIVE' => 'bg-green-100 text-green-800',
            'INACTIVE' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getTypeNameAttribute()
    {
        return $this->type === 'HOSTEL' ? 'Hostel' : 'Apartment';
    }

    public function getGenderPolicyNameAttribute()
    {
        return match($this->gender_policy) {
            'MALE_ONLY' => 'Male Only',
            'FEMALE_ONLY' => 'Female Only',
            'MIXED' => 'Mixed',
            default => 'Mixed',
        };
    }

    public function getFurnishingStatusNameAttribute()
    {
        return match($this->furnishing_status) {
            'FURNISHED' => 'Furnished',
            'SEMI_FURNISHED' => 'Semi-Furnished',
            'UNFURNISHED' => 'Unfurnished',
            default => 'Not Specified',
        };
    }

    // ========== SCOPES ==========
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'DRAFT');
    }

    public function scopeHostel($query)
    {
        return $query->where('type', 'HOSTEL');
    }

    public function scopeApartment($query)
    {
        return $query->where('type', 'APARTMENT');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('area', 'like', "%{$search}%");
    }

    // ========== BUSINESS LOGIC ==========
    public function canBeBooked()
    {
        return $this->status === 'ACTIVE' && 
               ($this->type === 'APARTMENT' || $this->availableRooms()->exists());
    }

    public function updateStatus($status)
    {
        $allowedStatuses = ['DRAFT', 'PENDING', 'ACTIVE', 'INACTIVE'];
        
        if (in_array($status, $allowedStatuses)) {
            $this->update(['status' => $status]);
            return true;
        }
        
        return false;
    }

    public static function getOwnerStats($ownerId)
    {
        return [
            'total' => self::where('owner_id', $ownerId)->count(),
            'active' => self::where('owner_id', $ownerId)->where('status', 'ACTIVE')->count(),
            'draft' => self::where('owner_id', $ownerId)->where('status', 'DRAFT')->count(),
            'inactive' => self::where('owner_id', $ownerId)->where('status', 'INACTIVE')->count(),
        ];
    }
}