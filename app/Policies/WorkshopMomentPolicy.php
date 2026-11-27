<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkshopMoment;

class WorkshopMomentPolicy
{
    // studenten hebben de capaciteit per ronde nodig
    public function viewAny(User $user): bool
    {
        return true;
    }

    // de boekingen (met namen van studenten) zijn alleen voor admins
    public function view(User $user, WorkshopMoment $workshopMoment): bool
    {
        return $user->hasRole('admin');
    }

    public function manage(User $user, WorkshopMoment $workshopMoment): bool
    {
        return $user->hasRole('admin') || $workshopMoment->workshop?->leader_id === $user->id;
    }
}
