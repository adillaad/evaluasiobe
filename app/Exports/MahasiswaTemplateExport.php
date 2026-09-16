<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class MahasiswaTemplateExport implements FromArray, WithHeadings, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                '220101001',
                'Budi Santoso',
                '2022',
                'Informatika'
            ],
            [
                '220101002',
                'Siti Aminah',
                '2022',
                'Informatika'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'NPM',
            'Nama',
            'Angkatan',
            'Program Studi'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 15,
            'D' => 25,
        ];
    }
}
