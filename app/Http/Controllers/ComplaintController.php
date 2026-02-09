<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Property;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
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
        
        $complaint = Complaint::create([
            'user_id' => Auth::id(),
            'complaint_type' => $request->complaint_type,
            'related_id' => $request->related_id,
            'related_type' => $relatedType,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'OPEN',
        ]);
        
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
        
        $complaint->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);
        
        return back()->with('success', 'Complaint updated successfully.');
    }
}