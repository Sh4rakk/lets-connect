<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Moment;
use App\Models\WorkshopMoment;

class WorkshopDashboardController extends Controller
{
    public function index(Request $request)
    {
        // withCount in plaats van count($wm->bookings) in de view, anders 1 query per rij
        $workshopMoments = WorkshopMoment::with(['workshop', 'moment'])->withCount('bookings')->get();

        // per ronde een tab, workshops op naam
        $rounds = Moment::orderBy('id')->get()->map(fn (Moment $moment) => [
            'moment' => $moment,
            'workshopMoments' => $workshopMoments
                ->where('moment_id', $moment->id)
                ->filter(fn ($wm) => $wm->workshop)
                ->sortBy(fn ($wm) => mb_strtolower((string) $wm->workshop->name))
                ->values(),
        ]);

        return view('dashboard.workshops')->with('rounds', $rounds);
    }

    // de studenten zelf worden door Alpine opgehaald via /api/workshop-moments/{id}
    public function showbookings(WorkshopMoment $wsm)
    {
        return view('dashboard.showbookings')
            ->with('wsm', $wsm->load(['workshop', 'moment']))
            ->with('class', null);
    }

    public function showfilteredbookings(WorkshopMoment $wsm, string $class)
    {
        return view('dashboard.showbookings')
            ->with('wsm', $wsm->load(['workshop', 'moment']))
            ->with('class', $class);
    }
}
