<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenilaianInstrumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penilaian_instrumen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cpl_mk_cpmk_penilaian_id');
            $table->string('kriteria');
            $table->float('bobot_metode');
            $table->timestamps();

            $table->foreign('cpl_mk_cpmk_penilaian_id')->references('id')->on('cpl_mk_cpmk_penilaian')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penilaian_instrumen');
    }
}
