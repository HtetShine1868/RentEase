<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Property;
use App\Models\ServiceProvider;
use App\Models\ComplaintConversation;
use App\Models\ComplaintAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    /**
     * Display a listing of complaints
     */
    public function index(Request $request)
    {
        $owner = Auth::user();
        
        // Get owner's properties and service providers
        $ownerProperties = Property::where('owner_id', $owner->id)->pluck('id');
        $ownerServiceProviders = ServiceProvider::where('user_id', $owner->id)->pluck('id');
        
        // Build base query
        $complaintsQuery = Complaint::with([
            'user:id,name,email,avatar_url',
            'assignedUser:id,name,avatar_url'
        ])->where(function($query) use ($ownerProperties, $ownerServiceProviders) {
            $query->where(function($q) use ($ownerProperties) {
                $q->where('related_type', 'PROPERTY')
                  ->whereIn('related_id', $ownerProperties);
            })
            ->orWhere(function($q) use ($ownerServiceProviders) {
                $q->where('related_type', 'SERVICE_PROVIDER')
                  ->whereIn('related_id', $ownerServiceProviders);
            });
        });
        
        // Apply status filter
        $currentStatus = $request->status ?? 'all';
        if ($currentStatus !== 'all') {
            $complaintsQuery->where('status', $currentStatus);
        }
        
        // Apply priority filter
        if ($request->filled('priority')) {
            $complaintsQuery->where('priority', $request->priority);
        }
        
        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $complaintsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('complaint_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Get statistics
        $stats = $this->getComplaintStats($owner, $complaintsQuery->clone());
        
        // Get selected complaint if specified
        $selectedComplaint = null;
        if ($request->filled('complaint_id')) {
            $selectedComplaint = $this->getComplaintWithDetails($request->complaint_id, $owner);
        }
        
        // Get paginated complaints
        $complaints = $complaintsQuery->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        // Manually load related entities for each complaint
        $complaints->getCollection()->transform(function ($complaint) use ($owner) {
            return $this->enrichComplaintData($complaint);
        });
        
        return view('owner.pages.complaints.index', [
            'complaints' => $complaints,
            'selectedComplaint' => $selectedComplaint,
            'stats' => $stats,
            'currentStatus' => $currentStatus,
            'filters' => [
                'status' => $currentStatus,
                'priority' => $request->priority,
                'search' => $request->search
            ]
        ]);
    }

    /**
     * Display the specified complaint
     */
    public function show($id)
    {
        $owner = Auth::user();
        $complaint = $this->getComplaintWithDetails($id, $owner);
        
        if (!$complaint) {
            abort(403, 'You are not authorized to view this complaint.');
        }
        
        return view('owner.complaint-detail', compact('complaint'));
    }

    /**
     * Update complaint status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:OPEN,IN_PROGRESS,RESOLVED,CLOSED',
            'resolution' => 'nullable|string|max:1000'
        ]);
        
        $owner = Auth::user();
        $complaint = Complaint::findOrFail($id);
        
        // Verify ownership
        if (!$this->verifyOwnership($complaint, $owner)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }
        
        DB::beginTransaction();
        
        try {
            $oldStatus = $complaint->status;
            $complaint->status = $request->status;
            
            if ($request->filled('resolution')) {
                $complaint->resolution = $request->resolution;
            }
            
            if ($request->status === 'RESOLVED' || $request->status === 'CLOSED') {
                $complaint->resolved_at = now();
            }
            
            $complaint->save();
            
            // Add status change to conversation
            ComplaintConversation::create([
                'complaint_id' => $complaint->id,
                'user_id' => $owner->id,
                'message' => "Status changed from {$oldStatus} to {$request->status}" . 
                            ($request->resolution ? "\n\nResolution: " . $request->resolution : ''),
                'type' => 'STATUS_UPDATE'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $this->enrichComplaintData($complaint)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign complaint to current user
     */
    public function assignToSelf($id)
    {
        $owner = Auth::user();
        $complaint = Complaint::findOrFail($id);
        
        // Verify ownership
        if (!$this->verifyOwnership($complaint, $owner)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }
        
        DB::beginTransaction();
        
        try {
            $complaint->assigned_to = $owner->id;
            $complaint->status = 'IN_PROGRESS';
            $complaint->save();
            
            // Add assignment to conversation
            ComplaintConversation::create([
                'complaint_id' => $complaint->id,
                'user_id' => $owner->id,
                'message' => "Complaint assigned to {$owner->name}",
                'type' => 'ASSIGNMENT'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Complaint assigned to you successfully',
                'data' => $this->enrichComplaintData($complaint)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign complaint: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send reply to complaint
     */
    public function sendReply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|min:1|max:2000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt',
        ]);
        
        $owner = Auth::user();
        $complaint = Complaint::findOrFail($id);
        
        // Verify ownership
        if (!$this->verifyOwnership($complaint, $owner)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }
        
        DB::beginTransaction();
        
        try {
            // Handle file attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('complaints/attachments/' . $complaint->id, 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'url' => Storage::url($path)
                    ];
                }
            }
            
            // Create conversation entry
            $conversation = ComplaintConversation::create([
                'complaint_id' => $complaint->id,
                'user_id' => $owner->id,
                'message' => $request->message,
                'type' => 'REPLY',
                'attachments' => $attachments
            ]);
            
            // Update complaint
            $complaint->touch();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully',
                'data' => [
                    'conversation' => $conversation->load('user'),
                    'complaint' => $this->enrichComplaintData($complaint)
                ]
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
     * Export complaints to CSV
     */
    public function export(Request $request)
    {
        $owner = Auth::user();
        
        // Build query for owner's complaints
        $complaintsQuery = $this->buildOwnerComplaintsQuery($owner);
        
        // Apply filters
        if ($request->filled('status') && $request->status !== 'all') {
            $complaintsQuery->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $complaintsQuery->where('priority', $request->priority);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $complaintsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('complaint_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $complaints = $complaintsQuery->with(['user', 'assignedUser'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="complaints_' . date('Y-m-d_H-i-s') . '.csv"',
        ];
        
        $callback = function() use ($complaints) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fputs($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, [
                'Complaint ID',
                'Reference',
                'Title',
                'Description',
                'Status',
                'Priority',
                'Complainant',
                'Email',
                'Phone',
                'Related To',
                'Related Type',
                'Assigned To',
                'Created Date',
                'Updated Date',
                'Resolved Date',
                'Resolution'
            ]);
            
            // Data rows
            foreach ($complaints as $complaint) {
                $relatedTo = $this->getRelatedEntityName($complaint);
                $assignedTo = $complaint->assignedUser ? $complaint->assignedUser->name : 'Unassigned';
                
                fputcsv($file, [
                    $complaint->id,
                    $complaint->complaint_reference,
                    $complaint->title,
                    strip_tags($complaint->description),
                    $complaint->status,
                    $complaint->priority,
                    $complaint->user->name,
                    $complaint->user->email,
                    $complaint->user->phone ?? 'N/A',
                    $relatedTo,
                    $complaint->related_type,
                    $assignedTo,
                    $complaint->created_at->format('Y-m-d H:i:s'),
                    $complaint->updated_at->format('Y-m-d H:i:s'),
                    $complaint->resolved_at ? $complaint->resolved_at->format('Y-m-d H:i:s') : '',
                    strip_tags($complaint->resolution ?? '')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get complaint statistics
     */
    public function statistics(Request $request)
    {
        $owner = Auth::user();
        $stats = $this->getComplaintStats($owner);
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * ==================== HELPER METHODS ====================
     */

    /**
     * Get complaint with all details
     */
    private function getComplaintWithDetails($complaintId, $owner)
    {
        $complaint = Complaint::with([
            'user:id,name,email,avatar_url',
            'assignedUser:id,name,email,avatar_url',
            'conversations.user:id,name,avatar_url',
        ])->find($complaintId);
        
        if (!$complaint || !$this->verifyOwnership($complaint, $owner)) {
            return null;
        }
        
        // Load the specific related entity
        if ($complaint->related_type === 'PROPERTY') {
            $complaint->load(['property' => function($query) {
                $query->select('id', 'name', 'type', 'city', 'area');
            }]);
        } elseif ($complaint->related_type === 'SERVICE_PROVIDER') {
            $complaint->load(['serviceProvider' => function($query) {
                $query->select('id', 'business_name', 'service_type', 'city');
            }]);
        }
        
        return $this->enrichComplaintData($complaint, true);
    }

    /**
     * Enrich complaint data with additional computed fields
     */
    private function enrichComplaintData($complaint, $full = false)
    {
        if (!$complaint) return null;
        
        // Add computed properties
        $complaint->status_text = ucfirst(strtolower($complaint->status));
        $complaint->priority_text = ucfirst(strtolower($complaint->priority));
        
        // Add icons
        $complaint->status_icon = $this->getStatusIcon($complaint->status);
        $complaint->priority_icon = $this->getPriorityIcon($complaint->priority);
        
        // Add badge classes
        $complaint->status_badge_class = $this->getStatusBadgeClass($complaint->status);
        $complaint->priority_badge_class = $this->getPriorityBadgeClass($complaint->priority);
        
        // Add related entity info
        $complaint->related_entity_name = $this->getRelatedEntityName($complaint);
        $complaint->related_entity_icon = $this->getRelatedEntityIcon($complaint);
        $complaint->elapsed_time = $complaint->created_at->diffForHumans();
        
        // Add counts if full details
        if ($full) {
            $complaint->conversations_count = $complaint->conversations->count();
            $complaint->attachments_count = $this->getAttachmentsCount($complaint);
        }
        
        return $complaint;
    }

    /**
     * Get attachments count for complaint
     */
    private function getAttachmentsCount($complaint)
    {
        $count = 0;
        foreach ($complaint->conversations as $conversation) {
            if ($conversation->attachments && is_array($conversation->attachments)) {
                $count += count($conversation->attachments);
            }
        }
        return $count;
    }

    /**
     * Get complaint statistics
     */
    private function getComplaintStats($owner, $baseQuery = null)
    {
        if (!$baseQuery) {
            $baseQuery = $this->buildOwnerComplaintsQuery($owner);
        }
        
        return [
            'total' => $baseQuery->count(),
            'open' => $baseQuery->clone()->where('status', 'OPEN')->count(),
            'in_progress' => $baseQuery->clone()->where('status', 'IN_PROGRESS')->count(),
            'resolved' => $baseQuery->clone()->where('status', 'RESOLVED')->count(),
            'closed' => $baseQuery->clone()->where('status', 'CLOSED')->count(),
            'urgent' => $baseQuery->clone()->where('priority', 'URGENT')->count(),
            'high' => $baseQuery->clone()->where('priority', 'HIGH')->count(),
            'medium' => $baseQuery->clone()->where('priority', 'MEDIUM')->count(),
            'low' => $baseQuery->clone()->where('priority', 'LOW')->count(),
            'high_priority' => $baseQuery->clone()->whereIn('priority', ['URGENT', 'HIGH'])->count(),
        ];
    }

    /**
     * Build base query for owner's complaints
     */
    private function buildOwnerComplaintsQuery($owner)
    {
        $ownerProperties = Property::where('owner_id', $owner->id)->pluck('id');
        $ownerServiceProviders = ServiceProvider::where('user_id', $owner->id)->pluck('id');
        
        return Complaint::where(function($query) use ($ownerProperties, $ownerServiceProviders) {
            $query->where(function($q) use ($ownerProperties) {
                $q->where('related_type', 'PROPERTY')
                  ->whereIn('related_id', $ownerProperties);
            })
            ->orWhere(function($q) use ($ownerServiceProviders) {
                $q->where('related_type', 'SERVICE_PROVIDER')
                  ->whereIn('related_id', $ownerServiceProviders);
            });
        });
    }

    /**
     * Verify ownership
     */
    private function verifyOwnership($complaint, $owner)
    {
        if ($complaint->related_type === 'PROPERTY') {
            $property = Property::find($complaint->related_id);
            return $property && $property->owner_id == $owner->id;
        }
        
        if ($complaint->related_type === 'SERVICE_PROVIDER') {
            $serviceProvider = ServiceProvider::find($complaint->related_id);
            return $serviceProvider && $serviceProvider->user_id == $owner->id;
        }
        
        return false;
    }

    /**
     * Get related entity name
     */
    private function getRelatedEntityName($complaint)
    {
        if ($complaint->related_type === 'PROPERTY') {
            if ($complaint->relationLoaded('property') && $complaint->property) {
                return $complaint->property->name . ' (' . $complaint->property->type . ')';
            }
            return 'Property #' . $complaint->related_id;
        }
        
        if ($complaint->related_type === 'SERVICE_PROVIDER') {
            if ($complaint->relationLoaded('serviceProvider') && $complaint->serviceProvider) {
                return $complaint->serviceProvider->business_name . ' (' . $complaint->serviceProvider->service_type . ')';
            }
            return 'Service #' . $complaint->related_id;
        }
        
        return 'Unknown';
    }

    /**
     * Get related entity icon
     */
    private function getRelatedEntityIcon($complaint)
    {
        if ($complaint->related_type === 'PROPERTY') {
            if ($complaint->relationLoaded('property') && $complaint->property) {
                return $complaint->property->type === 'HOSTEL' ? 'fa-bed' : 'fa-home';
            }
            return 'fa-home';
        }
        
        if ($complaint->related_type === 'SERVICE_PROVIDER') {
            if ($complaint->relationLoaded('serviceProvider') && $complaint->serviceProvider) {
                return $complaint->serviceProvider->service_type === 'FOOD' ? 'fa-utensils' : 'fa-tshirt';
            }
            return 'fa-store';
        }
        
        return 'fa-question-circle';
    }

    /**
     * Get status icon
     */
    private function getStatusIcon($status)
    {
        return match($status) {
            'OPEN' => 'fa-clock',
            'IN_PROGRESS' => 'fa-tools',
            'RESOLVED' => 'fa-check-circle',
            'CLOSED' => 'fa-check-double',
            default => 'fa-question-circle'
        };
    }

    /**
     * Get priority icon
     */
    private function getPriorityIcon($priority)
    {
        return match($priority) {
            'URGENT' => 'fa-exclamation-triangle',
            'HIGH' => 'fa-exclamation-circle',
            'MEDIUM' => 'fa-info-circle',
            'LOW' => 'fa-info',
            default => 'fa-question-circle'
        };
    }

    /**
     * Get status badge class
     */
    private function getStatusBadgeClass($status)
    {
        return match($status) {
            'OPEN' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'IN_PROGRESS' => 'bg-blue-100 text-blue-800 border-blue-200',
            'RESOLVED' => 'bg-green-100 text-green-800 border-green-200',
            'CLOSED' => 'bg-gray-100 text-gray-800 border-gray-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200'
        };
    }

    /**
     * Get priority badge class
     */
    private function getPriorityBadgeClass($priority)
    {
        return match($priority) {
            'URGENT' => 'bg-red-100 text-red-800 border-red-200',
            'HIGH' => 'bg-orange-100 text-orange-800 border-orange-200',
            'MEDIUM' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'LOW' => 'bg-green-100 text-green-800 border-green-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200'
        };
    }
}