<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Property;
use App\Models\Booking;
use App\Models\User;
use App\Traits\Notifiable; // ADD THIS
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    use Notifiable; // ADD THIS

    public function index()
    {
        $complaints = Complaint::where('user_id', Auth::id())
            ->with(['assignedTo'])
            ->latest()
            ->paginate(10);
            
        return view('complaints.index', compact('complaints'));
    }
    
    public function create()
    {
        // Get properties user has booked
        $properties = Property::whereHas('bookings', function($query) {
            $query->where('user_id', Auth::id());
        })->get();
        
        // Get user's bookings for reference
        $bookings = Booking::where('user_id', Auth::id())
            ->with('property')
            ->latest()
            ->get();
            
        return view('complaints.create', compact('properties', 'bookings'));
    }
    
public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'complaint_type' => 'required|in:PROPERTY,FOOD_SERVICE,LAUNDRY_SERVICE,USER,SYSTEM',
        'related_id' => 'required_if:complaint_type,PROPERTY',
        'title' => 'required|string|max:200',
        'description' => 'required|string',
        'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
    ]);
    
    // Determine related type based on complaint type
    $relatedType = match($request->complaint_type) {
        'PROPERTY' => 'PROPERTY',
        'FOOD_SERVICE' => 'SERVICE_PROVIDER',
        'LAUNDRY_SERVICE' => 'SERVICE_PROVIDER',
        default => 'USER'
    };
    
    // Create complaint
    $complaint = Complaint::create([
        'user_id' => Auth::id(),
        'complaint_reference' => 'COMP-' . strtoupper(uniqid()),
        'complaint_type' => $request->complaint_type,
        'related_id' => $request->related_id,
        'related_type' => $relatedType,
        'title' => $request->title,
        'description' => $request->description,
        'priority' => $request->priority,
        'status' => 'OPEN',
    ]);

    // ============ CREATE NOTIFICATION - EXACT SAME CODE AS TEST ============
    
    try {
        // Use the EXACT same code that worked in the test route
        $notification = \App\Models\Notification::create([
            'user_id' => Auth::id(),
            'type' => 'COMPLAINT',  // Using COMPLAINT type
            'title' => 'Complaint Submitted',
            'message' => "Your complaint #{$complaint->complaint_reference} has been submitted successfully.",
            'related_entity_type' => 'complaint',
            'related_entity_id' => $complaint->id,
            'is_read' => false,
            'channel' => 'IN_APP',
            'is_sent' => true,
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Log success
        \Log::info("✅ Complaint notification created with ID: " . $notification->id);
        
    } catch (\Exception $e) {
        // Log any errors
        \Log::error("❌ Failed to create complaint notification: " . $e->getMessage());
    }
    
    return redirect()->route('complaints.show', $complaint)
        ->with('success', 'Complaint submitted successfully. We will review it soon.');
}
    public function show(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id() && !Auth::user()->hasRole(['OWNER', 'SUPERADMIN'])) {
            abort(403);
        }
        
        $complaint->load(['user', 'assignedTo', 'related']);

        return view('complaints.show', compact('complaint'));
    }
    
    public function update(Request $request, Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
        ]);
        
        $oldPriority = $complaint->priority;
        
        $complaint->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);
        
        return back()->with('success', 'Complaint updated successfully.');
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        if (!Auth::user()->hasRole(['SUPERADMIN', 'OWNER'])) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:IN_PROGRESS,RESOLVED,CLOSED',
            'resolution' => 'required_if:status,RESOLVED|string|nullable'
        ]);

        $oldStatus = $complaint->status;
        $complaint->status = $request->status;
        
        if ($request->status === 'RESOLVED') {
            $complaint->resolution = $request->resolution;
            $complaint->resolved_at = now();
            $complaint->assigned_to = Auth::id();
        }
        
        $complaint->save();

        // ============ ADD NOTIFICATIONS ============
        
        // Notify the complainant about status change
        $this->sendComplaintNotification(
            $complaint->user_id,
            $complaint->complaint_reference,
            strtolower($request->status),
            $complaint->id
        );

        // If resolved, send detailed resolution notification
        if ($request->status === 'RESOLVED') {
            $this->sendSystemNotification(
                $complaint->user_id,
                'Complaint Resolved',
                "Your complaint #{$complaint->complaint_reference} has been resolved.\n\nResolution: {$request->resolution}\n\nThank you for your patience."
            );
        }

        // If closed, send closure notification
        if ($request->status === 'CLOSED') {
            $this->sendSystemNotification(
                $complaint->user_id,
                'Complaint Closed',
                "Your complaint #{$complaint->complaint_reference} has been closed. If you have any further issues, please submit a new complaint."
            );
        }

        // Notify other admins about status change
        $admins = User::whereHas('roles', function($q) {
            $q->where('name', 'SUPERADMIN');
        })->where('id', '!=', Auth::id())->get();

        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                'COMPLAINT',
                'Complaint Status Updated',
                "Complaint #{$complaint->complaint_reference} status changed from {$oldStatus} to {$request->status}",
                'complaint',
                $complaint->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Complaint status updated successfully'
        ]);
    }

    /**
     * Assign complaint to self (admin action)
     */
    public function assignToSelf(Complaint $complaint)
    {
        if (!Auth::user()->hasRole(['SUPERADMIN', 'OWNER'])) {
            abort(403);
        }

        $complaint->assigned_to = Auth::id();
        $complaint->status = 'IN_PROGRESS';
        $complaint->save();

        // ============ ADD NOTIFICATIONS ============
        
        // Notify the complainant that their complaint is being handled
        $this->createNotification(
            $complaint->user_id,
            'COMPLAINT',
            'Complaint Being Processed',
            "Your complaint #{$complaint->complaint_reference} is now being handled by our support team.",
            'complaint',
            $complaint->id
        );

        // Notify admin that they've been assigned
        $this->sendSystemNotification(
            Auth::id(),
            'Complaint Assigned to You',
            "You have been assigned to handle complaint #{$complaint->complaint_reference}."
        );

        return response()->json([
            'success' => true,
            'message' => 'Complaint assigned to you'
        ]);
    }

    /**
     * Add a reply to complaint (admin/user communication)
     */
    public function addReply(Request $request, Complaint $complaint)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        // You might want to create a complaint_replies table
        // For now, we'll store in a JSON field or just send notification
        
        // ============ ADD NOTIFICATIONS ============
        
        $recipientId = $complaint->user_id === Auth::id() 
            ? $complaint->assigned_to 
            : $complaint->user_id;

        if ($recipientId) {
            $senderName = Auth::user()->name;
            $this->createNotification(
                $recipientId,
                'COMPLAINT',
                'New Reply on Complaint',
                "{$senderName} replied to complaint #{$complaint->complaint_reference}: {$request->message}",
                'complaint',
                $complaint->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully'
        ]);
    }

    /**
     * Get complaint statistics (for admin dashboard)
     */
    public function statistics()
    {
        if (!Auth::user()->hasRole(['SUPERADMIN', 'OWNER'])) {
            abort(403);
        }

        $stats = [
            'total' => Complaint::count(),
            'open' => Complaint::where('status', 'OPEN')->count(),
            'in_progress' => Complaint::where('status', 'IN_PROGRESS')->count(),
            'resolved' => Complaint::where('status', 'RESOLVED')->count(),
            'closed' => Complaint::where('status', 'CLOSED')->count(),
            'urgent' => Complaint::where('priority', 'URGENT')->where('status', '!=', 'RESOLVED')->count(),
        ];

        $byPriority = [
            'LOW' => Complaint::where('priority', 'LOW')->count(),
            'MEDIUM' => Complaint::where('priority', 'MEDIUM')->count(),
            'HIGH' => Complaint::where('priority', 'HIGH')->count(),
            'URGENT' => Complaint::where('priority', 'URGENT')->count(),
        ];

        $byType = [
            'PROPERTY' => Complaint::where('complaint_type', 'PROPERTY')->count(),
            'FOOD_SERVICE' => Complaint::where('complaint_type', 'FOOD_SERVICE')->count(),
            'LAUNDRY_SERVICE' => Complaint::where('complaint_type', 'LAUNDRY_SERVICE')->count(),
            'USER' => Complaint::where('complaint_type', 'USER')->count(),
            'SYSTEM' => Complaint::where('complaint_type', 'SYSTEM')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'by_priority' => $byPriority,
            'by_type' => $byType
        ]);
    }
}