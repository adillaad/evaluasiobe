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


    public function getTakenCourses(?array $semestersToFilter = null): array
    {
        $query = DB::table('mutus')
            ->where('npm', $this->NPM);

        if (!empty($semestersToFilter)) {
            $query->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->whereIn('mks.semester', $semestersToFilter);
        }

        return $query->pluck('mutus.Course')
            ->unique()
            ->values()
            ->toArray();
    }

    public function subqueryLatestSemester()
    {
        return DB::table('mutus as m')
            ->leftJoin('tahun_ajaran as ta', 'ta.id', '=', 'm.tahun_ajaran_id')
            ->select(
                'm.Course',
                DB::raw("MAX(CONCAT(
                    CAST(COALESCE(ta.tahun, 2000) AS CHAR CHARACTER SET utf8mb4),
                    CASE WHEN COALESCE(ta.jenis_semester, 'Ganjil') = 'Genap' THEN '1' ELSE '0' END
                )) AS max_year_semester")
            )
            ->where(function($q) {
                $q->where('m.npm', $this->NPM)->orWhere('m.NPM', $this->NPM);
            })
            ->groupBy('m.Course');
    }

    public function getCourseRecords(string $courseKode): \Illuminate\Support\Collection
    {
        $allMutus = DB::table('mutus as m')
            ->leftJoin('tahun_ajaran as ta', 'ta.id', '=', 'm.tahun_ajaran_id')
            ->where(function($q) {
                $q->where('m.npm', $this->NPM)->orWhere('m.NPM', $this->NPM);
            })
            ->where('m.Course', $courseKode)
            ->select('m.*', 'ta.tahun as ta_tahun', 'ta.jenis_semester as ta_jenis_semester')
            ->get();

        if ($allMutus->isEmpty()) {
            return collect();
        }

        $hasKonversi = $allMutus->contains(fn($r) => ($r->sumber ?? '') === 'konversi' || !empty($r->konversi_metode_id));
        $hasRegular  = $allMutus->contains(fn($r) => ($r->sumber ?? '') !== 'konversi' && empty($r->konversi_metode_id));

        if ($hasKonversi && $hasRegular) {
            return $allMutus;
        }

        if ($hasKonversi) {
            return $allMutus->filter(fn($r) => ($r->sumber ?? '') === 'konversi' || !empty($r->konversi_metode_id));
        }

        $subLatestId = DB::table('mutus')
            ->where(function($q) {
                $q->where('npm', $this->NPM)->orWhere('NPM', $this->NPM);
            })
            ->where('Course', $courseKode)
            ->select(
                'Course',
                'Jenis',
                'Cpmk',
                'Cpl',
                'soal',
                'konversi_metode_id',
                DB::raw('MAX(id) as max_id')
            )
            ->groupBy('Course', 'Jenis', 'Cpmk', 'Cpl', 'soal', 'konversi_metode_id');

        return DB::table('mutus as m')
            ->leftJoin('tahun_ajaran as ta', 'ta.id', '=', 'm.tahun_ajaran_id')
            ->joinSub($subLatestId, 'latest', function ($join) {
                $join->on('m.id', '=', 'latest.max_id');
            })
            ->joinSub($this->subqueryLatestSemester(), 't', function ($join) {
                $join->on('m.Course', '=', 't.Course')
                    ->on(
                        DB::raw("CONCAT(
                            CAST(COALESCE(ta.tahun, 2000) AS CHAR CHARACTER SET utf8mb4),
                            CASE WHEN COALESCE(ta.jenis_semester, 'Ganjil') = 'Genap' THEN '1' ELSE '0' END
                        )"),
                        '=',
                        DB::raw("t.max_year_semester COLLATE utf8mb4_unicode_ci")
                    );
            })
            ->where(function($q) {
                $q->where('m.npm', $this->NPM)->orWhere('m.NPM', $this->NPM);
            })
            ->where('m.Course', $courseKode)
            ->select('m.*', 'ta.tahun as ta_tahun', 'ta.jenis_semester as ta_jenis_semester')
            ->get();
    }


    public function calculateSksLulus(?array $semestersToFilter = null): int
    {
        //akan meload prodi yang diambil dari controller
        $prodi = $this->prodi ?? $this->load('prodi')->prodi;
        if (!$prodi) return 0;

        $query = DB::table('mutus')
            ->where('npm', $this->NPM);

        if (!empty($semestersToFilter)) {
            $query->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->whereIn('mks.semester', $semestersToFilter);
        }

        //ambil semua mk yang pernah diambil lewat tabel mutus 
        $courses = $query->select('mutus.Course')
            ->groupBy('mutus.Course')//memastikan agar gak ada duplikat kode mk
            ->pluck('mutus.Course');

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


    public function calculateIPK(?array $semestersToFilter = null): float
    {
        //pastikan prodi ada
        $prodi = $this->prodi ?? $this->load('prodi')->prodi;
        if (!$prodi) return 0.0;

        $query = DB::table('mutus')
            ->where('npm', $this->NPM);

        if (!empty($semestersToFilter)) {
            $query->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->whereIn('mks.semester', $semestersToFilter);
        }

        //ambil semua mk yang ada di mutus
        $courses = $query->select('mutus.Course')
            ->groupBy('mutus.Course')
            ->pluck('mutus.Course');
        
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
    public function getCompetencyData(?array $semestersToFilter = null): array
    {
        $query = DB::table('mutus')
            ->where('npm', $this->NPM);

        if (!empty($semestersToFilter)) {
            $query->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->whereIn('mks.semester', $semestersToFilter);
        }

        //dari controller panggil getCompetencyData di bagian bawah ini ambil semua mk yang ada di tabel mutus karena ada nilainya
        $courses = $query->select('mutus.Course')
            ->groupBy('mutus.Course')
            ->pluck('mutus.Course');

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

            // hitung nilai CPMK khusus MK ini saja, disimpan sementara
            $cpmkScoresThisCourse = [];
            
            $directCpmkIds = $records->whereNotNull('Cpmk')->pluck('Cpmk')->map(fn($id) => (string)$id)->unique()->toArray();
            $konversiMetodeIds = $records->where('sumber', 'konversi')->whereNotNull('konversi_metode_id')->pluck('konversi_metode_id')->unique()->toArray();
            
            $cpmkToKmMap = [];
            $konversiCpmkIds = [];
            if (!empty($konversiMetodeIds)) {
                $mappedCpmkRows = DB::table('konversi_cpmk_metode')
                    ->whereIn('konversi_metode_id', $konversiMetodeIds)
                    ->get();

                foreach ($mappedCpmkRows as $mapRow) {
                    $cIdStr = (string)$mapRow->cpmk_id;
                    $konversiCpmkIds[] = $cIdStr;
                    $cpmkToKmMap[$cIdStr][] = $mapRow->konversi_metode_id;
                }
            }

            $allUniqueCpmkIds = array_unique(array_merge($directCpmkIds, $konversiCpmkIds));

            foreach ($allUniqueCpmkIds as $cpmkId) {
                $kmIdsForThisCpmk = array_unique($cpmkToKmMap[(string)$cpmkId] ?? []);

                $cpmkRecords = $records->filter(function($r) use ($cpmkId, $kmIdsForThisCpmk) {
                    $isDirectMatch = ((string)($r->Cpmk ?? '') === (string)$cpmkId);
                    $isLegacyKonversi = (empty($r->Cpmk) && !empty($r->konversi_metode_id) && in_array($r->konversi_metode_id, $kmIdsForThisCpmk));
                    return $isDirectMatch || $isLegacyKonversi;
                })->unique('id');

                if ($cpmkRecords->isEmpty()) continue;

                $score = $this->calcWeightedScore($cpmkRecords);
                if ($score > 0) {
                    $cpmkScoresThisCourse[$cpmkId] = $score;
                }
            }

            $this->accumulateCplScores($records, $allCplScores, $cpmkScoresThisCourse);
        }
        //setelah dapet data score cpmk tiap mk kemudian akan dikumpulkan semua cpmk yang sama dan di bagi rata
        $cpmks = collect($allCpmkScores)->map(function ($d, $id) {
            /*
            // -----------------------------------------------------------------------------
            // CATATAN: Opsi Perhitungan Akumulasi CPMK Mahasiswa Menggunakan Bobot (cpmk_mk.bobot)
            // Jika ingin mengaktifkan Rata-rata Tertimbang (Weighted Average) lintas MK,
            // aktifkan (uncomment) blok kode di bawah ini:
            // -----------------------------------------------------------------------------
            $totalWeight = array_sum($d['weights'] ?? []);
            if ($totalWeight > 0) {
                $weightedSum = 0;
                foreach ($d['scores'] as $idx => $score) {
                    $weightedSum += $score * ($d['weights'][$idx] ?? 0);
                }
                $avg = $weightedSum / $totalWeight;
            } else {
                $avg = array_sum($d['scores']) / count($d['scores']);
            }
            */

            $avg = array_sum($d['scores']) / count($d['scores']);//rata2 cpmk lintas mk biasa
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
            ->where('id_prodi', $this->id_prodi)
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

    public function calculateKetercapaianCpl(?array $semestersToFilter = null): array
    {
        $prodi = $this->prodi ?? $this->load('prodi')->prodi;
        if (!$prodi) return ['avg' => 0.0, 'items' => []];

        $isAptikom = (bool)$prodi->is_aptikom;

        // 1. Get all courses taken by student
        $query = DB::table('mutus')->where('npm', $this->NPM);
        if (!empty($semestersToFilter)) {
            $query->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->whereIn('mks.semester', $semestersToFilter);
        }
        $takenCourses = $query->select('mutus.Course')->groupBy('mutus.Course')->pluck('mutus.Course');

        // 2. Identify which courses passed
        $passedCourses = [];
        foreach ($takenCourses as $kode) {
            $grade = $this->calculateFinalGrade($kode, $isAptikom);
            if ($prodi->isLulus($grade)) {
                $passedCourses[] = $kode;
            }
        }

        // 3. Query mk_cpl mapping for this prodi
        $mkCplQuery = DB::table('mk_cpl')
            ->join('mks', 'mk_cpl.mk_kode', '=', 'mks.kode')
            ->where('mk_cpl.id_prodi', $this->id_prodi);

        if (!empty($semestersToFilter)) {
            $mkCplQuery->whereIn('mks.semester', $semestersToFilter);
        }

        $cplGroups = $mkCplQuery->select('mk_cpl.cpl_id', 'mk_cpl.mk_kode')
            ->get()
            ->groupBy('cpl_id');

        $cplList = DB::table('cpls')->where('id_prodi', $this->id_prodi)->get()->keyBy('id');

        $results = [];
        $totalPercent = 0.0;
        $count = 0;

        foreach ($cplGroups as $cplId => $items) {
            $cplObj = $cplList->get($cplId);
            $mkCodes = $items->pluck('mk_kode')->unique()->toArray();
            $totalRequired = count($mkCodes);
            $passedCount = count(array_intersect($mkCodes, $passedCourses));
            $percent = $totalRequired > 0 ? round(($passedCount / $totalRequired) * 100, 1) : 0.0;

            $results[] = [
                'cpl_id'     => $cplId,
                'kode'       => $cplObj?->kode ?? "CPL-$cplId",
                'deskripsi'  => $cplObj?->judul ?? '-',
                'total_mk'   => $totalRequired,
                'lulus_mk'   => $passedCount,
                'persentase' => $percent,
            ];

            $totalPercent += $percent;
            $count++;
        }

        $avg = $count > 0 ? round($totalPercent / $count, 1) : 0.0;
        return ['avg' => $avg, 'items' => $results];
    }

    public function getAcademicBreakdown(): array
    {
        $prodi = $this->prodi ?? $this->load('prodi')->prodi;
        if (!$prodi) {
            return ['semesters' => [], 'summary' => ['total_sks' => 0, 'total_sks_lulus' => 0, 'ipk_akhir' => 0.0, 'ips_terakhir' => 0.0, 'semester_terakhir' => '-', 'periode_terakhir' => '-']];
        }

        $isAptikom = (bool)$prodi->is_aptikom;
        $baseAngkatan = (int)$this->angkatan > 1900 ? (int)$this->angkatan : (int)substr($this->NPM, 0, 2) + 2000;
        if ($baseAngkatan < 2000) {
            $baseAngkatan = (int)date('Y') - 4;
        }

        // Ambil semua MK yang pernah diambil oleh mahasiswa dari tabel mutus
        $allMutus = DB::table('mutus as m')
            ->leftJoin('mks', 'm.Course', '=', 'mks.kode')
            ->leftJoin('tahun_ajaran as ta', 'm.tahun_ajaran_id', '=', 'ta.id')
            ->where(function ($q) {
                $q->where('m.npm', $this->NPM)->orWhere('m.NPM', $this->NPM);
            })
            ->select(
                'm.Course',
                'mks.nama as nama_mk',
                'mks.semester as mk_semester',
                DB::raw('(mks.bobot_teori + mks.bobot_praktikum) as sks'),
                'm.tahun_ajaran_id',
                'ta.tahun as ta_tahun',
                'ta.jenis_semester as ta_jenis_semester',
                'm.tahun as mutu_tahun'
            )
            ->distinct()
            ->get();

        if ($allMutus->isEmpty()) {
            return ['semesters' => [], 'summary' => ['total_sks' => 0, 'total_sks_lulus' => 0, 'ipk_akhir' => 0.0, 'ips_terakhir' => 0.0, 'semester_terakhir' => '-', 'periode_terakhir' => '-']];
        }

        $allCourseKodes = $allMutus->pluck('Course')->unique()->toArray();
        $gradeCache = [];
        foreach ($allCourseKodes as $cKode) {
            $finalGrade = $this->calculateFinalGrade($cKode, $isAptikom);
            [$letterGrade, $gradeWeight] = $prodi->convertGrade($finalGrade);
            $gradeCache[$cKode] = [
                'finalGrade' => $finalGrade,
                'letter' => $letterGrade ?? 'E',
                'weight' => (float)($gradeWeight ?? 0.0),
            ];
        }

        $grouped = [];
        foreach ($allMutus as $row) {
            $semNum = (int)($row->mk_semester ?: 1);

            // Resolusi label periode akademik (contoh: Genap - 2022/2023)
            if (!empty($row->ta_tahun) && !empty($row->ta_jenis_semester)) {
                $nextY = (int)$row->ta_tahun + 1;
                $periodLabel = "{$row->ta_jenis_semester} - {$row->ta_tahun}/{$nextY}";
                $academicYear = "{$row->ta_tahun}/{$nextY}";
                $semesterType = $row->ta_jenis_semester;
            } elseif (!empty($row->mutu_tahun) && preg_match('/(Ganjil|Genap)\s*(\d{4})/i', $row->mutu_tahun, $m)) {
                $y = (int)$m[2];
                $nextY = $y + 1;
                $periodLabel = "{$m[1]} - {$y}/{$nextY}";
                $academicYear = "{$y}/{$nextY}";
                $semesterType = ucfirst(strtolower($m[1]));
            } else {
                $yearOffset = (int)floor(($semNum - 1) / 2);
                $calYear = $baseAngkatan + $yearOffset;
                $nextYear = $calYear + 1;
                $semesterType = ($semNum % 2 === 1) ? 'Ganjil' : 'Genap';
                $periodLabel = "{$semesterType} - {$calYear}/{$nextYear}";
                $academicYear = "{$calYear}/{$nextYear}";
            }

            $key = "Sem_{$semNum}";
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'semNumber' => $semNum,
                    'periodLabel' => $periodLabel,
                    'academicYear' => $academicYear,
                    'semesterType' => $semesterType,
                    'courses' => []
                ];
            }

            if (!isset($grouped[$key]['courses'][$row->Course])) {
                $g = $gradeCache[$row->Course] ?? ['finalGrade' => 0, 'letter' => 'E', 'weight' => 0.0];
                $sks = (float)($row->sks ?: 0);
                $mutu = $sks * $g['weight'];
                $grouped[$key]['courses'][$row->Course] = [
                    'kode' => $row->Course,
                    'nama' => $row->nama_mk ?: $row->Course,
                    'sks' => $sks,
                    'nilai_akhir' => round($g['finalGrade'], 2),
                    'nilai_huruf' => $g['letter'],
                    'bobot' => $g['weight'],
                    'mutu' => round($mutu, 2),
                    'is_lulus' => $prodi->isLulus($g['finalGrade']),
                ];
            }
        }

        ksort($grouped, SORT_NATURAL);

        $cumulativeSks = 0;
        $cumulativeMutu = 0;
        $cumulativeSksLulus = 0;
        $resultSemesters = [];

        foreach ($grouped as $semKey => $semData) {
            $semSks = 0;
            $semMutu = 0;
            $semSksLulus = 0;

            foreach ($semData['courses'] as $c) {
                $semSks += $c['sks'];
                $semMutu += $c['mutu'];
                if ($c['is_lulus']) {
                    $semSksLulus += $c['sks'];
                }
            }

            $ips = $semSks > 0 ? round($semMutu / $semSks, 2) : 0.0;

            $cumulativeSks += $semSks;
            $cumulativeMutu += $semMutu;
            $cumulativeSksLulus += $semSksLulus;
            $ipk = $cumulativeSks > 0 ? round($cumulativeMutu / $cumulativeSks, 2) : 0.0;

            $resultSemesters[] = [
                'semester' => $semData['semNumber'],
                'periode' => $semData['periodLabel'],
                'semester_angka' => $semData['semNumber'],
                'semester_label' => "Semester {$semData['semNumber']}",
                'periode_label' => $semData['periodLabel'],
                'tahun_akademik' => $semData['academicYear'],
                'jenis_semester' => $semData['semesterType'],
                'sks_semester' => $semSks,
                'sks_lulus_semester' => $semSksLulus,
                'bobot_mutu_semester' => round($semMutu, 2),
                'ips' => $ips,
                'sks_kumulatif' => $cumulativeSks,
                'sks_lulus_kumulatif' => $cumulativeSksLulus,
                'bobot_mutu_kumulatif' => round($cumulativeMutu, 2),
                'ipk_kumulatif' => $ipk,
                'courses' => array_values($semData['courses']),
            ];
        }

        $lastSem = !empty($resultSemesters) ? end($resultSemesters) : null;

        return [
            'semesters' => $resultSemesters,
            'summary' => [
                'total_sks' => $cumulativeSks,
                'total_sks_lulus' => $cumulativeSksLulus,
                'ipk_akhir' => $lastSem ? $lastSem['ipk_kumulatif'] : 0.0,
                'ips_terakhir' => $lastSem ? $lastSem['ips'] : 0.0,
                'semester_terakhir' => $lastSem ? $lastSem['semester_label'] : '-',
                'periode_terakhir' => $lastSem ? $lastSem['periode_label'] : '-',
            ]
        ];
    }



    private function accumulateCpmkScores(
        \Illuminate\Support\Collection $records,
        array &$allCpmkScores
    ): void {
        if ($records->isEmpty()) return;

        $courseKode = $records->first()->Course;
        $npm = $records->first()->npm ?? $records->first()->NPM;

        $allCourseRecords = DB::table('mutus')
            ->where(function($q) use ($npm) {
                $q->where('npm', $npm)->orWhere('NPM', $npm);
            })
            ->where('Course', $courseKode)
            ->get();

        if ($allCourseRecords->isEmpty()) {
            $allCourseRecords = $records;
        }

        $konversiMetodeIds = $allCourseRecords->where('sumber', 'konversi')
            ->whereNotNull('konversi_metode_id')
            ->pluck('konversi_metode_id')
            ->unique()
            ->toArray();

        $cpmkToKmMap = [];
        $konversiCpmkIds = [];
        if (!empty($konversiMetodeIds)) {
            $mappedCpmkRows = DB::table('konversi_cpmk_metode')
                ->whereIn('konversi_metode_id', $konversiMetodeIds)
                ->get();

            foreach ($mappedCpmkRows as $mapRow) {
                $cId = (int)$mapRow->cpmk_id;
                $konversiCpmkIds[] = $cId;
                $cpmkToKmMap[$cId][] = $mapRow->konversi_metode_id;
            }
        }

        $directCpmkIds = [];
        foreach ($allCourseRecords->pluck('Cpmk')->filter() as $cVal) {
            $cValStr = trim((string)$cVal);
            if (is_numeric($cValStr)) {
                $directCpmkIds[] = (int)$cValStr;
            } else {
                $cIdFromDb = DB::table('cpmks')->where('kode', $cValStr)->value('id');
                if ($cIdFromDb) {
                    $directCpmkIds[] = (int)$cIdFromDb;
                }
            }
        }

        $allUniqueCpmkIds = array_unique(array_merge($directCpmkIds, $konversiCpmkIds));

        foreach ($allUniqueCpmkIds as $cpmkId) {
            $cpmkId = (int)$cpmkId;
            $kmIdsForThisCpmk = array_unique($cpmkToKmMap[$cpmkId] ?? []);

            if (empty($kmIdsForThisCpmk)) {
                $kmIdsForThisCpmk = DB::table('konversi_cpmk_metode as kcm')
                    ->join('konversi_metode as km', 'kcm.konversi_metode_id', '=', 'km.id')
                    ->join('penilaian_konversi as pk', 'km.penilaian_konversi_id', '=', 'pk.id')
                    ->where('pk.mk_kode', $courseKode)
                    ->where('kcm.cpmk_id', $cpmkId)
                    ->pluck('km.id')
                    ->unique()
                    ->toArray();
            }

            $isKonversiCourse = $allCourseRecords->contains(fn($r) => !empty($r->konversi_metode_id) || ($r->sumber ?? '') === 'konversi');

            if ($isKonversiCourse && !empty($kmIdsForThisCpmk)) {
                $cpmkRecords = $allCourseRecords->whereIn('konversi_metode_id', $kmIdsForThisCpmk);
            } else {
                $cpmkRecords = $allCourseRecords->filter(function($r) use ($cpmkId, $kmIdsForThisCpmk) {
                    $rCpmkId = null;
                    if (!empty($r->Cpmk)) {
                        $cValStr = trim((string)$r->Cpmk);
                        if (is_numeric($cValStr)) {
                            $rCpmkId = (int)$cValStr;
                        } else {
                            $rCpmkId = DB::table('cpmks')->where('kode', $cValStr)->value('id');
                        }
                    }

                    $isMatchByCpmk = ($rCpmkId !== null && (int)$rCpmkId === (int)$cpmkId);
                    $isMatchByKonversi = (!empty($r->konversi_metode_id) && in_array($r->konversi_metode_id, $kmIdsForThisCpmk));

                    return $isMatchByCpmk || $isMatchByKonversi;
                })->unique('id');
            }

            if ($cpmkRecords->isEmpty()) continue;

            $finalScore = $this->calcWeightedScore($cpmkRecords);
            if ($finalScore <= 0) continue;

            $detail = DB::table('cpmks')->where('id', $cpmkId)->first();

            $cpmkMkBobot = 0.0;
            if (\Illuminate\Support\Facades\Schema::hasColumn('cpmk_mk', 'bobot')) {
                $cpmkMkBobot = (float) DB::table('cpmk_mk')
                    ->where('cpmk_id', $cpmkId)
                    ->where('mk_kode', $courseKode)
                    ->value('bobot');
            }

            if (!isset($allCpmkScores[$cpmkId])) {
                $allCpmkScores[$cpmkId] = [
                    'scores'    => [],
                    'weights'   => [],
                    'kode'      => $detail?->kode  ?? 'CPMK-' . $cpmkId,
                    'deskripsi' => $detail?->judul ?? 'Tidak ada deskripsi',
                ];
            }

            $allCpmkScores[$cpmkId]['scores'][]  = $finalScore;
            $allCpmkScores[$cpmkId]['weights'][] = $cpmkMkBobot;
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
