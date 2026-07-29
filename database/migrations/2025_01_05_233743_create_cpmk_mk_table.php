<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpmkMkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpmk_mk', function (Blueprint $table) {
            $table->id();
            $table->string('mk_kode');
            $table->integer('cpmk_id');
            $table->timestamps();

            $table->foreign('mk_kode')->references('kode')->on('mks')->onDelete('cascade');
            $table->foreign('cpmk_id')->references('id')->on('cpmks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cpmk_mk');
    }
}
