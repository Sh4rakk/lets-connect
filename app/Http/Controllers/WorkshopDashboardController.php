<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkshopMoment;
use App\Models\Workshop;

class WorkshopDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.workshops')->with('workshopmoments', WorkshopMoment::with(['workshop'], ['bookings'])->get());

        //return view('dashboard.bookings')->with ('bookings',  Bookings::with(['student', 'workshopMoment.workshop', 'workshopMoment.moment'])->get());
    }
    public function showbookings(WorkShopMoment $wsm)
    {
        //$wsm->load('bookings');

        return view('dashboard.showbookings')->with('wsm', $wsm);
    }
    public function showfilteredbookings(WorkShopMoment $wsm, string $class)
    {
        $bookings = $wsm->bookings()->whereRelation('student', 'class', $class)->get();

        return view('dashboard.showbookings')
            ->with('wsm', $wsm)
            ->with('bookings', $bookings)
            ->with('class', $class);
    }

    // public function viewCapacity (Request $request)
    // {
    //     // Dummy data to be returned
    //     $dummyData = [
    //         'status' => 'success',
    //         'data' => [
    //             [
    //                 'workshop_id' => 1,
    //                 'capacity' => 30,
    //                 'wm_id' => 1,
    //             ],
    //             [
    //                 'id' => 2,
    //                 'name' => 'Workshop 2',
    //                 'capacity' => 50,
    //                 'wm_id' => 2,
    //                 'student_id' => 3,

    //             ],
    //             [
    //                 'id' => 3,
    //                 'name' => 'Workshop 3',
    //                 'capacity' => 20,
    //                 'wm_id' => 1,
    //                 'student_id' => 4,
    //             ]
    //         ]
    //     ];

    //     // Return the dummy data as a JSON response
    //     return response()->json($dummyData);
    // }
}
