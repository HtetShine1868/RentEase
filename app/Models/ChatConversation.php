<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'property_id'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class, 'conversation_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latest();
    }

    public function getOtherParticipant($userId)
    {
        $participant = $this->participants()
            ->with('user')
            ->where('user_id', '!=', $userId)
            ->first();
        
        return $participant ? $participant->user : null;
    }

    public function getUnreadCount($userId)
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if (!$participant || !$participant->last_read_at) {
            return $this->messages()->where('sender_id', '!=', $userId)->count();
        }

        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('created_at', '>', $participant->last_read_at)
            ->count();
    }
}