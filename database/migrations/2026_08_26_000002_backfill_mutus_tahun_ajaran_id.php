<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('mutus')) {
            // 1. Pastikan kolom tahun_ajaran_id sudah ada
            if (!Schema::hasColumn('mutus', 'tahun_ajaran_id')) {
                Schema::table('mutus', function (Blueprint $table) {
                    $table->unsignedBigInteger('tahun_ajaran_id')->nullable()->after('id_prodi');
                });
            }

            // 2. Populasikan mutus.tahun_ajaran_id dari mutus.tahun jika masih NULL
            if (Schema::hasColumn('mutus', 'tahun')) {
                $nullMutus = DB::table('mutus')->whereNull('tahun_ajaran_id')->whereNotNull('tahun')->get(['id', 'tahun']);
                foreach ($nullMutus as $row) {
                    $rawTahun = trim((string)$row->tahun);
                    if (empty($rawTahun)) continue;

                    // Parse jenis_semester dan angka tahun
                    $jenisSemester = 'Ganjil';
                    if (stripos($rawTahun, 'Genap') !== false) {
                        $jenisSemester = 'Genap';
                    }

                    preg_match('/\d{4}/', $rawTahun, $matches);
                    $tahunAngka = $matches[0] ?? date('Y');

                    // Cari atau buatkan master TahunAjaran
                    $ta = DB::table('tahun_ajaran')
                        ->where('tahun', 'like', "%{$tahunAngka}%")
                        ->where('jenis_semester', $jenisSemester)
                        ->first();

                    if (!$ta) {
                        $taId = DB::table('tahun_ajaran')->insertGetId([
                            'tahun' => (string)$tahunAngka,
                            'jenis_semester' => $jenisSemester,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $taId = $ta->id;
                    }

                    // Update mutus.tahun_ajaran_id
                    DB::table('mutus')->where('id', $row->id)->update([
                        'tahun_ajaran_id' => $taId,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No action needed for down
    }
};
