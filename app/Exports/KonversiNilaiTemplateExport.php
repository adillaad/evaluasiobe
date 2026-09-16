<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KonversiNilaiTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected array $columnHeaders;
    protected string $taLabel;
    protected string $mkNama;

    public function __construct(array $columnHeaders, string $taLabel = '', string $mkNama = '')
    {
        $this->columnHeaders = $columnHeaders;
        $this->taLabel = $taLabel;
        $this->mkNama = $mkNama;
    }

    public function headings(): array
    {
        return array_merge(['Tahun Ajaran', 'Nama Mata Kuliah', 'Angkatan', 'NPM', 'Nama'], $this->columnHeaders);
    }

    public function array(): array
    {
        if (empty($this->taLabel) && empty($this->mkNama)) {
            return [];
        }

        // Sediakan 3 contoh baris dummy yang kolom TA dan Nama MK-nya sudah terisi otomatis
        $sampleRows = [];
        for ($i = 1; $i <= 3; $i++) {
            $row = [
                $this->taLabel,
                $this->mkNama,
                '', // Angkatan (diisi dosen)
                '', // NPM (diisi dosen)
                '', // Nama (diisi dosen)
            ];
            foreach ($this->columnHeaders as $h) {
                $row[] = '';
            }
            $sampleRows[] = $row;
        }

        return $sampleRows;
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1F2937'],
            ],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAD3'],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }
}

