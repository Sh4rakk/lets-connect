<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function download($file)
    {
        $path = $this->exportPath($file);

        if (!$path) {
            abort(404, 'File not found');
        }

        return Storage::disk('exports')->download($path);
    }

    public function delete($file)
    {
        $path = $this->exportPath($file);

        if ($path) {
            Storage::disk('exports')->delete($path);
            return redirect()->back()->with('success', 'File deleted successfully');
        }

        return redirect()->back()->with('error', 'File not found');
    }

    // Alleen een losse .zip uit de zipped_exports map, nooit een pad.
    // Anders kan je met bv. ../../.env elk bestand op de server downloaden of verwijderen.
    private function exportPath(string $file): ?string
    {
        if (!preg_match('/^[A-Za-z0-9_\-][A-Za-z0-9_.\-]*\.zip$/', $file)) {
            return null;
        }

        $path = 'zipped_exports/' . $file;

        return Storage::disk('exports')->exists($path) ? $path : null;
    }
}
