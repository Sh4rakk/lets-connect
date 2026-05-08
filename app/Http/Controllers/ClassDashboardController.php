<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\WorkshopMoment;
use Illuminate\Http\Request;

class ClassDashboardController extends Controller
{
    public function index(Request $request)
    {
        $classes = Classes::all()->groupBy('class_group');
        $selectedClass = request('class') ? Classes::all()->firstWhere('name', request('class')) : null;

        return view('dashboard.classes', compact('classes', 'selectedClass'));
    }}
