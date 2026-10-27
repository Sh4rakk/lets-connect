<?php

namespace App\Policies;

use App\Models\User;

class WorkshopPolicy
{
    // iedereen die ingelogd is mag de workshops zien
    public function viewAny(User $user): bool
    {
        return true;
    }
}
