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
        if ($rows->isEmpty()) {
            return;
        }

        $row1 = $rows->first()->toArray();
        $row2 = ($rows->count() > 1 && $rows->get(1)) ? $rows->get(1)->toArray() : [];

        // Deteksi apakah Row 2 berisi metadata (SOAL|... atau TS|... atau format pipa/slash)
        $hasRow2Metadata = false;
        foreach ($row2 as $val) {
            $valStr = (string) $val;
            if (str_contains($valStr, '|') || str_contains($valStr, '/')) {
                $hasRow2Metadata = true;
                break;
            }
        }

        $metadataRow    = $hasRow2Metadata ? $row2 : $row1;
        $displayHeaders = $row1;

        $map = [];
        $itemStart = null;
        foreach ($row1 as $idx => $hName) {
            $cleanH = strtolower(trim((string)$hName));
            if ($cleanH === '') continue;
            if (str_contains($hName, '|') || str_contains($hName, '/')) {
                if ($itemStart === null) {
                    $itemStart = $idx;
                }
                continue;
            }
            $map[$cleanH] = $idx;
        }

        if ($itemStart === null) {
            foreach ($metadataRow as $idx => $mVal) {
                $mStr = (string) $mVal;
                if (str_contains($mStr, '|') || str_contains($mStr, '/')) {
                    $itemStart = $idx;
                    break;
                }
            }
        }

        $hasUniv = isset($map['universitas']);
        if ($itemStart === null) {
            $itemStart = $hasUniv ? 11 : 9;
        }

        $colNpm      = $map['npm'] ?? ($hasUniv ? 3 : 2);
        $colNama     = $map['nama'] ?? ($hasUniv ? 4 : 3);
        $colAngk     = $map['angkatan'] ?? ($hasUniv ? 2 : 1);
        $colTahun    = $map['tahun akademik'] ?? ($map['tahun'] ?? ($hasUniv ? 1 : 0));
        $colProdi    = $map['prodi'] ?? ($hasUniv ? 5 : null);
        $colUniv     = $map['universitas'] ?? ($hasUniv ? 0 : null);
        $colCourse   = $map['kode mk'] ?? ($map['kode'] ?? ($hasUniv ? 6 : 4));
        $colJenis    = $map['jenis'] ?? ($hasUniv ? 8 : 6);
        $colBobot    = $map['bobot jenis (%)'] ?? ($map['bobot jenis'] ?? ($hasUniv ? 9 : 7));
        $colNilai    = $map['nilai total'] ?? ($map['nilai'] ?? ($hasUniv ? 10 : 8));

        $skipRows = $hasRow2Metadata ? 2 : 1;

        foreach ($rows->skip($skipRows) as $row) {
            $npmRaw = $row[$colNpm] ?? null;
            if (empty($npmRaw)) continue;

            $npm = preg_replace('/[^0-9]/', '', (string) $npmRaw);
            if (empty($npm)) continue;

            $mahasiswa = Mahasiswa::where('NPM', $npm)->first();

            for ($i = $itemStart; $i < count($metadataRow); $i++) {
                $header = trim((string) ($metadataRow[$i] ?? ''));

                if ($header === '') continue;

                $displayLabel = trim((string) ($displayHeaders[$i] ?? ''));

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
                        $namaSoal = $displayLabel !== '' ? $displayLabel : ('Soal #' . $idSoal);
                    } elseif ($tipe === 'TS') {
                        $namaSoal = $displayLabel !== '' ? $displayLabel : ('Instrumen #' . $idSoal);
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

                    $namaSoal = $displayLabel !== '' ? $displayLabel : trim($parts[0]);
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

                $currentUser = auth()->user();
                $univIdRow   = ($colUniv !== null && !empty($row[$colUniv])) 
                    ? Universitas::where('nama', $row[$colUniv])->value('id') 
                    : null;
                $prodiIdRow  = ($colProdi !== null && !empty($row[$colProdi])) 
                    ? Prodi::where('nama', $row[$colProdi])->value('id') 
                    : null;

                Mutu::create([
                    'id_mahasiswa'   => $mahasiswa->id ?? null,
                    'universitas_id' => $univIdRow ?: ($currentUser->id_universitasUser ?? null),
                    'tahun'          => $row[$colTahun] ?? '',
                    'angkatan'       => $row[$colAngk] ?? '',
                    'npm'            => $npm,
                    'nama_mhs'       => $row[$colNama] ?? '',
                    'id_prodi'       => $prodiIdRow ?: ($currentUser->id_prodiUser ?? null),
                    'Course'         => $row[$colCourse] ?? '',
                    'Jenis'          => $row[$colJenis] ?? '',
                    'examWeight'     => $row[$colBobot] ?? 0,
                    'Nilai'          => $row[$colNilai] ?? 0,
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
