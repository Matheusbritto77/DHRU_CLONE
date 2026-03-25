<?php

namespace App\Policies;

use App\Models\User;

class HorizonAccessPolicy
{
    public function view(?User $user = null): bool
    {
        return $user?->can('view-admin') ?? false;
    }
}
