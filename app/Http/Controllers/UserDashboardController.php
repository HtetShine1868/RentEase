<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get user stats
        $stats = [
            'active_bookings' => Booking::where('user_id', $user->id)
                ->whereIn('status', ['PENDING', 'CONFIRMED', 'CHECKED_IN'])
                ->count(),
            'pending_orders' => 0, // Add your order logic here
            'total_spent' => Payment::where('user_id', $user->id)
                ->where('status', 'COMPLETED')
                ->sum('amount'),
            'avg_rating' => $this->getUserAverageRating($user->id),
            'total_reviews' => $this->getUserTotalReviews($user->id)
        ];

        // Get recent activities (combine from multiple sources)
        $recentActivities = $this->getRecentActivities($user->id);

        // Get upcoming bookings
        $upcomingBookings = Booking::with(['property', 'room'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['CONFIRMED', 'PENDING'])
            ->where('check_in', '>=', now())
            ->orderBy('check_in')
            ->limit(5)
            ->get();

        // Get user addresses
        $userAddresses = $user->addresses ?? [];

        // Get chat conversations
        $conversations = ChatConversation::whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with([
                'participants.user', 
                'property', 
                'messages' => function($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get first conversation for active chat (if exists)
        $activeConversation = $conversations->first();

        return view('dashboard.user', compact(
            'stats', 
            'recentActivities', 
            'upcomingBookings', 
            'userAddresses',
            'conversations',
            'activeConversation'
        ));
    }

    private function getUserAverageRating($userId)
    {
        // Implement your rating logic here
        // This is just a placeholder
        return 4.5;
    }

    private function getUserTotalReviews($userId)
    {
        // Implement your reviews count logic here
        // This is just a placeholder
        return 0;
    }

    private function getRecentActivities($userId)
    {
        $activities = collect();
        
        // Get recent bookings
        $recentBookings = Booking::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($booking) {
                return (object) [
                    'type' => 'BOOKING',
                    'title' => 'New Booking',
                    'message' => "Booked {$booking->property->name} from " . $booking->check_in->format('M d') . " to " . $booking->check_out->format('M d'),
                    'created_at' => $booking->created_at,
                    'related_entity_type' => 'booking',
                    'related_entity_id' => $booking->id
                ];
            });

        // Get recent payments
        $recentPayments = Payment::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($payment) {
                return (object) [
                    'type' => 'PAYMENT',
                    'title' => 'Payment Made',
                    'message' => "Payment of ৳" . number_format($payment->amount) . " completed",
                    'created_at' => $payment->created_at,
                    'related_entity_type' => 'payment',
                    'related_entity_id' => $payment->id
                ];
            });

        // Get recent messages
        $recentMessages = ChatConversation::whereHas('participants', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->pluck('messages')
            ->flatten()
            ->sortByDesc('created_at')
            ->take(5)
            ->map(function($message) {
                return (object) [
                    'type' => 'MESSAGE',
                    'title' => 'New Message',
                    'message' => "Message from: " . ($message->sender->name ?? 'Unknown'),
                    'created_at' => $message->created_at,
                    'related_entity_type' => 'chat',
                    'related_entity_id' => $message->conversation_id
                ];
            });

        // Merge and sort all activities
        $activities = $recentBookings->concat($recentPayments)
            ->concat($recentMessages)
            ->sortByDesc('created_at')
            ->take(10);

        return $activities;
    }
}