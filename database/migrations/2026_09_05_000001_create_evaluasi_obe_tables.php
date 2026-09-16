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
        // 1. Evaluasi CPL Mahasiswa (Kumulatif & Filtered Periode)
        Schema::create('evaluasi_cpl_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('npm', 30)->index();
            $table->unsignedBigInteger('id_prodi')->nullable()->index();
            $table->integer('angkatan')->nullable()->index();
            $table->unsignedBigInteger('cpl_id')->index();
            $table->string('tahun_filter', 20)->default('all')->index();
            $table->string('semester_filter', 20)->default('all')->index();
            $table->decimal('skor', 5, 2)->default(0);
            $table->string('status', 50)->default('Belum Ada Data');
            $table->timestamps();

            $table->unique(['npm', 'cpl_id', 'tahun_filter', 'semester_filter'], 'uniq_eval_cpl_mhs');
        });

        // 2. Evaluasi CPMK Mahasiswa per Mata Kuliah
        Schema::create('evaluasi_cpmk_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('npm', 30)->index();
            $table->unsignedBigInteger('id_prodi')->nullable()->index();
            $table->integer('angkatan')->nullable()->index();
            $table->string('kode_mk', 30)->index();
            $table->unsignedBigInteger('cpmk_id')->index();
            $table->decimal('skor', 5, 2)->default(0);
            $table->string('status', 50)->default('Belum Ada Data');
            $table->timestamps();

            $table->unique(['npm', 'kode_mk', 'cpmk_id'], 'uniq_eval_cpmk_mhs');
        });

        // 3. Evaluasi CPMK per Mata Kuliah per Angkatan (Visualisasi Mata Kuliah)
        Schema::create('evaluasi_mk_cpmk_angkatans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prodi')->index();
            $table->integer('angkatan')->index();
            $table->string('kode_mk', 30)->index();
            $table->unsignedBigInteger('cpmk_id')->index();
            $table->decimal('avg_skor', 5, 2)->default(0);
            $table->decimal('min_skor', 5, 2)->default(0);
            $table->decimal('max_skor', 5, 2)->default(0);
            $table->string('status', 50)->default('Belum Ada Data');
            $table->timestamps();

            $table->unique(['id_prodi', 'angkatan', 'kode_mk', 'cpmk_id'], 'uniq_eval_mk_cpmk_akt');
        });

        // 4. Evaluasi CPL per Angkatan / Prodi (Visualisasi Angkatan)
        Schema::create('evaluasi_cpl_angkatans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prodi')->index();
            $table->integer('angkatan')->index();
            $table->unsignedBigInteger('cpl_id')->index();
            $table->string('tahun_filter', 20)->default('all')->index();
            $table->string('semester_filter', 20)->default('all')->index();
            $table->decimal('avg_skor', 5, 2)->default(0);
            $table->decimal('min_skor', 5, 2)->default(0);
            $table->decimal('max_skor', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['id_prodi', 'angkatan', 'cpl_id', 'tahun_filter', 'semester_filter'], 'uniq_eval_cpl_akt');
        });

        // 5. Evaluasi Dashboard Penjamin Mutu (Universitas, Fakultas, Prodi)
        Schema::create('evaluasi_dashboard_pm', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['universitas', 'fakultas', 'prodi'])->index();
            $table->unsignedBigInteger('id_universitas')->nullable()->index();
            $table->unsignedBigInteger('id_fakultas')->nullable()->index();
            $table->unsignedBigInteger('id_prodi')->nullable()->index();
            $table->string('tahun_filter', 20)->default('all')->index();
            $table->string('semester_filter', 20)->default('all')->index();
            $table->unsignedBigInteger('cpl_id')->nullable()->index();
            $table->decimal('skor_rata_rata', 5, 2)->default(0);
            $table->string('status', 50)->default('Belum Ada Data');
            $table->longText('detail_payload')->nullable(); // JSON data for radar & comparisons
            $table->timestamps();

            $table->index(['level', 'id_universitas', 'id_fakultas', 'id_prodi'], 'idx_eval_pm_level');
        });

        // 6. Evaluasi Transkrip Kompetensi & Dashboard Mahasiswa
        Schema::create('evaluasi_transkrip_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('npm', 30)->index();
            $table->unsignedBigInteger('id_prodi')->nullable()->index();
            $table->integer('angkatan')->nullable()->index();
            $table->string('tahun_filter', 20)->default('all')->index();
            $table->string('semester_filter', 20)->default('all')->index();
            $table->decimal('ipk_obe', 4, 2)->default(0);
            $table->integer('total_sks')->default(0);
            $table->integer('total_mk')->default(0);
            $table->longText('rekap_cpl_json')->nullable();
            $table->longText('rekap_mk_json')->nullable();
            $table->timestamps();

            $table->unique(['npm', 'tahun_filter', 'semester_filter'], 'uniq_eval_transkrip_mhs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluasi_transkrip_mahasiswas');
        Schema::dropIfExists('evaluasi_dashboard_pm');
        Schema::dropIfExists('evaluasi_cpl_angkatans');
        Schema::dropIfExists('evaluasi_mk_cpmk_angkatans');
        Schema::dropIfExists('evaluasi_cpmk_mahasiswas');
        Schema::dropIfExists('evaluasi_cpl_mahasiswas');
    }
};
