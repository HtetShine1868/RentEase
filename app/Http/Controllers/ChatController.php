<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        $conversations = ChatConversation::whereHas('participants', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['participants.user', 'messages' => function($query) {
            $query->latest()->take(1);
        }, 'property'])
        ->orderBy('updated_at', 'desc')
        ->get();

        // Get unread counts for each conversation
        foreach ($conversations as $conversation) {
            $conversation->unread_count = $conversation->getUnreadCount($user->id);
        }

        return view('chat.index', compact('conversations'));
    }

    public function show(ChatConversation $conversation)
    {
        $user = Auth::user();
        
        // Check if user is participant
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        // Mark messages as read
        $participant = $conversation->participants()->where('user_id', $user->id)->first();
        $participant->update(['last_read_at' => now()]);

        // Mark all unread messages as read
        ChatMessage::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        $otherParticipant = $conversation->getOtherParticipant($user->id);

        return view('chat.show', compact('conversation', 'messages', 'otherParticipant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required_without:conversation_id|exists:properties,id',
            'conversation_id' => 'required_without:property_id|exists:chat_conversations,id',
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        DB::beginTransaction();
        try {
            // Get or create conversation
            if ($request->conversation_id) {
                $conversation = ChatConversation::findOrFail($request->conversation_id);
                
                // Check if user is participant
                if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            } else {
                $property = Property::findOrFail($request->property_id);
                
                // Check if user is not the owner (users can't chat with themselves)
                if ($property->owner_id == $user->id) {
                    return response()->json(['error' => 'Cannot start conversation with yourself'], 400);
                }

                // Check if conversation already exists
                $existingConversation = ChatConversation::where('property_id', $property->id)
                    ->whereHas('participants', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->whereHas('participants', function($query) use ($property) {
                        $query->where('user_id', $property->owner_id);
                    })
                    ->first();

                if ($existingConversation) {
                    $conversation = $existingConversation;
                } else {
                    // Create new conversation
                    $conversation = ChatConversation::create([
                        'property_id' => $property->id
                    ]);

                    // Add participants
                    $conversation->participants()->createMany([
                        ['user_id' => $user->id],
                        ['user_id' => $property->owner_id]
                    ]);
                }
            }

            // Create message
            $message = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'message' => $request->message
            ]);

            // Update conversation timestamp
            $conversation->touch();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message->load('sender'),
                    'conversation_id' => $conversation->id
                ]);
            }

            return redirect()->route('chat.show', $conversation)
                ->with('success', 'Message sent successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to send message'], 500);
            }
            
            return back()->with('error', 'Failed to send message');
        }
    }

    public function startConversation(Property $property)
    {
        $user = Auth::user();
        
        // Don't allow chatting with yourself
        if ($property->owner_id == $user->id) {
            return redirect()->route('properties.show', $property)
                ->with('error', 'You cannot start a conversation with yourself');
        }

        // Find existing conversation
        $conversation = ChatConversation::where('property_id', $property->id)
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereHas('participants', function($query) use ($property) {
                $query->where('user_id', $property->owner_id);
            })
            ->first();

        if ($conversation) {
            return redirect()->route('chat.show', $conversation);
        }

        // Create new conversation
        DB::transaction(function() use ($property, $user, &$conversation) {
            $conversation = ChatConversation::create([
                'property_id' => $property->id
            ]);

            $conversation->participants()->createMany([
                ['user_id' => $user->id],
                ['user_id' => $property->owner_id]
            ]);
        });

        return redirect()->route('chat.show', $conversation);
    }

    public function markAsRead(ChatConversation $conversation)
    {
        $user = Auth::user();
        
        $participant = $conversation->participants()
            ->where('user_id', $user->id)
            ->first();

        if ($participant) {
            $participant->update(['last_read_at' => now()]);
            
            ChatMessage::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
        }

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $conversations = ChatConversation::whereHas('participants', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        $totalUnread = 0;
        foreach ($conversations as $conversation) {
            $totalUnread += $conversation->getUnreadCount($user->id);
        }

        return response()->json(['unread_count' => $totalUnread]);
    }
    // Add this method to get messages for a conversation
public function getMessages(ChatConversation $conversation)
{
    $user = Auth::user();
    
    // Check if user is participant
    if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $messages = $conversation->messages()
        ->with('sender')
        ->orderBy('created_at', 'asc')
        ->get();

    return view('chat.partials.messages', compact('conversation', 'messages'))->render();
}

// Add this method to get new messages
public function getNewMessages(ChatConversation $conversation, $lastId)
{
    $user = Auth::user();
    
    // Check if user is participant
    if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $messages = $conversation->messages()
        ->with('sender')
        ->where('id', '>', $lastId)
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json($messages);
}
}