<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Classes;
use App\Models\User;
use App\Models\WorkshopMoment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClassDashboardController extends Controller
{
    public function index(Request $request)
    {
        $classes = Classes::all()->groupBy('class_group');
        $selectedClass = request('class') ? Classes::all()->firstWhere('name', request('class')) : null;

        $users = request('class') ? User::all()->where('class', request('class')) : null;
        $registeredStudents = 0;

        if ($users) {
            foreach ($users as $user) {
                $bookings = Bookings::where('student_id', $user->id)->get();
                if (count($bookings) === 3) {
                    $registeredStudents++;
                }
            }
        }

        $zippedExportsPath = storage_path('exports/zipped_exports');
        $exportFiles  = Storage::disk('exports')->files('zipped_exports');

        return view('dashboard.classes', compact('classes', 'selectedClass', 'users', 'registeredStudents', 'exportFiles'));
    }}
