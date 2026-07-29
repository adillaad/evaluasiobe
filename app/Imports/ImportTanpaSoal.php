<?php

namespace App\Imports;

use App\Models\Mutu;
use App\Models\Prodi;
use App\Models\Universitas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ImportTanpaSoal implements  WithStartRow,ToCollection,WithValidation, WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        $prodiId = Prodi::where('nama',$rows[1][5])->value('id');
        $univId = Universitas::where('nama',$rows[1][0])->value('id');

        foreach ($rows as $row) 
        {
            if ($rows->count() > 0) {
                $headers = $rows->first();
                $columnCount = count($headers);
            }
            for ($i=11; $i < $columnCount ; $i++) { 
                $substring = explode('/', $headers[$i]);
                if ($row['3'] != 'NPM') {
                    Mutu::create([
                        'universitas_id' => $univId,
                        'tahun' => ucwords($row[1]),
                        'angkatan' => $row[2],
                        'NPM' => $row[3],
                        'Nama_mhs' => ucwords($row[4]),
                        'id_prodi' => $prodiId,
                        'kode_course' => strtoupper($row[6]),
                      //  'namaCourse' => strtoupper($row[7]),
                        'Jenis' => strtolower($row[8]),
                        'examWeight' => $row[9],
                        'Nilai' => $row[10],
                        'soal' => $substring[0],
                        'BobotSoal' => $substring[1],
                        'nilaiSoal' => $row[$i],
                    ]);
                }
            }
        }
    }
    public function startRow(): int
    {
            return 1;
    }

    public function rules(): array
    {
        return [
            '0' => 'required',
            '1' => 'required',
            '2' => 'required',
            '3' => 'required',
            '4' => 'required',
            '5' => 'required',
            '6' => 'required',
            '7' => 'required',
            '8' => 'required',
            '9' => 'required',
            '10' => 'required',
            '11' => 'required',
        ];
    }
}
