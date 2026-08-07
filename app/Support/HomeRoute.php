<?php

namespace App\Support;

use App\Models\User;

class HomeRoute
{
    public static function for(?User $user): string
    {
        if ($user === null) {
            return route('login');
        }

        if ($user->hasRole('Registration Staff')) {
            return route('dashboard');
        }

        return route('dashboard');
    }
}
