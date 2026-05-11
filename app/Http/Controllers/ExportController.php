<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Exports\UsersExport;
use App\Exports\ClassExport;
use App\Models\WorkshopMoment;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function exportAll()
    {
        return Excel::download(
            new UsersExport, 'alle-gebruikers-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportClass($class)
    {
        return Excel::download(
            new ClassExport($class), $class.'-lijst-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    ## Workshop export functie gemaakt door Kofmel (verplaatst door Fokke)
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
}
