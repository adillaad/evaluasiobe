<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBkMkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bk_mk', function (Blueprint $table) {
            $table->id();
            $table->string('mk_kode');
            $table->unsignedBigInteger('bk_id');
            $table->timestamps();

            $table->foreign('mk_kode')->references('kode')->on('mks')->onDelete('cascade');
            $table->foreign('bk_id')->references('id')->on('bks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bk_mk');
    }
}
