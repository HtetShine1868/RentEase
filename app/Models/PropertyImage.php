<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    protected $fillable = [
        'property_id',
        'image_path',
        'original_name',
        'mime_type',
        'file_size',
        'is_primary',
        'display_order',
        'metadata'
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'is_primary' => 'boolean',
        'file_size' => 'integer'
    ];
    
    /**
     * Get the property that owns the image.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
    
    /**
     * Get the full URL of the image.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }
    
    /**
     * Get image dimensions from metadata.
     */
    public function getDimensionsAttribute(): ?array
    {
        return $this->metadata['dimensions'] ?? null;
    }
    
    /**
     * Scope a query to only include primary images.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
    
    /**
     * Scope a query to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at');
    }
}