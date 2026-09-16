<?php

namespace App\Console\Commands;

use App\Services\EvaluasiSyncService;
use Illuminate\Console\Command;

class SyncEvaluasiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluasi:sync {--npm= : Synchronize specific student NPM} {--prodi= : Synchronize specific Prodi ID} {--angkatan= : Synchronize specific Angkatan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize and persist all OBE visualisasi and evaluasi data into database tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(EvaluasiSyncService $syncService)
    {
        $this->info('Starting Evaluasi OBE database synchronization...');

        $npm = $this->option('npm');
        $prodi = $this->option('prodi');
        $angkatan = $this->option('angkatan');

        if ($npm) {
            $this->info("Synchronizing Mahasiswa NPM: {$npm}...");
            $syncService->syncMahasiswa($npm);
            $syncService->syncTranskripMahasiswa($npm);
            $this->info("Mahasiswa {$npm} synchronized successfully.");
            return 0;
        }

        if ($prodi && $angkatan) {
            $this->info("Synchronizing Prodi ID: {$prodi}, Angkatan: {$angkatan}...");
            $syncService->syncAngkatan($prodi, $angkatan);
            $this->info("Prodi and cohort synchronized successfully.");
            return 0;
        }

        $this->info('Running full synchronization across all students, courses, cohorts, and dashboards...');
        $syncService->syncAll();
        $this->info('All 7 Visualisasi & Evaluasi modules have been successfully synchronized and stored in the database!');

        return 0;
    }
}
