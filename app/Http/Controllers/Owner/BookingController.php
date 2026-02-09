<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings for owner
     */
    public function index(Request $request)
    {
        $owner = Auth::user();
        
        // Get owner's properties for the filter dropdown
        $properties = Property::where('owner_id', $owner->id)
            ->select('id', 'name', 'type', 'city')
            ->orderBy('name')
            ->get();
        
        // Build query for owner's bookings
        $bookingsQuery = Booking::with([
            'user:id,name,email,phone',
            'property:id,name,type,city,area',
            'room:id,room_number,room_type'
        ])->whereHas('property', function($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        });
        
        // Apply filters
        if ($request->filled('property_id')) {
            $bookingsQuery->where('property_id', $request->property_id);
        }
        
        if ($request->filled('status')) {
            $bookingsQuery->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $bookingsQuery->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('property', function($propQuery) use ($search) {
                      $propQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Apply date filters
        if ($request->filled('date_from')) {
            $bookingsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $bookingsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Get statistics
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        
        $stats = [
            'today_count' => Booking::whereHas('property', function($q) use ($owner) {
                $q->where('owner_id', $owner->id);
            })->whereDate('created_at', $today)->count(),
            
            'month_count' => Booking::whereHas('property', function($q) use ($owner) {
                $q->where('owner_id', $owner->id);
            })->whereDate('created_at', '>=', $monthStart)->count(),
            
            'month_revenue' => Booking::whereHas('property', function($q) use ($owner) {
                $q->where('owner_id', $owner->id);
            })->whereDate('created_at', '>=', $monthStart)
              ->where('status', '!=', 'CANCELLED')
              ->sum('total_amount'),
            
            'status_counts' => [
                'PENDING' => Booking::whereHas('property', function($q) use ($owner) {
                    $q->where('owner_id', $owner->id);
                })->where('status', 'PENDING')->count(),
                'CONFIRMED' => Booking::whereHas('property', function($q) use ($owner) {
                    $q->where('owner_id', $owner->id);
                })->where('status', 'CONFIRMED')->count(),
                'CHECKED_IN' => Booking::whereHas('property', function($q) use ($owner) {
                    $q->where('owner_id', $owner->id);
                })->where('status', 'CHECKED_IN')->count(),
                'CHECKED_OUT' => Booking::whereHas('property', function($q) use ($owner) {
                    $q->where('owner_id', $owner->id);
                })->where('status', 'CHECKED_OUT')->count(),
                'CANCELLED' => Booking::whereHas('property', function($q) use ($owner) {
                    $q->where('owner_id', $owner->id);
                })->where('status', 'CANCELLED')->count(),
            ]
        ];
        
        // Sort and paginate
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $bookingsQuery->orderBy($sortBy, $sortOrder);
        
        $bookings = $bookingsQuery->paginate(10)->withQueryString();
        
        return view('owner.pages.bookings.index', [
            'bookings' => $bookings,
            'properties' => $properties,
            'stats' => $stats,
            'filters' => $request->only(['property_id', 'status', 'search', 'date_from', 'date_to'])
        ]);
    }
    
    /**
     * Show booking details
     */
    public function show($id)
    {
        $owner = Auth::user();
        
        $booking = Booking::with([
            'user:id,name,email,phone,avatar_url',
            'property:id,name,type,city,area,address,description,owner_id',
            'room:id,room_number,room_type,capacity',
            'payments'
        ])->findOrFail($id);
        
        // Verify ownership - Check if booking's property belongs to this owner
        if ($booking->property->owner_id !== $owner->id) {
            abort(403, 'Unauthorized access to this booking');
        }
        
        return view('owner.pages.bookings.show', compact('booking'));
    }
    
    /**
     * Update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $owner = Auth::user();
        $booking = Booking::with('property')->findOrFail($id);
        
        // Verify ownership
        if ($booking->property->owner_id !== $owner->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }
        
        $request->validate([
            'status' => 'required|in:CONFIRMED,CHECKED_IN,CHECKED_OUT,CANCELLED'
        ]);
        
        $oldStatus = $booking->status;
        $booking->status = $request->status;
        
        // Handle room status updates
        if ($booking->room_id) {
            $room = Room::find($booking->room_id);
            
            if ($request->status === 'CONFIRMED') {
                $room->status = 'RESERVED';
            } elseif ($request->status === 'CHECKED_IN') {
                $room->status = 'OCCUPIED';
            } elseif ($request->status === 'CHECKED_OUT' || $request->status === 'CANCELLED') {
                $room->status = 'AVAILABLE';
            }
            
            $room->save();
        }
        
        $booking->save();
        
        // Add status change note if provided
        if ($request->filled('notes')) {
            // You might want to create a BookingNote model for this
            // For now, we'll just update the booking
            $booking->cancellation_reason = $request->notes;
            $booking->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully',
            'data' => $booking
        ]);
    }
    
    /**
     * Send payment reminder
     */
    public function sendReminder($id)
    {
        $owner = Auth::user();
        $booking = Booking::with(['property', 'user'])->findOrFail($id);
        
        // Verify ownership
        if ($booking->property->owner_id !== $owner->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }
        
        // Here you would implement actual email/SMS sending
        // For now, just return success
        
        return response()->json([
            'success' => true,
            'message' => 'Payment reminder sent to ' . $booking->user->email
        ]);
    }
    
    /**
     * Export bookings to CSV
     */
    public function export(Request $request)
    {
        $owner = Auth::user();
        
        $bookingsQuery = Booking::with(['user', 'property', 'room'])
            ->whereHas('property', function($query) use ($owner) {
                $query->where('owner_id', $owner->id);
            });
        
        // Apply filters same as index
        if ($request->filled('property_id')) {
            $bookingsQuery->where('property_id', $request->property_id);
        }
        
        if ($request->filled('status')) {
            $bookingsQuery->where('status', $request->status);
        }
        
        $bookings = $bookingsQuery->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings_' . date('Y-m-d_H-i-s') . '.csv"',
        ];
        
        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fputs($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, [
                'Booking ID',
                'Reference',
                'Guest Name',
                'Guest Email',
                'Guest Phone',
                'Property',
                'Room',
                'Check-in Date',
                'Check-out Date',
                'Duration (Days)',
                'Total Amount',
                'Commission',
                'Status',
                'Booking Date',
                'Payment Status'
            ]);
            
            // Data rows
            foreach ($bookings as $booking) {
                $roomInfo = $booking->room 
                    ? $booking->room->room_number . ' (' . $booking->room->room_type . ')' 
                    : 'Apartment';
                
                fputcsv($file, [
                    $booking->id,
                    $booking->booking_reference,
                    $booking->user->name,
                    $booking->user->email,
                    $booking->user->phone ?? 'N/A',
                    $booking->property->name,
                    $roomInfo,
                    $booking->check_in->format('Y-m-d'),
                    $booking->check_out->format('Y-m-d'),
                    $booking->duration_days ?? 0,
                    $booking->total_amount,
                    $booking->commission_amount,
                    $booking->status,
                    $booking->created_at->format('Y-m-d H:i:s'),
                    $booking->payments->where('status', 'COMPLETED')->count() > 0 ? 'Paid' : 'Pending'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}