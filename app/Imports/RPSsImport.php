<?php

namespace App\Imports;

use App\Models\RPS;
use Maatwebsite\Excel\Concerns\ToModel;

class RPSsImport implements ToModel
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        
        return new RPS([
            'kode_mk' => $row[0],
            'nomor'    => $row[1],
            'prodi' => $row[2],
            'dosen' => $row[3],
            'pengembang' => $row[4],
            'koordinator' => $row[5],
            'kaprodi' => $row[6],
            'kurikulum' => $row[7],
            'semester' => $row[8],
            'materi_mk' => $row[9],
            'pustaka_utama' => $row[10],
            'pustala_pendukung' => $row[11],
            'tipe' => $row[12],
            'waktu' => $row[13],
            'syarat_ujian' => $row[14],
            'syarat_studi' => $row[15],
            'media' => $row[16],
            'kontrak' => $row[17]
        ]);
    }
}
