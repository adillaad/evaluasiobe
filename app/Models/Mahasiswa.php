<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table    = 'mahasiswa';
    protected $fillable = ['NPM', 'Nama', 'angkatan', 'id_prodi'];


    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }


    public static function findForUser(\App\Models\User $user): ?self
    {
        return static::where('Nama', $user->name)
            ->where('id_prodi', $user->id_prodiUser)
            ->first();
    }


    public function getTakenCourses(): array
    {
        return DB::table('mutus')
            ->where('npm', $this->NPM)
            ->pluck('Course')
            ->unique()
            ->values()
            ->toArray();
    }

    public function subqueryLatestSemester()//bagian ini adalah bagian untuk mengubah format 
    {
        return DB::table('mutus')
            ->select(
                'Course',
                DB::raw("MAX(CONCAT(
                    SUBSTRING_INDEX(tahun,' ',-1),
                    CASE WHEN SUBSTRING_INDEX(tahun,' ',1)='Genap' THEN '1' ELSE '0' END
                )) AS max_year_semester")
            )
            ->where('npm', $this->NPM)
            ->groupBy('Course');
    }

    public function getCourseRecords(string $courseKode): \Illuminate\Support\Collection
    {
        //dibagian ini subLatesId mengambil id terbesar dari kombinasi soal
        $subLatestId = DB::table('mutus')
            ->where('npm', $this->NPM)
            ->where('Course', $courseKode)          // hanya MK ini, bukan MK lain
            ->select(
                'Course',
                'Jenis',
                'Cpmk',
                'Cpl',
                'soal',                             
                DB::raw('MAX(id) as max_id')        // id terbesar = data terbaru karena auto increment
            )
            ->groupBy('Course', 'Jenis', 'Cpmk', 'Cpl', 'soal');
        return DB::table('mutus as m')
            ->joinSub($subLatestId, 'latest', function ($join) {
                $join->on('m.id', '=', 'latest.max_id');
            })
            ->joinSub($this->subqueryLatestSemester(), 't', function ($join) {
                $join->on('m.Course', '=', 't.Course')
                    ->on(
                        DB::raw("CONCAT(
                        SUBSTRING_INDEX(m.tahun,' ',-1),
                        CASE WHEN SUBSTRING_INDEX(m.tahun,' ',1)='Genap' THEN '1' ELSE '0' END
                    )"),
                        '=',
                        't.max_year_semester'
                    );
            })
            ->where('m.npm', $this->NPM)
            ->where('m.Course', $courseKode)
            ->get();
    }


    public function calculateSksLulus(): int
    {
        //akan meload prodi yang diambil dari controller
        $prodi = $this->prodi ?? $this->load('prodi')->prodi;
        if (!$prodi) return 0;
        //ambil semua mk yang pernah diambil lewat tabel mutus 
        $courses = DB::table('mutus')
            ->where('npm', $this->NPM)
            ->select('Course')
            ->groupBy('Course')//memastikan agar gak ada duplikat kode mk
            ->pluck('Course');

        if ($courses->isEmpty()) return 0; //jika data belum ada seperti mahasiswa baru maka akan otomatis retur 0
        //ambil data sks semua mk sekaligus
        $sksMap = DB::table('mks')
            ->whereIn('kode', $courses->toArray())
            ->select('kode', DB::raw('(bobot_teori + bobot_praktikum) as sks'))
            ->get()
            ->keyBy('kode');
        // tentukan metode perhiungan karena beda jenis prodi beda perhitungan 
        $isAptikom = (bool) $prodi->is_aptikom;
        $totalSks  = 0;
        //hitung nilai dan cek kelulusan tiap mk
        foreach ($courses as $courseKode) {
            //menghitung final nilai dari calculateFinalGrade
            $finalGrade = $this->calculateFinalGrade($courseKode, $isAptikom);
            //cek kelulusan yang ada di isLulus 
            if (!$prodi->isLulus($finalGrade)) continue;
            //akumulasi sks
            $sks = (float) ($sksMap->get($courseKode)?->sks ?? 0);
            $totalSks += $sks;
        }

        return (int) $totalSks;
    }

    public function calculateFinalGrade(string $courseKode, bool $isAptikom): float
    {
        $records = $this->getCourseRecords($courseKode);
        if ($records->isEmpty()) return 0.0;

        return $isAptikom
            ? $this->calcFinalGradeAptikom($records, $courseKode)
            : $this->calcFinalGradeNonAptikom($records);
    }
    private function calcFinalGradeAptikom(
        \Illuminate\Support\Collection $records,
        string $courseKode
    ): float {
        $total = 0.0;

        foreach ($records->groupBy('Jenis') as $jenis => $group) {
            $examWeight = $group->first()->examWeight ?? 0;

            // Ambil totalBobotUtuh dari mapping tabel (sama seperti controller)
            $totalBobotUtuh = DB::table('cpl_mk_cpmk_penilaian as cmcp')
                ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
                ->join('metode_penilaian as mp', 'mp.id', '=', 'pm.metode_id')
                ->where('cmcp.mk_kode', $courseKode)
                ->where('mp.nama', $jenis)
                ->sum('pm.bobot');

            $pembagi = $totalBobotUtuh > 0 ? $totalBobotUtuh : $group->sum('BobotSoal');
            if ($pembagi <= 0) continue;
            //Nilai Akhir MK = Σ ( examWeight/100 × nilaiJenis )
            //nilaiJenis = Σ ( BobotSoal/totalBobotUtuh × nilaiSoal )

            $nilaiJenis = $group->sum(fn($r) => ($r->BobotSoal / $pembagi) * ($r->nilaiSoal ?? 0));
            $total += ($examWeight / 100) * $nilaiJenis;
        }

        return round($total, 2);
    }
    private function calcFinalGradeNonAptikom(\Illuminate\Support\Collection $records): float
    {
        $finalGrade  = 0.0;
        $totalWeight = 0;

        foreach ($records->groupBy('Jenis') as $group) {
            $weight      = $group->first()->examWeight ?? 0;
            $nilai       = $group->avg('Nilai') ?? 0;  // Kolom pre-computed
            $finalGrade += ($weight / 100) * $nilai;
            $totalWeight += $weight;
        }

        if ($totalWeight > 0 && $totalWeight != 100) {
            $finalGrade = $finalGrade * (100 / $totalWeight);
        }

        return round($finalGrade, 2);
    }


    public function calculateIPK(): float
    {
        //pastikan prodi ada
        $prodi = $this->prodi ?? $this->load('prodi')->prodi;
        if (!$prodi) return 0.0;
        //ambil semua mk yang ada di mutus
        $courses = DB::table('mutus')
            ->where('npm', $this->NPM)
            ->select('Course')
            ->groupBy('Course')
            ->pluck('Course');
        
        if ($courses->isEmpty()) return 0.0;
        //ambil sksk semua mk
        $sksMap = DB::table('mks')
            ->whereIn('kode', $courses->toArray())
            ->select('kode', DB::raw('(bobot_teori + bobot_praktikum) as sks'))
            ->get()
            ->keyBy('kode');

        $totalBobotMutu = 0.0;
        $totalSks = 0.0;
        $isAptikom = (bool) $prodi->is_aptikom;

        foreach ($courses as $courseKode) {
            $finalGrade = $this->calculateFinalGrade($courseKode, $isAptikom);
            if ($finalGrade <= 0) continue;

            $sks = (float) ($sksMap->get($courseKode)?->sks ?? 0);
            if ($sks <= 0) continue;
            //ini intinya ngambil methode convertGrade di model prodi 
            [, $gradeWeight] = $prodi->convertGrade($finalGrade);
            if ($gradeWeight === null) continue;
            //akumulasi bobot mutu dan sksk
            $totalBobotMutu += $gradeWeight * $sks;
            $totalSks += $sks;
        }
        //hitung ipk final 
        return $totalSks > 0 ? round($totalBobotMutu / $totalSks, 2) : 0.0;
    }
    public function getCompetencyData(): array
    {
        //dari controller panggil getCompetencyData di bagian bawah ini ambil semua mk yang ada di tabel mutus karena ada nilainya
        $courses = DB::table('mutus')
            ->where('npm', $this->NPM)
            ->select('Course')
            ->groupBy('Course')
            ->pluck('Course');

        if ($courses->isEmpty()) {
            return ['cpmks' => collect(), 'cpls' => collect()];
        }

        $allCpmkScores = [];
        $allCplScores  = [];
        // kumpulkan nilai cpmk score
        foreach ($courses as $courseKode) {
            //baca dulu model getcourseRecord yang ada di model ini untuk tahu kalau di data mutus ambil yang paling terbaru
            $records = $this->getCourseRecords($courseKode);
            if ($records->isEmpty()) continue;
            // hitung nilai CPMK lintas semua MK, hasilnya langsung masuk ke $allCpmkScores
            $this->accumulateCpmkScores($records, $allCpmkScores);

            // panggilan untuk calcWeightedScore yang masuk ke allCplScores
            // hitung nilai CPMK khusus MK ini saja, disimpan sementara
            $cpmkScoresThisCourse = [];
            foreach ($records->whereNotNull('Cpmk')->pluck('Cpmk')->unique() as $cpmkId) {
                $cpmkRecs = $records->where('Cpmk', (string) $cpmkId);
                $score    = $this->calcWeightedScore($cpmkRecs);//calcWeightedScore untuk inti perhitungan score per cpmk
                if ($score > 0) {
                    $cpmkScoresThisCourse[$cpmkId] = $score;//nilai disimpan dulu disini lalu dikirim lewat accumulatecplscore di bawah
                }
            }

            $this->accumulateCplScores($records, $allCplScores, $cpmkScoresThisCourse);
        }
        //setelah dapet data score cpmk tiap mk kemudian akan dikumpulkan semua cpmk yang sama dan di bagi rata
        $cpmks = collect($allCpmkScores)->map(function ($d, $id) {
            $avg = array_sum($d['scores']) / count($d['scores']);//rata2 cpmk lintas mk
            return [
                'id'        => $id,
                'kode'      => $d['kode'],
                'deskripsi' => $d['deskripsi'],
                'nilai'     => round($avg, 2),
                'status'    => $this->getStatusLabel($avg),//untuk lihat labelnya apa aja ada di model ini
            ];
        })->values();
        //data kemudian disimpan di allCpmkScores diproses menjadi $cpmks di baris 277 ke $allCpmk (controller) dan dipecah lagi jadi 2 avg('nilai') $avgCpmk dan sortByDesc->take(5) $topCpmks
        //data mentah allCplScore difinalisasi di bagian ini 
        $cpls = collect($allCplScores)->map(function ($d, $id) {
            //membgi rata rata semua score
            $avg = array_sum($d['scores']) / count($d['scores']);
            return [
                'id'        => $id,
                'kode'      => $d['kode'],
                'deskripsi' => $d['deskripsi'],
                'nilai'     => round($avg, 2),
                'status'    => $this->getStatusLabel($avg),
            ];
        })->values();

        return compact('cpmks', 'cpls');//dikembalikan ke controller
    }
    public function getTopProfesi(\Illuminate\Support\Collection $allCpmks): \Illuminate\Support\Collection
    {
        $cpmkMap = $allCpmks->filter(fn($c) => ($c['nilai'] ?? 0) > 0)->keyBy('id');
        if ($cpmkMap->isEmpty()) return collect();

        return DB::table('profesi')
            ->join('profesi_cpmk', 'profesi.id', '=', 'profesi_cpmk.profesi_id')
            ->select('profesi.id as profesi_id', 'profesi.nama', 'profesi_cpmk.cpmk_id')
            ->get()
            ->groupBy('profesi_id')
            ->map(function ($items) use ($cpmkMap) {
                $allIds     = $items->pluck('cpmk_id')->unique();
                $dinilaiIds = $allIds->filter(fn($id) => $cpmkMap->has($id));

                if ($dinilaiIds->isEmpty()) return null;

                $total = $dinilaiIds->sum(fn($id) => $cpmkMap->get($id)['nilai'] ?? 0);

                return [
                    'nama'             => $items->first()->nama,
                    'match_percentage' => round($total / $dinilaiIds->count(), 1),
                ];
            })
            ->filter()
            ->sortByDesc('match_percentage')
            ->take(4)
            ->values();
    }


    private function accumulateCpmkScores(
        \Illuminate\Support\Collection $records,
        array &$allCpmkScores
    ): void {
        foreach ($records->whereNotNull('Cpmk')->pluck('Cpmk')->unique() as $cpmkId) {
            //akan mengambil detail dari cpmk dari tabel cpmks dengan id
            $detail     = DB::table('cpmks')->where('id', $cpmkId)->first();
            //mengambi cpmk record string
            $cpmkRecs   = $records->where('Cpmk', (string) $cpmkId);
            //final recor memanggil calcWeightredScore untuk menghitung nilai tertimbang
            $finalScore = $this->calcWeightedScore($cpmkRecs);

            if ($finalScore <= 0) continue;

            if (!isset($allCpmkScores[$cpmkId])) {
                $allCpmkScores[$cpmkId] = [
                    'scores'    => [],
                    'kode'      => $detail?->kode  ?? 'CPMK-' . $cpmkId,
                    'deskripsi' => $detail?->judul ?? 'Tidak ada deskripsi',
                ];
            }

            $allCpmkScores[$cpmkId]['scores'][] = $finalScore;
        }
    }

    private function accumulateCplScores(
        \Illuminate\Support\Collection $records,
        array &$allCplScores,
        array $cpmkScoresForThisCourse  // ← tambah parameter ini
    ): void {
        // Ambil mapping CPMK → CPL dari records
        $cpmkToCpl = $records
            ->whereNotNull('Cpl')
            ->whereNotNull('Cpmk')
            ->mapWithKeys(fn($r) => [(string)$r->Cpmk => (string)$r->Cpl])
            ->toArray();

        // Kelompokkan nilai CPMK berdasarkan CPL-nya
        $cplGroups = [];
        foreach ($cpmkScoresForThisCourse as $cpmkId => $nilaiCpmk) {
            $cplId = $cpmkToCpl[$cpmkId] ?? null;
            if (!$cplId) continue;
            //nilaiCpmk hasil dari calcEWeightedScore
            $cplGroups[$cplId][] = $nilaiCpmk;
        }

        // Rata-ratakan nilai CPMK per CPL, per MK
        foreach ($cplGroups as $cplId => $nilaiList) {
            $nilaiCplDiMkIni = array_sum($nilaiList) / count($nilaiList);
            $detail = DB::table('cpls')->where('id', $cplId)->first();

            // Kumpulkan semua nilai CPL dari semua MK
            $allCplScores[$cplId]['scores'][]   = $nilaiCplDiMkIni;
            $allCplScores[$cplId]['kode']       = $detail?->kode  ?? 'CPL-' . $cplId;
            $allCplScores[$cplId]['deskripsi']  = $detail?->judul ?? '-';
        }
    }
    //methode dibawah berguna untuk menghitung nilai tertimbang 
    public function calcWeightedScore(\Illuminate\Support\Collection $records): float
    {
        //atur dulu default 0
        $weightedSum = 0.0;
        $totalWeight = 0.0;
        //kelompokkan per jenis misal UTS, UAS DLL
        foreach ($records->groupBy('Jenis') as $group) {
            $totalBobot = $group->sum('BobotSoal');
            if ($totalBobot <= 0) continue;

            // nilai per jenis = jumlah (BobotSoal/totalBobot × nilaiSoal) dari semua soal dalam jenis itu
            $examScore = $group->sum(
                fn($r) => ($r->BobotSoal / $totalBobot) * ($r->nilaiSoal ?? 0)
            );
            $examWeight   = (float) ($group->first()->examWeight ?? 0);//bobot jenis misal uts=40
            $weightedSum += $examWeight * $examScore;//ini examweight dikali dengan examscore
            $totalWeight += $examWeight;//total weight dari dari exam weight
        }

        return $totalWeight > 0 ? $weightedSum / $totalWeight : 0.0;//ini nilai cpmk dari pembagian weighted sum dibagi totalWeight 
    }

    public function getStatusLabel(float $score): string
    {
        if ($score >= 85) return 'Sangat Baik';
        if ($score >= 70) return 'Baik';
        if ($score >= 60) return 'Cukup';
        return 'Kurang';
    }
}
