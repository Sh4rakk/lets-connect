<?php

namespace App\Http\Controllers;

use App\Models\Moment;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\WorkshopMoment;

class WorkshopDashboardController extends Controller
{
    public function index(Request $request)
    {
        // withCount in plaats van count($wm->bookings) in de view, anders 1 query per rij
        $workshopMoments = WorkshopMoment::with('workshop')
            ->withCount('bookings')
            ->get()
            ->sortBy(fn (WorkshopMoment $wm) => mb_strtolower((string) $wm->workshop->name))
            ->groupBy('moment_id');

        return view('dashboard.workshops', [
            'moments' => Moment::orderBy('id')->get(),
            'workshopMoments' => $workshopMoments,
            'signupsOpen' => Setting::signupsOpen(),
        ]);
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
