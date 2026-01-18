<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleApplication extends Model
{
    use HasFactory;

    protected $table = 'role_applications';

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
        'rejection_reason'
    ];

    protected $casts = [
        'role_type' => 'string',
        'status' => 'string',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'service_radius_km' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getRoleDisplayNameAttribute()
    {
        return match($this->role_type) {
            'OWNER' => 'Property Owner',
            'FOOD' => 'Food Provider',
            'LAUNDRY' => 'Laundry Provider',
            default => ucfirst(strtolower($this->role_type))
        };
    }
}