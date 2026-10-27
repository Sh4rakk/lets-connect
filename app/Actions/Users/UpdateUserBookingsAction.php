<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Http\Requests\Users\UpdateUserBookingsRequest;
use App\Http\Resources\BookingsResource;
use App\Mail\ChoiceChangedMail;
use App\Models\Bookings;
use App\Models\User;
use App\Models\WorkshopMoment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use SingleQuote\LaravelApiResource\Infra\Action;

final class UpdateUserBookingsAction extends Action
{
    /**
     * Admin past de workshops van een student aan (per ronde een workshop moment of leeg).
     *
     * @param UpdateUserBookingsRequest $request
     * @param User $user
     * @return JsonResponse|RedirectResponse
     */
    public function handle(UpdateUserBookingsRequest $request, User $user): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

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
                    throw ValidationException::withMessages([
                        'rounds.' . $round => 'De gekozen workshop hoort niet bij deze ronde.',
                    ]);
                }
            }
        }

        $changes = [];

        DB::transaction(function () use ($user, $selectedByRound, $selectedIds, &$changes) {
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
                    // Fokke: Er is een bestaande boeking. Werk deze bij als de selectie is gewijzigd en de verschillen meesturen.
                    if ((int) $existingBooking->wm_id !== (int) $selectedWmId) {
                        $old = WorkshopMoment::with('workshop')->find($existingBooking->wm_id);
                        $new = WorkshopMoment::with('workshop')->find($selectedWmId);

                        $changes[$round] = [
                            'round' => $round,
                            'old_workshop_name' => $old?->workshop?->name ?? 'Onbekend',
                            'old_workshop_location' => $old?->workshop?->location ?? 'Onbekend',
                            'new_workshop_name' => $new?->workshop?->name ?? 'Onbekend',
                            'new_workshop_location' => $new?->workshop?->location ?? 'Onbekend',
                        ];

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

        $currentBookings = [];
        $allBookings = Bookings::with(['workshopMoments.workshop', 'workshopMoments.moment'])
            ->where('student_id', $user->id)
            ->get();

        foreach ($allBookings as $booking) {
            $workshopMoment = $booking->workshopMoments;
            if ($workshopMoment && $workshopMoment->moment) {
                $round = (int) $workshopMoment->moment_id;
                if ($round >= 1 && $round <= 3) {
                    $currentBookings[$round] = [
                        'workshop_name' => $workshopMoment->workshop?->name ?? 'Onbekend',
                        'workshop_location' => $workshopMoment->workshop?->location ?? 'Onbekend',
                        'time' => $workshopMoment->moment?->time ?? 'Onbekend',
                    ];
                }
            }
        }

        if (!empty($changes)) {
            Mail::to($user->email)->send(new ChoiceChangedMail($user->name, $changes, $currentBookings));
        }

        $bookings = $user->bookings()->with('workshopMoments.workshop', 'workshopMoments.moment')->get();

        return $this->withData(BookingsResource::collection($bookings))->result();
    }
}
