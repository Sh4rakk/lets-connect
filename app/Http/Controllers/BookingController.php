<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use App\Models\Bookings;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function bookWorkshop(Request $request)
    {
        $signupsOpen = Setting::where('key', 'signups_open')->first();
        $isOpen = $signupsOpen && $signupsOpen->value == '1';

        if (!$isOpen) {
            return view('auth.signups-closed');
        }

        // Retrieve submitted workshop values (UUID preferred, legacy names supported).
        $submittedValues = $request->only(['save1', 'save2', 'save3']);

        $workshops = [];
        foreach ($submittedValues as $index => $submittedValue) {
            $workshop = $this->resolveWorkshop($submittedValue);
            if (!$workshop) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Ongeldige workshop selectie voor {$index}."
                ], 422);
            }
            $workshops[] = $workshop;
        }


        // Initialize the error message
        $errormessage = "";

        // Loop through the workshops and check if there are available spots
        foreach ($workshops as $index => $workshop) {
            // Get the workshop moment for the current index
            $wm = $this->getWorkshopMoment($workshop->id, $index + 1);
            // Check if the workshop moment has available spots
            if (!$this->checkWorkshopMomentCapacity($wm)) {
                $errormessage .= "Workshop " . ($index + 1) . " was unavailable. ";
                return view('errors.409', ['errormessage' => $errormessage]);
            }
        }

        // If there's an error message, return it
        if ($errormessage) {
            return response()->json([
                'status' => 'error',
                'message' => $errormessage
            ], 400);
        }

        // Check if the user has any existing bookings
        if (Bookings::where('student_id', auth()->id())->count() < 1) {
            // Create new bookings for each workshop moment
            foreach ($workshops as $index => $workshop) {
                $wm = $this->getWorkshopMoment($workshop->id, $index + 1);

                Bookings::create([
                    'wm_id' => $wm->id,
                    'student_id' => auth()->id(),
                ]);
            }
        } else {
            foreach ($workshops as $index => $workshop) {
                // Calculate the modulus value
                $modValue = ($index + 1) % 3;  // This will give 1, 2, 0 for the 3 workshops

                $wm = $this->getWorkshopMoment($workshop->id, $index + 1);

                // Update the booking based on the modulus value
                DB::table('bookings')
                    ->where('student_id', auth()->id())
                    ->whereRaw("MOD(id, 3) = ?", [$modValue])
                    ->update(['wm_id' => $wm->id]);
            }
        }

        return redirect('/send-mail');
    }

    private function resolveWorkshop($submittedValue)
    {
        if (!$submittedValue) {
            return null;
        }

        // Prefer primary key lookup (UUID), then fallback to legacy name lookup.
        return Workshop::find($submittedValue) ?? Workshop::where('name', $submittedValue)->first();
    }

    private function getWorkshopMoment($workshopId, $momentId)
    {
        return WorkshopMoment::with(['workshop', 'bookings'])
            ->where('workshop_id', $workshopId)
            ->where('moment_id', $momentId)
            ->first();
    }
    private function checkWorkshopMomentCapacity($wm)
    {
        return $wm && $wm->workshop->capacity > $wm->bookings->count();
    }

    public function viewCapacity()
    {
        // Get all workshops along with their moments and bookings count
        $workshops = Workshop::with(['workshopMoments' => function ($query) {
            $query->withCount('bookings');
        }])->get();

        // Prepare the response data in a structured format
        $data = $workshops->map(function ($workshop) {
            return [
                'workshop_name' => $workshop->name,
                'moments' => $workshop->workshopMoments->map(function ($moment) use ($workshop) {
                    return [
                        'workshop_id' => $workshop->id,
                        'capacity' => $moment->workshop->capacity,
                        'wm_id' => $moment->id,
                        'bookings' => $moment->bookings_count,
                        'status' => $moment->bookings_count >= $moment->workshop->capacity
                            ? 'Fully booked'
                            : 'Available spots',
                    ];
                })
            ];
        });

        // Check if $data is not empty
        if ($data->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No data available',
            ], 404);
        }

        // If data exists, return success with the data
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
