<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MkKodeUpdater
{
  /**
   * Tabel dan kolom yang menyimpan referensi ke mks.kode.
   *
   * @var array<int, array{0: string, 1: string}>
   */
    private const REFERENCES = [
        ['mutus', 'Course'],
        ['soals', 'kode_mk'],
        ['tanpa_soal', 'kode_mk'],
        ['rpss', 'kode_mk'],
        ['cpmk_mk', 'mk_kode'],
        ['mk_cpl', 'mk_kode'],
        ['bk_mk', 'mk_kode'],
        ['mk_sub_cpmk', 'mk_kode'],
        ['rubrics', 'mk_kode'],
        ['cpl_mk_cpmk_penilaian', 'mk_kode'],
        ['pustaka', 'kode_mk'],
        ['instrumen_tanpa_soal', 'kode_mk'],
        ['cmcp', 'mk_kode'],
        ['cpmks', 'kode_mk'],
        ['cplmks', 'kode_mk'],
        ['cpl_mk', 'kode_mk'],
    ];

    public static function rename(string $oldKode, string $newKode, array $mkAttributes = []): void
    {
        if ($oldKode === $newKode) {
            if ($mkAttributes !== []) {
                DB::table('mks')->where('kode', $oldKode)->update($mkAttributes);
            }

            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::transaction(function () use ($oldKode, $newKode, $mkAttributes) {
                foreach (self::REFERENCES as [$table, $column]) {
                    if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                        continue;
                    }

                    DB::table($table)
                        ->where($column, $oldKode)
                        ->update([$column => $newKode]);
                }

                $payload = array_merge($mkAttributes, ['kode' => $newKode]);

                DB::table('mks')
                    ->where('kode', $oldKode)
                    ->update($payload);
            });
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    public static function databaseErrorMessage(\Illuminate\Database\QueryException $e): string
    {
        $errorCode = $e->errorInfo[1] ?? null;

        return match ($errorCode) {
            1062 => 'Kode mata kuliah sudah digunakan.',
            1406 => 'Kode MK terlalu panjang. Maksimal 50 karakter.',
            1451 => 'Kode MK tidak dapat diubah karena masih digunakan data penilaian/soal. Hubungi admin jika perlu.',
            default => 'Terjadi kesalahan pada database.',
        };
    }
}
