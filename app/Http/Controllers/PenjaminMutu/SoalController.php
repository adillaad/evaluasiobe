<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Soal;
use App\Models\MK;
use App\Models\CPMK;
use App\Models\Mutu;
use App\Models\TanpaSoal;
use App\Models\MetodePenilaian;
use App\Traits\UniversityFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PDF;

class SoalController extends Controller
{
    use UniversityFilterTrait;

    public function list(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;

        $mkDenganSoalDiajukan = DB::table('soals')
            ->whereIn('status', ['Menunggu', 'Valid', 'Tolak'])
            ->pluck('kode_mk')
            ->merge(
                DB::table('tanpa_soal')
                    ->whereIn('status', ['Menunggu Validasi', 'Valid', 'Ditolak'])
                    ->pluck('kode_mk')
            )
            ->unique()
            ->values();

        if ($mkDenganSoalDiajukan->isEmpty()) {
            $filterData = $this->getFilterData($request);
            return view('penjamin-mutu.soal.list', array_merge(
                ['mkList' => collect()],
                $filterData
            ));
        }

        $mkQuery = DB::table('mks')
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->whereIn('mks.kode', $mkDenganSoalDiajukan)
            ->select(
                'mks.kode',
                'mks.nama as nama_mk',
                'prodi.nama as nama_prodi',
                'prodi.id as prodi_id',
                'fakultas.nama as nama_fakultas',
                'universitas.nama as nama_universitas'
            );

        if ($otoritas === 'Penjamin Mutu Universitas') {
            $mkQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif ($otoritas === 'Penjamin Mutu Fakultas') {
            $mkQuery->where('prodi.id_fakultas', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $mkQuery->where('mks.id_prodi', auth()->user()->id_prodiUser);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $mkQuery->where(function ($q) use ($search) {
                $q->where('mks.nama', 'like', "%{$search}%")
                    ->orWhere('mks.kode', 'like', "%{$search}%");
            });
        }

        $filterData = $this->getFilterData($request);
        $mks        = $mkQuery->orderBy('mks.kode')->get();

        $mkList = $mks->map(function ($mk) {
            return $this->buildMkSummary($mk->kode, $mk);
        });

        return view('penjamin-mutu.soal.list', array_merge(
            ['mkList' => $mkList],
            $filterData
        ));
    }

    public function detail($kode_mk)
    {
        $mk = DB::table('mks')
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->where('mks.kode', $kode_mk)
            ->select('mks.*', 'prodi.nama as nama_prodi')
            ->first();

        if (!$mk) {
            return redirect()->back()->with('error', 'Mata Kuliah tidak ditemukan.');
        }

        $metodes = DB::table('cpl_mk_cpmk_penilaian as cmcp')
            ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
            ->join('metode_penilaian as mp', 'mp.id', '=', 'pm.metode_id')
            ->where('cmcp.mk_kode', $kode_mk)
            ->select('mp.id as metode_id', 'mp.nama as nama_metode', DB::raw('SUM(pm.bobot) as total_bobot_metode'))
            ->groupBy('mp.id', 'mp.nama')
            ->orderBy('mp.nama')
            ->get();

        $cpmkMk = DB::table('cpmk_mk')
            ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
            ->where('cpmk_mk.mk_kode', $kode_mk)
            ->select('cpmks.id', 'cpmks.kode', 'cpmks.judul')
            ->get();

        $detailMetodes = $metodes->map(function ($metode) use ($kode_mk) {
            $totalBobotMetode = (float) $metode->total_bobot_metode;

            // CPMK yang memang dikonfigurasi untuk metode penilaian ini di Asesmen
            $cpmkMkMetode = DB::table('cpl_mk_cpmk_penilaian as cmcp')
                ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
                ->join('cpmks', 'cmcp.cpmk_id', '=', 'cpmks.id')
                ->where('cmcp.mk_kode', $kode_mk)
                ->where('pm.metode_id', $metode->metode_id)
                ->select(
                    'cpmks.id',
                    'cpmks.kode',
                    'cpmks.judul',
                    DB::raw('SUM(pm.bobot) as bobot_cpmk_metode')
                )
                ->groupBy('cpmks.id', 'cpmks.kode', 'cpmks.judul')
                ->get();

            $soals = DB::table('soals')
                ->leftJoin('cpls', 'soals.cpl', '=', 'cpls.id')
                ->leftJoin('cpmks', 'soals.cpmk', '=', 'cpmks.id')
                ->where('soals.kode_mk', $kode_mk)
                ->where('soals.jenis', $metode->metode_id)
                ->whereIn('soals.status', ['Menunggu', 'Valid', 'Tolak'])
                ->select(
                    'soals.id',
                    'soals.pertanyaan',
                    'soals.bobotSoal',
                    'soals.persentase_cpmk',
                    'soals.status',
                    'soals.komentar',
                    'cpls.kode as kode_cpl',
                    'cpmks.id as cpmk_id',
                    'cpmks.kode as kode_cpmk',
                    'cpmks.judul as judul_cpmk'
                )
                ->orderBy('soals.cpmk')->orderBy('soals.id')
                ->get();

            $tanpaSoals = DB::table('tanpa_soal')
                ->leftJoin('cpls', 'tanpa_soal.cpl_id', '=', 'cpls.id')
                ->leftJoin('cpmks', 'tanpa_soal.cpmk_id', '=', 'cpmks.id')
                ->where('tanpa_soal.kode_mk', $kode_mk)
                ->where('tanpa_soal.metode_id', $metode->metode_id)
                ->select(
                    'tanpa_soal.id',
                    'tanpa_soal.nama_instrumen',
                    'tanpa_soal.bobot_TS as bobotSoal',
                    'tanpa_soal.persentase_cpmk',
                    'tanpa_soal.status',
                    'cpls.kode as kode_cpl',
                    'cpmks.id as cpmk_id',
                    'cpmks.kode as kode_cpmk',
                    'cpmks.judul as judul_cpmk'
                )
                ->orderBy('tanpa_soal.cpmk_id')
                ->get();

            // Hitung ulang bobotSoal jika di database bernilai 0 / null
            $totalSoalPerCpmk = $soals->groupBy('cpmk_id');
            $totalTanpaPerCpmk = $tanpaSoals->groupBy('cpmk_id');

            // Sematkan bobot_cpmk dari cpmkMkMetode ke masing-masing item soal / tanpa_soal
            $soals->transform(function ($soal) use ($cpmkMkMetode, $totalSoalPerCpmk, $totalTanpaPerCpmk) {
                $cpmkBobot = (float)($cpmkMkMetode->where('id', $soal->cpmk_id)->first()->bobot_cpmk_metode ?? 0);
                $soal->bobot_cpmk = $cpmkBobot;

                if ((float)$soal->bobotSoal == 0) {
                    if ((float)$soal->persentase_cpmk > 0) {
                        $soal->bobotSoal = round(($soal->persentase_cpmk / 100) * $cpmkBobot, 2);
                    } else {
                        // Jika persentase_cpmk belum diisi, bagi rata bobot CPMK ke jumlah soal di CPMK tsb
                        $countSoal = isset($totalSoalPerCpmk[$soal->cpmk_id]) ? $totalSoalPerCpmk[$soal->cpmk_id]->count() : 0;
                        $countTanpa = isset($totalTanpaPerCpmk[$soal->cpmk_id]) ? $totalTanpaPerCpmk[$soal->cpmk_id]->count() : 0;
                        $totalItem = $countSoal + $countTanpa;
                        if ($totalItem > 0 && $cpmkBobot > 0) {
                            $soal->bobotSoal = round($cpmkBobot / $totalItem, 2);
                            $soal->persentase_cpmk = round(100 / $totalItem, 1);
                        }
                    }
                }
                return $soal;
            });

            $tanpaSoals->transform(function ($ts) use ($cpmkMkMetode, $totalSoalPerCpmk, $totalTanpaPerCpmk) {
                $cpmkBobot = (float)($cpmkMkMetode->where('id', $ts->cpmk_id)->first()->bobot_cpmk_metode ?? 0);
                $ts->bobot_cpmk = $cpmkBobot;

                if ((float)$ts->bobotSoal == 0) {
                    if ((float)$ts->persentase_cpmk > 0) {
                        $ts->bobotSoal = round(($ts->persentase_cpmk / 100) * $cpmkBobot, 2);
                    } else {
                        $countSoal = isset($totalSoalPerCpmk[$ts->cpmk_id]) ? $totalSoalPerCpmk[$ts->cpmk_id]->count() : 0;
                        $countTanpa = isset($totalTanpaPerCpmk[$ts->cpmk_id]) ? $totalTanpaPerCpmk[$ts->cpmk_id]->count() : 0;
                        $totalItem = $countSoal + $countTanpa;
                        if ($totalItem > 0 && $cpmkBobot > 0) {
                            $ts->bobotSoal = round($cpmkBobot / $totalItem, 2);
                            $ts->persentase_cpmk = round(100 / $totalItem, 1);
                        }
                    }
                }
                return $ts;
            });

            $totalBobotSoal    = $soals->sum('bobotSoal');
            $totalBobotTanpa   = $tanpaSoals->sum('bobotSoal');
            $totalBobotTerisi  = $totalBobotSoal + $totalBobotTanpa;
            $persenKelengkapan = $totalBobotMetode > 0
                ? round(($totalBobotTerisi / $totalBobotMetode) * 100, 1)
                : 0;

            $cpmkTerpetakan = collect($soals->pluck('cpmk_id'))
                ->merge($tanpaSoals->pluck('cpmk_id'))
                ->filter()->unique()->values();

            $cpmkBelum = $cpmkMkMetode->filter(fn($c) => !$cpmkTerpetakan->contains($c->id))->values();

            $cpmkKelengkapan = $cpmkMkMetode->map(function ($cpmk) use ($soals, $tanpaSoals) {
                $pctSoal  = $soals->where('cpmk_id', $cpmk->id)->sum('persentase_cpmk');
                $pctTanpa = $tanpaSoals->where('cpmk_id', $cpmk->id)->sum('persentase_cpmk');
                $total    = $pctSoal + $pctTanpa;

                // Jika persentase_cpmk belum terisi/0 tetapi bobotSoal sudah terisi
                if ($total == 0 && (float)$cpmk->bobot_cpmk_metode > 0) {
                    $bobotSoal  = $soals->where('cpmk_id', $cpmk->id)->sum('bobotSoal');
                    $bobotTanpa = $tanpaSoals->where('cpmk_id', $cpmk->id)->sum('bobotSoal');
                    $totalBobot = $bobotSoal + $bobotTanpa;
                    if ($totalBobot > 0) {
                        $total = round(($totalBobot / $cpmk->bobot_cpmk_metode) * 100, 1);
                    }
                }

                return [
                    'id'        => $cpmk->id,
                    'kode'      => $cpmk->kode,
                    'judul'     => $cpmk->judul,
                    'total_pct' => $total,
                    'lengkap'   => $total >= 100,
                ];
            });

            $semuaCpmkLengkap = $cpmkKelengkapan->every(fn($c) => $c['lengkap']);
            $bobotLengkap     = abs($totalBobotTerisi - $totalBobotMetode) < 0.01;

            return [
                'metode_id'          => $metode->metode_id,
                'nama_metode'        => $metode->nama_metode,
                'total_bobot_metode' => $totalBobotMetode,
                'total_bobot_terisi' => round($totalBobotTerisi, 2),
                'persen_kelengkapan' => $persenKelengkapan,
                'bobot_lengkap'      => $bobotLengkap,
                'cpmk_lengkap'       => $semuaCpmkLengkap,
                'semua_lengkap'      => $bobotLengkap && $semuaCpmkLengkap,
                'soals'              => $soals,
                'tanpa_soals'        => $tanpaSoals,
                'cpmk_kelengkapan'   => $cpmkKelengkapan,
                'cpmk_belum'         => $cpmkBelum,
            ];
        });

        // mkLengkap hanya untuk info/peringatan, tidak memblokir validasi
        $mkLengkap = $detailMetodes->every(fn($m) => $m['semua_lengkap']);

        $statusSoal = DB::table('soals')
            ->where('kode_mk', $kode_mk)
            ->whereIn('status', ['Menunggu', 'Valid', 'Tolak'])
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        return view('penjamin-mutu.soal.detail', compact(
            'mk',
            'detailMetodes',
            'cpmkMk',
            'mkLengkap',
            'statusSoal',
            'kode_mk'
        ));
    }

    public function validasiMK(Request $request, $kode_mk)
    {
        $mk = DB::table('mks')->where('kode', $kode_mk)->first();
        if (!$mk) {
            return redirect()->back()->with('error', 'MK tidak ditemukan.');
        }

        DB::table('soals')
            ->where('kode_mk', $kode_mk)
            ->whereIn('status', ['Menunggu', 'Tolak'])
            ->update(['status' => 'Valid', 'komentar' => null]);

        DB::table('tanpa_soal')
            ->where('kode_mk', $kode_mk)
            ->whereIn('status', ['Menunggu Validasi', 'Ditolak'])
            ->update(['status' => 'Valid']);

        return $this->redirectToList("Semua soal MK {$kode_mk} berhasil divalidasi.");
    }

    public function tolakMK(Request $request, $kode_mk)
    {
        $request->validate([
            'komentar' => 'required|string|min:10',
        ], [
            'komentar.required' => 'Komentar wajib diisi sebelum menolak.',
            'komentar.min'      => 'Komentar minimal 10 karakter.',
        ]);

        $mk = DB::table('mks')->where('kode', $kode_mk)->first();
        if (!$mk) {
            return redirect()->back()->with('error', 'MK tidak ditemukan.');
        }

        DB::table('soals')
            ->where('kode_mk', $kode_mk)
            ->whereIn('status', ['Menunggu'])
            ->update(['status' => 'Tolak', 'komentar' => $request->komentar]);

        DB::table('tanpa_soal')
            ->where('kode_mk', $kode_mk)
            ->whereIn('status', ['Menunggu Validasi'])
            ->update(['status' => 'Ditolak']);

        return $this->redirectToList("Soal MK {$kode_mk} ditolak. Komentar dikirim ke dosen.");
    }

    public function pesanMK(Request $request, $kode_mk)
    {
        $request->validate([
            'pesan' => 'required|string|min:5',
        ], [
            'pesan.required' => 'Pesan tidak boleh kosong.',
        ]);

        $mk = DB::table('mks')->where('kode', $kode_mk)->first();
        if (!$mk) {
            return redirect()->back()->with('error', 'MK tidak ditemukan.');
        }

        DB::table('soals')
            ->where('kode_mk', $kode_mk)
            ->whereIn('status', ['Menunggu', 'Valid'])
            ->update(['komentar' => '[Catatan PM] ' . $request->pesan]);

        return redirect()->back()
            ->with('success', "Pesan berhasil dikirim ke dosen MK {$kode_mk}. Status soal tidak berubah.");
    }

    // ─── Helper ────────────────────────────────────────────────────────────────

    private function redirectToList($message)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $routeMap = [
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.list-soal',
            'Penjamin Mutu Fakultas'    => 'penjamin-mutu.fakultas.list-soal',
            'Kepala Program Studi'      => 'kepala-program-studi.list-soal',
            'default'                   => 'penjamin-mutu.program-studi.list-soal',
        ];
        $routeName = $routeMap[$otoritas] ?? 'penjamin-mutu.program-studi.list-soal';
        return redirect()->route($routeName)->with('success', $message);
    }

    private function buildMkSummary($kode_mk, $mkInfo)
    {
        $metodes = DB::table('cpl_mk_cpmk_penilaian as cmcp')
            ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
            ->join('metode_penilaian as mp', 'mp.id', '=', 'pm.metode_id')
            ->where('cmcp.mk_kode', $kode_mk)
            ->select('mp.id', 'mp.nama', DB::raw('SUM(pm.bobot) as total_bobot'))
            ->groupBy('mp.id', 'mp.nama')
            ->get();

        $totalCpmk = DB::table('cpmk_mk')->where('mk_kode', $kode_mk)->count();

        $cpmkTerpetakan = DB::table('soals')
            ->where('kode_mk', $kode_mk)
            ->whereNotNull('cpmk')
            ->whereIn('status', ['Menunggu', 'Valid'])
            ->select('cpmk as cpmk_id')
            ->union(
                DB::table('tanpa_soal')
                    ->where('kode_mk', $kode_mk)
                    ->whereNotNull('cpmk_id')
                    ->whereIn('status', ['Menunggu Validasi', 'Valid'])
                    ->select('cpmk_id')
            )
            ->distinct()
            ->count();

        $jumlahDiajukan = DB::table('soals')
            ->where('kode_mk', $kode_mk)
            ->whereIn('status', ['Menunggu', 'Valid', 'Tolak'])
            ->count()
            + DB::table('tanpa_soal')
            ->where('kode_mk', $kode_mk)
            ->whereIn('status', ['Menunggu Validasi', 'Valid', 'Ditolak'])
            ->count();

        $statusSoal = DB::table('soals')
            ->where('kode_mk', $kode_mk)
            ->whereIn('status', ['Menunggu', 'Valid', 'Tolak'])
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $statusTanpa = DB::table('tanpa_soal')
            ->where('kode_mk', $kode_mk)
            ->whereIn('status', ['Menunggu Validasi', 'Valid', 'Ditolak'])
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $adaSoalValid    = ($statusSoal->get('Valid', 0) + $statusTanpa->get('Valid', 0)) > 0;
        $adaSoalMenunggu = ($statusSoal->get('Menunggu', 0) + $statusTanpa->get('Menunggu Validasi', 0)) > 0;
        $adaSoalTolak    = ($statusSoal->get('Tolak', 0) + $statusTanpa->get('Ditolak', 0)) > 0;

        $bobotLengkap = $metodes->every(function ($metode) use ($kode_mk) {
            $bobotSoal  = DB::table('soals')
                ->where('kode_mk', $kode_mk)->where('jenis', $metode->id)
                ->whereIn('status', ['Menunggu', 'Valid'])->sum('bobotSoal');
            $bobotTanpa = DB::table('tanpa_soal')
                ->where('kode_mk', $kode_mk)->where('metode_id', $metode->id)
                ->whereIn('status', ['Menunggu Validasi', 'Valid'])->sum('bobot_TS');
            return abs(($bobotSoal + $bobotTanpa) - $metode->total_bobot) < 0.01;
        });

        $cpmkLengkap  = ($totalCpmk > 0 && $cpmkTerpetakan >= $totalCpmk);
        $semuaLengkap = $bobotLengkap && $cpmkLengkap;

        if ($adaSoalValid && !$adaSoalMenunggu && !$adaSoalTolak) {
            $statusLabel = 'valid';
        } elseif ($adaSoalTolak && !$adaSoalMenunggu) {
            $statusLabel = 'ditolak';
        } elseif ($adaSoalMenunggu || $semuaLengkap) {
            $statusLabel = 'siap';
        } else {
            $statusLabel = 'belum';
        }

        return (object) [
            'kode'             => $kode_mk,
            'nama_mk'          => $mkInfo->nama_mk,
            'nama_prodi'       => $mkInfo->nama_prodi,
            'nama_fakultas'    => $mkInfo->nama_fakultas ?? null,
            'nama_universitas' => $mkInfo->nama_universitas ?? null,
            'total_cpmk'       => $totalCpmk,
            'cpmk_terpetakan'  => $cpmkTerpetakan,
            'cpmk_lengkap'     => $cpmkLengkap,
            'bobot_lengkap'    => $bobotLengkap,
            'semua_lengkap'    => $semuaLengkap,
            'status_label'     => $statusLabel,
            'jumlah_metode'    => $metodes->count(),
            'jumlah_diajukan'  => $jumlahDiajukan,
        ];
    }

    public function cetakSoal($id)
    {
        $soals = Soal::findOrFail($id);
        return view('penjamin-mutu.soal.cetakSoal', compact('soals'));
    }

    public function import(Request $request)
    {
        $filterData = $this->getFilterData($request);
        $mutus = $this->getPaginatedGroupedMutus($request, 'soal');

        return view('penjamin-mutu.soal.importMutu', array_merge(compact('mutus'), $filterData));
    }

    public function import1(Request $request)
    {
        $filterData = $this->getFilterData($request);
        $mutus = $this->getPaginatedGroupedMutus($request, 'konversi');

        return view('penjamin-mutu.soal.importTanpaSoal', array_merge(compact('mutus'), $filterData));
    }

    public function filter(Request $request)
    {
        $filterData = $this->getFilterData($request);
        $type = $request->get('type') === 'tanpa-soal' ? 'konversi' : 'soal';
        $mutus = $this->getPaginatedGroupedMutus($request, $type);

        $view = $request->get('type') === 'tanpa-soal'
            ? 'penjamin-mutu.soal.importTanpaSoal'
            : 'penjamin-mutu.soal.importMutu';

        return view($view, array_merge(compact('mutus'), $filterData));
    }

    private function getPaginatedGroupedMutus(Request $request, string $type = 'soal', int $perPage = 10)
    {
        $allRecords = $this->mutuPenilaianQuery($request, $type)->get();

        // Grouping records per Mahasiswa + Course + Jenis + Tahun Ajaran
        $grouped = $allRecords->groupBy(function ($item) {
            $npmKey = trim((string)($item->npm ?? $item->NPM ?? 'unknown'));
            $courseKey = trim((string)($item->Course ?? $item->nama_mk ?? 'unknown'));
            $jenisKey = strtolower(trim((string)($item->Jenis ?? 'umum')));
            $taKey = (string)($item->tahun_ajaran_id ?? $item->tahun ?? 'ta_default');
            return $npmKey . '_' . $courseKey . '_' . $jenisKey . '_' . $taKey;
        });

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $grouped->slice(($currentPage - 1) * $perPage, $perPage);

        $paginatedGrouped = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $grouped->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return $paginatedGrouped;
    }

    private function mutuPenilaianQuery(?Request $request = null, string $type = 'soal')
    {
        $query = Mutu::query()
            ->with(['mahasiswa', 'tahunAjaran', 'cpl', 'cpmk'])
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->leftJoin('tahun_ajaran', 'mutus.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->leftJoin('cpls', 'mutus.Cpl', '=', 'cpls.id')
            ->leftJoin('cpmks', 'mutus.Cpmk', '=', 'cpmks.id')
            ->select(
                'mutus.*',
                'prodi.nama as nama_prodi',
                'fakultas.nama as nama_fakultas',
                'mks.nama as nama_mk',
                'tahun_ajaran.tahun as ta_tahun',
                'tahun_ajaran.jenis_semester as ta_semester',
                'cpls.kode as cpl_kode',
                'cpls.judul as cpl_judul',
                'cpmks.kode as cpmk_kode',
                'cpmks.judul as cpmk_judul'
            );

        if ($type === 'konversi') {
            $query->where('mutus.sumber', 'konversi');
        } else {
            $query->where(function ($q) {
                $q->whereNull('mutus.sumber')
                  ->orWhere('mutus.sumber', '!=', 'konversi');
            });
        }

        if ($request) {
            if ($request->filled('course')) {
                $keyword = $request->course;
                $query->where(function ($q) use ($keyword) {
                    $q->where('mutus.nama_mhs', 'like', '%' . $keyword . '%')
                        ->orWhere('mutus.Nama_mhs', 'like', '%' . $keyword . '%')
                        ->orWhereHas('mahasiswa', function ($sub) use ($keyword) {
                            $sub->where('Nama', 'like', '%' . $keyword . '%');
                        })
                        ->orWhereRaw(
                            'EXISTS (SELECT 1 FROM mahasiswa WHERE mahasiswa.NPM = COALESCE(NULLIF(mutus.npm, 0), mutus.NPM) AND mahasiswa.Nama LIKE ?)',
                            ['%' . $keyword . '%']
                        );
                });
            }

            if ($request->filled('mk_kode')) {
                $query->where('mutus.Course', $request->mk_kode);
            }

            if ($request->filled('fakultas_id')) {
                $query->where('fakultas.id', $request->fakultas_id);
            }

            if ($request->filled('prodi_id')) {
                $query->where('prodi.id', $request->prodi_id);
            }

            if ($request->filled('kurikulum_id')) {
                $query->where('mks.id_kurikulum', $request->kurikulum_id);
            }

            if ($request->filled('tahun_ajaran_id')) {
                $query->where('mutus.tahun_ajaran_id', $request->tahun_ajaran_id);
            }

            if ($request->filled('metode_id')) {
                $metodeObj = \App\Models\MetodePenilaian::find($request->metode_id);
                if ($metodeObj) {
                    $query->where('mutus.Jenis', $metodeObj->nama);
                }
            }
        }

        $otoritas = auth()->user()->otoritas->otoritas;
        if ($otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif ($otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('prodi.id_fakultas', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('mutus.id_prodi', auth()->user()->id_prodiUser);
        }

        return $query;
    }
}
