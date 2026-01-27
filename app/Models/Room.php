<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'room_number',
        'room_type',
        'floor_number',
        'capacity',
        'base_price',
        'commission_rate',
        'status',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'capacity' => 'integer',
        'floor_number' => 'integer',
    ];

    protected $appends = [
        'total_price',
        'formatted_price',
        'room_type_name',
        'status_badge',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Accessors
    public function getTotalPriceAttribute()
    {
        return $this->base_price + ($this->base_price * $this->commission_rate / 100);
    }

    public function getFormattedPriceAttribute()
    {
        return '৳' . number_format($this->total_price, 2);
    }

    public function getRoomTypeNameAttribute()
    {
        return match($this->room_type) {
            'SINGLE' => 'Single',
            'DOUBLE' => 'Double',
            'TRIPLE' => 'Triple',
            'QUAD' => 'Quad',
            'DORM' => 'Dormitory',
            default => 'Standard',
        };
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'AVAILABLE' => 'bg-green-100 text-green-800',
            'OCCUPIED' => 'bg-red-100 text-red-800',
            'MAINTENANCE' => 'bg-yellow-100 text-yellow-800',
            'RESERVED' => 'bg-blue-100 text-blue-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    // Business Logic
    public function isAvailableForDates($checkIn, $checkOut)
    {
        // Check if room has overlapping bookings
        $overlappingBookings = $this->bookings()
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut])
                      ->orWhere(function($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                      });
            })
            ->whereNotIn('status', ['CANCELLED'])
            ->exists();

        return !$overlappingBookings && $this->status === 'AVAILABLE';
    }

    public function updateStatus($status)
    {
        $allowedStatuses = ['AVAILABLE', 'OCCUPIED', 'MAINTENANCE', 'RESERVED'];
        
        if (in_array($status, $allowedStatuses)) {
            $this->update(['status' => $status]);
            return true;
        }
        
        return false;
    }
}