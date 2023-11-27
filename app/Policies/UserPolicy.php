<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $loggedInUser, User $userToDelete)
    {
        // Check if the logged-in user is trying to delete their own account
        return $loggedInUser->id !== $userToDelete->id;
    }
}
