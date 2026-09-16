<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class DosenTemplateExport implements FromArray, WithHeadings, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                'Dr. Ahmad Dahlan, M.Kom.',
                'ahmad.dahlan@unila.ac.id',
                'Dosen, Kepala Program Studi',
                'Informatika',
                'Unilajaya!'
            ],
            [
                'Siti Rahmawati, S.T., M.T.',
                'siti.rahmawati@unila.ac.id',
                'Dosen',
                'Informatika',
                'Unilajaya!'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Otoritas',
            'Program Studi',
            'Password'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 30,
            'C' => 35,
            'D' => 25,
            'E' => 20,
        ];
    }
}
