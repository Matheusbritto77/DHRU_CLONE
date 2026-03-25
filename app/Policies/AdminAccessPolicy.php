<?php

namespace App\Policies;

use App\Models\User;

class AdminAccessPolicy
{
    public function view(User $user): bool
    {
        return (bool) $user->id_admin;
    }
}
