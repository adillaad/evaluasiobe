<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Drop tabel-tabel evaluasi lama yang tidak efisien / deprecated
        Schema::dropIfExists('evaluasi_transkrip_mahasiswas');
        Schema::dropIfExists('evaluasi_dashboard_pm');
        Schema::dropIfExists('evaluasi_cpl_angkatans');
        Schema::dropIfExists('evaluasi_mk_cpmk_angkatans');
        Schema::dropIfExists('evaluasi_cpmk_mahasiswas');
        Schema::dropIfExists('evaluasi_cpl_mahasiswas');

        // 2. Tabel Visualisasi Per Mahasiswa
        Schema::create('visualisasi_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('npm', 30)->index();
            $table->unsignedBigInteger('id_prodi')->nullable()->index();
            $table->integer('angkatan')->nullable()->index();
            $table->string('tahun_filter', 20)->default('all')->index();
            $table->string('semester_filter', 20)->default('all')->index();
            $table->decimal('ipk_obe', 4, 2)->default(0);
            $table->decimal('avg_cpl', 5, 2)->default(0);
            $table->decimal('avg_cpmk', 5, 2)->default(0);
            $table->integer('total_sks')->default(0);
            $table->integer('total_mk')->default(0);
            $table->longText('rekap_cpl_json')->nullable();
            $table->longText('rekap_cpmk_json')->nullable();
            $table->longText('rekap_mk_json')->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['npm', 'tahun_filter', 'semester_filter'], 'uniq_vis_mhs');
        });

        // 3. Tabel Visualisasi Per Angkatan
        Schema::create('visualisasi_angkatans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prodi')->index();
            $table->integer('angkatan')->index();
            $table->string('tahun_filter', 20)->default('all')->index();
            $table->string('semester_filter', 20)->default('all')->index();
            $table->decimal('avg_skor_cpl', 5, 2)->default(0);
            $table->longText('rekap_cpl_json')->nullable();
            $table->longText('rekap_cpmk_json')->nullable();
            $table->longText('distribusi_kelulusan_json')->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['id_prodi', 'angkatan', 'tahun_filter', 'semester_filter'], 'uniq_vis_akt');
        });

        // 4. Tabel Visualisasi Per Mata Kuliah
        Schema::create('visualisasi_mata_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prodi')->index();
            $table->integer('angkatan')->index();
            $table->string('kode_mk', 30)->index();
            $table->string('tahun_filter', 20)->default('all')->index();
            $table->string('semester_filter', 20)->default('all')->index();
            $table->decimal('avg_skor_mk', 5, 2)->default(0);
            $table->longText('rekap_cpmk_json')->nullable();
            $table->longText('rekap_cpl_json')->nullable();
            $table->longText('distribusi_nilai_json')->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['id_prodi', 'angkatan', 'kode_mk', 'tahun_filter', 'semester_filter'], 'uniq_vis_mk');
        });

        // 5. Tabel Visualisasi Per Program Studi
        Schema::create('visualisasi_prodis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prodi')->index();
            $table->string('tahun_ajaran', 20)->default('all')->index();
            $table->string('semester_filter', 20)->default('all')->index();
            $table->decimal('avg_cpl_keseluruhan', 5, 2)->default(0);
            $table->longText('radar_cpl_json')->nullable();
            $table->longText('rekap_angkatan_json')->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['id_prodi', 'tahun_ajaran', 'semester_filter'], 'uniq_vis_prodi');
        });

        // 6. Tabel Visualisasi Per Fakultas & Universitas
        Schema::create('visualisasi_fakultas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_fakultas')->nullable()->index();
            $table->unsignedBigInteger('id_universitas')->nullable()->index();
            $table->string('tahun_ajaran', 20)->default('all')->index();
            $table->string('semester_filter', 20)->default('all')->index();
            $table->decimal('avg_cpl_fakultas', 5, 2)->default(0);
            $table->longText('rekap_prodi_json')->nullable();
            $table->longText('radar_cpl_json')->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->index(['id_fakultas', 'id_universitas', 'tahun_ajaran', 'semester_filter'], 'idx_vis_fak');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visualisasi_fakultas');
        Schema::dropIfExists('visualisasi_prodis');
        Schema::dropIfExists('visualisasi_mata_kuliahs');
        Schema::dropIfExists('visualisasi_angkatans');
        Schema::dropIfExists('visualisasi_mahasiswas');
    }
};
