<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Services\EvaluasiSyncService;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. UPDATE konversi_cpmk_metode
        if (Schema::hasTable('konversi_cpmk_metode') && Schema::hasTable('konversi_metode')) {
            // Standarisasi nama_soal null/kosong menjadi 'Soal #1'
            DB::table('konversi_cpmk_metode')
                ->where(function($q) {
                    $q->whereNull('nama_soal')
                      ->orWhere('nama_soal', '');
                })
                ->update(['nama_soal' => 'Soal #1']);

            // Recalculate bobot efektif di konversi_cpmk_metode
            $allKm = DB::table('konversi_metode')->get();
            foreach ($allKm as $km) {
                $metodeBobot = (float)$km->bobot;
                $cms = DB::table('konversi_cpmk_metode')->where('konversi_metode_id', $km->id)->get();
                if ($cms->isEmpty()) continue;

                $uniqueCpmkCount = $cms->pluck('cpmk_id')->unique()->count() ?: 1;
                $cpmkPortion = $metodeBobot > 0 ? ($metodeBobot / $uniqueCpmkCount) : 0;

                $groupedByCpmk = $cms->groupBy('cpmk_id');

                foreach ($groupedByCpmk as $cpmkId => $soalGroup) {
                    $numSoals = $soalGroup->count();
                    $sumBobot = $soalGroup->sum(fn($s) => (float)$s->bobot_soal);

                    foreach ($soalGroup as $soal) {
                        $current = (float)$soal->bobot_soal;
                        if ($numSoals === 1) {
                            $target = round($cpmkPortion, 4);
                        } else {
                            if (abs($sumBobot - 100) < 1.0) {
                                $target = round(($current / 100.0) * $cpmkPortion, 4);
                            } else if ($sumBobot > 0 && abs($sumBobot - $cpmkPortion) > 0.01) {
                                $target = round(($current / $sumBobot) * $cpmkPortion, 4);
                            } else {
                                $target = $current;
                            }
                        }

                        if (abs($current - $target) > 0.0001) {
                            DB::table('konversi_cpmk_metode')
                                ->where('id', $soal->id)
                                ->update(['bobot_soal' => $target]);
                        }
                    }
                }
            }
        }

        // 2. UPDATE mutus (sumber = 'konversi')
        if (Schema::hasTable('mutus')) {
            // Standarisasi soal null/kosong/'Soal <id>' menjadi 'Soal #1'
            DB::table('mutus')
                ->where('sumber', 'konversi')
                ->where(function($q) {
                    $q->whereNull('soal')
                      ->orWhere('soal', '')
                      ->orWhere('soal', 'like', 'Soal 1%');
                })
                ->update(['soal' => 'Soal #1']);

            // Update BobotSoal ke bobot efektif
            $mutus = DB::table('mutus')
                ->where('sumber', 'konversi')
                ->get(['id', 'NPM', 'Course', 'Jenis', 'Cpmk', 'soal', 'BobotSoal', 'examWeight', 'konversi_metode_id']);

            $byCourseAndJenis = $mutus->groupBy(function($item) {
                return $item->Course . '___' . $item->Jenis . '___' . ($item->konversi_metode_id ?: 'no_km');
            });

            foreach ($byCourseAndJenis as $groupKey => $records) {
                $first = $records->first();
                $examWeight = (float)($first->examWeight ?? 0);
                $kmId = $first->konversi_metode_id;

                $kcmMap = [];
                if ($kmId && Schema::hasTable('konversi_cpmk_metode')) {
                    $cms = DB::table('konversi_cpmk_metode')->where('konversi_metode_id', $kmId)->get();
                    foreach ($cms as $cm) {
                        $cKey = (string)$cm->cpmk_id;
                        $sKey = trim((string)$cm->nama_soal);
                        $kcmMap[$cKey . '___' . $sKey] = (float)$cm->bobot_soal;
                        if (!isset($kcmMap[$cKey . '___default'])) {
                            $kcmMap[$cKey . '___default'] = (float)$cm->bobot_soal;
                        }
                    }
                }

                $cpmkList = $records->pluck('Cpmk')->filter()->unique()->values();
                $uniqueCpmkCount = $cpmkList->count() ?: 1;
                $cpmkPortion = $examWeight > 0 ? ($examWeight / $uniqueCpmkCount) : 0;

                $recordsByNpm = $records->groupBy('NPM');
                $sampleMhs = $recordsByNpm->first();
                $groupedByCpmk = $sampleMhs ? $sampleMhs->groupBy(fn($si) => (string)($si->Cpmk ?? 'cpmk_default')) : collect();

                foreach ($records as $record) {
                    $currentBobot = (float)($record->BobotSoal ?? 0);
                    $cpmkKey = (string)($record->Cpmk ?? 'cpmk_default');
                    $soalKey = trim((string)($record->soal ?? ''));

                    $targetBobot = null;
                    if (isset($kcmMap[$cpmkKey . '___' . $soalKey]) && $kcmMap[$cpmkKey . '___' . $soalKey] > 0) {
                        $targetBobot = $kcmMap[$cpmkKey . '___' . $soalKey];
                    } else if (isset($kcmMap[$cpmkKey . '___default']) && $kcmMap[$cpmkKey . '___default'] > 0) {
                        $targetBobot = $kcmMap[$cpmkKey . '___default'];
                    } else {
                        $soalsInCpmk = $groupedByCpmk->get($cpmkKey, collect());
                        $numSoals = $soalsInCpmk->count() ?: 1;
                        $totalInputInCpmk = $soalsInCpmk->sum(fn($s) => (float)($s->BobotSoal ?? 0));

                        if ($cpmkPortion > 0) {
                            if ($numSoals > 1 && $totalInputInCpmk > 0) {
                                $rasio = $currentBobot / $totalInputInCpmk;
                                $targetBobot = round($rasio * $cpmkPortion, 4);
                            } else {
                                $targetBobot = round($cpmkPortion, 4);
                            }
                        } else {
                            $targetBobot = $currentBobot;
                        }
                    }

                    if ($targetBobot !== null && abs($currentBobot - $targetBobot) > 0.0001) {
                        DB::table('mutus')
                            ->where('id', $record->id)
                            ->update(['BobotSoal' => $targetBobot]);
                    }
                }
            }
        }

        // 3. Sinkronkan snapshot dan tabel evaluasi OBE jika class EvaluasiSyncService ada
        try {
            if (class_exists(EvaluasiSyncService::class)) {
                $syncService = app(EvaluasiSyncService::class);
                $syncService->syncAll();
            }
        } catch (\Throwable $e) {
            // Log jika terjadi error sync agar migration tidak gagal
            \Illuminate\Support\Facades\Log::warning('EvaluasiSyncService failed during migration: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Data update migration tidak memerlukan rollback destruktif
    }
};
