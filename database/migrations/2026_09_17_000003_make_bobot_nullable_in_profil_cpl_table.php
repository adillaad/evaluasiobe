<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('profil_cpl') && Schema::hasColumn('profil_cpl', 'bobot')) {
            Schema::table('profil_cpl', function (Blueprint $table) {
                $table->decimal('bobot', 8, 2)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('profil_cpl') && Schema::hasColumn('profil_cpl', 'bobot')) {
            Schema::table('profil_cpl', function (Blueprint $table) {
                $table->decimal('bobot', 8, 2)->nullable(false)->change();
            });
        }
    }
};
