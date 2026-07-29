<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Barryvdh\Snappy\Facades\SnappyPdf as Pdf;

class TranskripKompetensiController extends Controller
{

    public function index()
    {
        $user       = Auth::user();
        $mahasiswa  = $this->guardMahasiswa($user); //validasi user mahasiswa aktif

        [$mahasiswaData, $prodi, $universitas] = $this->loadMahasiswaContext($mahasiswa->NPM);
        //kemudian menyimoan si session untuk request berikutnya
        session([
            'current_npm'        => $mahasiswaData->npm,
            'current_is_aptikom' => (bool) ($prodi->is_aptikom ?? false),
            'current_jenjang'    => $prodi->jenjang ?? null,
        ]);

        return view('mahasiswa.transkrip-kompetensi', compact('mahasiswaData', 'prodi', 'universitas'));
    }

    public function getCompetencyData(Request $request)
    {
        try {
            //validasi npm
            $npm = $request->input('npm') ?? session('current_npm');

            if (!$npm) {
                return $this->jsonError('NPM tidak ditemukan. Silakan muat ulang halaman.');
            }

            if (!preg_match('/^[0-9]+$/', $npm)) {
                return $this->jsonError('Format NPM tidak valid.');
            }

            $prodiInfo = $this->getProdiInfo($npm);
            if (!$prodiInfo) {
                return $this->jsonError('Data program studi tidak ditemukan.');
            }
            //cek prodi
            $prodi     = Prodi::find($prodiInfo->prodi_id);
            //cek cache dulu 
            $cacheKey  = "transkrip_data_{$npm}_" . ($prodi->is_aptikom ? 'aptikom' : 'nonaptikom');
            $useCache  = config('app.env') === 'production';
            $fromCache = false;

            if ($useCache && Cache::has($cacheKey)) {
                $data      = Cache::get($cacheKey); //ambil dari cache
                $fromCache = true;
            } else {
                $mahasiswa = Mahasiswa::where('NPM', $npm)->firstOrFail();
                $data      = $this->buildCompetencyData($mahasiswa, $prodi); //hitung fresh

                if ($useCache) {
                    Cache::put($cacheKey, $data, 3600); //simpan 1 jam 
                }
            }

            $data = $this->prepareDataForBlade($data, $prodi);

            Log::info('getCompetencyData sukses', [
                'npm'           => $npm,
                'type'          => $prodi->is_aptikom ? 'APTIKOM' : 'Non-APTIKOM',
                'jumlah_course' => count($data['courses'] ?? []),
                'from_cache'    => $fromCache,
            ]);

            return response()->json([
                'success' => true,
                'data'    => $data,
                'cached'  => $fromCache,
                'type'    => $prodi->is_aptikom ? 'aptikom' : 'non_aptikom',
            ]);
        } catch (\Throwable $e) {
            Log::error('getCompetencyData ERROR', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return $this->jsonError(
                config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan server. Silakan coba lagi.',
                500
            );
        }
    }

    public function printTranskrip()
    {
        [$mahasiswaData, $prodi, $universitas, $transkripData] = $this->resolveTranskripData();

        return view('mahasiswa.pdftranskrip_kompetensi', compact(
            'mahasiswaData',
            'prodi',
            'universitas',
            'transkripData'
        ));
    }

    public function downloadTranskripPdf()
    {
        [$mahasiswaData, $prodi, $universitas, $transkripData] = $this->resolveTranskripData();

        $pdf = Pdf::loadView('mahasiswa.pdftranskrip_kompetensi', compact(
            'mahasiswaData',
            'prodi',
            'universitas',
            'transkripData'
        ));

        $pdf->setPaper('a4')
            ->setOrientation('portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('encoding', 'UTF-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('no-stop-slow-scripts', true)
            ->setOption('javascript-delay', 0)
            ->setOption('disable-javascript', true);

        return $pdf->download('Transkrip-Kompetensi-' . $mahasiswaData->npm . '.pdf');
    }

    public function clearTranskripCache(?string $npm = null)
    {
        if ($npm) {
            Cache::forget("transkrip_data_{$npm}_aptikom");
            Cache::forget("transkrip_data_{$npm}_nonaptikom");
            return response()->json(['success' => true, 'message' => "Cache NPM {$npm} berhasil dihapus."]);
        }

        Cache::flush();
        return response()->json(['success' => true, 'message' => 'Semua cache berhasil dihapus.']);
    }

    //inti darin semua data yang ditampilkan
    private function buildCompetencyData(Mahasiswa $mahasiswa, Prodi $prodi): array
    {
        //ambil mk dari mutus
        $courses = DB::table('mutus')
            ->where('npm', $mahasiswa->NPM)
            ->select('Course')
            ->groupBy('Course')
            ->pluck('Course');

        if ($courses->isEmpty()) {
            return $this->emptyStructure();
        }

        $courseData    = [];
        $allCpmkScores = [];
        $allCplScores  = [];

        foreach ($courses as $courseKode) {
            $mkInfo  = $this->getMkInfo($courseKode, $prodi->is_aptikom);
            $records = $mahasiswa->getCourseRecords($courseKode);
            if ($records->isEmpty()) continue;

            $latestRecord = $records->first();
            $totalSks     = ($mkInfo->bobot_teori ?? 0) + ($mkInfo->bobot_praktikum ?? 0);

            // --- CPMK: hitung dulu $cpmk data isinya cpmk ini saja perhitungannya
            $cpmkData = $this->buildCpmkData($records, $prodi, $allCpmkScores);

            // --- CPL: sekarang pakai $cpmkData, bukan records mentah ---
            $cplData = $this->buildCplData($records, $prodi, $allCplScores, $cpmkData);

            // --- Final Grade ---
            $finalGrade = $prodi->is_aptikom
                ? $this->calcFinalGradeAptikom($records, $courseKode)
                : $this->calcFinalGradeNonAptikom($records);
            //konversi nilai angka ke huruf mutu
            [$letterGrade, $gradeWeight] = $prodi->convertGrade($finalGrade);
            $mutu = $gradeWeight !== null ? $gradeWeight * $totalSks : null;
            //semua data mk dikumpukan disini
            $courseData[] = [
                'kode'        => $courseKode,
                'nama'        => $mkInfo->nama ?? $courseKode,
                'tahun'       => $latestRecord->tahun ?? null,
                'kurikulum'   => $mkInfo->tahun_kurikulum ?? $mkInfo->kurikulum ?? null,
                'semester'    => $mkInfo->semester ?? $mkInfo->smt ?? null,
                'sks'         => $totalSks,
                'grade_huruf' => $letterGrade,
                'bobot_mutu'  => $gradeWeight,
                'mutu'        => $mutu,
                'cpmks'       => $cpmkData,
                'cpls'        => $cplData,
                'nilai_akhir' => round($finalGrade, 2),
                'status'      => $prodi->getStatusKelulusan($finalGrade),
            ];
        }

        $avgCpmk = count($allCpmkScores) > 0
            ? round(array_sum(array_column($allCpmkScores, 'nilai')) / count($allCpmkScores), 2)
            : 0;

        $finalCplScores = [];
        foreach ($allCplScores as $cplId => $d) {
            $avg = array_sum($d['scores']) / count($d['scores']);
            $finalCplScores[] = [
                'id'        => $cplId,
                'kode'      => $d['kode'],
                'deskripsi' => $d['deskripsi'],
                'nilai'     => round($avg, 2),
                'status'    => $prodi->getStatusKelulusan($avg),
            ];
        }

        $avgCpl = count($finalCplScores) > 0
            ? round(array_sum(array_column($finalCplScores, 'nilai')) / count($finalCplScores), 2)
            : 0;

        return [
            'courses'  => $courseData, //data per mk
            'cpmks'    => array_values($allCpmkScores), //semua data cpmk
            'cpls'     => $finalCplScores, //semua cpl
            'avg_cpmk' => $avgCpmk,
            'avg_cpl'  => $avgCpl,
            'type'     => $prodi->is_aptikom ? 'aptikom' : 'non_aptikom',
        ];
    }

    /**
     * Hitung skor semua CPMK dari satu set record MK.
     * Menggunakan Mahasiswa::calcWeightedScore() agar rumus tidak terduplikasi.
     */
    private function buildCpmkData(
        \Illuminate\Support\Collection $records,
        Prodi $prodi,
        array &$allCpmkScores
    ): array {
        $result = [];

        foreach ($records->whereNotNull('Cpmk')->pluck('Cpmk')->unique() as $cpmkId) {
            $detail   = DB::table('cpmks')->where('id', $cpmkId)->first();
            $mahasiswa = new Mahasiswa(); // untuk akses calcWeightedScore
            $score    = $mahasiswa->calcWeightedScore($records->where('Cpmk', $cpmkId));

            if ($score <= 0) continue;

            $status = $prodi->getStatusKelulusan($score);
            $entry  = [
                'id'         => $cpmkId,
                'kode'       => $detail?->kode     ?? 'CPMK-' . $cpmkId,
                'deskripsi'  => $detail?->judul    ?? 'Tidak ada deskripsi',
                'indikator'  => $detail?->indikator ?? '',
                'kriteria'   => $detail?->kriteria  ?? '',
                'nilai'      => round($score, 2),
                'status'     => $status,
            ];

            $result[]                  = $entry;
            $allCpmkScores[$cpmkId]    = $entry; // simpan untuk ringkasan
        }

        return $result;
    }

    /**
     * Hitung skor semua CPL dari satu set record MK.
     */
    private function buildCplData(
        \Illuminate\Support\Collection $records,
        Prodi $prodi,
        array &$allCplScores,
        array $cpmkData   // ← hasil buildCpmkData(), bukan records mentah
    ): array {
        // 1. Mapping cpmk_id → cpl_id dari records (kolom Cpmk & Cpl)
        $cpmkToCpl = $records
            ->whereNotNull('Cpl')
            ->whereNotNull('Cpmk')
            ->mapWithKeys(fn($r) => [(string) $r->Cpmk => (string) $r->Cpl])
            ->toArray();

        // 2. Kelompokkan nilai CPMK (yang sudah dihitung) berdasarkan CPL-nya
        $cplGroups = [];
        foreach ($cpmkData as $cpmkEntry) {
            $cpmkId = (string) $cpmkEntry['id'];
            $cplId  = $cpmkToCpl[$cpmkId] ?? null;
            if (!$cplId) continue;

            $cplGroups[$cplId][] = $cpmkEntry['nilai'];
        }

        // 3. Rata-ratakan nilai CPMK per CPL → entry CPL
        $result = [];
        foreach ($cplGroups as $cplId => $nilaiList) {
            $score = array_sum($nilaiList) / count($nilaiList);
            if ($score <= 0) continue;

            $detail = DB::table('cpls')->where('id', $cplId)->first();
            $status = $prodi->getStatusKelulusan($score);

            $entry = [
                'id'        => $cplId,
                'kode'      => $detail?->kode  ?? 'CPL-' . $cplId,
                'deskripsi' => $detail?->judul ?? 'Tidak ada deskripsi',
                'nilai'     => round($score, 2),
                'status'    => $status,
            ];

            $result[] = $entry;

            $allCplScores[$cplId]['scores'][]  = $score;
            $allCplScores[$cplId]['kode']      = $entry['kode'];
            $allCplScores[$cplId]['deskripsi'] = $entry['deskripsi'];
            $allCplScores[$cplId]['status']    = $entry['status'];
        }

        return $result;
    }

    /**
     * Final grade APTIKOM: menggunakan totalBobotUtuh dari tabel mapping
     * untuk pembagi yang jujur terhadap instrumen penuh.
     */
    private function calcFinalGradeAptikom(
        \Illuminate\Support\Collection $records,
        string $courseKode
    ): float {
        $total = 0.0;

        foreach ($records->groupBy('Jenis') as $jenis => $group) {
            $examWeight = $group->first()->examWeight ?? 0;

            $totalBobotUtuh = DB::table('cpl_mk_cpmk_penilaian as cmcp')
                ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
                ->join('metode_penilaian as mp', 'mp.id', '=', 'pm.metode_id')
                ->where('cmcp.mk_kode', $courseKode)
                ->where('mp.nama', $jenis)
                ->sum('pm.bobot');

            $pembagi = $totalBobotUtuh > 0 ? $totalBobotUtuh : $group->sum('BobotSoal');
            if ($pembagi <= 0) continue;

            $nilaiJenis = $group->sum(fn($r) => ($r->BobotSoal / $pembagi) * ($r->nilaiSoal ?? 0));
            $total     += ($examWeight / 100) * $nilaiJenis;
        }

        return round($total, 2);
    }

    /**
     * Final grade Non-APTIKOM: rata-rata tertimbang sederhana dari nilaiSoal.
     */
    private function calcFinalGradeNonAptikom(\Illuminate\Support\Collection $records): float
    {
        $finalGrade  = 0.0;
        $totalWeight = 0;

        foreach ($records->groupBy('Jenis') as $group) {
            $weight      = $group->first()->examWeight ?? 0;
            $nilai       = $group->avg('Nilai') ?? 0;
            $finalGrade += ($weight / 100) * $nilai;
            $totalWeight += $weight;
        }

        if ($totalWeight > 0 && $totalWeight != 100) {
            $finalGrade = $finalGrade * (100 / $totalWeight);
        }

        return round($finalGrade, 2);
    }

    private function prepareDataForBlade(array $data, Prodi $prodi): array
    {
        $data['cpl_status']     = $prodi->getStatusKelulusan($data['avg_cpl']);
        $data['progress_class'] = Prodi::progressClass($data['avg_cpl']);

        $data['cpls'] = array_map(fn($cpl) => array_merge($cpl, [
            'badge_class' => Prodi::badgeClassKelulusan($cpl['status']),
        ]), $data['cpls'] ?? []);

        $data['courses'] = array_map(function ($course) use ($prodi) {
            $course['grade_color']  = Prodi::gradeColorClass($course['grade_huruf']);
            $course['status_badge'] = Prodi::badgeClassKelulusan($course['status']);

            $course['cpmks'] = array_map(fn($c) => array_merge($c, [
                'badge_class' => Prodi::badgeClassKelulusan($c['status']),
            ]), $course['cpmks'] ?? []);

            $course['cpls'] = array_map(fn($c) => array_merge($c, [
                'badge_class' => Prodi::badgeClassKelulusan($c['status']),
            ]), $course['cpls'] ?? []);

            return $course;
        }, $data['courses'] ?? []);

        return $data;
    }

    private function addIpkAndTotals(array $data): array
    {
        $totalSks   = 0;
        $totalMutu  = 0;

        foreach ($data['courses'] ?? [] as $c) {
            $sks        = (float) ($c['sks'] ?? 0);
            $bobot      = (float) ($c['bobot_mutu'] ?? 0);
            $totalSks  += $sks;
            $totalMutu += $bobot * $sks;
        }

        $data['total_sks']  = $totalSks;
        $data['total_mutu'] = $totalMutu;
        $data['ipk']        = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0;

        return $data;
    }

    /** Guard: validasi user adalah mahasiswa aktif, return model Mahasiswa. */
    private function guardMahasiswa(\App\Models\User $user): Mahasiswa
    {
        $isMahasiswa = DB::table('user_otoritas')
            ->where('user_id', $user->id)
            ->where('otoritas', 'Mahasiswa')
            ->where('active', 1)
            ->exists();

        if (!$isMahasiswa) {
            abort(403, 'Halaman ini hanya dapat diakses oleh Mahasiswa.');
        }

        $mahasiswa = Mahasiswa::findForUser($user);

        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan.');
        }

        return $mahasiswa;
    }

    /** Load mahasiswaData (dari mutus), prodi, dan universitas sekaligus. */
    private function loadMahasiswaContext(string $npm): array
    {
        $mahasiswaData = DB::table('mutus')
            ->where('npm', $npm)
            ->select('npm', 'nama_mhs', 'angkatan', 'id_prodi', 'universitas_id')
            ->first();

        if (!$mahasiswaData) {
            abort(404, 'Data mahasiswa tidak ditemukan di sistem penilaian.');
        }

        $prodi      = Prodi::findOrFail($mahasiswaData->id_prodi);
        $universitas = DB::table('universitas')->where('id', $mahasiswaData->universitas_id)->first();

        return [$mahasiswaData, $prodi, $universitas];
    }

    /** Ambil info prodi dari tabel mutus (join prodi). */
    private function getProdiInfo(string $npm): ?object
    {
        return DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->where('mutus.npm', $npm)
            ->select('prodi.id as prodi_id', 'prodi.is_aptikom', 'prodi.jenjang')
            ->first();
    }

    /** Ambil info MK (with kurikulum join untuk APTIKOM). */
    private function getMkInfo(string $courseKode, bool $isAptikom): object
    {
        $query = DB::table('mks');

        if ($isAptikom) {
            $query->leftJoin('kurikulums', 'mks.id_kurikulum', '=', 'kurikulums.id')
                ->select('mks.*', 'kurikulums.tahun as tahun_kurikulum');
        } else {
            $query->select('mks.*');
        }

        return $query->where('mks.kode', $courseKode)->first() ?? (object) [];
    }

    /** Resolve data lengkap untuk print/download — dipakai oleh dua method PDF. */
    private function resolveTranskripData(): array
    {
        $npm = session('current_npm');
        abort_unless($npm, 404, 'NPM tidak ditemukan.');

        // loadMahasiswaContext mengembalikan object dengan field:
        // npm, nama_mhs, angkatan, id_prodi, universitas_id
        [$mahasiswaData, $prodi, $universitas] = $this->loadMahasiswaContext($npm);

        $mahasiswa     = Mahasiswa::where('NPM', $npm)->firstOrFail();
        $transkripData = $this->buildCompetencyData($mahasiswa, $prodi);
        $transkripData = $this->prepareDataForBlade($transkripData, $prodi);
        $transkripData = $this->addIpkAndTotals($transkripData);

        // Kembalikan $mahasiswaData (bukan $mahasiswa model)
        // agar field npm, nama_mhs, angkatan tersedia di Blade
        return [$mahasiswaData, $prodi, $universitas, $transkripData];
    }

    private function emptyStructure(): array
    {
        return ['courses' => [], 'cpmks' => [], 'cpls' => [], 'avg_cpmk' => 0, 'avg_cpl' => 0, 'type' => null];
    }

    private function jsonError(string $message, int $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}
