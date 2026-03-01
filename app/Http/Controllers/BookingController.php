<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display pending bookings for owner's properties
     */
    public function index(Request $request)
    {
        $ownerId = Auth::id();
        
        $query = Booking::with(['user', 'property'])
            ->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by property
        if ($request->has('property_id') && $request->property_id != 'all') {
            $query->where('property_id', $request->property_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get owner's properties for filter dropdown
        $properties = Property::where('owner_id', $ownerId)->get();

        // Stats
        $stats = [
            'pending' => Booking::whereHas('property', fn($q) => $q->where('owner_id', $ownerId))
                ->where('status', Booking::STATUS_PENDING)->count(),
            'approved' => Booking::whereHas('property', fn($q) => $q->where('owner_id', $ownerId))
                ->where('status', Booking::STATUS_APPROVED)->count(),
            'confirmed' => Booking::whereHas('property', fn($q) => $q->where('owner_id', $ownerId))
                ->where('status', Booking::STATUS_CONFIRMED)->count(),
            'rejected' => Booking::whereHas('property', fn($q) => $q->where('owner_id', $ownerId))
                ->where('status', Booking::STATUS_REJECTED)->count(),
        ];

        return view('owner.bookings.index', compact('bookings', 'properties', 'stats'));
    }

    /**
     * Show booking details for owner
     */
    public function show($id)
    {
        $booking = Booking::with(['user', 'property', 'room'])
            ->whereHas('property', fn($q) => $q->where('owner_id', Auth::id()))
            ->findOrFail($id);

        // Get competing requests for the same property/dates
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

        return view('owner.bookings.show', compact('booking', 'competingRequests'));
    }

    /**
     * Approve a booking request
     */
    public function approve(Request $request, $id)
    {
        $booking = Booking::with('property')
            ->whereHas('property', fn($q) => $q->where('owner_id', Auth::id()))
            ->findOrFail($id);

        if ($booking->status !== Booking::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'This booking request has already been processed.'
            ], 422);
        }

        $request->validate([
            'owner_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Check if property is still available
            $hasConfirmedBooking = Booking::where('property_id', $booking->property_id)
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
                return response()->json([
                    'success' => false,
                    'message' => 'This property is no longer available for the selected dates.'
                ], 422);
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
                'Booking Approved - Payment Required',
                "Your booking request for {$booking->property->name} has been approved! Please complete payment within 24 hours to confirm your booking.",
                'booking',
                $booking->id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking approved successfully. The guest has been notified to make payment.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a booking request
     */
    public function reject(Request $request, $id)
    {
        $booking = Booking::with('property')
            ->whereHas('property', fn($q) => $q->where('owner_id', Auth::id()))
            ->findOrFail($id);

        if ($booking->status !== Booking::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'This booking request has already been processed.'
            ], 422);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
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
                'Booking Request Rejected',
                "Your booking request for {$booking->property->name} has been rejected. Reason: {$request->rejection_reason}",
                'booking',
                $booking->id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking rejected successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject booking: ' . $e->getMessage()
            ], 500);
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
     * Send notification
     */
    private function sendNotification($userId, $title, $message, $type, $referenceId)
    {
        Notification::create([
            'user_id' => $userId,
            'type' => strtoupper($type),
            'title' => $title,
            'message' => $message,
            'related_entity_type' => $type,
            'related_entity_id' => $referenceId,
            'is_read' => false,
        ]);
    }
}