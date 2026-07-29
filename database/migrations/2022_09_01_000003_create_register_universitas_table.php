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
        if (!Schema::hasTable('register_universitas')) {
            Schema::create('register_universitas', function (Blueprint $table) {
                $table->id();
                $table->string('nama_universitas')->nullable();
                $table->string('nama_fakultas')->nullable();
                $table->string('nama_prodi')->nullable();
                $table->boolean('is_aptikom')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_universitas');
    }
};
