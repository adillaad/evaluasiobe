<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\CPMK;
use App\Models\KonversiCpmkMetode;
use App\Models\KonversiMetode;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MetodePenilaian;
use App\Models\MK;
use App\Models\Mutu;
use App\Models\PenilaianKonversi;
use App\Models\Prodi;
use App\Models\SubCpmk;
use App\Models\TahunAjaran;
use App\Exports\KonversiNilaiTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class KonversiNilaiController extends Controller
{
    public function __construct()
    {
        // Auto-check and add missing database columns if migration wasn't executed
        try {
            if (Schema::hasTable('konversi_metode') && !Schema::hasColumn('konversi_metode', 'bobot')) {
                Schema::table('konversi_metode', function ($table) {
                    $table->float('bobot')->default(0)->after('metode_id');
                });
            }
            if (!Schema::hasTable('penilaian_konversi')) {
                Schema::create('penilaian_konversi', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('dosen_id');
                    $table->string('mk_kode');
                    $table->unsignedBigInteger('tahun_ajaran_id');
                    $table->unsignedBigInteger('kurikulum_id');
                    $table->timestamps();
                });
            }
            if (!Schema::hasTable('konversi_metode')) {
                Schema::create('konversi_metode', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('penilaian_konversi_id');
                    $table->unsignedBigInteger('metode_id');
                    $table->float('bobot')->default(0);
                    $table->timestamps();
                });
            }
            if (!Schema::hasTable('konversi_cpmk_metode')) {
                Schema::create('konversi_cpmk_metode', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('konversi_metode_id');
                    $table->unsignedBigInteger('cpmk_id');
                    $table->unsignedBigInteger('sub_cpmk_id')->nullable();
                    $table->string('nama_soal')->nullable();
                    $table->float('bobot_soal')->default(0);
                    $table->timestamps();
                });
            } else {
                Schema::table('konversi_cpmk_metode', function ($table) {
                    if (!Schema::hasColumn('konversi_cpmk_metode', 'nama_soal')) $table->string('nama_soal')->nullable();
                    if (!Schema::hasColumn('konversi_cpmk_metode', 'bobot_soal')) $table->float('bobot_soal')->default(0);
                });
            }
            if (Schema::hasTable('mutus')) {
                Schema::table('mutus', function ($table) {
                    if (!Schema::hasColumn('mutus', 'sumber')) $table->string('sumber')->nullable();
                    if (!Schema::hasColumn('mutus', 'tahun_ajaran_id')) $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
                    if (!Schema::hasColumn('mutus', 'konversi_metode_id')) $table->unsignedBigInteger('konversi_metode_id')->nullable();
                    if (!Schema::hasColumn('mutus', 'sub_cpmk_id')) $table->unsignedBigInteger('sub_cpmk_id')->nullable();
                    if (!Schema::hasColumn('mutus', 'namaCourse')) $table->string('namaCourse')->nullable();
                    if (!Schema::hasColumn('mutus', 'nama_mhs')) $table->string('nama_mhs')->nullable();
                    if (!Schema::hasColumn('mutus', 'Nama_mhs')) $table->string('Nama_mhs')->nullable();
                    if (!Schema::hasColumn('mutus', 'npm')) $table->string('npm')->nullable();
                    if (!Schema::hasColumn('mutus', 'nilaiSoal')) $table->float('nilaiSoal')->nullable();
                    if (!Schema::hasColumn('mutus', 'BobotSoal')) $table->float('BobotSoal')->nullable();
                    if (!Schema::hasColumn('mutus', 'examWeight')) $table->float('examWeight')->nullable();
                    if (!Schema::hasColumn('mutus', 'id_mahasiswa')) $table->unsignedBigInteger('id_mahasiswa')->nullable();
                });

                // Drop foreign key on mutus that depends on mutus_konversi_unique index first
                try {
                    DB::statement("SET FOREIGN_KEY_CHECKS=0;");
                    try { DB::statement("ALTER TABLE `mutus` ADD INDEX `idx_km_id` (`konversi_metode_id`)"); } catch (\Exception $e) {}
                    try { DB::statement("ALTER TABLE `mutus` DROP FOREIGN KEY `fk_mutus_konversi`"); } catch (\Exception $e) {}
                    try { DB::statement("ALTER TABLE `mutus` DROP FOREIGN KEY `mutus_konversi_unique`"); } catch (\Exception $e) {}
                    try { DB::statement("ALTER TABLE `mutus` DROP INDEX `mutus_konversi_unique`"); } catch (\Exception $e) {}
                    try { DB::statement("ALTER TABLE `mutus` ADD INDEX `idx_mutus_konversi` (`id_mahasiswa`, `Course`, `konversi_metode_id`)"); } catch (\Exception $e) {}
                    DB::statement("SET FOREIGN_KEY_CHECKS=1;");
                } catch (\Exception $e) {
                    DB::statement("SET FOREIGN_KEY_CHECKS=1;");
                }

                // Create non-unique index on konversi_metode_id first so foreign key is preserved, then drop unique index
                try {
                    DB::statement("ALTER TABLE `konversi_cpmk_metode` ADD INDEX `idx_konversi_metode_id` (`konversi_metode_id`)");
                } catch (\Exception $e) {}

                try {
                    DB::statement("ALTER TABLE `konversi_cpmk_metode` DROP INDEX `konversi_cpmk_metode_unique`");
                } catch (\Exception $e) {}

                // Run automatic backfill for mutus records where tahun_ajaran_id is NULL
                if (Schema::hasColumn('mutus', 'tahun')) {
                    $nullMutus = DB::table('mutus')->whereNull('tahun_ajaran_id')->whereNotNull('tahun')->limit(500)->get(['id', 'tahun']);
                    foreach ($nullMutus as $row) {
                        $rawTahun = trim((string)$row->tahun);
                        if (empty($rawTahun)) continue;

                        $jenisSemester = (stripos($rawTahun, 'Genap') !== false) ? 'Genap' : 'Ganjil';
                        preg_match('/\d{4}/', $rawTahun, $matches);
                        $tahunAngka = $matches[0] ?? date('Y');

                        $ta = DB::table('tahun_ajaran')
                            ->where('tahun', 'like', "%{$tahunAngka}%")
                            ->where('jenis_semester', $jenisSemester)
                            ->first();

                        if (!$ta) {
                            $taId = DB::table('tahun_ajaran')->insertGetId([
                                'tahun' => (string)$tahunAngka,
                                'jenis_semester' => $jenisSemester,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } else {
                            $taId = $ta->id;
                        }

                        DB::table('mutus')->where('id', $row->id)->update(['tahun_ajaran_id' => $taId]);
                    }
                }

                // Run automatic backfill for mutus records where Cpl is NULL
                if (Schema::hasColumn('mutus', 'Cpl') && Schema::hasColumn('mutus', 'Cpmk')) {
                    DB::statement("
                        UPDATE mutus m
                        JOIN cpmks c ON m.Cpmk = c.id
                        SET m.Cpl = c.cpl_id
                        WHERE m.Cpl IS NULL AND c.cpl_id IS NOT NULL AND m.sumber = 'konversi'
                    ");
                }
            }
        } catch (\Exception $e) {
            // Ignore schema setup errors if already up-to-date
        }
    }

    // Daftar Konversi Nilai Dosen
    public function index()
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        $konversis = PenilaianKonversi::where('dosen_id', $user->id)
            ->with(['mk', 'tahunAjaran', 'kurikulum', 'konversiMetode.metodePenilaian'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('dosen.konversi.index', compact('konversis', 'userOtoritas'));
    }

    // Step 1: Form Setup Konversi (MK, Tahun Ajaran, Kurikulum)
    public function create()
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        $prodiId = $user->id_prodiUser ?: 1;

        $mks = MK::where('id_prodi', $prodiId)->get();
        if ($mks->isEmpty()) {
            $mks = MK::all();
        }

        $tahunAjarans = TahunAjaran::orderBy('tahun', 'desc')->get();
        
        $kurikulums = Kurikulum::where('id_prodi', $prodiId)->orderBy('tahun', 'desc')->get();
        if ($kurikulums->isEmpty()) {
            $kurikulums = Kurikulum::orderBy('tahun', 'desc')->get();
        }

        return view('dosen.konversi.create', compact('mks', 'tahunAjarans', 'kurikulums', 'userOtoritas'));
    }

    // Ajax handler untuk mengambil MK berdasarkan Kurikulum
    public function getMksByKurikulum($kurikulumId)
    {
        $user = auth()->user();
        $prodiId = $user->id_prodiUser ?: 1;

        $mks = MK::where('id_kurikulum', $kurikulumId)->get();
        if ($mks->isEmpty()) {
            // Fallback jika id_kurikulum di mks mengacu ke string kurikulum atau prodi
            $kurikulum = Kurikulum::find($kurikulumId);
            if ($kurikulum) {
                $mks = MK::where('id_prodi', $prodiId)
                    ->where(function($q) use ($kurikulum) {
                        $q->where('id_kurikulum', $kurikulum->id)
                          ->orWhere('kurikulum', $kurikulum->tahun);
                    })->get();
            }
        }

        return response()->json($mks->map(function ($mk) {
            return [
                'kode' => $mk->kode,
                'nama' => $mk->nama,
                'total_sks' => $mk->total_sks,
            ];
        }));
    }

    // Step 1 Store: Simpan ke penilaian_konversi
    public function storeSetup(Request $request)
    {
        $user = auth()->user();

        // Validasi pembatasan jalur import dinonaktifkan sementara (dihapus/di-bypass)
        /*
        $hasRegulerImport = Mutu::where('Course', $request->mk_kode)
            ->where(function ($q) use ($request) {
                $q->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                  ->orWhere('tahun', function ($sub) use ($request) {
                      $sub->select('tahun')->from('tahun_ajaran')->where('id', $request->tahun_ajaran_id);
                  });
            })
            ->where(function ($q) {
                $q->whereNull('sumber')->orWhere('sumber', '!=', 'konversi');
            })
            ->exists();

        if ($hasRegulerImport) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat setup konversi! Mata Kuliah dan Tahun Ajaran ini sudah memiliki data penilaian dari jalur Normal (Per Soal). Satu kombinasi MK dan Tahun Ajaran hanya boleh memilih SATU jalur import.');
        }
        */

        $konversi = PenilaianKonversi::create([
            'dosen_id' => $user->id,
            'mk_kode' => $request->mk_kode,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'kurikulum_id' => $request->kurikulum_id,
        ]);

        return redirect()->route('dosen.konversi-nilai.step-metode', $konversi->id)
            ->with('success', 'Setup konversi berhasil disimpan. Silakan lanjutkan ke pemilihan metode & pemetaan CPMK.');
    }

    // Step 2 & 3: Kelola Metode & Pemetaan CPMK/Sub-CPMK
    public function stepMetode($id)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        $konversi = PenilaianKonversi::with(['mk', 'tahunAjaran', 'kurikulum', 'konversiMetode.cpmkMetode'])
            ->where('dosen_id', $user->id)
            ->findOrFail($id);

        $prodiId = $konversi->mk->id_prodi ?: ($user->id_prodiUser ?: 1);

        // Ambil master metode penilaian prodi
        $masterMetodes = MetodePenilaian::where('id_prodi', $prodiId)->get();
        if ($masterMetodes->isEmpty()) {
            $masterMetodes = MetodePenilaian::all();
        }

        // Ambil CPMK milik MK ini via relasi cpmks / cpmk_mk atau kode_mk
        $cpmks = $konversi->mk->cpmks()->with('subCpmks')->get();
        if ($cpmks->isEmpty()) {
            $cpmks = CPMK::whereHas('mks', function ($q) use ($konversi) {
                $q->where('kode', $konversi->mk_kode);
            })->with('subCpmks')->get();
        }

        // Rekonstruksi data existing jika sudah pernah disimpan
        $existingMetodes = $konversi->konversiMetode->keyBy('metode_id');
        
        // Count mapped CPMKs
        $mappedCpmkIds = [];
        foreach ($konversi->konversiMetode as $km) {
            foreach ($km->cpmkMetode as $cm) {
                if (!in_array($cm->cpmk_id, $mappedCpmkIds)) {
                    $mappedCpmkIds[] = $cm->cpmk_id;
                }
            }
        }

        $totalCpmkCount = $cpmks->count();
        $mappedCpmkCount = count($mappedCpmkIds);
        $totalBobotSum = $konversi->konversiMetode->sum('bobot');
        $isComplete = ($totalBobotSum == 100) && ($mappedCpmkCount >= $totalCpmkCount) && ($totalCpmkCount > 0);

        return view('dosen.konversi.step_metode', compact(
            'konversi',
            'masterMetodes',
            'cpmks',
            'existingMetodes',
            'totalCpmkCount',
            'mappedCpmkCount',
            'totalBobotSum',
            'isComplete',
            'userOtoritas'
        ));
    }

    // Step 2 & 3 Store: Simpan metode + bobot + pemetaan CPMK/Sub-CPMK
    public function storeMetodeCpmk(Request $request, $id)
    {
        $user = auth()->user();

        $konversi = PenilaianKonversi::with('mk')
            ->where('dosen_id', $user->id)
            ->findOrFail($id);

        $request->validate([
            'metode_ids' => 'required|array|min:1',
            'metode_ids.*' => 'required|integer|exists:metode_penilaian,id',
            'bobot' => 'required|array',
            'bobot.*' => 'required|numeric|min:0.01|max:100',
        ]);

        $metodeIds = $request->metode_ids;
        $bobotInputs = $request->bobot;
        $cpmkMapping = $request->input('cpmk_mapping', []); // [metode_id => [cpmk_id => [sub_cpmk_id or 0]]]
        $cpmkSoal = $request->input('cpmk_soal', []); // [metode_id => [cpmk_id => [ ['nama_soal' => '...', 'bobot_soal' => ...], ... ]]]

        // 1. Validasi Total Bobot Metode == 100
        $totalBobot = 0;
        foreach ($metodeIds as $mId) {
            $totalBobot += (float) ($bobotInputs[$mId] ?? 0);
        }

        if (round($totalBobot, 2) != 100) {
            return redirect()->back()->with('error', 'Total bobot semua metode penilaian harus 100%. Total saat ini: ' . number_format($totalBobot, 2) . '%');
        }

        // 2. Validasi Completeness CPMK (semua CPMK milik MK harus terpetakan)
        $allMkCpmks = $konversi->mk->cpmks;
        if ($allMkCpmks->isEmpty()) {
            $allMkCpmks = CPMK::whereHas('mks', function($q) use ($konversi) {
                $q->where('kode', $konversi->mk_kode);
            })->get();
        }
        $allMkCpmkIds = $allMkCpmks->pluck('id')->toArray();

        $selectedCpmkIds = [];
        foreach ($cpmkMapping as $mId => $cpmksData) {
            if (in_array($mId, $metodeIds)) {
                foreach ($cpmksData as $cpmkId => $subCpmksData) {
                    if (!in_array($cpmkId, $selectedCpmkIds)) {
                        $selectedCpmkIds[] = (int) $cpmkId;
                    }
                }
            }
        }

        $missingCpmks = array_diff($allMkCpmkIds, $selectedCpmkIds);
        if (!empty($missingCpmks) && !empty($allMkCpmkIds)) {
            $missingNames = CPMK::whereIn('id', $missingCpmks)->pluck('kode')->implode(', ');
            return redirect()->back()->with('error', "Semua CPMK milik Mata Kuliah ini harus terpetakan ke minimal 1 metode penilaian. CPMK belum terpetakan: {$missingNames}");
        }

        // 3. Validasi Total Kontribusi Soal per CPMK == 100% (jika memuat LEBIH DARI 1 breakdown soal)
        foreach ($cpmkSoal as $mId => $cpmksData) {
            foreach ($cpmksData as $cpmkId => $soalItems) {
                if (is_array($soalItems) && count($soalItems) > 1) {
                    $sumBobotSoal = 0;
                    foreach ($soalItems as $sItem) {
                        $sumBobotSoal += (float) ($sItem['bobot_soal'] ?? 0);
                    }
                    if (round($sumBobotSoal, 2) != 100) {
                        $cpmkCode = CPMK::find($cpmkId)->kode ?? 'CPMK';
                        return redirect()->back()->with('error', "Total bobot kontribusi soal pada {$cpmkCode} harus bernilai 100%. Total saat ini: " . number_format($sumBobotSoal, 2) . '%');
                    }
                }
            }
        }

        DB::beginTransaction();
        try {
            // Hapus record metode_penilaian yang sudah tidak lagi dipilih di form
            $existingKms = KonversiMetode::where('penilaian_konversi_id', $konversi->id)->get();
            foreach ($existingKms as $exKm) {
                if (!in_array($exKm->metode_id, $metodeIds)) {
                    KonversiCpmkMetode::where('konversi_metode_id', $exKm->id)->delete();
                    $exKm->delete();
                }
            }

            foreach ($metodeIds as $mId) {
                $bobotVal = (float) ($bobotInputs[$mId] ?? 0);

                // Gunakan updateOrCreate agar tidak melanggar unique constraint (penilaian_konversi_id, metode_id)
                $km = KonversiMetode::updateOrCreate(
                    [
                        'penilaian_konversi_id' => $konversi->id,
                        'metode_id' => $mId,
                    ],
                    [
                        'bobot' => $bobotVal,
                    ]
                );

                // Hapus pemetaan CPMK lama milik metode ini sebelum menyimpan yang baru
                KonversiCpmkMetode::where('konversi_metode_id', $km->id)->delete();

                // Update record di mutus jika metode ini sebelumnya pernah di-import nilainya
                Mutu::where('konversi_metode_id', $km->id)->update([
                    'BobotSoal' => $bobotVal,
                    'examWeight' => $bobotVal,
                ]);

                // Simpan pemetaan CPMK & Sub-CPMK & Breakdown Soal jika ada
                if (isset($cpmkMapping[$mId])) {
                    $selectedCpmksForMetode = array_keys($cpmkMapping[$mId]);
                    $numCpmks = count($selectedCpmksForMetode);
                    // Porsi bobot metode yang dibagi rata ke masing-masing CPMK
                    $cpmkPortion = $numCpmks > 0 ? ($bobotVal / $numCpmks) : $bobotVal;

                    foreach ($cpmkMapping[$mId] as $cpmkId => $subData) {
                        $soalList = $cpmkSoal[$mId][$cpmkId] ?? [];
                        
                        if (!empty($soalList) && is_array($soalList)) {
                            // Dosen menambah breakdown soal spesifik pada CPMK ini
                            $soalIdx = 1;
                            foreach ($soalList as $sItem) {
                                $namaSoal = !empty($sItem['nama_soal']) ? trim($sItem['nama_soal']) : ('Soal #' . $soalIdx);
                                $relBobot = (isset($sItem['bobot_soal']) && $sItem['bobot_soal'] !== '') ? (float)$sItem['bobot_soal'] : 100;
                                $soalIdx++;

                                $effBobotSoal = round(($relBobot / 100.0) * $cpmkPortion, 4);

                                $subIds = is_array($subData) ? array_filter($subData, fn($v) => (int)$v > 0) : [];
                                if (!empty($subIds)) {
                                    foreach ($subIds as $subId) {
                                        KonversiCpmkMetode::create([
                                            'konversi_metode_id' => $km->id,
                                            'cpmk_id' => $cpmkId,
                                            'sub_cpmk_id' => $subId,
                                            'nama_soal' => $namaSoal,
                                            'bobot_soal' => $effBobotSoal,
                                        ]);
                                    }
                                } else {
                                    KonversiCpmkMetode::create([
                                        'konversi_metode_id' => $km->id,
                                        'cpmk_id' => $cpmkId,
                                        'sub_cpmk_id' => null,
                                        'nama_soal' => $namaSoal,
                                        'bobot_soal' => $effBobotSoal,
                                    ]);
                                }
                            }
                        } else {
                            // Standard tanpa breakdown soal (1 CPMK mengambil porsi cpmkPortion)
                            $effBobotSoal = round($cpmkPortion, 4);

                            if (is_array($subData) && !empty($subData)) {
                                $subIds = array_filter($subData, fn($v) => (int)$v > 0);
                                if (!empty($subIds)) {
                                    foreach ($subIds as $subId) {
                                        KonversiCpmkMetode::create([
                                            'konversi_metode_id' => $km->id,
                                            'cpmk_id' => $cpmkId,
                                            'sub_cpmk_id' => $subId,
                                            'nama_soal' => null,
                                            'bobot_soal' => $effBobotSoal,
                                        ]);
                                    }
                                } else {
                                    KonversiCpmkMetode::create([
                                        'konversi_metode_id' => $km->id,
                                        'cpmk_id' => $cpmkId,
                                        'sub_cpmk_id' => null,
                                        'nama_soal' => null,
                                        'bobot_soal' => $effBobotSoal,
                                    ]);
                                }
                            } else {
                                KonversiCpmkMetode::create([
                                    'konversi_metode_id' => $km->id,
                                    'cpmk_id' => $cpmkId,
                                    'sub_cpmk_id' => null,
                                    'nama_soal' => null,
                                    'bobot_soal' => $effBobotSoal,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Metode & Pemetaan CPMK berhasil disimpan. Anda sekarang dapat mengunduh template Excel.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan metode & pemetaan CPMK: ' . $e->getMessage());
        }
    }

    // Step 5: Download Template Excel
    public function downloadTemplate(Request $request, $id)
    {
        $user = auth()->user();

        $konversi = PenilaianKonversi::with(['mk', 'tahunAjaran', 'konversiMetode.metodePenilaian', 'konversiMetode.cpmkMetode.cpmk'])
            ->where('dosen_id', $user->id)
            ->findOrFail($id);

        if ($konversi->konversiMetode->isEmpty()) {
            return redirect()->back()->with('failed', 'Metode penilaian belum dikonfigurasi.');
        }

        $taLabel = isset($konversi->tahunAjaran) 
            ? ($konversi->tahunAjaran->tahun . ($konversi->tahunAjaran->jenis_semester ? ' - ' . $konversi->tahunAjaran->jenis_semester : ''))
            : '';
        $mkNama = $konversi->mk?->nama ? ($konversi->mk->nama . ' (' . $konversi->mk_kode . ')') : $konversi->mk_kode;

        $formatType = $request->query('format_type', 'standar'); // standar (A), metode_cpmk (B), breakdown_soal (C)
        $metodeIdParam = $request->query('metode_id');

        $headers = [];

        // Opsi C: Breakdown Soal per 1 Metode Penilaian
        if ($formatType === 'breakdown_soal') {
            $targetKm = null;
            if ($metodeIdParam) {
                $targetKm = $konversi->konversiMetode->firstWhere('metode_id', $metodeIdParam);
            }
            if (!$targetKm) {
                $targetKm = $konversi->konversiMetode->first();
            }

            $mNama = $targetKm->metodePenilaian->nama ?? 'Metode';
            $mBobot = $targetKm->bobot ?? 0;

            // Kolom Nilai Metode Murni (Input manual dosen)
            $headers[] = 'Nilai Metode ' . $mNama . ' (Bobot ' . $mBobot . '%)';

            // Kolom rincian soal: [Nama Soal] - [Kode CPMK]
            if ($targetKm && $targetKm->cpmkMetode->isNotEmpty()) {
                foreach ($targetKm->cpmkMetode as $cm) {
                    $cpmkKode = $cm->cpmk?->kode ?? ('CPMK-' . $cm->cpmk_id);
                    $soalName = !empty($cm->nama_soal) ? $cm->nama_soal : 'Soal';
                    $headers[] = $soalName . ' - ' . $cpmkKode;
                }
            } else {
                $headers[] = $mNama . ' - Soal 1';
            }

            $mNamaClean = preg_replace('/[^A-Za-z0-9_\-]/', '_', $mNama);
            $fileName = 'Template_Konversi_Soal_' . $mNamaClean . '_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $konversi->mk_kode) . '.xlsx';
        }
        // Opsi B: Nilai Akhir Per Metode & CPMK
        else if ($formatType === 'metode_cpmk') {
            foreach ($konversi->konversiMetode as $km) {
                $mNama = $km->metodePenilaian->nama ?? ('Metode ' . $km->metode_id);
                $mBobot = $km->bobot ?? 0;
                $uniqueCpmks = $km->cpmkMetode->unique('cpmk_id');

                // Kolom Nilai Metode Murni (Input manual dosen untuk Nilai Akhir Metode)
                $headers[] = 'Nilai Metode ' . $mNama . ' (Bobot ' . $mBobot . '%)';

                if ($uniqueCpmks->isNotEmpty()) {
                    foreach ($uniqueCpmks as $cm) {
                        $cpmkKode = $cm->cpmk?->kode ?? ('CPMK-' . $cm->cpmk_id);
                        $headers[] = $mNama . ' - ' . $cpmkKode;
                    }
                }
            }
            $fileName = 'Template_Konversi_Metode_CPMK_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $konversi->mk_kode) . '.xlsx';
        }
        // Opsi A: Nilai Akhir Per Metode (Format Standar / All Metode)
        else {
            $headers = $konversi->konversiMetode->map(fn($km) => $km->metodePenilaian->nama ?? 'Metode ' . $km->metode_id)->toArray();
            $fileName = 'Template_Konversi_Standar_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $konversi->mk_kode) . '.xlsx';
        }

        return Excel::download(new KonversiNilaiTemplateExport($headers, $taLabel, $mkNama), $fileName);
    }

    // Step 6: Validasi & Simpan Excel Upload
    public function uploadExcel(Request $request, $id)
    {
        $user = auth()->user();

        $konversi = PenilaianKonversi::with(['mk', 'tahunAjaran', 'konversiMetode.metodePenilaian', 'konversiMetode.cpmkMetode.cpmk'])
            ->where('dosen_id', $user->id)
            ->findOrFail($id);

        $actionOption = $request->input('action_option');
        $parsedRowsData = $request->input('parsed_rows_data') ?: session('temp_parsed_rows');
        $metodeMapData = $request->input('metode_column_map_data') ?: session('temp_metode_map');
        $formatType = $request->input('format_type') ?: session('temp_format_type', 'standar');
        $metodeIdParam = $request->input('metode_id') ?: session('temp_metode_id');

        // Skenario A: Submit konfirmasi dari Modal (tanpa upload ulang file Excel)
        if (!empty($actionOption) && !empty($parsedRowsData)) {
            $parsedRows = is_array($parsedRowsData) ? $parsedRowsData : (json_decode($parsedRowsData, true) ?: []);
            $rawMetodeMap = is_array($metodeMapData) ? $metodeMapData : (json_decode($metodeMapData, true) ?: []);
            
            $columnMap = [];
            foreach ($rawMetodeMap as $colIdx => $info) {
                $kmId = is_array($info) ? ($info['km_id'] ?? null) : $info;
                $km = $konversi->konversiMetode->firstWhere('id', $kmId);
                if ($km) {
                    $cmId = is_array($info) ? ($info['cm_id'] ?? null) : null;
                    $cpmkId = is_array($info) ? ($info['cpmk_id'] ?? null) : null;
                    $type = is_array($info) ? ($info['type'] ?? 'standar') : 'standar';
                    $nilaiMetodeCol = is_array($info) ? ($info['nilai_metode_col'] ?? null) : null;
                    
                    $cmList = [];
                    if ($cmId) {
                        $cmList = $km->cpmkMetode->where('id', $cmId);
                    } else if ($cpmkId) {
                        $cmList = $km->cpmkMetode->where('cpmk_id', $cpmkId);
                    } else {
                        $cmList = $km->cpmkMetode;
                    }

                    $columnMap[$colIdx] = [
                        'type' => $type,
                        'km' => $km,
                        'cm_list' => $cmList,
                        'cm' => $cmList->first(),
                        'nilai_metode_col' => $nilaiMetodeCol,
                    ];
                }
            }

            if (empty($columnMap)) {
                $colIdxCursor = 3;
                foreach ($konversi->konversiMetode as $km) {
                    $columnMap[$colIdxCursor] = [
                        'type' => 'standar',
                        'km' => $km,
                        'cm_list' => $km->cpmkMetode,
                    ];
                    $colIdxCursor++;
                }
            }
        } 
        // Skenario B: Upload file Excel pertama kali
        else if ($request->hasFile('excel_file')) {
            $file = $request->file('excel_file');
            if (!$file || !$file->isValid()) {
                return redirect()->back()->with('error', 'File Excel tidak valid.');
            }

            $filePath = $file->getRealPath() ?: $file->getPathname();
            
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $dataRows = $worksheet->toArray();
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
            }

            if (count($dataRows) <= 1) {
                return redirect()->back()->with('error', 'File Excel kosong atau hanya berisi header.');
            }

            // Ambil header row (baris 1)
            $headerRow = array_map(fn($h) => trim((string)$h), $dataRows[0]);
            unset($dataRows[0]); // Sisakan baris data

            $columnMap = []; // [colIndex => ['type' => ..., 'km' => ..., 'cm_list' => ..., 'cm' => ...]]
            $tempMetodeMapSession = []; // serializable array for session

            // --- Logika Pemetaan Kolom berdasarkan Format Type ---
            if ($formatType === 'breakdown_soal') {
                // Opsi C: Breakdown Soal per Metode Penilaian (Otomatis dideteksi dari file Excel)
                $targetKm = null;

                // 1. Prioritaskan deteksi otomatis berdasarkan nama atau kode metode di header kolom Excel
                foreach ($headerRow as $colIdx => $colName) {
                    if ($colIdx < 5) continue;
                    $colNameLower = strtolower(trim((string)$colName));

                    foreach ($konversi->konversiMetode as $km) {
                        $mNama = strtolower(trim($km->metodePenilaian->nama ?? ''));
                        $mKode = strtolower(trim($km->metodePenilaian->kode ?? ''));

                        if (!empty($mNama) && strpos($colNameLower, $mNama) !== false) {
                            $targetKm = $km;
                            break 2;
                        }
                        if (!empty($mKode) && strpos($colNameLower, $mKode) !== false) {
                            $targetKm = $km;
                            break 2;
                        }
                    }
                }

                // 2. Fallback jika tidak terdeteksi via header, tapi ada metodeIdParam
                if (!$targetKm && $metodeIdParam) {
                    $targetKm = $konversi->konversiMetode->firstWhere('metode_id', $metodeIdParam)
                        ?: $konversi->konversiMetode->firstWhere('id', $metodeIdParam);
                }

                // 3. Fallback terakhir: gunakan metode pertama
                if (!$targetKm) {
                    $targetKm = $konversi->konversiMetode->first();
                }

                if ($targetKm && $targetKm->cpmkMetode->isNotEmpty()) {
                    $cmItems = $targetKm->cpmkMetode->values();

                    // Cari indeks kolom Nilai Akhir Metode (header mengandung "Nilai Metode" / "Bobot")
                    $nilaiMetodeColIdx = null;
                    foreach ($headerRow as $colIdx => $colName) {
                        if ($colIdx < 5) continue;
                        if (strpos(strtolower($colName), 'nilai metode') !== false || strpos(strtolower($colName), 'bobot') !== false) {
                            $nilaiMetodeColIdx = $colIdx;
                            break;
                        }
                    }

                    $offset = ($nilaiMetodeColIdx !== null) ? ($nilaiMetodeColIdx + 1) : 5;

                    foreach ($headerRow as $colIdx => $colName) {
                        if ($colIdx < 5) continue; // Kolom 0: TA, 1: Nama MK, 2: Angkatan, 3: NPM, 4: Nama
                        if ($nilaiMetodeColIdx !== null && $colIdx === $nilaiMetodeColIdx) continue; // Skip kolom Nilai Metode murni

                        $matchedCm = null;
                        foreach ($cmItems as $cm) {
                            $cpmkKode = $cm->cpmk?->kode ?? ('CPMK-' . $cm->cpmk_id);
                            $expectedLabel1 = !empty($cm->nama_soal) ? ($cm->nama_soal . ' - ' . $cpmkKode) : $cpmkKode;
                            $expectedLabel2 = !empty($cm->nama_soal) ? ($cm->nama_soal . ' (' . $cpmkKode . ')') : $cpmkKode;
                            $expectedLabel3 = $cm->nama_soal ?: $cpmkKode;

                            if (strcasecmp(trim($colName), trim($expectedLabel1)) === 0 ||
                                strcasecmp(trim($colName), trim($expectedLabel2)) === 0 ||
                                strcasecmp(trim($colName), trim($expectedLabel3)) === 0 ||
                                strcasecmp(trim($colName), trim($cpmkKode)) === 0 ||
                                strpos(strtolower($colName), strtolower($cpmkKode)) !== false) {
                                $matchedCm = $cm;
                                break;
                            }
                        }

                        // Fallback matching berdasar urutan jika tidak ketemu persis
                        if (!$matchedCm) {
                            $idxInGroup = $colIdx - $offset;
                            if ($idxInGroup >= 0 && isset($cmItems[$idxInGroup])) {
                                $matchedCm = $cmItems[$idxInGroup];
                            }
                        }

                        if ($matchedCm) {
                            $columnMap[$colIdx] = [
                                'type' => 'breakdown_soal',
                                'km' => $targetKm,
                                'cm' => $matchedCm,
                                'cm_list' => collect([$matchedCm]),
                                'nilai_metode_col' => $nilaiMetodeColIdx,
                            ];
                            $tempMetodeMapSession[$colIdx] = [
                                'type' => 'breakdown_soal',
                                'km_id' => $targetKm->id,
                                'cm_id' => $matchedCm->id,
                                'nilai_metode_col' => $nilaiMetodeColIdx,
                            ];
                        }
                    }
                }
            } else if ($formatType === 'metode_cpmk') {
                // Opsi B: Nilai Akhir per Metode & CPMK
                $nilaiMetodeCols = []; // [km_id => colIdx]
                foreach ($headerRow as $colIdx => $colName) {
                    if ($colIdx < 5) continue;
                    foreach ($konversi->konversiMetode as $km) {
                        $mNama = $km->metodePenilaian->nama ?? ('Metode ' . $km->metode_id);
                        if (strpos(strtolower($colName), strtolower($mNama)) !== false &&
                            (strpos(strtolower($colName), 'nilai metode') !== false || strpos(strtolower($colName), 'bobot') !== false)) {
                            $nilaiMetodeCols[$km->id] = $colIdx;
                            break;
                        }
                    }
                }

                foreach ($headerRow as $colIdx => $colName) {
                    if ($colIdx < 5) continue;
                    if (in_array($colIdx, $nilaiMetodeCols)) continue; // Skip kolom Nilai Metode murni

                    $matchedKm = null;
                    $matchedCpmkId = null;

                    foreach ($konversi->konversiMetode as $km) {
                        $mNama = $km->metodePenilaian->nama ?? '';
                        foreach ($km->cpmkMetode as $cm) {
                            $cpmkKode = $cm->cpmk?->kode ?? ('CPMK-' . $cm->cpmk_id);
                            $expectedHeader1 = $mNama . ' - ' . $cpmkKode;
                            $expectedHeader2 = $mNama . ' (' . $cpmkKode . ')';

                            if (strcasecmp(trim($colName), trim($expectedHeader1)) === 0 ||
                                strcasecmp(trim($colName), trim($expectedHeader2)) === 0 ||
                                strcasecmp(trim($colName), trim($cpmkKode)) === 0) {
                                $matchedKm = $km;
                                $matchedCpmkId = $cm->cpmk_id;
                                break 2;
                            }
                        }
                    }

                    if ($matchedKm && $matchedCpmkId) {
                        $matchedCms = $matchedKm->cpmkMetode->where('cpmk_id', $matchedCpmkId);
                        $columnMap[$colIdx] = [
                            'type' => 'metode_cpmk',
                            'km' => $matchedKm,
                            'cpmk_id' => $matchedCpmkId,
                            'cm_list' => $matchedCms,
                            'nilai_metode_col' => $nilaiMetodeCols[$matchedKm->id] ?? null,
                        ];
                        $tempMetodeMapSession[$colIdx] = [
                            'type' => 'metode_cpmk',
                            'km_id' => $matchedKm->id,
                            'cpmk_id' => $matchedCpmkId,
                            'nilai_metode_col' => $nilaiMetodeCols[$matchedKm->id] ?? null,
                        ];
                    }
                }
            } else {
                // Opsi A: Nilai Akhir per Metode (Standar)
                foreach ($headerRow as $colIdx => $colName) {
                    if ($colIdx < 5) continue;

                    foreach ($konversi->konversiMetode as $km) {
                        $mNama = $km->metodePenilaian->nama ?? '';
                        if (strcasecmp(trim($mNama), trim($colName)) === 0 || strpos(strtolower($colName), strtolower($mNama)) !== false) {
                            $columnMap[$colIdx] = [
                                'type' => 'standar',
                                'km' => $km,
                                'cm_list' => $km->cpmkMetode,
                            ];
                            $tempMetodeMapSession[$colIdx] = [
                                'type' => 'standar',
                                'km_id' => $km->id,
                            ];
                            break;
                        }
                    }
                }
            }

            // Fallback jika header tidak terdeteksi sama sekali, petakan secara sekuensial
            if (empty($columnMap)) {
                if ($formatType === 'breakdown_soal') {
                    $targetKm = $konversi->konversiMetode->firstWhere('metode_id', $metodeIdParam) ?: $konversi->konversiMetode->first();
                    if ($targetKm && $targetKm->cpmkMetode->isNotEmpty()) {
                        $colIdxCursor = 5;
                        foreach ($targetKm->cpmkMetode as $cm) {
                            $columnMap[$colIdxCursor] = [
                                'type' => 'breakdown_soal',
                                'km' => $targetKm,
                                'cm' => $cm,
                                'cm_list' => collect([$cm]),
                            ];
                            $tempMetodeMapSession[$colIdxCursor] = [
                                'type' => 'breakdown_soal',
                                'km_id' => $targetKm->id,
                                'cm_id' => $cm->id,
                            ];
                            $colIdxCursor++;
                        }
                    }
                } else {
                    $colIdxCursor = 5;
                    foreach ($konversi->konversiMetode as $km) {
                        $columnMap[$colIdxCursor] = [
                            'type' => 'standar',
                            'km' => $km,
                            'cm_list' => $km->cpmkMetode,
                        ];
                        $tempMetodeMapSession[$colIdxCursor] = [
                            'type' => 'standar',
                            'km_id' => $km->id,
                        ];
                        $colIdxCursor++;
                    }
                }
            }

            if (empty($columnMap)) {
                return redirect()->back()->with('error', 'Kolom nilai pada file Excel tidak cocok dengan metode / CPMK yang telah dikonfigurasi.');
            }

            // Kumpulkan data per NPM dari file
            $parsedRows = [];
            $unregisteredMhs = []; // [NPM => Data]
            $rowLine = 1;

            foreach ($dataRows as $row) {
                $rowLine++;
                $angkatan = isset($row[2]) ? trim((string)$row[2]) : '';
                $npm = isset($row[3]) ? trim((string)$row[3]) : '';
                $nama = isset($row[4]) ? trim((string)$row[4]) : '';

                if (empty($npm)) continue; // Skip baris kosong

                $mhs = Mahasiswa::where('NPM', $npm)->first();

                if (!$mhs) {
                    $unregisteredMhs[$npm] = [
                        'line' => $rowLine,
                        'npm' => $npm,
                        'nama' => $nama ?: 'Tanpa Nama',
                        'angkatan' => $angkatan,
                    ];
                }

                $parsedRows[] = [
                    'line' => $rowLine,
                    'npm' => $npm,
                    'nama' => $nama,
                    'angkatan' => $angkatan,
                    'mhs_id' => $mhs ? $mhs->id : null,
                    'scores' => array_values($row),
                ];
            }

            // Jika ada NPM belum terdaftar dan dosen BELUM memilih opsi tindakan (register/skip)
            if (!empty($unregisteredMhs)) {
                session()->flash('unregistered_mhs', $unregisteredMhs);
                session()->flash('temp_parsed_rows', $parsedRows);
                session()->flash('temp_metode_map', $tempMetodeMapSession);
                session()->flash('temp_format_type', $formatType);
                session()->flash('temp_metode_id', $metodeIdParam);
                session()->flash('parsed_rows', $parsedRows);
                return redirect()->back()->with('warning_unregistered', true);
            }
        } else {
            return redirect()->back()->with('error', 'Silakan pilih file Excel terlebih dahulu.');
        }

        // Jalankan import jika tidak ada error
        $successCount = 0;
        $skippedLogs = [];
        $totalInput = count($parsedRows);

        $prodiId = $konversi->mk?->id_prodi ?: ($user->id_prodiUser ?: 1);
        $prodiObj = Prodi::with('fakultas')->find($prodiId);
        $univId = $prodiObj?->fakultas?->id_universitas ?: 1;

        // Force drop restrictive unique index if it still exists in MySQL database
        try {
            DB::statement("SET FOREIGN_KEY_CHECKS=0;");
            try { DB::statement("ALTER TABLE `mutus` ADD INDEX `idx_km_id` (`konversi_metode_id`)"); } catch (\Exception $e) {}
            try { DB::statement("ALTER TABLE `mutus` DROP FOREIGN KEY `fk_mutus_konversi`"); } catch (\Exception $e) {}
            try { DB::statement("ALTER TABLE `mutus` DROP FOREIGN KEY `mutus_konversi_unique`"); } catch (\Exception $e) {}
            try { DB::statement("ALTER TABLE `mutus` DROP INDEX `mutus_konversi_unique`"); } catch (\Exception $e) {}
            try { DB::statement("ALTER TABLE `mutus` ADD INDEX `idx_mutus_konversi` (`id_mahasiswa`, `Course`, `konversi_metode_id`)"); } catch (\Exception $e) {}
            DB::statement("SET FOREIGN_KEY_CHECKS=1;");
        } catch (\Exception $e) {
            DB::statement("SET FOREIGN_KEY_CHECKS=1;");
        }

        DB::beginTransaction();
        try {
            foreach ($parsedRows as $item) {
                $npm = $item['npm'];
                $nama = $item['nama'];
                $angkatan = $item['angkatan'];
                $line = $item['line'];
                $mhsId = $item['mhs_id'];

                // Handling NPM un-registered
                if (!$mhsId) {
                    if ($actionOption === 'register') {
                        // Tambah mahasiswa baru
                        $newMhs = Mahasiswa::create([
                            'NPM' => $npm,
                            'Nama' => $nama ?: 'Mahasiswa Baru',
                            'angkatan' => $angkatan ?: null,
                            'id_prodi' => $prodiId,
                        ]);
                        $mhsId = $newMhs->id;
                    } else {
                        // Opsi Skip
                        $skippedLogs[] = "Baris {$line}: NPM {$npm} ({$nama}) tidak ditemukan";
                        continue;
                    }
                }

                // Update angkatan mahasiswa jika diisi dari Excel
                if ($mhsId && !empty($angkatan)) {
                    Mahasiswa::where('id', $mhsId)->update(['angkatan' => $angkatan]);
                }

                // 1. Hapus data lama KHUSUS untuk metode yang di-upload saat ini (melindungi metode lain agar tidak terhapus, sekaligus membersihkan baris lama jika dosen berpindah mode A/B -> C atau sebaliknya)
                $kmIdsInUpload = collect($columnMap)->pluck('km.id')->unique()->filter()->toArray();
                if (!empty($kmIdsInUpload)) {
                    Mutu::where('NPM', $npm)
                        ->where('Course', $konversi->mk_kode)
                        ->whereIn('konversi_metode_id', $kmIdsInUpload)
                        ->where('sumber', 'konversi')
                        ->delete();
                }

                // 2. Simpan data baru per kolom Excel
                foreach ($columnMap as $colIdx => $info) {
                    $scoreVal = isset($item['scores'][$colIdx]) ? (float) $item['scores'][$colIdx] : 0;
                    $km = $info['km'];
                    $metodeNama = $km->metodePenilaian->nama ?? 'Metode ' . $km->metode_id;
                    $type = $info['type'] ?? 'standar';

                    if ($type === 'breakdown_soal' && !empty($info['cm'])) {
                        $cm = $info['cm'];
                        $cplId = $cm->cpmk?->cpl_id;
                        if (!$cplId && !empty($cm->cpmk_id)) {
                            $cplId = DB::table('cpmks')->where('id', $cm->cpmk_id)->value('cpl_id');
                        }

                        // Ambil Nilai Akhir Metode murni dari kolom Nilai Metode (contoh Col 5)
                        $metodeColIdx = $info['nilai_metode_col'] ?? null;
                        $nilaiMetodeVal = null;
                        if ($metodeColIdx !== null && isset($item['scores'][$metodeColIdx]) && $item['scores'][$metodeColIdx] !== '') {
                            $nilaiMetodeVal = (float) $item['scores'][$metodeColIdx];
                        }

                        // Jika kolom Nilai Metode murni diisi, gunakan nilainya untuk 'Nilai'. Jika tidak, fallback ke $scoreVal
                        $finalNilaiMetode = ($nilaiMetodeVal !== null) ? $nilaiMetodeVal : $scoreVal;

                        Mutu::updateOrCreate(
                            [
                                'NPM' => $npm,
                                'Course' => $konversi->mk_kode,
                                'konversi_metode_id' => $km->id,
                                'Cpmk' => $cm->cpmk_id,
                                'soal' => $cm->nama_soal ?: ('Soal ' . $cm->cpmk_id),
                                'sumber' => 'konversi',
                            ],
                            [
                                'id_mahasiswa' => $mhsId,
                                'universitas_id' => $univId,
                                'Nama_mhs' => $nama,
                                'angkatan' => $angkatan ?: null,
                                'id_prodi' => $prodiId,
                                'namaCourse' => $konversi->mk?->nama ?? '',
                                'Jenis' => $metodeNama,
                                'Cpl' => $cplId,
                                'sub_cpmk_id' => $cm->sub_cpmk_id,
                                'Nilai' => $finalNilaiMetode,  // Nilai Akhir Metode
                                'nilaiSoal' => $scoreVal,       // Nilai per soal CPMK
                                'BobotSoal' => ($cm->bobot_soal > 0) ? $cm->bobot_soal : $km->bobot,
                                'examWeight' => $km->bobot,
                                'tahun_ajaran_id' => $konversi->tahun_ajaran_id,
                                'tahun' => $konversi->tahunAjaran->tahun ?? null,
                            ]
                        );
                    } else if ($type === 'metode_cpmk' && !empty($info['cm_list']) && $info['cm_list']->isNotEmpty()) {
                        $metodeColIdx = $info['nilai_metode_col'] ?? null;
                        $nilaiMetodeVal = null;
                        if ($metodeColIdx !== null && isset($item['scores'][$metodeColIdx]) && $item['scores'][$metodeColIdx] !== '') {
                            $nilaiMetodeVal = (float) $item['scores'][$metodeColIdx];
                        }

                        $finalNilaiMetode = ($nilaiMetodeVal !== null) ? $nilaiMetodeVal : $scoreVal;

                        foreach ($info['cm_list'] as $cm) {
                            $cplId = $cm->cpmk?->cpl_id;
                            if (!$cplId && !empty($cm->cpmk_id)) {
                                $cplId = DB::table('cpmks')->where('id', $cm->cpmk_id)->value('cpl_id');
                            }

                            Mutu::updateOrCreate(
                                [
                                    'NPM' => $npm,
                                    'Course' => $konversi->mk_kode,
                                    'konversi_metode_id' => $km->id,
                                    'Cpmk' => $cm->cpmk_id,
                                    'soal' => $cm->nama_soal,
                                    'sumber' => 'konversi',
                                ],
                                [
                                    'id_mahasiswa' => $mhsId,
                                    'universitas_id' => $univId,
                                    'Nama_mhs' => $nama,
                                    'angkatan' => $angkatan ?: null,
                                    'id_prodi' => $prodiId,
                                    'namaCourse' => $konversi->mk?->nama ?? '',
                                    'Jenis' => $metodeNama,
                                    'Cpl' => $cplId,
                                    'sub_cpmk_id' => $cm->sub_cpmk_id,
                                    'Nilai' => $finalNilaiMetode,  // Nilai Akhir Metode murni dari kolom Nilai Metode
                                    'nilaiSoal' => $scoreVal,       // Nilai per CPMK
                                    'BobotSoal' => ($cm->bobot_soal > 0) ? $cm->bobot_soal : $km->bobot,
                                    'examWeight' => $km->bobot,
                                    'tahun_ajaran_id' => $konversi->tahun_ajaran_id,
                                    'tahun' => $konversi->tahunAjaran->tahun ?? null,
                                ]
                            );
                        }
                    } else {
                        // Format Standar / Fallback
                        $cpmkItems = $km->cpmkMetode;
                        if ($cpmkItems->isNotEmpty()) {
                            foreach ($cpmkItems as $cm) {
                                $cplId = $cm->cpmk?->cpl_id;
                                if (!$cplId && !empty($cm->cpmk_id)) {
                                    $cplId = DB::table('cpmks')->where('id', $cm->cpmk_id)->value('cpl_id');
                                }
                                
                                Mutu::updateOrCreate(
                                    [
                                        'NPM' => $npm,
                                        'Course' => $konversi->mk_kode,
                                        'konversi_metode_id' => $km->id,
                                        'Cpmk' => $cm->cpmk_id,
                                        'soal' => $cm->nama_soal,
                                        'sumber' => 'konversi',
                                    ],
                                    [
                                        'id_mahasiswa' => $mhsId,
                                        'universitas_id' => $univId,
                                        'Nama_mhs' => $nama,
                                        'angkatan' => $angkatan ?: null,
                                        'id_prodi' => $prodiId,
                                        'namaCourse' => $konversi->mk?->nama ?? '',
                                        'Jenis' => $metodeNama,
                                        'Cpl' => $cplId,
                                        'sub_cpmk_id' => $cm->sub_cpmk_id,
                                        'Nilai' => $scoreVal,
                                        'nilaiSoal' => $scoreVal,
                                        'BobotSoal' => ($cm->bobot_soal > 0) ? $cm->bobot_soal : $km->bobot,
                                        'examWeight' => $km->bobot,
                                        'tahun_ajaran_id' => $konversi->tahun_ajaran_id,
                                        'tahun' => $konversi->tahunAjaran->tahun ?? null,
                                    ]
                                );
                            }
                        } else {
                            Mutu::updateOrCreate(
                                [
                                    'NPM' => $npm,
                                    'Course' => $konversi->mk_kode,
                                    'konversi_metode_id' => $km->id,
                                    'sumber' => 'konversi',
                                ],
                                [
                                    'id_mahasiswa' => $mhsId,
                                    'universitas_id' => $univId,
                                    'Nama_mhs' => $nama,
                                    'angkatan' => $angkatan ?: null,
                                    'id_prodi' => $prodiId,
                                    'namaCourse' => $konversi->mk?->nama ?? '',
                                    'Jenis' => $metodeNama,
                                    'Cpmk' => null,
                                    'sub_cpmk_id' => null,
                                    'soal' => null,
                                    'Nilai' => $scoreVal,
                                    'nilaiSoal' => $scoreVal,
                                    'BobotSoal' => $km->bobot,
                                    'examWeight' => $km->bobot,
                                    'tahun_ajaran_id' => $konversi->tahun_ajaran_id,
                                    'tahun' => $konversi->tahunAjaran->tahun ?? null,
                                ]
                            );
                        }
                    }
                }

                $successCount++;
            }

            DB::commit();

            $reportMsg = "{$successCount} dari {$totalInput} baris berhasil diimpor & diolah ke skor CPMK Mahasiswa.";
            if (!empty($skippedLogs)) {
                $reportMsg .= " " . count($skippedLogs) . " baris dilewati:<br>" . implode("<br>", $skippedLogs);
            }

            // Redirect langsung ke halaman Daftar Nilai (detail)
            return redirect()->route('dosen.konversi-nilai.detail', $konversi->id)->with('success', $reportMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengimpor nilai konversi: ' . $e->getMessage());
        }
    }

    // Step 7: Tampilan Halaman Daftar Nilai Hasil Konversi
    public function showDetail($id)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        $konversi = PenilaianKonversi::with(['mk', 'tahunAjaran', 'kurikulum', 'konversiMetode.metodePenilaian'])
            ->where('dosen_id', $user->id)
            ->findOrFail($id);

        $metodeList = [];
        foreach ($konversi->konversiMetode as $km) {
            $metodeList[$km->id] = $km->metodePenilaian->nama ?? 'Metode ' . $km->metode_id;
        }

        $mutuData = Mutu::with('mahasiswa')
            ->where('Course', $konversi->mk_kode)
            ->where('sumber', 'konversi')
            ->where('tahun_ajaran_id', $konversi->tahun_ajaran_id)
            ->get();

        $mhsGrouped = $mutuData->groupBy('NPM');

        return view('dosen.konversi.detail', compact('konversi', 'metodeList', 'mutuData', 'mhsGrouped', 'userOtoritas'));
    }

    // Hapus Data Konversi Nilai Historis
    public function destroy($id)
    {
        $user = auth()->user();

        $konversi = PenilaianKonversi::where('dosen_id', $user->id)->findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. Dapatkan semua ID konversi_metode
            $kmIds = KonversiMetode::where('penilaian_konversi_id', $konversi->id)->pluck('id');

            // 2. Hapus data nilai mahasiswa di tabel mutus terkait konversi ini
            if ($kmIds->isNotEmpty()) {
                Mutu::whereIn('konversi_metode_id', $kmIds)->delete();
                KonversiCpmkMetode::whereIn('konversi_metode_id', $kmIds)->delete();
                KonversiMetode::whereIn('id', $kmIds)->delete();
            }

            // Hapus mutus tambahan jika ada yang terikat lewat mk_kode & tahun_ajaran_id & sumber='konversi'
            Mutu::where('Course', $konversi->mk_kode)
                ->where('tahun_ajaran_id', $konversi->tahun_ajaran_id)
                ->where('sumber', 'konversi')
                ->delete();

            // 3. Hapus record utama penilaian_konversi
            $konversi->delete();

            DB::commit();

            return redirect()->route('dosen.konversi-nilai.index')->with('success', 'Data konversi nilai beserta seluruh riwayat nilainya berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data konversi: ' . $e->getMessage());
        }
    }

    // Direct helper route to drop mutus_konversi_unique index & auto backfill all NULL tahun_ajaran_id in mutus
    public function fixDbIndex()
    {
        $messages = [];

        // 1. Drop Index mutus_konversi_unique safely by bypassing foreign key check
        try {
            DB::statement("SET FOREIGN_KEY_CHECKS=0");
            try { DB::statement("ALTER TABLE `mutus` DROP FOREIGN KEY `fk_mutus_konversi`"); } catch (\Exception $e) {}
            try { DB::statement("ALTER TABLE `mutus` DROP FOREIGN KEY `mutus_konversi_unique`"); } catch (\Exception $e) {}
            try { DB::statement("ALTER TABLE `mutus` DROP INDEX `mutus_konversi_unique`"); } catch (\Exception $e) {}
            DB::statement("SET FOREIGN_KEY_CHECKS=1");
            $messages[] = "1. Indeks & Foreign Key 'mutus_konversi_unique' BERHASIL dihapus dari tabel mutus.";
        } catch (\Exception $e) {
            DB::statement("SET FOREIGN_KEY_CHECKS=1");
            $messages[] = "1. Indeks 'mutus_konversi_unique' sudah terhapus sebelumnya atau error: " . $e->getMessage();
        }

        // 2. Autofill tahun_ajaran_id untuk semua data lama di mutus yang masih NULL
        try {
            $updatedCount = 0;
            if (Schema::hasColumn('mutus', 'tahun')) {
                $nullMutus = DB::table('mutus')->whereNull('tahun_ajaran_id')->whereNotNull('tahun')->get(['id', 'tahun']);
                foreach ($nullMutus as $row) {
                    $rawTahun = trim((string)$row->tahun);
                    if (empty($rawTahun)) continue;

                    $jenisSemester = (stripos($rawTahun, 'Genap') !== false) ? 'Genap' : 'Ganjil';
                    preg_match('/\d{4}/', $rawTahun, $matches);
                    $tahunAngka = $matches[0] ?? date('Y');

                    $ta = DB::table('tahun_ajaran')
                        ->where('tahun', 'like', "%{$tahunAngka}%")
                        ->where('jenis_semester', $jenisSemester)
                        ->first();

                    if (!$ta) {
                        $taId = DB::table('tahun_ajaran')->insertGetId([
                            'tahun' => (string)$tahunAngka,
                            'jenis_semester' => $jenisSemester,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $taId = $ta->id;
                    }

                    DB::table('mutus')->where('id', $row->id)->update(['tahun_ajaran_id' => $taId]);
                    $updatedCount++;
                }
            }
            $messages[] = "2. Autofill Tahun Ajaran BERHASIL: {$updatedCount} baris data lama di tabel mutus telah otomatis diisi kolom tahun_ajaran_id-nya.";
        } catch (\Exception $e) {
            $messages[] = "2. Autofill Tahun Ajaran mengalami error: " . $e->getMessage();
        }

        return "<h3>Proses Pemeliharaan Database Selesai!</h3><br>" . implode("<br><br>", $messages) . "<br><br><a href='" . route('dosen.konversi-nilai.index') . "' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Kembali ke Konversi Nilai</a>";
    }

    // Quick store Metode Penilaian baru via AJAX modal
    public function storeQuickMetode(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $prodiId = $user->id_prodiUser ?: 1;

        $namaTrim = trim($request->nama);

        // Cek jika sudah ada dengan nama yang sama untuk prodi ini
        $existing = MetodePenilaian::where('id_prodi', $prodiId)
            ->whereRaw('LOWER(nama) = ?', [strtolower($namaTrim)])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'id' => $existing->id,
                'nama' => $existing->nama,
                'message' => 'Metode penilaian sudah ada.'
            ]);
        }

        $metode = MetodePenilaian::create([
            'nama' => $namaTrim,
            'id_prodi' => $prodiId,
        ]);

        return response()->json([
            'success' => true,
            'id' => $metode->id,
            'nama' => $metode->nama,
            'message' => 'Metode penilaian baru berhasil ditambahkan.'
        ]);
    }
}
