<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('cpls')) {
            Schema::create('cpls', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('aspek')->nullable();
                $table->string('kode')->nullable();
                $table->string('nomor')->nullable();
                $table->text('judul')->nullable();
                $table->unsignedBigInteger('id_kurikulum')->nullable();
                $table->unsignedBigInteger('id_prodi')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpls');
    }
};
