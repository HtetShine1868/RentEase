<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionConfig extends Model
{
    use HasFactory;

    protected $table = 'commission_configs';

    protected $fillable = [
        'service_type',
        'rate'
    ];

    protected $casts = [
        'rate' => 'decimal:2'
    ];

    /**
     * Get the commission rate for a service type
     */
    public static function getRate($serviceType)
    {
        $config = self::where('service_type', $serviceType)->first();
        return $config ? $config->rate : 0;
    }

    /**
     * Calculate commission for an amount
     */
    public static function calculate($serviceType, $amount)
    {
        $rate = self::getRate($serviceType);
        return ($amount * $rate) / 100;
    }

    /**
     * Calculate total with commission
     */
    public static function calculateTotal($serviceType, $amount)
    {
        $commission = self::calculate($serviceType, $amount);
        return $amount + $commission;
    }
}