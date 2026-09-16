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
        if (!Schema::hasTable('penilaian_konversi')) {
            Schema::create('penilaian_konversi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dosen_id');
                $table->string('mk_kode');
                $table->unsignedBigInteger('tahun_ajaran_id');
                $table->unsignedBigInteger('kurikulum_id');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('konversi_metode')) {
            Schema::create('konversi_metode', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('penilaian_konversi_id');
                $table->unsignedBigInteger('metode_id');
                $table->float('bobot')->default(0);
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('konversi_metode', 'bobot')) {
                Schema::table('konversi_metode', function (Blueprint $table) {
                    $table->float('bobot')->default(0)->after('metode_id');
                });
            }
        }

        if (!Schema::hasTable('konversi_cpmk_metode')) {
            Schema::create('konversi_cpmk_metode', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('konversi_metode_id');
                $table->unsignedBigInteger('cpmk_id');
                $table->unsignedBigInteger('sub_cpmk_id')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('mutus')) {
            Schema::table('mutus', function (Blueprint $table) {
                if (!Schema::hasColumn('mutus', 'sumber')) {
                    $table->string('sumber')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'tahun_ajaran_id')) {
                    $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'konversi_metode_id')) {
                    $table->unsignedBigInteger('konversi_metode_id')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'sub_cpmk_id')) {
                    $table->unsignedBigInteger('sub_cpmk_id')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'namaCourse')) {
                    $table->string('namaCourse')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'nama_mhs')) {
                    $table->string('nama_mhs')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'Nama_mhs')) {
                    $table->string('Nama_mhs')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'npm')) {
                    $table->string('npm')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'nilaiSoal')) {
                    $table->float('nilaiSoal')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'BobotSoal')) {
                    $table->float('BobotSoal')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'examWeight')) {
                    $table->float('examWeight')->nullable();
                }
                if (!Schema::hasColumn('mutus', 'id_mahasiswa')) {
                    $table->unsignedBigInteger('id_mahasiswa')->nullable();
                }
            });

            try {
                DB::statement("ALTER TABLE `mutus` DROP INDEX `mutus_konversi_unique`");
            } catch (\Exception $e) {
                // Index already dropped or not exists
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
        Schema::dropIfExists('konversi_cpmk_metode');
        Schema::dropIfExists('konversi_metode');
        Schema::dropIfExists('penilaian_konversi');
    }
};
