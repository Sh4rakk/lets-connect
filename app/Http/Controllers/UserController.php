<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkshopMoment;

class UserController extends Controller
{
    // de studenten zelf worden door de pagina opgehaald via /api/users
    public function index()
    {
        return view('dashboard.students-overview');
    }

    public function edit($id)
    {
        // findOrFail stopt de uitvoering en geeft een 404-pagina als het model niet wordt gevonden.
        $user = User::with([
            'bookings.workshopMoments.workshop',
            'bookings.workshopMoments.moment',
        ])->findOrFail($id);

        // Fokke: array voor boekingen per ronde.
        $bookingsByRound = [1 => null, 2 => null, 3 => null];

        // Fokke: loop over de bookings van de gebruiker.
        foreach ($user->bookings as $booking) {
            $workshopMoment = $booking->workshopMoments;

            if (!$workshopMoment || !$workshopMoment->moment) {
                continue;
            }

            $round = (int) $workshopMoment->moment->id;

            // Fokke: filter bookings per ronde, geen dubbele boekingen per ronde.
            if ($round < 1 || $round > 3 || $bookingsByRound[$round] !== null) {
                continue;
            }

            $bookingsByRound[$round] = [
                'booking_id' => $booking->id,
                'wm_id' => $workshopMoment->id,
                'workshop' => $workshopMoment->workshop?->name ?? 'Onbekende workshop',
                'time' => $workshopMoment->moment->time,
            ];
        }

        $workshopOptionsByRound = [1 => [], 2 => [], 3 => []];

        // Fokke: Haalt alle workshopmomenten op.
        $workshopMoments = WorkshopMoment::with('workshop')
            ->whereIn('moment_id', [1, 2, 3])
            ->get();

        // Fokke: verbind de workshop moment id en workshop naam.
        foreach ($workshopMoments as $workshopMoment) {
            $round = (int) $workshopMoment->moment_id;

            if ($round < 1 || $round > 3) {
                continue;
            }

            $workshopOptionsByRound[$round][] = [
                'id' => $workshopMoment->id,
                'name' => $workshopMoment->workshop?->name ?? 'Onbekende workshop',
            ];
        }

        // Fokke: sorteert de workshopopties alfabetisch.
        foreach ($workshopOptionsByRound as $round => $options) {
            usort($options, fn ($a, $b) => strcmp($a['name'], $b['name']));
            $workshopOptionsByRound[$round] = $options;
        }

        return view('dashboard.student-edit')->with([
            'user' => $user,
            'bookingsByRound' => $bookingsByRound,
            'workshopOptionsByRound' => $workshopOptionsByRound,
        ]);
    }
}
