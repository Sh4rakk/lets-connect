<?php

declare(strict_types=1);

namespace App\Actions\Bookings;

use App\Http\Requests\Bookings\StoreBookingsRequest;
use App\Http\Resources\BookingsResource;
use App\Models\Bookings;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkshopMoment;
use App\Services\BookingConfirmationMailer;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SingleQuote\LaravelApiResource\Infra\Action;

final class StoreBookingsAction extends Action
{
    public function __construct(private BookingConfirmationMailer $mailer)
    {
    }

    /**
     * Slaat de 3 workshopkeuzes van de ingelogde student op (1 per ronde).
     *
     * @param StoreBookingsRequest $request
     * @return JsonResponse
     */
    public function handle(StoreBookingsRequest $request): JsonResponse
    {
        if (!Setting::signupsOpen()) {
            return $this->statusCode(403)->withData(['message' => 'De inschrijvingen zijn gesloten.'])->result(false);
        }

        $student = $request->user();
        $workshopIdsByRound = $request->validated('rounds');

        // 1 opslaan per student tegelijk: dubbel klikken zou anders dubbele
        // boekingen en een tweede bevestigingsmail kunnen geven
        try {
            Cache::lock('booking:' . $student->id, 10)->block(5, function () use ($student, $workshopIdsByRound) {
                $this->saveRounds($student, $workshopIdsByRound);
            });
        } catch (LockTimeoutException) {
            return $this->statusCode(409)->withData(['message' => 'Je keuzes worden al opgeslagen, probeer het zo nog eens.'])->result(false);
        }

        // de mail pas versturen nadat de student het antwoord heeft, SMTP kan seconden duren
        // en dan leek de site te bevriezen. Stuurt alleen een mail als de keuzes anders zijn
        // dan de vorige bevestiging, dus dubbel opslaan geeft geen tweede mail.
        $mailer = $this->mailer;
        app()->terminating(fn () => $mailer->send($student));

        $bookings = $student->bookings()->with('workshopMoments.workshop', 'workshopMoments.moment')->get();

        return $this->statusCode(201)->withData(BookingsResource::collection($bookings))->result();
    }

    /**
     * @param User $student
     * @param array<int, int> $workshopIdsByRound
     * @return void
     */
    private function saveRounds(User $student, array $workshopIdsByRound): void
    {
        DB::transaction(function () use ($student, $workshopIdsByRound) {
            foreach ($workshopIdsByRound as $round => $workshopId) {
                // lockForUpdate zodat twee studenten niet tegelijk de laatste plek pakken
                $workshopMoment = WorkshopMoment::with('workshop')
                    ->where('workshop_id', $workshopId)
                    ->where('moment_id', $round)
                    ->lockForUpdate()
                    ->first();

                if (!$workshopMoment) {
                    throw ValidationException::withMessages([
                        "rounds.$round" => "Deze workshop is er niet in ronde $round.",
                    ]);
                }

                // de eigen boeking van de student telt niet mee voor de capaciteit
                $otherBookings = Bookings::where('wm_id', $workshopMoment->id)
                    ->where('student_id', '!=', $student->id)
                    ->count();

                if ($otherBookings >= $workshopMoment->effectiveCapacity()) {
                    throw ValidationException::withMessages([
                        "rounds.$round" => "Workshop {$workshopMoment->workshop->name} zit vol in ronde $round.",
                    ]);
                }

                // de bestaande boeking van deze ronde zoeken via de ronde, niet via het id
                $existingBooking = Bookings::where('student_id', $student->id)
                    ->whereRelation('workshopMoments', 'moment_id', $round)
                    ->first();

                if ($existingBooking) {
                    $existingBooking->update(['wm_id' => $workshopMoment->id]);
                } else {
                    Bookings::create(['wm_id' => $workshopMoment->id, 'student_id' => $student->id]);
                }
            }
        });
    }
}
