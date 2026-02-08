<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplaintConversation extends Model
{
    protected $table = 'complaint_conversations';
    
    protected $fillable = [
        'complaint_id',
        'user_id',
        'message',
        'type',
        'attachments',
        'is_read'
    ];
    
    protected $casts = [
        'attachments' => 'array',
         'is_read' => 'boolean'
    ];
    
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
    
            public function markAsRead()
        {
            $this->update(['is_read' => true]);
        }

        public function markAsUnread()
        {
            $this->update(['is_read' => false]);
        }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function attachmentFiles(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class, 'complaint_conversation_id');
    }
    
}