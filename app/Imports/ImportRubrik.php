<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportRubrik implements ToCollection
{
    public $type = null;
    public $data = [];
    public $headers = [];

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        // ambil 3 baris pertama untuk deteksi lebih aman
        $sampleRows = $rows->take(3)->map(function ($row) {
            return is_array($row) ? $row : $row->toArray();
        });

        $flattened = [];
        foreach ($sampleRows as $row) {
            foreach ($row as $cell) {
                $flattened[] = trim((string) $cell);
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
            str_contains($header, 'CRITERIAS') &&
            str_contains($header, 'PERCENTAGE')
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
            str_contains($header, 'QUESTIONS') &&
            str_contains($header, 'YES')
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