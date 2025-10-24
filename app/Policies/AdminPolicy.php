<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{
    public function accessAdminPanel(User $user)
    {
        return $user->role === 'admin';
    }
}