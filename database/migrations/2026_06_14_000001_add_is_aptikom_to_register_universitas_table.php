<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAptikomToRegisterUniversitasTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('register_universitas')) {
            Schema::table('register_universitas', function (Blueprint $table) {
                if (!Schema::hasColumn('register_universitas', 'is_aptikom')) {
                    $table->boolean('is_aptikom')->default(false)->after('nama_prodi');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('register_universitas', function (Blueprint $table) {
            if (Schema::hasColumn('register_universitas', 'is_aptikom')) {
                $table->dropColumn('is_aptikom');
            }
        });
    }
}
