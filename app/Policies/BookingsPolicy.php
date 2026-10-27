<?php

namespace App\Policies;

use App\Models\User;

class BookingsPolicy
{
    // elke ingelogde student mag zijn eigen workshops kiezen
    public function create(User $user): bool
    {
        return true;
    }
}
