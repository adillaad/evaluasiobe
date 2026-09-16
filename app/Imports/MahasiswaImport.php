<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class MahasiswaImport implements ToCollection
{
    protected ?int $defaultProdiId;

    public function __construct(?int $defaultProdiId = null)
    {
        $this->defaultProdiId = $defaultProdiId;
    }

    public function collection(Collection $rows)
    {
        $importedCount = 0;

        foreach ($rows as $index => $row) {
            $rowArr = $row instanceof Collection ? $row->toArray() : (array) $row;

            $npmRaw = $rowArr['npm'] ?? $rowArr['NPM'] ?? $rowArr[0] ?? null;
            $namaRaw = $rowArr['nama'] ?? $rowArr['Nama'] ?? $rowArr['nama_mahasiswa'] ?? $rowArr[1] ?? null;
            $angkatanRaw = $rowArr['angkatan'] ?? $rowArr['Angkatan'] ?? $rowArr[2] ?? null;
            $prodiRaw = $rowArr['program_studi'] ?? $rowArr['Program Studi'] ?? $rowArr['prodi'] ?? $rowArr[3] ?? null;

            $npmStr = trim((string) $npmRaw);
            $namaStr = trim((string) $namaRaw);
            $angkatanStr = trim((string) $angkatanRaw);
            $prodiStr = trim((string) $prodiRaw);

            // Skip header row
            if (strcasecmp($npmStr, 'NPM') === 0 || strcasecmp($namaStr, 'Nama') === 0 || strcasecmp($namaStr, 'Nama Mahasiswa') === 0) {
                continue;
            }

            if (empty($npmStr) || empty($namaStr)) {
                continue;
            }

            // Formatting NPM jika angka berupa desimal dari Excel
            if (is_numeric($npmStr) && str_contains($npmStr, '.')) {
                $npmStr = number_format((float) $npmStr, 0, '', '');
            }

            // Tentukan ID Prodi
            $idProdi = $this->defaultProdiId;

            if (!empty($prodiStr)) {
                $foundProdi = Prodi::where('nama', 'LIKE', '%' . $prodiStr . '%')->first();
                if ($foundProdi) {
                    // Jika defaultProdiId tidak ada (misal Admin Global), gunakan prodi dari Excel
                    if (!$idProdi) {
                        $idProdi = $foundProdi->id;
                    }
                }
            }

            if (!$idProdi) {
                $user = auth()->user();
                $idProdi = $user->id_prodiUser;
                if (!$idProdi && $user->prodis()->exists()) {
                    $idProdi = $user->prodis()->first()->id;
                }
                if (!$idProdi) {
                    $idProdi = Prodi::first()?->id;
                }
            }

            if (!$idProdi) {
                continue;
            }

            Mahasiswa::updateOrCreate(
                ['NPM' => $npmStr],
                [
                    'Nama' => $namaStr,
                    'angkatan' => $angkatanStr ?: date('Y'),
                    'id_prodi' => $idProdi,
                ]
            );

            $importedCount++;
        }

        if ($importedCount === 0) {
            throw new \Exception('Tidak ada data mahasiswa yang valid untuk diimport dari file Excel ini. Pastikan format kolom sesuai (NPM, Nama, Angkatan, Program Studi).');
        }
    }
}
