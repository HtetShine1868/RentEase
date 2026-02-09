<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Complaint;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplaintPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the complaint.
     */
    public function view(User $user, Complaint $complaint): bool
    {
        // Owner can only view complaints related to their properties
        if ($complaint->complaint_type === 'PROPERTY') {
            $booking = $complaint->related;
            if ($booking && $booking->property) {
                return $booking->property->owner_id == $user->id;
            }
        }
        
        return false;
    }

    /**
     * Determine if the user can reply to the complaint.
     */
    public function reply(User $user, Complaint $complaint): bool
    {
        return $this->view($user, $complaint) && 
               in_array($complaint->status, ['OPEN', 'IN_PROGRESS']);
    }

    /**
     * Determine if the user can update the complaint status.
     */
    public function update(User $user, Complaint $complaint): bool
    {
        return $this->view($user, $complaint);
    }
}