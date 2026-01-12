<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles; 

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'gender'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }
    public function hasRole($role)
    {
        return $this->role === $role;
    }
        public function isOperational(): bool
    {
        return in_array($this->role, ['owner', 'food_provider', 'laundry_provider']);
    }

    public function canApplyForRole(): bool
    {
        return $this->role === 'user' && !$this->isOperational();
    }

}

