<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMutusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutus', function (Blueprint $table) {
            $table->id();
            $table->integer('NPM');
            $table->string('Nama');
            $table->string('Course');
            $table->string('Jenis',10);
            $table->string('Bobot',3);
            $table->string('Cpl');
            $table->string('Cpmk');
            $table->string('Soal');
            $table->integer('Nilai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mutus');
    }
}
