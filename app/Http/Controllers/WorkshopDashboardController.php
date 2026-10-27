<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkshopMoment;

class WorkshopDashboardController extends Controller
{
    public function index(Request $request)
    {
        // withCount in plaats van count($wm->bookings) in de view, anders 1 query per rij
        $workshopMoments = WorkshopMoment::with(['workshop', 'moment'])->withCount('bookings')->get();

        return view('dashboard.workshops')->with('workshopmoments', $workshopMoments);
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
