<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function download($file)
    {
        $filePath = storage_path('exports/zipped_exports/' . $file);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath);
    }

    public function delete($file)
    {
        $filePath = storage_path('exports/zipped_exports/' . $file);

        if (file_exists($filePath)) {
            unlink($filePath);
            return redirect()->back()->with('success', 'File deleted successfully');
        }

        return redirect()->back()->with('error', 'File not found');
    }
}
