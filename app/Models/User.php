<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'email_verified_at',
        'verification_code', 
        'verification_code_sent_at', 
        'verification_attempts',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verification_code_sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'ACTIVE',
         'gender' => null,
         'verification_attempts' => 0,
    ];
        // Check if email is verified
    public function isVerified()
    {
        return !is_null($this->email_verified_at);
    }


    // Generate verification code
// In User model, update sendVerificationCode method:
public function sendVerificationCode()
{
    // Generate 6-digit code
    $this->verification_code = rand(100000, 999999);
    $this->verification_code_sent_at = now();
    $this->save();

    // Debug: Always log the code
    \Log::info("=== VERIFICATION CODE ===");
    \Log::info("For: {$this->email}");
    \Log::info("Code: {$this->verification_code}");
    \Log::info("========================");

    // Try to send email
    try {
        \Mail::raw("Your RMS verification code is: {$this->verification_code}", function($message) {
            $message->to($this->email)
                    ->subject('RMS Verification Code');
        });
        \Log::info("Email sent successfully to {$this->email}");
    } catch (\Exception $e) {
        \Log::error("Failed to send email to {$this->email}: " . $e->getMessage());
    }

    return $this->verification_code;
}

public function verifyCode($code)
{
    // Check if code matches
    if ($this->verification_code === $code) {
        // Check if code is not expired (10 minutes)
            if ($this->verification_code_sent_at->addMinutes(10)->isFuture()) {
                $this->email_verified_at = now();
                $this->verification_code = null;
                $this->verification_code_sent_at = null;
                $this->save();
                return true;
            }
        }
        
        // Increment attempts
        $this->increment('verification_attempts');
        
        // Check if too many attempts
        if ($this->verification_attempts >= 5) {
            $this->verification_code = null;
            $this->verification_code_sent_at = null;
            $this->save();
        }
        
        return false;
    }

    /**
     * Check if verification code is expired
     */
    public function isVerificationCodeExpired()
    {
        if (!$this->verification_code_sent_at) {
            return true;
        }
        
        return $this->verification_code_sent_at->addMinutes(10)->isPast();
    }

    /**
     * Check if user can request new code
     */
    public function canRequestNewCode()
    {
        if (!$this->verification_code_sent_at) {
            return true;
        }
        
        // Allow new code after 1 minute
        return $this->verification_code_sent_at->addMinutes(1)->isPast();
    }

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