<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can access administrative areas.
     */
    public function accessAdmin(User $user): bool
    {
        return (bool) $user->id_admin;
    }
}
