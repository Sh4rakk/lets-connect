<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserExportController extends Controller
{
    public function export()
    {
        return Excel::download(
            new UsersExport,
            'users-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}