<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenilaianMetodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penilaian_metode', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cpl_mk_cpmk_penilaian_id');
            $table->unsignedBigInteger('metode_id');
            $table->float('bobot');
            $table->timestamps();

            $table->foreign('cpl_mk_cpmk_penilaian_id')->references('id')->on('cpl_mk_cpmk_penilaian')->onDelete('cascade');
            $table->foreign('metode_id')->references('id')->on('metode_penilaian')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penilaian_metode');
    }
}
