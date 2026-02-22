<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fix: Change 'user_id' to 'tenant_id'
        $conversations = ChatConversation::where('tenant_id', $user->id)
            ->orWhere('owner_id', $user->id)
            ->with(['booking.property', 'tenant', 'owner', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('rental.chats.index', compact('conversations'));
    }

    public function show(Booking $booking)
    {
        $user = Auth::user();
        
        // Verify user is either tenant or owner
        if ($user->id !== $booking->user_id && $user->id !== $booking->property->owner_id) {
            abort(403);
        }

        // Get or create conversation
        $conversation = ChatConversation::firstOrCreate(
            [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id
            ],
            [
                'tenant_id' => $booking->user_id,
                'owner_id' => $booking->property->owner_id
            ]
        );

        // Get messages
        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        // Mark messages as read
        ChatMessage::where('conversation_id', $conversation->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $otherUser = $user->id === $booking->user_id ? $booking->property->owner : $booking->user;

        return view('rental.chats.show', compact('booking', 'conversation', 'messages', 'otherUser'));
    }

    public function sendMessage(Request $request, Booking $booking)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $user = Auth::user();
        
        if ($user->id !== $booking->user_id && $user->id !== $booking->property->owner_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get or create conversation
        $conversation = ChatConversation::firstOrCreate(
            [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id
            ],
            [
                'tenant_id' => $booking->user_id,
                'owner_id' => $booking->property->owner_id
            ]
        );

        // Determine receiver
        $receiverId = $user->id === $booking->user_id 
            ? $booking->property->owner_id 
            : $booking->user_id;

        // Create message with receiver_id
        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message
        ]);

        $conversation->touch(); // Update updated_at

        $message->load('sender');

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'sender' => [
                    'name' => $message->sender->name,
                    'avatar_url' => $message->sender->avatar_url
                ],
                'created_at' => $message->created_at->format('g:i A'),
                'is_read' => $message->is_read
            ]
        ]);
    }

    public function getNewMessages(Request $request, Booking $booking)
    {
        $user = Auth::user();
        
        if ($user->id !== $booking->user_id && $user->id !== $booking->property->owner_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation = ChatConversation::where('booking_id', $booking->id)->first();
        
        if (!$conversation) {
            return response()->json(['messages' => []]);
        }

        $lastMessageId = $request->get('last_message_id', 0);

        $messages = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        foreach ($messages as $message) {
            if ($message->receiver_id === $user->id && !$message->is_read) {
                $message->update(['is_read' => true, 'read_at' => now()]);
            }
        }

        return response()->json([
            'messages' => $messages->map(function($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'sender' => [
                        'name' => $message->sender->name,
                        'avatar_url' => $message->sender->avatar_url
                    ],
                    'created_at' => $message->created_at->format('g:i A'),
                    'is_read' => $message->is_read
                ];
            }),
            'last_message_id' => $messages->isNotEmpty() ? $messages->last()->id : $lastMessageId
        ]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $count = ChatMessage::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}