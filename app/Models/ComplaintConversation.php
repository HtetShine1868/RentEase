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
        'sender_type',
        'sender_name',
        'sender_role'
    ];
    
    /**
     * Get the complaint
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
    
    /**
     * Get the user who sent the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get attachments
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class, 'conversation_id');
    }
}