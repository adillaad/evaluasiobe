<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill NULL created_at and updated_at in mk_cpl table.
     * Old data was inserted without timestamps (via seeders/imports).
     * We set a default fallback date of 2023-01-01 for historical records.
     */
    public function up(): void
    {
        // For rows with updated_at but no created_at, copy updated_at
        DB::table('mk_cpl')
            ->whereNull('created_at')
            ->whereNotNull('updated_at')
            ->update(['created_at' => DB::raw('updated_at')]);

        // For rows with both null, set a default historical date
        $fallbackDate = '2023-01-01 00:00:00';
        DB::table('mk_cpl')
            ->whereNull('created_at')
            ->whereNull('updated_at')
            ->update([
                'created_at' => $fallbackDate,
                'updated_at' => $fallbackDate,
            ]);
    }

    public function down(): void
    {
        // Cannot reliably reverse a backfill — no-op
    }
};
