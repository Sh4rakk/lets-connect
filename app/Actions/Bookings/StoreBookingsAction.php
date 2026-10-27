<?php

declare(strict_types=1);

namespace App\Actions\Bookings;

use App\Http\Requests\Bookings\StoreBookingsRequest;
use App\Http\Resources\BookingsResource;
use App\Mail\SendMail;
use App\Models\Bookings;
use App\Models\Setting;
use App\Models\WorkshopMoment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use SingleQuote\LaravelApiResource\Infra\Action;
use Throwable;

final class StoreBookingsAction extends Action
{
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

                if ($otherBookings >= $workshopMoment->workshop->capacity) {
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

        try {
            Mail::to($student->email)->send(new SendMail("Let's Connect", 'test'));
        } catch (Throwable $e) {
            // net als vroeger: als de mail mislukt is de keuze wel opgeslagen
            report($e);
        }

        $bookings = $student->bookings()->with('workshopMoments.workshop', 'workshopMoments.moment')->get();

        return $this->statusCode(201)->withData(BookingsResource::collection($bookings))->result();
    }
}
