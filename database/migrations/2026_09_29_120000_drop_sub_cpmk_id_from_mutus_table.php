<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mutus') && Schema::hasColumn('mutus', 'sub_cpmk_id')) {
            Schema::table('mutus', function (Blueprint $table) {
                // Drop foreign key dulu sebelum drop kolom
                try {
                    $table->dropForeign('mutus_sub_cpmk_id_foreign');
                } catch (\Exception $e) {
                    // FK mungkin tidak ada, lanjut
                }
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
