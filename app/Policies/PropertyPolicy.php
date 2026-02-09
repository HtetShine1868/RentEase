<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Property;

class PropertyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('OWNER') || $user->hasRole('SUPERADMIN');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Property $property): bool
    {
        return $property->owner_id === $user->id || $user->hasRole('SUPERADMIN');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('OWNER') || $user->hasRole('SUPERADMIN');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Property $property): bool
    {
        return $property->owner_id === $user->id || $user->hasRole('SUPERADMIN');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Property $property): bool
    {
        // Only allow deletion if property is in draft or inactive status
        if (!in_array($property->status, ['DRAFT', 'INACTIVE'])) {
            return false;
        }
        
        return $property->owner_id === $user->id || $user->hasRole('SUPERADMIN');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Property $property): bool
    {
        return $user->hasRole('SUPERADMIN');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Property $property): bool
    {
        return $user->hasRole('SUPERADMIN');
    }
}