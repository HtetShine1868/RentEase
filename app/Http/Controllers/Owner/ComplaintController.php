<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Complaint;
use App\Models\Property;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class ComplaintController extends Controller
{
    /**
     * Display complaint management page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get owner's properties
        $properties = Property::where('owner_id', $user->id)->pluck('id');
        
        // Get bookings for these properties
        $bookingIds = Booking::whereIn('property_id', $properties)->pluck('id');
        
        // Base query for complaints related to owner's properties
        $query = Complaint::where(function($q) use ($bookingIds) {
                $q->where('complaint_type', 'PROPERTY')
                  ->whereIn('related_id', $bookingIds)
                  ->where('related_type', 'BOOKING');
            })
            ->orWhere(function($q) use ($user) {
                // Also include complaints where owner is mentioned in description
                $q->where('description', 'LIKE', '%' . $user->name . '%')
                  ->orWhere('description', 'LIKE', '%' . $user->properties->pluck('name')->implode('|') . '%');
            });
        
        // Apply filters
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('complaint_reference', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($query) use ($search) {
                      $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Sort
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sort, $order);
        
        $complaints = $query->with(['user', 'property', 'booking'])
            ->paginate(10)
            ->withQueryString();
        
        // Get statistics
        $stats = $this->getComplaintStatistics($user->id);
        
        return view('owner.complaint-management', compact('complaints', 'stats'));
    }
    
    /**
     * Get complaint statistics
     */
    private function getComplaintStatistics($ownerId)
    {
        $properties = Property::where('owner_id', $ownerId)->pluck('id');
        $bookingIds = Booking::whereIn('property_id', $properties)->pluck('id');
        
        return [
            'total' => Complaint::where('complaint_type', 'PROPERTY')
                ->whereIn('related_id', $bookingIds)
                ->where('related_type', 'BOOKING')
                ->count(),
            
            'pending' => Complaint::where('complaint_type', 'PROPERTY')
                ->whereIn('related_id', $bookingIds)
                ->where('related_type', 'BOOKING')
                ->where('status', 'OPEN')
                ->count(),
            
            'in_progress' => Complaint::where('complaint_type', 'PROPERTY')
                ->whereIn('related_id', $bookingIds)
                ->where('related_type', 'BOOKING')
                ->where('status', 'IN_PROGRESS')
                ->count(),
            
            'resolved' => Complaint::where('complaint_type', 'PROPERTY')
                ->whereIn('related_id', $bookingIds)
                ->where('related_type', 'BOOKING')
                ->where('status', 'RESOLVED')
                ->count(),
            
            'closed' => Complaint::where('complaint_type', 'PROPERTY')
                ->whereIn('related_id', $bookingIds)
                ->where('related_type', 'BOOKING')
                ->where('status', 'CLOSED')
                ->count(),
        ];
    }
    
    /**
     * Show complaint details
     */
    public function show($id)
    {
        $complaint = Complaint::with([
            'user',
            'booking.room.property',
            'conversations' => function($q) {
                $q->orderBy('created_at', 'asc');
            }
        ])->findOrFail($id);
        
        // Check if owner has permission to view this complaint
        $this->authorize('view', $complaint);
        
        $conversations = $complaint->conversations ?? collect();
        
        return response()->json([
            'success' => true,
            'complaint' => $complaint,
            'conversations' => $conversations,
            'timeline' => $this->getComplaintTimeline($complaint)
        ]);
    }
    
    /**
     * Get complaint timeline
     */
    private function getComplaintTimeline($complaint)
    {
        $timeline = [];
        
        // Complaint filed
        $timeline[] = [
            'action' => 'Complaint Filed',
            'description' => 'User submitted the complaint',
            'timestamp' => $complaint->created_at,
            'icon' => 'fa-plus',
            'color' => 'green'
        ];
        
        // Status changes
        if ($complaint->status === 'IN_PROGRESS') {
            $timeline[] = [
                'action' => 'In Progress',
                'description' => 'Complaint marked as in progress',
                'timestamp' => $complaint->updated_at,
                'icon' => 'fa-tools',
                'color' => 'blue'
            ];
        }
        
        // Add conversation entries
        if ($complaint->conversations) {
            foreach ($complaint->conversations->where('sender_type', 'OWNER') as $message) {
                $timeline[] = [
                    'action' => 'Owner Response',
                    'description' => 'Owner replied to complaint',
                    'timestamp' => $message->created_at,
                    'icon' => 'fa-reply',
                    'color' => 'purple'
                ];
            }
        }
        
        // Sort by timestamp
        usort($timeline, function($a, $b) {
            return strtotime($a['timestamp']) - strtotime($b['timestamp']);
        });
        
        return $timeline;
    }
    
    /**
     * Send reply to complaint
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120', // 5MB max per file
        ]);
        
        $complaint = Complaint::findOrFail($id);
        $this->authorize('reply', $complaint);
        
        try {
            DB::beginTransaction();
            
            // Create conversation entry
            $conversation = $complaint->conversations()->create([
                'user_id' => Auth::id(),
                'message' => $request->message,
                'sender_type' => 'OWNER',
                'sender_name' => Auth::user()->name,
                'sender_role' => 'Owner'
            ]);
            
            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('complaints/attachments', 'public');
                    
                    $conversation->attachments()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
            
            // Update complaint updated_at
            $complaint->touch();
            
            // Create notification for user
            $this->createNotification($complaint->user_id, 'COMPLAINT', 'New Reply to Your Complaint', 
                "The owner has replied to your complaint: {$complaint->title}", 
                $complaint->id, 'Complaint');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully',
                'conversation' => $conversation->load('attachments')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reply: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update complaint status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:IN_PROGRESS,RESOLVED,CLOSED',
            'resolution' => 'nullable|string|max:1000',
        ]);
        
        $complaint = Complaint::findOrFail($id);
        $this->authorize('update', $complaint);
        
        try {
            $oldStatus = $complaint->status;
            $complaint->status = $request->status;
            
            if ($request->resolution) {
                $complaint->resolution = $request->resolution;
            }
            
            if ($request->status === 'RESOLVED') {
                $complaint->resolved_at = now();
                $complaint->assigned_to = Auth::id();
            }
            
            $complaint->save();
            
            // Log status change
            $this->createNotification($complaint->user_id, 'COMPLAINT', 'Complaint Status Updated', 
                "Your complaint '{$complaint->title}' status has been changed from {$oldStatus} to {$complaint->status}", 
                $complaint->id, 'Complaint');
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'complaint' => $complaint
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get statistics
     */
    public function statistics()
    {
        $stats = $this->getComplaintStatistics(Auth::id());
        
        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }
    
    /**
     * Create notification
     */
    private function createNotification($userId, $type, $title, $message, $relatedId, $relatedType)
    {
        // You'll need to create a Notification model or use Laravel's notification system
        Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_entity_type' => $relatedType,
            'related_entity_id' => $relatedId,
            'channel' => 'IN_APP',
            'is_read' => false,
            'is_sent' => false,
            'created_at' => now()
        ]);
    }
}