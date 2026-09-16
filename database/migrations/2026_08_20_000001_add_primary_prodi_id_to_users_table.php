<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPrimaryProdiIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'primary_prodi_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('primary_prodi_id')->nullable()->after('id_prodiUser');
            });
        }

        // Populate primary_prodi_id for existing users
        DB::statement("UPDATE users SET primary_prodi_id = id_prodiUser WHERE id_prodiUser IS NOT NULL AND primary_prodi_id IS NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'primary_prodi_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('primary_prodi_id');
            });
        }
    }
}
