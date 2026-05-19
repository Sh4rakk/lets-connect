<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class InformationController extends Controller
{
    public function latestRecord(): ?User
    {
        return User::latest()->first();
    }

    public function popularWorkshop(): ?Workshop
    {
        return Workshop::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->first();
    }

    public function totalStudents(): int
    {
        return User::count()-2;
    }

    public function totalBookings(): int
    {
        return Bookings::distinct('student_id')->count('student_id');
    }

    public function totalGhosts(): int
    {
        $ghostUser = User::whereNotNull('login_code_hash')->count();
        if ($ghostUser > 0) {
            return $ghostUser;
        } else {
            return 0;
        }
    }

    public function noWorkshops(): int
    {
        return User::whereDoesntHave('bookings')->count()-2;
    }

    public function studentsWithoutWorkshops(): SupportCollection
    {
        return User::whereDoesntHave('bookings')->orderBy('created_at')->get(['id', 'name', 'class', 'email'])->slice(2)->values();
    }
}


