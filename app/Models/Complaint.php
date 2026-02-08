<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        'resolved_at'
    ];
    
    protected $casts = [
        'resolved_at' => 'datetime',
    ];
    
    /**
     * Generate unique complaint reference
     */
    public static function generateReference()
    {
        do {
            $reference = 'CMP-' . strtoupper(uniqid());
        } while (self::where('complaint_reference', $reference)->exists());
        
        return $reference;
    }
    
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
     * Get the user who filed the complaint
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the related entity (polymorphic)
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Get the assigned user
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Get complaint conversations
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(ComplaintConversation::class, 'complaint_id')
            ->orderBy('created_at', 'asc');
    }
    
    /**
     * Scope for owner complaints
     */
    public function scopeForOwner($query, $ownerId)
    {
        return $query->where('complaint_type', 'PROPERTY')
            ->whereHas('related', function($q) use ($ownerId) {
                $q->whereHas('property', function($propertyQuery) use ($ownerId) {
                    $propertyQuery->where('owner_id', $ownerId);
                });
            });
    }
    
    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'OPEN' => 'yellow',
            'IN_PROGRESS' => 'blue',
            'RESOLVED' => 'green',
            'CLOSED' => 'gray',
            default => 'gray'
        };
    }
    
    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'URGENT' => 'red',
            'HIGH' => 'orange',
            'MEDIUM' => 'yellow',
            'LOW' => 'green',
            default => 'gray'
        };
    }
    
    /**
     * Check if complaint is assigned to current user
     */
    public function isAssignedTo($userId): bool
    {
        return $this->assigned_to == $userId;
    }
    
    /**
     * Get elapsed time since creation
     */
    public function getElapsedTimeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}