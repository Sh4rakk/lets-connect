<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkshopMoment;
use App\Models\Workshop;
use App\Models\Setting;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\LaravelPdf\Facades\Pdf;


class WorkshopDashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard.workshops')->with('workshopmoments', WorkshopMoment::with(['workshop'], ['bookings'])->get());

        //return view('dashboard.bookings')->with ('bookings',  Bookings::with(['student', 'workshopMoment.workshop', 'workshopMoment.moment'])->get());

    }

    public function showbookings(WorkShopMoment $wsm)
    {
        //$wsm->load('bookings');
        $bookings = $wsm->bookings()->get();

        return view('dashboard.showbookings')
            ->with('wsm', $wsm)
            ->with('bookings', $bookings);

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

    public function toggleSignups()
    {
        \Log::info('Toggle signups called');
        $setting = Setting::where('key', 'signups_open')->first();

        \Log::info('Current setting:', ['setting' => $setting ? $setting->toArray() : 'null']);

        if ($setting) {
            $setting->value = $setting->value == '1' ? '0' : '1';
            $setting->save();
            \Log::info('Setting updated to:', ['value' => $setting->value]);
        } else {
            \Log::warning('Setting not found, creating new one');
            Setting::create(['key' => 'signups_open', 'value' => '1']);
        }

        return back()->with('success', 'Inschrijvingen status gewijzigd.');
    }

    private function styleDataRow($sheet, $row)
    {
        for ($col = 1; $col <= 5; $col++) {
            $cell = $sheet->getCellByColumnAndRow($col, $row);
            $cell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);

            if ($row % 2 === 0) {
                $cell->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFF2F2F2'));
            }
        }

        $sheet->getRowDimension($row)->setRowHeight(25);
    }
}
