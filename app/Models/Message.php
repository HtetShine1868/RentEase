<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'message_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'is_read',
        'is_delivered',
        'is_edited',
        'is_deleted',
        'deleted_for',
        'read_at',
        'delivered_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_delivered' => 'boolean',
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'deleted_for' => 'array',
        'read_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }

    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    public function markAsDelivered()
    {
        if (!$this->is_delivered) {
            $this->update([
                'is_delivered' => true,
                'delivered_at' => now()
            ]);
        }
    }
}