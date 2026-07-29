<?php

namespace App\Imports;

use App\Models\Mutu;
use App\Models\Prodi;
use App\Models\Universitas;
use App\Models\Mahasiswa;
use App\Models\MetodePenilaian;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class MutuImport implements ToCollection, WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        $headers = $rows->first()->toArray();

        foreach ($rows->skip(1) as $row) {
            if (empty($row[3])) continue;

            $npm = preg_replace('/[^0-9]/', '', (string) $row[3]);
            $mahasiswa = Mahasiswa::where('NPM', $npm)->first();

            for ($i = 11; $i < count($headers); $i++) {
                $header = trim((string) ($headers[$i] ?? ''));

                
                if ($header === '') continue;

                
                if (str_contains($header, '|')) {
                    $parts = explode('|', $header);

                    if (count($parts) < 5) {
                        Log::warning("MutuImport: header kolom {$i} tidak lengkap (pipe): '{$header}'");
                        continue;
                    }

                    $tipe   = strtoupper(trim($parts[0])); // SOAL atau TS
                    $idSoal = trim($parts[1]);
                    $bobot  = trim($parts[2]);
                    $cpl    = trim($parts[3]);
                    $cpmk   = trim($parts[4]);

                    if ($tipe === 'SOAL') {
                        $namaSoal = 'Soal #' . $idSoal;
                    } elseif ($tipe === 'TS') {
                        $namaSoal = 'Instrumen #' . $idSoal;
                        $idSoal   = null; 
                    } else {
                        Log::warning("MutuImport: tipe tidak dikenal di kolom {$i}: '{$tipe}'");
                        continue;
                    }
                } elseif (str_contains($header, '/')) {
                    $parts = explode('/', $header);

                    if (count($parts) < 5) {
                        Log::warning("MutuImport: header kolom {$i} tidak lengkap (slash): '{$header}'");
                        continue;
                    }

                    $namaSoal = trim($parts[0]);
                    $idSoal   = trim($parts[1]);
                    $bobot    = trim($parts[2]);
                    $cpl      = trim($parts[3]);
                    $cpmk     = trim($parts[4]);
                } else {
                    Log::warning("MutuImport: format header tidak dikenal di kolom {$i}: '{$header}'");
                    continue;
                }

                $nilai = $row[$i] ?? null;
                if ($nilai === null || $nilai === '') continue;

                Mutu::create([
                    'id_mahasiswa'   => $mahasiswa->id ?? null,
                    'universitas_id' => Universitas::where('nama', $row[0])->value('id'),
                    'tahun'          => $row[1],
                    'angkatan'       => $row[2],
                    'npm'            => $npm,
                    'nama_mhs'       => $row[4],
                    'id_prodi'       => Prodi::where('nama', $row[5])->value('id'),
                    'Course'         => $row[6],
                    'Jenis'          => $row[8],
                    'examWeight'     => $row[9],
                    'Nilai'          => $row[10],
                    'soal'           => $namaSoal,
                    'idSoal'         => $idSoal,
                    'BobotSoal'      => $bobot,
                    'Cpl'            => $cpl,
                    'Cpmk'           => $cpmk,
                    'nilaiSoal'      => $nilai,
                ]);
            }
        }
    }
}
