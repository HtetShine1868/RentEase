<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_type',
        'business_name',
        'business_registration',
        'document_path',
        'contact_person',
        'contact_email',
        'contact_phone',
        'business_address',
        'latitude',
        'longitude',
        'service_radius_km',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'additional_data' // JSON field for role-specific data
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'service_radius_km' => 'float',
        'additional_data' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'PENDING' => 'bg-yellow-100 text-yellow-800',
            'APPROVED' => 'bg-green-100 text-green-800',
            'REJECTED' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getRoleTypeNameAttribute()
    {
        return match($this->role_type) {
            'OWNER' => 'Property Owner',
            'FOOD' => 'Food Provider',
            'LAUNDRY' => 'Laundry Provider',
            default => $this->role_type,
        };
    }

    // Role-specific getters
    public function getOwnerDataAttribute()
    {
        if ($this->role_type !== 'OWNER') return null;
        return $this->additional_data['owner'] ?? [];
    }

    public function getFoodProviderDataAttribute()
    {
        if ($this->role_type !== 'FOOD') return null;
        return $this->additional_data['food_provider'] ?? [];
    }

    public function getLaundryProviderDataAttribute()
    {
        if ($this->role_type !== 'LAUNDRY') return null;
        return $this->additional_data['laundry_provider'] ?? [];
    }

    // Check if user can apply for a role
    public static function canApply($userId, $roleType)
    {
        $user = User::find($userId);
        
        // User already has this role?
        if ($user->hasRole($roleType)) {
            return false;
        }

        // Check for existing pending application
        $existingApplication = self::where('user_id', $userId)
            ->where('role_type', $roleType)
            ->whereIn('status', ['PENDING'])
            ->exists();

        return !$existingApplication;
    }

    // Get role-specific requirements
    public static function getRoleRequirements($roleType)
    {
        return match($roleType) {
            'OWNER' => [
                'Property details',
                'Owner identification',
                'Property ownership proof'
            ],
            'FOOD' => [
                'Food business license',
                'Kitchen photos',
                'Menu details',
                'Delivery coverage area'
            ],
            'LAUNDRY' => [
                'Business registration',
                'Equipment photos',
                'Service coverage area',
                'Pricing details'
            ],
            default => []
        };
    }
}