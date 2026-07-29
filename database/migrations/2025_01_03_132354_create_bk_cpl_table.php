<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBkCplTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bk_cpl', function (Blueprint $table) {
            $table->id();
            $table->integer('cpl_id');
            $table->unsignedBigInteger('bk_id');
            $table->timestamps();

            $table->foreign('cpl_id')->references('id')->on('cpls')->onDelete('cascade');
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
        Schema::dropIfExists('bk_cpl');
    }
}
