<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMkSubCpmkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mk_sub_cpmk', function (Blueprint $table) {
            $table->id();
            $table->string('sub_cpmk_kode');
            $table->string('mk_kode');
            $table->timestamps();

            $table->foreign('sub_cpmk_kode')->references('kode')->on('sub_cpmk')->onDelete('cascade');
            $table->foreign('mk_kode')->references('kode')->on('mks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mk_sub_cpmk');
    }
}
