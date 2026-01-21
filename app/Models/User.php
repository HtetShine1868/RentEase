<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'gender',
        'avatar_url',
        'status',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'ACTIVE',
         'gender' => null,
    ];

    // Relationships
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withTimestamps();
    }

    public function roleApplications()
    {
        return $this->hasMany(RoleApplication::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function foodOrders()
    {
        return $this->hasMany(FoodOrder::class);
    }

    public function laundryOrders()
    {
        return $this->hasMany(LaundryOrder::class);
    }

    public function activeRole()
    {
        return $this->roles()->wherePivot('is_active', true)->first();
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('SUPERADMIN');
    }

    public function isOwner()
    {
        return $this->hasRole('OWNER');
    }

    public function isFoodProvider()
    {
        return $this->hasRole('FOOD');
    }

    public function isLaundryProvider()
    {
        return $this->hasRole('LAUNDRY');
    }

    public function getDefaultAddressAttribute()
    {
        return $this->addresses()->where('is_default', true)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeBanned($query)
    {
        return $query->where('status', 'BANNED');
    }
}