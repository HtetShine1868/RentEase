<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintAttachment extends Model
{
    protected $table = 'complaint_attachments';
    
    protected $fillable = [
        'complaint_id',
        'conversation_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'uploaded_by'
    ];
    
    /**
     * Get the complaint
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
    
    /**
     * Get the conversation
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ComplaintConversation::class);
    }
    
    /**
     * Get file URL
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
    
    /**
     * Get file size in human readable format
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } else {
            $bytes = $bytes . ' bytes';
        }
        
        return $bytes;
    }
}