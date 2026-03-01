<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnerBookingController extends Controller
{
    /**
     * Display a listing of booking requests for owner's properties
     */
    public function index(Request $request)
    {
        $ownerId = Auth::id();
        
        $query = Booking::with(['user', 'property', 'room'])
            ->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });

        // Filter by status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by property
        if ($request->filled('property_id') && $request->property_id != 'all') {
            $query->where('property_id', $request->property_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('property', function($propQ) use ($search) {
                      $propQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->filled('date_range')) {
            $now = Carbon::now();
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', $now->today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
            }
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get owner's properties for filter dropdown
        $properties = Property::where('owner_id', $ownerId)->get();

        // Statistics
        $stats = [
            'pending' => Booking::whereHas('property', fn($q) => $q->where('owner_id', $ownerId))
                ->where('status', Booking::STATUS_PENDING)->count(),
            'approved' => Booking::whereHas('property', fn($q) => $q->where('owner_id', $ownerId))
                ->where('status', Booking::STATUS_APPROVED)->count(),
            'confirmed' => Booking::whereHas('property', fn($q) => $q->where('owner_id', $ownerId))
                ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])->count(),
            'rejected' => Booking::whereHas('property', fn($q) => $q->where('owner_id', $ownerId))
                ->where('status', Booking::STATUS_REJECTED)->count(),
        ];

        // Status counts for the current filter
        $baseQuery = Booking::whereHas('property', fn($q) => $q->where('owner_id', $ownerId));
        if ($request->filled('property_id') && $request->property_id != 'all') {
            $baseQuery->where('property_id', $request->property_id);
        }
        
        $statusCounts = [
            'pending' => (clone $baseQuery)->where('status', Booking::STATUS_PENDING)->count(),
            'approved' => (clone $baseQuery)->where('status', Booking::STATUS_APPROVED)->count(),
            'confirmed' => (clone $baseQuery)->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])->count(),
            'rejected' => (clone $baseQuery)->where('status', Booking::STATUS_REJECTED)->count(),
            'all' => (clone $baseQuery)->count(),
        ];

        return view('owner.bookings.index', compact(
            'bookings', 
            'properties', 
            'stats', 
            'statusCounts'
        ));
    }

    /**
     * Show booking details for owner
     */
    public function show($id)
    {
        $booking = Booking::with(['user', 'property', 'room', 'payments'])
            ->whereHas('property', fn($q) => $q->where('owner_id', Auth::id()))
            ->findOrFail($id);

        // Get competing requests for the same property/dates
        $competingRequests = collect();
        if ($booking->status === Booking::STATUS_PENDING) {
            $competingRequests = Booking::where('property_id', $booking->property_id)
                ->where('id', '!=', $booking->id)
                ->where('status', Booking::STATUS_PENDING)
                ->where(function($q) use ($booking) {
                    $q->whereBetween('check_in', [$booking->check_in, $booking->check_out])
                      ->orWhereBetween('check_out', [$booking->check_in, $booking->check_out])
                      ->orWhere(function($q2) use ($booking) {
                          $q2->where('check_in', '<=', $booking->check_in)
                             ->where('check_out', '>=', $booking->check_out);
                      });
                })
                ->with('user')
                ->get();
        }

        return view('owner.bookings.show', compact('booking', 'competingRequests'));
    }

    /**
     * Approve a booking request
     */
    public function approve(Request $request, $id)
    {
        $booking = Booking::with('property', 'user')
            ->whereHas('property', fn($q) => $q->where('owner_id', Auth::id()))
            ->findOrFail($id);

        if ($booking->status !== Booking::STATUS_PENDING) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking request has already been processed.'
                ], 422);
            }
            return back()->with('error', 'This booking request has already been processed.');
        }

        $request->validate([
            'owner_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Check if property is still available (no confirmed bookings for the dates)
            $hasConfirmedBooking = Booking::where('property_id', $booking->property_id)
                ->where('id', '!=', $booking->id)
                ->where(function($q) use ($booking) {
                    $q->whereBetween('check_in', [$booking->check_in, $booking->check_out])
                      ->orWhereBetween('check_out', [$booking->check_in, $booking->check_out])
                      ->orWhere(function($q2) use ($booking) {
                          $q2->where('check_in', '<=', $booking->check_in)
                             ->where('check_out', '>=', $booking->check_out);
                      });
                })
                ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])
                ->exists();

            if ($hasConfirmedBooking) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This property is no longer available for the selected dates.'
                    ], 422);
                }
                return back()->with('error', 'This property is no longer available for the selected dates.');
            }

            // Update booking status
            $booking->status = Booking::STATUS_APPROVED;
            $booking->owner_notes = $request->owner_notes;
            $booking->approved_at = now();
            $booking->save();

            // Reject all other pending requests for the same property/dates
            $this->rejectCompetingRequests($booking);

            // Notify user
            $this->sendNotification(
                $booking->user_id,
                'BOOKING_APPROVED',
                'Booking Request Approved - Payment Required',
                "Your booking request for {$booking->property->name} has been approved! Please complete payment within 24 hours to confirm your booking.",
                'booking',
                $booking->id
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking approved successfully. The guest has been notified.'
                ]);
            }

            return redirect()->route('owner.bookings.show', $booking)
                ->with('success', 'Booking approved successfully. The guest has been notified.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking approval error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve booking: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to approve booking: ' . $e->getMessage());
        }
    }

    /**
     * Reject a booking request
     */
    public function reject(Request $request, $id)
    {
        $booking = Booking::with('property', 'user')
            ->whereHas('property', fn($q) => $q->where('owner_id', Auth::id()))
            ->findOrFail($id);

        if ($booking->status !== Booking::STATUS_PENDING) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking request has already been processed.'
                ], 422);
            }
            return back()->with('error', 'This booking request has already been processed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $booking->status = Booking::STATUS_REJECTED;
            $booking->rejection_reason = $request->rejection_reason;
            $booking->rejected_at = now();
            $booking->save();

            // Notify user
            $this->sendNotification(
                $booking->user_id,
                'BOOKING_REJECTED',
                'Booking Request Rejected',
                "Your booking request for {$booking->property->name} has been rejected. Reason: {$request->rejection_reason}",
                'booking',
                $booking->id
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking rejected successfully.'
                ]);
            }

            return redirect()->route('owner.bookings.show', $booking)
                ->with('success', 'Booking rejected successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking rejection error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject booking: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to reject booking: ' . $e->getMessage());
        }
    }

    /**
     * Reject competing booking requests
     */
    private function rejectCompetingRequests($approvedBooking)
    {
        $competingRequests = Booking::where('property_id', $approvedBooking->property_id)
            ->where('id', '!=', $approvedBooking->id)
            ->where('status', Booking::STATUS_PENDING)
            ->where(function($q) use ($approvedBooking) {
                $q->whereBetween('check_in', [$approvedBooking->check_in, $approvedBooking->check_out])
                  ->orWhereBetween('check_out', [$approvedBooking->check_in, $approvedBooking->check_out])
                  ->orWhere(function($q2) use ($approvedBooking) {
                      $q2->where('check_in', '<=', $approvedBooking->check_in)
                         ->where('check_out', '>=', $approvedBooking->check_out);
                  });
            })
            ->get();

        foreach ($competingRequests as $request) {
            $request->status = Booking::STATUS_REJECTED;
            $request->rejection_reason = 'Another booking was approved for the same dates.';
            $request->rejected_at = now();
            $request->save();

            // Notify the rejected user
            $this->sendNotification(
                $request->user_id,
                'BOOKING_REJECTED',
                'Booking Request Rejected',
                "Your booking request for {$approvedBooking->property->name} has been rejected as another booking was approved for the same dates.",
                'booking',
                $request->id
            );
        }
    }

    /**
     * Get pending requests count for a property
     */
    public function getPendingCount($propertyId)
    {
        $count = Booking::where('property_id', $propertyId)
            ->where('status', Booking::STATUS_PENDING)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Export bookings to CSV
     */
    public function export(Request $request)
    {
        $ownerId = Auth::id();
        
        $query = Booking::with(['user', 'property', 'room'])
            ->whereHas('property', fn($q) => $q->where('owner_id', $ownerId));

        // Apply filters
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('property_id') && $request->property_id != 'all') {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('date_range')) {
            $now = Carbon::now();
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', $now->today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
            }
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $filename = 'bookings-export-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            'Booking Ref',
            'Date',
            'Customer',
            'Email',
            'Phone',
            'Property',
            'Room',
            'Check In',
            'Check Out',
            'Duration',
            'Total Amount',
            'Status',
            'Payment Status'
        ];

        $callback = function() use ($bookings, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_reference,
                    $booking->created_at->format('Y-m-d H:i'),
                    $booking->user->name ?? 'N/A',
                    $booking->user->email ?? 'N/A',
                    $booking->user->phone ?? 'N/A',
                    $booking->property->name,
                    $booking->room ? $booking->room->room_number : 'N/A',
                    Carbon::parse($booking->check_in)->format('Y-m-d'),
                    Carbon::parse($booking->check_out)->format('Y-m-d'),
                    $booking->duration_days . ' days',
                    $booking->total_amount,
                    $booking->status,
                    $booking->payments->where('status', 'COMPLETED')->count() > 0 ? 'Paid' : 'Unpaid'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send notification
     */
    private function sendNotification($userId, $type, $title, $message, $entityType, $entityId)
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'related_entity_type' => $entityType,
                'related_entity_id' => $entityId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification: ' . $e->getMessage());
        }
    }
}