<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Header Styling
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

        // Tambahkan Data Validation (hanya angka 0-100, boleh desimal) untuk kolom nilai (mulai kolom 6 / F)
        if ($highestColumnIndex >= 6) {
            for ($col = 6; $col <= $highestColumnIndex; $col++) {
                $columnLetter = Coordinate::stringFromColumnIndex($col);

                $validation = new DataValidation();
                $validation->setType(DataValidation::TYPE_DECIMAL);
                $validation->setOperator(DataValidation::OPERATOR_BETWEEN);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setFormula1(0);
                $validation->setFormula2(100);

                $validation->setErrorTitle('Input Tidak Valid!');
                $validation->setError('Nilai harus berupa angka rentang 0 s/d 100 (boleh desimal, contoh: 85.5). Tidak boleh teks/huruf.');

                $validation->setPromptTitle('Input Nilai');
                $validation->setPrompt('Masukkan nilai angka 0 s/d 100 (boleh desimal, contoh: 85.5)');

                $sheet->setDataValidation("{$columnLetter}2:{$columnLetter}1000", $validation);
            }
        }

        return [];
    }
}


