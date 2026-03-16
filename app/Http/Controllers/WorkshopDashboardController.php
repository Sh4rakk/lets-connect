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


    public function pdf(Request $request){
        $workshopmoments = WorkshopMoment::with(['workshop', 'bookings.student', 'moment'])->get();

        if ($request->query('pdf')) {
            $pdf = Pdf::view('dashboard.showbookings', compact('workshopmoments'));
            return $pdf->download('boekingen.pdf');
        }

        return view('dashboard.showbookings', compact('workshopmoments'));
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

    public function exportWorkshop($workshopName)
    {
        $workshopMoments = WorkshopMoment::whereHas('workshop', function ($query) use ($workshopName) {
            $query->where('name', urldecode($workshopName));
        })
        ->with(['workshop', 'moment', 'bookings.student'])
        ->join('moments', 'workshop_moments.moment_id', '=', 'moments.id')
        ->orderBy('moments.id')
        ->get(['workshop_moments.*']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Workshop Data');

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);

        $headers = ['Workshop', 'Ronde', 'Tijd', 'Student Naam', 'Klas'];
        $sheet->fromArray($headers, null, 'A1');

        for ($col = 1; $col <= 5; $col++) {
            $cell = $sheet->getCellByColumnAndRow($col, 1);
            $cell->getStyle()->getFont()->setBold(true)->setSize(12)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
            $cell->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF4472C4'));
            $cell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        $row = 2;
        foreach ($workshopMoments as $wm) {
            $roundNumber = $wm->moment->id;
            $time = $wm->moment->time;
            $workshop = $wm->workshop->name;

            if ($wm->bookings->count() === 0) {
                $sheet->setCellValue('A' . $row, $workshop);
                $sheet->setCellValue('B' . $row, 'Ronde ' . $roundNumber);
                $sheet->setCellValue('C' . $row, $time);
                $sheet->setCellValue('D' . $row, 'Geen inschrijvingen');
                $sheet->setCellValue('E' . $row, '');

                $this->styleDataRow($sheet, $row);
                $row++;
            } else {
                foreach ($wm->bookings as $booking) {
                    $student = $booking->student;
                    $sheet->setCellValue('A' . $row, $workshop);
                    $sheet->setCellValue('B' . $row, 'Ronde ' . $roundNumber);
                    $sheet->setCellValue('C' . $row, $time);
                    $sheet->setCellValue('D' . $row, $student->name ?? '');
                    $sheet->setCellValue('E' . $row, $student->class ?? '');

                    $this->styleDataRow($sheet, $row);
                    $row++;
                }
            }
        }

        $fileName = 'Workshop_' . str_replace(' ', '_', urldecode($workshopName)) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->stream(
            function() use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
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
