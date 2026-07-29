<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCplMkCpmkPenilaianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpl_mk_cpmk_penilaian', function (Blueprint $table) {
            $table->id();
            $table->string('mk_kode');
            $table->integer('cpmk_id');
            $table->integer('cpl_id');
            $table->string('tahap_penilaian');
            $table->string('instrumen');
            $table->timestamps();

            $table->foreign('mk_kode')->references('kode')->on('mks')->onDelete('cascade');
            $table->foreign('cpmk_id')->references('id')->on('cpmks')->onDelete('cascade');
            $table->foreign('cpl_id')->references('id')->on('cpls')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cpl_mk_cpmk_penilaian');
    }
}
