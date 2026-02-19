<?php

namespace App\Traits;

use App\Models\Notification;

trait Notifiable
{
    /**
     * Create a notification
     */
    public function createNotification($userId, $type, $title, $message, $entityType = null, $entityId = null)
    {
        // Validate that type is one of the allowed ENUM values
        $allowedTypes = ['BOOKING', 'ORDER', 'PAYMENT', 'COMPLAINT', 'SYSTEM', 'MARKETING'];
        
        if (!in_array($type, $allowedTypes)) {
            $type = 'SYSTEM'; // Default to SYSTEM if invalid type
        }
        
        try {
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'related_entity_type' => $entityType,
                'related_entity_id' => $entityId,
                'is_read' => false,
                'channel' => 'IN_APP',
                'is_sent' => true,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return $notification;
            
        } catch (\Exception $e) {
            \Log::error("Notification creation failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send complaint notification
     */
    public function sendComplaintNotification($userId, $complaintReference, $status, $complaintId)
    {
        $statusText = ucfirst(strtolower($status));
        
        return $this->createNotification(
            $userId,
            'COMPLAINT',  // Using correct ENUM value
            "Complaint {$statusText}",
            "Your complaint #{$complaintReference} has been {$statusText}.",
            'complaint',
            $complaintId
        );
    }

    /**
     * Send system notification
     */
    public function sendSystemNotification($userId, $title, $message)
    {
        return $this->createNotification(
            $userId,
            'SYSTEM',  // Using correct ENUM value
            $title,
            $message,
            null,
            null
        );
    }

    /**
     * Send booking notification
     */
    public function sendBookingNotification($userId, $bookingReference, $status, $bookingId)
    {
        $statusText = ucfirst(strtolower($status));
        
        return $this->createNotification(
            $userId,
            'BOOKING',  // Using correct ENUM value
            "Booking {$statusText}",
            "Your booking #{$bookingReference} has been {$statusText}.",
            'booking',
            $bookingId
        );
    }

    /**
     * Send order notification
     */
    public function sendOrderNotification($userId, $orderReference, $status, $orderId, $type = 'FOOD')
    {
        $statusText = ucfirst(strtolower($status));
        $serviceType = $type === 'FOOD' ? 'Food' : 'Laundry';
        
        return $this->createNotification(
            $userId,
            'ORDER',  // Using correct ENUM value
            "{$serviceType} Order {$statusText}",
            "Your {$serviceType} order #{$orderReference} is now {$statusText}.",
            $type === 'FOOD' ? 'food_order' : 'laundry_order',
            $orderId
        );
    }

    /**
     * Send payment notification
     */
    public function sendPaymentNotification($userId, $amount, $status, $paymentId)
    {
        $statusText = ucfirst(strtolower($status));
        
        return $this->createNotification(
            $userId,
            'PAYMENT',  // Using correct ENUM value
            "Payment {$statusText}",
            "Your payment of ₹{$amount} has been {$statusText}.",
            'payment',
            $paymentId
        );
    }
}