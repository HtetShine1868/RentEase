<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Complaint extends Model
{
    protected $table = 'complaints';
    
    protected $fillable = [
        'complaint_reference',
        'user_id',
        'complaint_type',
        'related_id',
        'related_type',
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'resolution',
        'resolved_at',
        'reviewed_by'
    ];
    
    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Status constants
    const STATUS_OPEN = 'OPEN';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_RESOLVED = 'RESOLVED';
    const STATUS_CLOSED = 'CLOSED';
    
    // Priority constants
    const PRIORITY_URGENT = 'URGENT';
    const PRIORITY_HIGH = 'HIGH';
    const PRIORITY_MEDIUM = 'MEDIUM';
    const PRIORITY_LOW = 'LOW';
    
    // Complaint type constants
    const TYPE_PROPERTY = 'PROPERTY';
    const TYPE_FOOD_SERVICE = 'FOOD_SERVICE';
    const TYPE_LAUNDRY_SERVICE = 'LAUNDRY_SERVICE';
    const TYPE_USER = 'USER';
    const TYPE_SYSTEM = 'SYSTEM';
    
    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($complaint) {
            if (empty($complaint->complaint_reference)) {
                $complaint->complaint_reference = self::generateReference();
            }
        });
    }
    
    /**
     * Generate unique complaint reference
     */
    public static function generateReference(): string
    {
        do {
            $reference = 'CMP-' . strtoupper(substr(uniqid(), -8));
        } while (self::where('complaint_reference', $reference)->exists());
        
        return $reference;
    }
    
    /**
     * Get the user who filed the complaint
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the assigned user (admin/owner)
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Get the reviewer
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    
    /**
     * Get property if complaint is about property
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'related_id')
            ->where('related_type', self::TYPE_PROPERTY);
    }
    
    /**
     * Get service provider if complaint is about service
     */
    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'related_id')
            ->where('related_type', 'SERVICE_PROVIDER');
    }
    
    /**
     * Get all conversations for this complaint
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(ComplaintConversation::class)
            ->orderBy('created_at', 'asc');
    }
    
    /**
     * Get latest conversation
     */
    public function latestConversation()
    {
        return $this->hasOne(ComplaintConversation::class)
            ->latestOfMany();
    }
    
    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => 'yellow',
            self::STATUS_IN_PROGRESS => 'blue',
            self::STATUS_RESOLVED => 'green',
            self::STATUS_CLOSED => 'gray',
            default => 'gray'
        };
    }
    
    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_URGENT => 'red',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_MEDIUM => 'yellow',
            self::PRIORITY_LOW => 'green',
            default => 'gray'
        };
    }
    
    /**
     * Get status badge class for Tailwind
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::STATUS_IN_PROGRESS => 'bg-blue-100 text-blue-800 border-blue-200',
            self::STATUS_RESOLVED => 'bg-green-100 text-green-800 border-green-200',
            self::STATUS_CLOSED => 'bg-gray-100 text-gray-800 border-gray-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200'
        };
    }
    
    /**
     * Get priority badge class for Tailwind
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_URGENT => 'bg-red-100 text-red-800 border-red-200',
            self::PRIORITY_HIGH => 'bg-orange-100 text-orange-800 border-orange-200',
            self::PRIORITY_MEDIUM => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::PRIORITY_LOW => 'bg-green-100 text-green-800 border-green-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200'
        };
    }
    
    /**
     * Get elapsed time since creation
     */
    public function getElapsedTimeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
    
    /**
     * Check if complaint is assigned to current user
     */
    public function isAssignedTo($userId): bool
    {
        return $this->assigned_to == $userId;
    }
    
    /**
     * Get related entity name
     */
    public function getRelatedEntityNameAttribute(): string
    {
        if ($this->related_type === 'PROPERTY') {
            return $this->property ? $this->property->name : 'Property #' . $this->related_id;
        }
        
        if ($this->related_type === 'SERVICE_PROVIDER') {
            return $this->serviceProvider ? $this->serviceProvider->business_name : 'Service #' . $this->related_id;
        }
        
        return 'Unknown';
    }
    
    /**
     * Get related entity icon
     */
    public function getRelatedEntityIconAttribute(): string
    {
        if ($this->related_type === 'PROPERTY') {
            if ($this->property && $this->property->type === 'HOSTEL') {
                return 'fa-bed';
            }
            return 'fa-home';
        }
        
        if ($this->related_type === 'SERVICE_PROVIDER') {
            if ($this->serviceProvider && $this->serviceProvider->service_type === 'FOOD') {
                return 'fa-utensils';
            }
            return 'fa-tshirt';
        }
        
        return 'fa-question-circle';
    }
    
    /**
     * Scope for owner's complaints
     */
    public function scopeForOwner($query, $ownerId)
    {
        return $query->where(function($q) use ($ownerId) {
            // Property complaints
            $q->where(function($q1) use ($ownerId) {
                $q1->where('related_type', 'PROPERTY')
                   ->whereHas('property', function($propertyQuery) use ($ownerId) {
                       $propertyQuery->where('owner_id', $ownerId);
                   });
            })
            // Service provider complaints
            ->orWhere(function($q2) use ($ownerId) {
                $q2->where('related_type', 'SERVICE_PROVIDER')
                   ->whereHas('serviceProvider', function($spQuery) use ($ownerId) {
                       $spQuery->where('user_id', $ownerId);
                   });
            });
        });
    }
    
    /**
     * Get conversations count
     */
    public function getConversationsCountAttribute(): int
    {
        return $this->conversations()->count();
    }
}