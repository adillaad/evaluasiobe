<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBksUniqueIndexfix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bks', function (Blueprint $table) {
          
            $table->unique(['id_prodi', 'kurikulum_id', 'kode']);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bks', function (Blueprint $table) {
            $table->dropUnique(['id_prodi', 'kurikulum_id', 'kode']);
            $table->unique(['id_prodi', 'kode']); // kalau mau rollback ke semula
        });
        
    }
}
