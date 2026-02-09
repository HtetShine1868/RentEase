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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Constants for status
    const STATUS_OPEN = 'OPEN';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_RESOLVED = 'RESOLVED';
    const STATUS_CLOSED = 'CLOSED';
    
    // Constants for priority
    const PRIORITY_URGENT = 'URGENT';
    const PRIORITY_HIGH = 'HIGH';
    const PRIORITY_MEDIUM = 'MEDIUM';
    const PRIORITY_LOW = 'LOW';
    
    // Constants for complaint types
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
        
        // Generate unique complaint reference before creating
        static::creating(function ($complaint) {
            if (empty($complaint->complaint_reference)) {
                $complaint->complaint_reference = self::generateReference();
            }
        });
        
        // Record status history when status changes
        static::updating(function ($complaint) {
            if ($complaint->isDirty('status')) {
                // Check if ComplaintStatusHistory model exists
                if (class_exists(ComplaintStatusHistory::class)) {
                    ComplaintStatusHistory::create([
                        'complaint_id' => $complaint->id,
                        'old_status' => $complaint->getOriginal('status'),
                        'new_status' => $complaint->status,
                        'changed_by' => auth()->id() ?? $complaint->assigned_to,
                        'notes' => 'Status updated'
                    ]);
                }
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the related entity (polymorphic)
     * FIXED: Map string values to actual model classes
     */
    public function related(): MorphTo
    {
        // Map string values to model classes
        $morphMap = [
            'PROPERTY' => Property::class,
            'SERVICE_PROVIDER' => ServiceProvider::class,
            'USER' => User::class,
        ];
        
        $morphClass = $morphMap[$this->related_type] ?? $this->related_type;
        
        return $this->morphTo('related', $morphClass, 'related_type', 'related_id');
    }
    
    /**
     * Get the property if complaint is about property
     * FIXED: Remove the where clause that references related_type
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'related_id');
    }
    
    /**
     * Get the service provider if complaint is about service
     * FIXED: Remove the where clause that references related_type
     */
    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'related_id');
    }
    
    /**
     * Get the booking if related
     * FIXED: Remove the where clause that references related_type
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'related_id');
    }
    
    /**
     * Get the assigned user (admin/owner)
     */
    public function assignedToUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
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
     * Get all attachments for this complaint
     */
    public function attachments(): HasMany
    {
        // Check if ComplaintAttachment model exists
        if (class_exists(ComplaintAttachment::class)) {
            return $this->hasMany(ComplaintAttachment::class, 'complaint_id')
                ->orderBy('created_at', 'desc');
        }
        
        // Return empty relationship if model doesn't exist
        return $this->hasMany(ComplaintConversation::class)
            ->whereNotNull('attachments');
    }
    
    /**
     * Get status history for this complaint
     */
    public function statusHistory(): HasMany
    {
        // Check if ComplaintStatusHistory model exists
        if (class_exists(ComplaintStatusHistory::class)) {
            return $this->hasMany(ComplaintStatusHistory::class)
                ->orderBy('created_at', 'asc');
        }
        
        // Return empty relationship
        return $this->hasMany(ComplaintConversation::class)
            ->where('type', 'STATUS_UPDATE')
            ->orderBy('created_at', 'asc');
    }
    
    /**
     * Get the reviewer (admin who reviewed)
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    
    /**
     * Scope for property complaints
     */
    public function scopePropertyComplaints($query)
    {
        return $query->where('complaint_type', 'PROPERTY')
                    ->where('related_type', 'PROPERTY');
    }
    
    /**
     * Scope for service complaints
     */
    public function scopeServiceComplaints($query)
    {
        return $query->whereIn('complaint_type', ['FOOD_SERVICE', 'LAUNDRY_SERVICE'])
                    ->where('related_type', 'SERVICE_PROVIDER');
    }
    
    /**
     * Scope for owner's complaints
     */
    public function scopeForOwner($query, $ownerId)
    {
        return $query->where(function($q) use ($ownerId) {
            // Property complaints
            $q->where(function($q1) use ($ownerId) {
                $q1->where('complaint_type', 'PROPERTY')
                   ->where('related_type', 'PROPERTY')
                   ->whereHas('property', function($propertyQuery) use ($ownerId) {
                       $propertyQuery->where('owner_id', $ownerId);
                   });
            })
            // Service provider complaints
            ->orWhere(function($q2) use ($ownerId) {
                $q2->whereIn('complaint_type', ['FOOD_SERVICE', 'LAUNDRY_SERVICE'])
                   ->where('related_type', 'SERVICE_PROVIDER')
                   ->whereHas('serviceProvider', function($spQuery) use ($ownerId) {
                       $spQuery->where('user_id', $ownerId);
                   });
            });
        });
    }
    
    /**
     * Scope for open complaints
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['OPEN', 'IN_PROGRESS']);
    }
    
    /**
     * Scope for resolved complaints
     */
    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['RESOLVED', 'CLOSED']);
    }
    
    /**
     * Scope by priority
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
    
    /**
     * Scope by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope by complaint type
     */
    public function scopeType($query, $type)
    {
        return $query->where('complaint_type', $type);
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
     * Get status text (human readable)
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
            default => ucfirst(strtolower($this->status))
        };
    }
    
    /**
     * Get priority text (human readable)
     */
    public function getPriorityTextAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_URGENT => 'Urgent',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_LOW => 'Low',
            default => ucfirst(strtolower($this->priority))
        };
    }
    
    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => 'fa-clock',
            self::STATUS_IN_PROGRESS => 'fa-tools',
            self::STATUS_RESOLVED => 'fa-check-circle',
            self::STATUS_CLOSED => 'fa-check-double',
            default => 'fa-question-circle'
        };
    }
    
    /**
     * Get priority icon
     */
    public function getPriorityIconAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_URGENT => 'fa-exclamation-triangle',
            self::PRIORITY_HIGH => 'fa-exclamation-circle',
            self::PRIORITY_MEDIUM => 'fa-info-circle',
            self::PRIORITY_LOW => 'fa-info',
            default => 'fa-question-circle'
        };
    }
    
    /**
     * Get status badge class for Tailwind CSS
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
     * Get priority badge class for Tailwind CSS
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
     * Get complaint type text
     */
    public function getComplaintTypeTextAttribute(): string
    {
        return match($this->complaint_type) {
            self::TYPE_PROPERTY => 'Property Issue',
            self::TYPE_FOOD_SERVICE => 'Food Service',
            self::TYPE_LAUNDRY_SERVICE => 'Laundry Service',
            self::TYPE_USER => 'User Issue',
            self::TYPE_SYSTEM => 'System Issue',
            default => ucfirst(strtolower(str_replace('_', ' ', $this->complaint_type)))
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
     * Get time since last update
     */
    public function getLastUpdateAttribute(): string
    {
        return $this->updated_at->diffForHumans();
    }
    
    /**
     * Get related entity name
     */
    public function getRelatedEntityNameAttribute(): ?string
    {
        // Don't try to use the polymorphic relationship
        // Instead, use the specific relationships or return a simple string
        if ($this->related_type === 'PROPERTY') {
            if ($this->relationLoaded('property') && $this->property) {
                return $this->property->name ?? 'Unknown Property';
            }
            return 'Property #' . $this->related_id;
        }
        
        if ($this->related_type === 'SERVICE_PROVIDER') {
            if ($this->relationLoaded('serviceProvider') && $this->serviceProvider) {
                return $this->serviceProvider->business_name ?? 'Unknown Service';
            }
            return 'Service #' . $this->related_id;
        }
        
        if ($this->related_type === 'USER') {
            return 'User #' . $this->related_id;
        }
        
        return null;
    }
    
    /**
     * Get related entity type icon
     */
    public function getRelatedEntityIconAttribute(): string
    {
        if ($this->related_type === 'PROPERTY') {
            // Check if property is loaded and has type
            if ($this->relationLoaded('property') && $this->property && $this->property->type) {
                return $this->property->type === 'HOSTEL' ? 'fa-bed' : 'fa-home';
            }
            return 'fa-home';
        }
        
        if ($this->related_type === 'SERVICE_PROVIDER') {
            // Check if serviceProvider is loaded and has service_type
            if ($this->relationLoaded('serviceProvider') && $this->serviceProvider && $this->serviceProvider->service_type) {
                return $this->serviceProvider->service_type === 'FOOD' ? 'fa-utensils' : 'fa-tshirt';
            }
            return $this->complaint_type === 'FOOD_SERVICE' ? 'fa-utensils' : 'fa-tshirt';
        }
        
        if ($this->related_type === 'USER') {
            return 'fa-user';
        }
        
        return 'fa-question-circle';
    }
    
    /**
     * Check if complaint is open
     */
    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_IN_PROGRESS]);
    }
    
    /**
     * Check if complaint is resolved
     */
    public function isResolved(): bool
    {
        return in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }
    
    /**
     * Check if complaint is assigned to current user
     */
    public function isAssignedTo($userId): bool
    {
        return $this->assigned_to == $userId;
    }
    
    /**
     * Check if complaint is assigned
     */
    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }
    
    /**
     * Check if complaint has resolution
     */
    public function hasResolution(): bool
    {
        return !empty($this->resolution);
    }
    
    /**
     * Check if complaint is high priority
     */
    public function isHighPriority(): bool
    {
        return in_array($this->priority, [self::PRIORITY_URGENT, self::PRIORITY_HIGH]);
    }
    
    /**
     * Get conversations count
     */
    public function getConversationsCountAttribute(): int
    {
        return $this->conversations()->count();
    }
    
    /**
     * Get attachments count
     */
    public function getAttachmentsCountAttribute(): int
    {
        return $this->attachments()->count();
    }
    
    /**
     * Get open duration in days
     */
    public function getOpenDurationAttribute(): ?int
    {
        if ($this->isOpen()) {
            return $this->created_at->diffInDays(now());
        }
        
        if ($this->resolved_at) {
            return $this->created_at->diffInDays($this->resolved_at);
        }
        
        return null;
    }
    
    /**
     * Get formatted open duration
     */
    public function getOpenDurationFormattedAttribute(): ?string
    {
        $days = $this->open_duration;
        
        if (is_null($days)) {
            return null;
        }
        
        if ($days == 0) {
            return 'Today';
        }
        
        if ($days == 1) {
            return '1 day';
        }
        
        if ($days < 7) {
            return "{$days} days";
        }
        
        $weeks = floor($days / 7);
        if ($weeks == 1) {
            return '1 week';
        }
        
        return "{$weeks} weeks";
    }
    
    /**
     * Get latest conversation
     */
    public function latestConversation()
    {
        return $this->conversations()->latest()->first();
    }
    
    /**
     * Get latest reply from user (non-owner/admin)
     */
    public function latestUserReply()
    {
        return $this->conversations()
            ->where('type', 'REPLY')
            ->whereHas('user', function($q) {
                $q->where('id', $this->user_id);
            })
            ->latest()
            ->first();
    }
    
    /**
     * Get unread replies count for owner
     */
    public function getUnreadRepliesCountAttribute(): int
    {
        return $this->conversations()
            ->where('type', 'REPLY')
            ->where('user_id', '!=', $this->assigned_to ?? 0)
            ->where('is_read', false)
            ->count();
    }
    
    /**
     * Mark all conversations as read for a user
     */
    public function markAsRead($userId)
    {
        $this->conversations()
            ->where('user_id', '!=', $userId)
            ->update(['is_read' => true]);
    }
    
    /**
     * Assign complaint to a user
     */
    public function assignTo($userId)
    {
        $this->update([
            'assigned_to' => $userId,
            'status' => self::STATUS_IN_PROGRESS
        ]);
    }
    
    /**
     * Resolve complaint
     */
    public function resolve($resolution = null)
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolution' => $resolution,
            'resolved_at' => now()
        ]);
    }
    
    /**
     * Close complaint
     */
    public function close()
    {
        $this->update([
            'status' => self::STATUS_CLOSED,
            'resolved_at' => $this->resolved_at ?? now()
        ]);
    }
    
    /**
     * Reopen complaint
     */
    public function reopen()
    {
        $this->update([
            'status' => self::STATUS_OPEN,
            'assigned_to' => null,
            'resolved_at' => null
        ]);
    }
    
    /**
     * Get complaint statistics for dashboard
     */
    public static function getStatistics($ownerId = null)
    {
        $query = self::query();
        
        if ($ownerId) {
            $query->forOwner($ownerId);
        }
        
        $total = $query->count();
        $open = $query->clone()->open()->count();
        $resolved = $query->clone()->resolved()->count();
        $highPriority = $query->clone()->whereIn('priority', [self::PRIORITY_URGENT, self::PRIORITY_HIGH])->count();
        
        return [
            'total' => $total,
            'open' => $open,
            'in_progress' => $query->clone()->status(self::STATUS_IN_PROGRESS)->count(),
            'resolved' => $resolved,
            'closed' => $query->clone()->status(self::STATUS_CLOSED)->count(),
            'high_priority' => $highPriority,
            'urgent' => $query->clone()->priority(self::PRIORITY_URGENT)->count(),
            'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 1) : 0,
        ];
    }
    
    /**
     * Get recent complaints
     */
    public static function getRecent($limit = 10, $ownerId = null)
    {
        $query = self::with(['user', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->limit($limit);
        
        if ($ownerId) {
            $query->forOwner($ownerId);
        }
        
        return $query->get();
    }
}