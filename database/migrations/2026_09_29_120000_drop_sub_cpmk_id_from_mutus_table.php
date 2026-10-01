<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mutus') && Schema::hasColumn('mutus', 'sub_cpmk_id')) {
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE mutus DROP FOREIGN KEY mutus_sub_cpmk_id_foreign");
            } catch (\Throwable $e) {
                // Ignore if foreign key does not exist
            }
            Schema::table('mutus', function (Blueprint $table) {
                $table->dropColumn('sub_cpmk_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mutus') && !Schema::hasColumn('mutus', 'sub_cpmk_id')) {
            Schema::table('mutus', function (Blueprint $table) {
                $table->unsignedBigInteger('sub_cpmk_id')->nullable();
            });
        }
    }
};
