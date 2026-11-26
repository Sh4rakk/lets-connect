<?php

declare(strict_types=1);

namespace App\Actions\Statistics;

use App\Http\Resources\UserResource;
use App\Http\Resources\WorkshopResource;
use App\Models\Bookings;
use App\Models\Moment;
use App\Models\WorkshopMoment;
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
            'total_students' => max(0, User::count() - 2),
            'total_bookings' => Bookings::distinct('student_id')->count('student_id'),
            'total_ghosts' => User::whereNotNull('login_code_hash')->count(),
            'no_workshops' => max(0, User::whereDoesntHave('bookings')->count() - 2),
            'students_without_workshops' => UserResource::collection($studentsWithoutWorkshops),
            'rounds' => $this->rounds(),
        ])->result();
    }

    /**
     * Per ronde hoeveel plekken er bezet zijn, voor de balkjes op het dashboard.
     *
     * @return array
     */
    private function rounds(): array
    {
        $workshopMoments = WorkshopMoment::with('workshop')->withCount('bookings')->get()
            ->filter(fn (WorkshopMoment $wm) => $wm->workshop);

        return Moment::orderBy('id')->get()->values()->map(function (Moment $moment, int $i) use ($workshopMoments) {
            $inRound = $workshopMoments->where('moment_id', $moment->id);

            return [
                'id' => $moment->id,
                'number' => $i + 1,
                'time' => $moment->time,
                'workshops' => $inRound->count(),
                'booked' => (int) $inRound->sum('bookings_count'),
                'capacity' => (int) $inRound->sum(fn (WorkshopMoment $wm) => $wm->effectiveCapacity()),
                'full' => $inRound->filter(fn (WorkshopMoment $wm) => $wm->effectiveCapacity() > 0 && $wm->bookings_count >= $wm->effectiveCapacity())->count(),
            ];
        })->all();
    }
}
