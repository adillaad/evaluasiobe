<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfesiCpmkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profesi_cpmk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profesi_id');
            $table->integer('cpmk_id');
            $table->timestamps();

            $table->foreign('profesi_id')->references('id')->on('profesi')->onDelete('cascade');
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
        Schema::dropIfExists('profesi_cpmk');
    }
}
