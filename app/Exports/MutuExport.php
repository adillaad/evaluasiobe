<?php

namespace App\Exports;

use App\Models\Mutu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Facades\DB;

class MutuExport implements FromCollection,WithHeadings,WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Mutu::all();
    }
    // public function query()
    // {
    //     // Define your database query here
    //     return Mutu::query()
    //         ->select('id', 'NPM', 'Nama','Course', 'Jenis', 'BobotSoal', 'BobotNilai', 'Cpl', 'Cpmk', Mutu::raw('GROUP_CONCAT(CASE WHEN Soal = "Soal 1" THEN Nilai END) as Q1'), Mutu::raw('GROUP_CONCAT(CASE WHEN Soal = "Soal 2" THEN Nilai END) as Q2'), Mutu::raw('GROUP_CONCAT(CASE WHEN Soal = "Soal 3" THEN Nilai END) as Q3'), Mutu::raw('GROUP_CONCAT(CASE WHEN Soal = "Soal 4" THEN Nilai END) as Q4'), Mutu::raw('GROUP_CONCAT(CASE WHEN Soal = "Soal 5" THEN Nilai END) as Q5'))
    //         // ->where('Course', $inputValue)
    //         ->groupBy('Nama');
    // }
    public function headings():array
    {
        return [
            'id',
            'NPM',
            'Nama',
            'Course',
            'Jenis',
            'BobotSoal',
            'BobotNilai',
            'Cpl',
            'Cpmk',
            'Soal',
            'Nilai',
        ];
    }
    public function columnWidths(): array
    {
        return [
            'B' => 11,
            'C' => 25,
            'D' => 25,              
        ];
    }
}
