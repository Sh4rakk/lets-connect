<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClassDashboardController extends Controller
{
    // de klassen en studenten haalt de pagina zelf op via de api, hier alleen de export bestanden
    public function index(Request $request)
    {
        $exportFiles = Storage::disk('exports')->files('zipped_exports');

        return view('dashboard.classes', compact('exportFiles'));
    }
}
