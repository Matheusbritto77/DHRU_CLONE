<?php

namespace App\Policies;

use App\Models\User;

class PulseAccessPolicy
{
    public function view(?User $user = null): bool
    {
        return $user?->can('view-admin') ?? false;
    }
}
