<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CplProdiTemplateExport extends DefaultValueBinder implements 
    FromArray, 
    WithHeadings, 
    WithColumnWidths, 
    WithColumnFormatting, 
    WithEvents, 
    WithCustomValueBinder
{
    public function bindValue(Cell $cell, $value)
    {
        // Pastikan Kolom A (Tahun) dan Kolom B (Nomor CPL) selalu disimpan sebagai Teks (String) murni
        $column = $cell->getColumn();
        if (in_array($column, ['A', 'B'])) {
            $cell->setValueExplicit((string)$value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

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
            'Nomor CPL (Contoh: 01, 02)',
            'Aspek',
            'Deskripsi CPL'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 26,
            'C' => 25,
            'D' => 80,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Format seluruh kolom A dan B sebagai Text (@) agar saat user mengetik '02', '03' angka 0 di depan tidak hilang
                $sheet->getStyle('A:B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                $sheet->getStyle('A1:B500')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

                // Set nilai eksplisit string untuk data contoh
                for ($row = 2; $row <= 5; $row++) {
                    $sheet->getCell("A{$row}")->setValueExplicit((string)$sheet->getCell("A{$row}")->getValue(), DataType::TYPE_STRING);
                    $sheet->getCell("B{$row}")->setValueExplicit((string)$sheet->getCell("B{$row}")->getValue(), DataType::TYPE_STRING);
                }

                // Styling Header
                $sheet->getStyle('A1:D1')->getFont()->setBold(true);
                $sheet->getStyle('A1:D1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE2EFDA');
                $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:D1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Alignment isi tabel
                $sheet->getStyle('A2:A500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B2:B500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D2:D500')->getAlignment()->setWrapText(true);
            },
        ];
    }
}
