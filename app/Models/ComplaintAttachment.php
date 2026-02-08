<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ComplaintAttachment extends Model
{
    protected $table = 'complaint_attachments';
    
    protected $fillable = [
        'complaint_conversation_id',
        'complaint_id',
        'original_name',
        'path',
        'mime_type',
        'size',
        'disk'
    ];
    
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ComplaintConversation::class, 'complaint_conversation_id');
    }
    
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
    
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
    
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
    
    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
    
    public function getFileIconAttribute(): string
    {
        return match(true) {
            $this->is_image => 'fa-image',
            $this->is_pdf => 'fa-file-pdf',
            str_contains($this->mime_type, 'word') => 'fa-file-word',
            str_contains($this->mime_type, 'excel') => 'fa-file-excel',
            default => 'fa-file'
        };
    }
}