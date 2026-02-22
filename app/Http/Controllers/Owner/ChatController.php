<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Show all chats for the owner
     */
    public function index()
    {
        $ownerId = Auth::id();
        
        $conversations = ChatConversation::where('owner_id', $ownerId)
            ->with([
                'booking.property', 
                'tenant', 
                'messages' => function($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('owner.pages.chats.index', compact('conversations'));
    }

    /**
     * Show chat for a specific booking
     */
    public function show(Booking $booking)
    {
        $ownerId = Auth::id();
        
        // Verify owner owns this property
        if ($booking->property->owner_id !== $ownerId) {
            abort(403, 'You are not authorized to view this chat.');
        }

        // Get or create conversation
        $conversation = ChatConversation::firstOrCreate(
            [
                'booking_id' => $booking->id
            ],
            [
                'property_id' => $booking->property_id,
                'tenant_id' => $booking->user_id,
                'owner_id' => $ownerId
            ]
        );

        // Get messages
        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        // Mark messages as read
        ChatMessage::where('conversation_id', $conversation->id)
            ->where('receiver_id', $ownerId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $tenant = $booking->user;

        return view('owner.pages.chats.show', compact('booking', 'conversation', 'messages', 'tenant'));
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request, Booking $booking)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $ownerId = Auth::id();
        
        // Verify owner owns this property
        if ($booking->property->owner_id !== $ownerId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get or create conversation
        $conversation = ChatConversation::firstOrCreate(
            [
                'booking_id' => $booking->id
            ],
            [
                'property_id' => $booking->property_id,
                'tenant_id' => $booking->user_id,
                'owner_id' => $ownerId
            ]
        );

        // Create message
        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $ownerId,
            'receiver_id' => $booking->user_id,
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

    /**
     * Get new messages (polling)
     */
    public function getNewMessages(Request $request, Booking $booking)
    {
        $ownerId = Auth::id();
        
        if ($booking->property->owner_id !== $ownerId) {
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
            if ($message->receiver_id === $ownerId && !$message->is_read) {
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

    /**
     * Get unread count for the owner
     */
    public function getUnreadCount()
    {
        $ownerId = Auth::id();
        
        $count = ChatMessage::where('receiver_id', $ownerId)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}