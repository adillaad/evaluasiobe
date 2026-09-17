<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mk_kurikulum')) {
            Schema::create('mk_kurikulum', function (Blueprint $table) {
                $table->id();
                $table->string('mk_kode');
                $table->integer('id_kurikulum');
                $table->integer('id_prodi')->nullable();
                $table->string('semester')->nullable();
                $table->timestamps();

                $table->unique(['mk_kode', 'id_kurikulum']);
                $table->foreign('mk_kode')->references('kode')->on('mks')->onDelete('cascade');
                $table->foreign('id_kurikulum')->references('id')->on('kurikulums')->onDelete('cascade');
                $table->foreign('id_prodi')->references('id')->on('prodi')->onDelete('cascade');
            });
        }

        // Backfill existing id_kurikulum assignments from mks table into mk_kurikulum
        $mks = DB::table('mks')->whereNotNull('id_kurikulum')->get();
        foreach ($mks as $mk) {
            DB::table('mk_kurikulum')->updateOrInsert(
                [
                    'mk_kode' => $mk->kode,
                    'id_kurikulum' => $mk->id_kurikulum,
                ],
                [
                    'id_prodi' => $mk->id_prodi,
                    'semester' => (string)$mk->semester,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_kurikulum');
    }
};
