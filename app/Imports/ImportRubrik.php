<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportRubrik implements ToCollection
{
    public $type = null;
    public $data = [];
    public $headers = [];

    public static function fromFile($fileOrPath)
    {
        $instance = new self();

        if ($fileOrPath instanceof \Illuminate\Http\UploadedFile) {
            $filePath = $fileOrPath->getRealPath() ?: $fileOrPath->getPathname();
        } else {
            $filePath = (string) $fileOrPath;
        }

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = collect($worksheet->toArray());

        $instance->collection($rows);

        return $instance;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        // ambil 5 baris pertama untuk deteksi lebih aman
        $sampleRows = $rows->take(5)->map(function ($row) {
            return is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array) $row);
        });

        $flattened = [];
        foreach ($sampleRows as $row) {
            foreach ($row as $cell) {
                if ($cell !== null && $cell !== '') {
                    $flattened[] = trim((string) $cell);
                }
            }
        }

        $headerString = strtoupper(implode(' ', $flattened));

        $this->type = $this->detectTemplate($headerString);

        if ($this->type === 'criteria_percentage') {
            $this->headers = ['Criteria', 'Percentage', '100', '80', '60', '40', '20'];
            $this->parseCriteriaPercentage($rows);
        } elseif ($this->type === 'program_report') {
            $this->headers = ['Criteria', 'Program 100', 'Program 50', 'Program 0', 'Report 100', 'Report 50', 'Report 0'];
            $this->parseProgramReport($rows);
        } elseif ($this->type === 'questions') {
            $this->headers = ['Question', 'Criteria', 'No', 'Yes'];
            $this->parseQuestions($rows);
        }
    }

    private function detectTemplate($header)
    {
        if (
            (str_contains($header, 'CRITERIA') || str_contains($header, 'KRITERIA')) &&
            (str_contains($header, 'PERCENTAGE') || str_contains($header, 'PERSENTASE') || str_contains($header, '%'))
        ) {
            return 'criteria_percentage';
        }

        if (
            str_contains($header, 'PROGRAM') &&
            str_contains($header, 'REPORT')
        ) {
            return 'program_report';
        }

        if (
            (str_contains($header, 'QUESTION') || str_contains($header, 'PERTANYAAN') || str_contains($header, 'SOAL')) &&
            (str_contains($header, 'YES') || str_contains($header, 'NO') || str_contains($header, 'YA') || str_contains($header, 'TIDAK'))
        ) {
            return 'questions';
        }

        return null;
    }

    private function parseCriteriaPercentage($rows)
    {
        foreach ($rows->slice(1) as $row) {
            $row = is_array($row) ? $row : $row->toArray();

            if (empty(trim((string)($row[0] ?? '')))) continue;

            $this->data[] = [
                'criteria' => $row[0] ?? '',
                'percentage' => $row[1] ?? '',
                '100' => $row[2] ?? '',
                '80' => $row[3] ?? '',
                '60' => $row[4] ?? '',
                '40' => $row[5] ?? '',
                '20' => $row[6] ?? '',
            ];
        }
    }

    private function parseProgramReport($rows)
    {
        foreach ($rows->slice(2) as $row) {
            $row = is_array($row) ? $row : $row->toArray();

            if (empty(trim((string)($row[0] ?? '')))) continue;

            $this->data[] = [
                'criteria' => $row[0] ?? '',
                'program_100' => $row[1] ?? '',
                'program_50' => $row[2] ?? '',
                'program_0' => $row[3] ?? '',
                'report_100' => $row[4] ?? '',
                'report_50' => $row[5] ?? '',
                'report_0' => $row[6] ?? '',
            ];
        }
    }

    private function parseQuestions($rows)
    {
        foreach ($rows->slice(1) as $row) {
            $row = is_array($row) ? $row : $row->toArray();

            if (empty(trim((string)($row[0] ?? '')))) continue;

            $this->data[] = [
                'question' => $row[0] ?? '',
                'criteria' => $row[1] ?? '',
                'no' => $row[2] ?? '',
                'yes' => $row[3] ?? '',
            ];
        }
    }
}