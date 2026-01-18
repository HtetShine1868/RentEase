<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'gender',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $attributes = [
        'status' => 'ACTIVE',
    ];

    // Relationships
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function roleApplications()
    {
        return $this->hasMany(RoleApplication::class);
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

    public function getActiveRoleAttribute()
    {
        if ($this->isSuperAdmin()) {
            return 'SUPERADMIN';
        }

        $operationalRoles = ['OWNER', 'FOOD', 'LAUNDRY'];
        foreach ($operationalRoles as $role) {
            if ($this->hasRole($role)) {
                return $role;
            }
        }

        return 'USER';
    }


}