<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClassExport implements FromQuery, WithHeadings, ShouldAutoSize
{
    public function __construct(private string $class) {}
    public function Query()
    {
        return User::query()->where('class', $this->class)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Class',
        ];
    }
}
