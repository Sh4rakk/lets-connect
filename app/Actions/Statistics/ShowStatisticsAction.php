<?php

declare(strict_types=1);

namespace App\Actions\Statistics;

use App\Http\Resources\UserResource;
use App\Http\Resources\WorkshopResource;
use App\Models\Bookings;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\JsonResponse;
use SingleQuote\LaravelApiResource\Infra\Action;

final class ShowStatisticsAction extends Action
{
    /**
     * De cijfers voor de info widget op het admin dashboard.
     * Dezelfde berekeningen als de oude InformationController.
     *
     * @return JsonResponse|array
     */
    public function handle(): JsonResponse|array
    {
        // de eerste 2 gebruikers zijn de admin accounts uit de seeder
        $studentsWithoutWorkshops = User::whereDoesntHave('bookings')
            ->orderBy('created_at')
            ->get(['id', 'name', 'class', 'email'])
            ->slice(2)
            ->values();

        return $this->withData([
            'latest_user' => new UserResource(User::latest()->first()),
            'popular_workshop' => new WorkshopResource(Workshop::withCount('bookings')->orderBy('bookings_count', 'desc')->first()),
            'total_students' => User::count() - 2,
            'total_bookings' => Bookings::distinct('student_id')->count('student_id'),
            'total_ghosts' => User::whereNotNull('login_code_hash')->count(),
            'no_workshops' => User::whereDoesntHave('bookings')->count() - 2,
            'students_without_workshops' => UserResource::collection($studentsWithoutWorkshops),
        ])->result();
    }
}
