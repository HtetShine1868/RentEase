<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Complaint extends Model
{
    use HasFactory;

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
        'resolved_at' => 'datetime'
    ];

    protected $appends = ['priority_badge', 'status_badge'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function related()
    {
        return $this->morphTo();
    }

    // Accessors
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'LOW' => 'bg-blue-100 text-blue-800',
            'MEDIUM' => 'bg-yellow-100 text-yellow-800',
            'HIGH' => 'bg-orange-100 text-orange-800',
            'URGENT' => 'bg-red-100 text-red-800'
        ];

        return $badges[$this->priority] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'OPEN' => 'bg-red-100 text-red-800',
            'IN_PROGRESS' => 'bg-yellow-100 text-yellow-800',
            'RESOLVED' => 'bg-green-100 text-green-800',
            'CLOSED' => 'bg-gray-100 text-gray-800'
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getComplaintTypeNameAttribute()
    {
        return match($this->complaint_type) {
            'PROPERTY' => 'Property Issue',
            'FOOD_SERVICE' => 'Food Service',
            'LAUNDRY_SERVICE' => 'Laundry Service',
            'USER' => 'User Issue',
            'SYSTEM' => 'System Issue',
            default => $this->complaint_type
        };
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['OPEN', 'IN_PROGRESS']);
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['RESOLVED', 'CLOSED']);
    }

    // Business Logic
    public function markAsInProgress($assignedTo = null)
    {
        $this->update([
            'status' => 'IN_PROGRESS',
            'assigned_to' => $assignedTo ?? auth()->id()
        ]);
    }

    public function resolve($resolution, $resolvedBy = null)
    {
        $this->update([
            'status' => 'RESOLVED',
            'resolution' => $resolution,
            'resolved_at' => now(),
            'assigned_to' => $resolvedBy ?? auth()->id()
        ]);
    }

    public function close()
    {
        $this->update(['status' => 'CLOSED']);
    }

    public function reopen()
    {
        $this->update([
            'status' => 'OPEN',
            'assigned_to' => null,
            'resolution' => null,
            'resolved_at' => null
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($complaint) {
            $complaint->complaint_reference = 'COMP-' . strtoupper(Str::random(8));
        });
    }
}