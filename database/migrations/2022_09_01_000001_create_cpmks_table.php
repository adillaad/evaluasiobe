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
        if (!Schema::hasTable('cpmks')) {
            Schema::create('cpmks', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('kode_mk')->nullable();
                $table->string('kode')->nullable();
                $table->text('judul')->nullable();
                $table->unsignedBigInteger('id_prodi')->nullable();
                $table->integer('cpl_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpmks');
    }
};
