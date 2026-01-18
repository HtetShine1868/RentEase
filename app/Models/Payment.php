<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_reference',
        'user_id',
        'payable_type',
        'payable_id',
        'amount',
        'commission_amount',
        'payment_method',
        'transaction_id',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    protected $appends = ['provider_earning'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    // Attributes
    public function getProviderEarningAttribute()
    {
        return $this->amount - $this->commission_amount;
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'COMPLETED');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    // Methods
    public function markAsCompleted($transactionId = null)
    {
        $this->update([
            'status' => 'COMPLETED',
            'paid_at' => now(),
            'transaction_id' => $transactionId
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->payment_reference = 'PAY' . strtoupper(uniqid());
        });
    }
}