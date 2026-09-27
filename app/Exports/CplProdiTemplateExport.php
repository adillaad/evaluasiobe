<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class CplProdiTemplateExport implements FromArray, WithHeadings, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                '2024',
                '01',
                'Sikap',
                'Lulusan mampu menunjukkan kepribadian yang berakhlak dan berintegritas melalui proses pembelajaran yang menghargai kebhinekaan.'
            ],
            [
                '2024',
                '02',
                'Pengetahuan',
                'Lulusan mampu menguasai konsep teoritis bidang pengetahuan secara umum dan konsep teoritis bagian khusus dalam bidang pengetahuan tersebut secara mendalam.'
            ],
            [
                '2024',
                '03',
                'Keterampilan Umum',
                'Lulusan mampu menerapkan pemikiran logis, kritis, sistematis, dan inovatif dalam konteks pengembangan atau implementasi ilmu pengetahuan.'
            ],
            [
                '2024',
                '04',
                'Keterampilan Khusus',
                'Lulusan mampu merancang dan mengimplementasikan sistem perangkat lunak yang aman, efisien, dan teruji.'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Tahun Kurikulum',
            'Nomor CPL (Angka)',
            'Aspek',
            'Deskripsi CPL'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 15,
            'C' => 25,
            'D' => 80,
        ];
    }
}
