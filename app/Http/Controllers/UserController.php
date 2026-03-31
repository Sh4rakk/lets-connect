<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Bookings;
use App\Models\WorkshopMoment;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Fokke: Haalt alle gebruikers op en filtert ze op basis van de request.
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        if ($request->filled('class')) {
            $query->where('class', 'like', '%' . $request->input('class') . '%');
        }

        $users = $query->get();

        return view('dashboard.students-overview')->with([
            'users' => $users,
            'filters' => [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'class' => $request->input('class'),
            ]
        ]);
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


    public function updateBookings(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'rounds.1' => ['nullable', 'integer', 'exists:workshop_moments,id'],
            'rounds.2' => ['nullable', 'integer', 'exists:workshop_moments,id'],
            'rounds.3' => ['nullable', 'integer', 'exists:workshop_moments,id'],
        ]);

        $selectedByRound = [
            1 => $validated['rounds'][1] ?? null,
            2 => $validated['rounds'][2] ?? null,
            3 => $validated['rounds'][3] ?? null,
        ];

        // Fokke: filtert de null eruit, list met selected teurg.
        $selectedIds = array_values(array_filter($selectedByRound));

        // Fokke: check of workshop moment bij de ronde hoort.
        if (!empty($selectedIds)) {
            $selectedWorkshopMoments = WorkshopMoment::whereIn('id', $selectedIds)->get()->keyBy('id');

            foreach ($selectedByRound as $round => $wmId) {
                if (!$wmId) {
                    continue;
                }

                $workshopMoment = $selectedWorkshopMoments->get((int) $wmId);

                if (!$workshopMoment || (int) $workshopMoment->moment_id !== $round) {
                    return back()
                        ->withErrors(['rounds.' . $round => 'De gekozen workshop hoort niet bij deze ronde.'])
                        ->withInput();
                }
            }
        }

        DB::transaction(function () use ($user, $selectedByRound, $selectedIds) {
            $existingBookings = Bookings::with('workshopMoments')
                ->where('student_id', $user->id)
                ->lockForUpdate()
                ->get();

            $existingByRound = [1 => null, 2 => null, 3 => null];
            $duplicates = [];

            // Fokke: Verdeelt bestaande boekingen over rondes en identificeert eventuele dubbele boekingen (meer dan één per ronde).
            foreach ($existingBookings as $booking) {
                $round = (int) ($booking->workshopMoments->moment_id ?? 0);

                if ($round < 1 || $round > 3) {
                    continue;
                }

                if ($existingByRound[$round] === null) {
                    $existingByRound[$round] = $booking;
                    continue;
                }

                $duplicates[] = $booking;
            }

            // Fokke: Capaciteitscontrole voor de geselecteerde workshops.
            if (!empty($selectedIds)) {
                // Fokke: Lock ook de geselecteerde workshopmomenten om hun capaciteitsgegevens te beschermen.
                $lockedWorkshopMoments = WorkshopMoment::with('workshop')
                    ->whereIn('id', $selectedIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Fokke: Telt het huidige aantal boekingen voor de geselecteerde workshops. Ook gelockt.
                $currentCounts = Bookings::query()
                    ->select('wm_id', DB::raw('COUNT(*) as total'))
                    ->whereIn('wm_id', $selectedIds)
                    ->lockForUpdate()
                    ->groupBy('wm_id')
                    ->pluck('total', 'wm_id');

                foreach ([1, 2, 3] as $round) {
                    $selectedWmId = $selectedByRound[$round];

                    if (!$selectedWmId) {
                        continue;
                    }

                    $workshopMoment = $lockedWorkshopMoments->get((int) $selectedWmId);

                    if (!$workshopMoment || !$workshopMoment->workshop) {
                        // Fokke: Gooit een ValidationException als de workshop niet (meer) bestaat. De transactie wordt hierdoor teruggedraaid.
                        throw ValidationException::withMessages([
                            'rounds.' . $round => 'De gekozen workshop is niet beschikbaar.',
                        ]);
                    }

                    $existingBooking = $existingByRound[$round];
                    $isSameBooking = $existingBooking && (int) $existingBooking->wm_id === (int) $selectedWmId;
                    $currentCount = (int) ($currentCounts[(int) $selectedWmId] ?? 0);
                    // Fokke: Berekent het verwachte aantal boekingen. Als de boeking niet verandert, telt deze niet als nieuw.
                    $projectedCount = $currentCount + ($isSameBooking ? 0 : 1);
                    $capacity = (int) $workshopMoment->workshop->capacity;

                    if ($projectedCount > $capacity) {
                        // Fokke: Gooit een exception als de capaciteit wordt overschreden.
                        throw ValidationException::withMessages([
                            'rounds.' . $round => 'Deze workshop is vol voor ronde ' . $round . '.',
                        ]);
                    }
                }
            }

            // Fokke: Verwijder de geïdentificeerde dubbele boekingen.
            foreach ($duplicates as $duplicate) {
                $duplicate->delete();
            }

            // Fokke: Verwerkt de updates, creaties en verwijderingen.
            foreach ([1, 2, 3] as $round) {
                $selectedWmId = $selectedByRound[$round];
                $existingBooking = $existingByRound[$round];

                if (!$selectedWmId) {
                    // Fokke: Geen selectie voor deze ronde, dus verwijder de bestaande boeking indien aanwezig.
                    if ($existingBooking) {
                        $existingBooking->delete();
                    }
                    continue;
                }

                if ($existingBooking) {
                    // Fokke: Er is een bestaande boeking. Werk deze bij als de selectie is gewijzigd.
                    if ((int) $existingBooking->wm_id !== (int) $selectedWmId) {
                        $existingBooking->wm_id = $selectedWmId;
                        $existingBooking->save();
                    }
                    continue;
                }

                // Fokke: Geen bestaande boeking, dus maak een nieuwe aan.
                Bookings::create([
                    'student_id' => $user->id,
                    'wm_id' => $selectedWmId,
                ]);
            }
        });

        // Fokke: Redirect terug naar de edit-pagina met een succesbericht.
        return redirect()
            ->route('edit-student', $user->id)
            ->with('status', 'success')
            ->with('message', 'Inschrijvingen bijgewerkt.');
    }
}
