<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\RPS;
use App\Models\CPLMK;
use App\Models\CPMK;
use App\Models\Kurikulum;
use App\Models\MK;
use App\Models\Soal;
use App\Models\CPMKSoal;
use App\Models\CPL;
use App\Models\Mutu;
use App\Models\Komponen;
use App\Models\Universitas;
use App\Models\Prodi;
use App\Models\MetodePenilaian;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDF;
use Illuminate\Support\Facades\Log;
use App\Exports\MutuExport;
use App\Imports\MutuImport;
use App\Imports\ImportTanpaSoal;
use App\Models\CplMkCpmkPenilaian;
use App\Models\TanpaSoal;
use App\Models\InstrumenPenilaian;
use App\Models\PenilaianInstrumen;
use App\Traits\UniversityFilterTrait;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Barryvdh\Snappy\Facades\SnappyPdf;

class SoalController extends Controller
{
    use UniversityFilterTrait;
    public function Add()
    {
        $cpls = CPL::orderBy('judul', 'asc')->get();
        $rpss = RPS::where('pengembang', auth()->user()->name)
            ->whereHas('mk')
            ->get();
        $rps_id = $rpss->pluck('id');
        $kurikulum = Kurikulum::all();
        $metodePenilaians = MetodePenilaian::where('id_prodi', auth()->user()->id_prodiUser)->get();
        $instrumenPenilaians = InstrumenPenilaian::where('id_prodi', auth()->user()->id_prodiUser)->get();
        $prodi = Prodi::all();
        $latestData = Soal::latest()->first();
      
        return view('dosen.soal.add', compact(
            'rpss',
            'kurikulum',
            'cpls',
            'metodePenilaians',
            'instrumenPenilaians',
            'latestData',
            'prodi'
        ));
    }

    public function addRaw()
    {
        $kurikulum = Kurikulum::query()
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->select('kurikulums.*')->get();

        $cpls = CPL::orderBy('judul', 'asc')->get();
        $rpss = RPS::where('pengembang', auth()->user()->name)
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->whereHas('mk')
            ->get();
        $rps_id = $rpss->pluck('id');
        $komponen = Komponen::groupBy('jenis')->get();
        $prodi = Prodi::where('id', auth()->user()->id_prodiUser)->get();
        $latestData = Soal::latest()->first();
        return view('dosen.soal.addRaw', compact('rpss', 'kurikulum', 'cpls', 'komponen', 'latestData', 'prodi'));
    }

    public function getCPLBykode_mk(Request $request)
    {
        $kode_mk = $request->kode_mk;

        $mk = MK::where('kode', $kode_mk)
            ->with(['cpl:id,kode,judul'])
            ->first();

        if (!$mk) {
            return response()->json(['cpls' => []]);
        }

        return response()->json(['cpls' => $mk->cpl]);
    }

    public function getCPMKBykode_mk($cplId)
    {
        $kode_mk = request('kode_mk');

        $cpmks = CPMK::where('cpl_id', $cplId)
            ->whereHas('mks', function ($query) use ($kode_mk) {
                $query->where('kode', $kode_mk);
            })
            ->with('mk')
            ->get();

        return response()->json(['cpmks' => $cpmks]);
    }

    public function getJenisKriteria($cplId, Request $request)
    {
        $kode_mk = request('kode_mk');
        $cpmkId  = request('cpmkId');

        $dataKriteria = DB::table('penilaian_metode as pm')
            ->join('metode_penilaian as mp', 'pm.metode_id', '=', 'mp.id')
            ->join('cpl_mk_cpmk_penilaian as cmcp', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
            ->where('cmcp.cpl_id', $cplId)
            ->where('cmcp.cpmk_id', $request->cpmkId)
            ->where('cmcp.mk_kode', $request->kode_mk)
            ->select(
                'mp.id as metode_id',
                'mp.nama as jenis',
                'pm.bobot'
            )
            ->get();

        return response()->json(['dataKriteria' => $dataKriteria]);
    }

    public function getJenisSoalByMk($kode_mk)
    {
        $dataSoal = Soal::join('cpls', 'soals.cpl', '=', 'cpls.id')->join('cpmks', 'soals.cpmk', '=', 'cpmks.id')->where('kode_mk', $kode_mk)->select('soals.*', 'cpls.kode as cplKode', 'cpmks.kode as cpmkKode')->get();
        return response()->json(['dataSoal' => $dataSoal]);
    }

    public function getJenisSoalByKriteria(Request $request, $jenis)
    {
        $kode_mk = $request->kode_mk;

        $dataSoal = Soal::join('cpls', 'soals.cpl', '=', 'cpls.id')->join('cpmks', 'soals.cpmk', '=', 'cpmks.id')->where('jenis', $jenis)->where('kode_mk', $kode_mk)->select('soals.*', 'cpls.kode as cplKode', 'cpmks.kode as cpmkKode')->get();
        return response()->json(['dataSoal' => $dataSoal]);
    }
    public function getPersentaseCpmk(Request $request)
    {
        $kodeMk   = $request->kode_mk;
        $cpmkId   = $request->cpmk_id;
        $metodeId = $request->metode_id; // ← TAMBAH INI

        if (!$kodeMk || !$cpmkId) {
            return response()->json(['total' => 0]);
        }

        $totalSoal = DB::table('soals')
            ->where('kode_mk', $kodeMk)
            ->where('cpmk', $cpmkId)
            ->when($metodeId, fn($q) => $q->where('jenis', $metodeId)) // ← FILTER PER METODE
            ->sum('persentase_cpmk');

        $totalTanpa = DB::table('tanpa_soal')
            ->where('kode_mk', $kodeMk)
            ->where('cpmk_id', $cpmkId)
            ->when($metodeId, fn($q) => $q->where('metode_id', $metodeId)) // ← FILTER PER METODE
            ->sum('persentase_cpmk');

        $total = $totalSoal + $totalTanpa;

        return response()->json(['total' => (float) $total]);
    }

    public function cetakSoal($id)
    {
        $soals = Soal::findOrFail($id);
        return view('dosen.soal.cetakSoal', compact('soals'));
    }

    public function New(Request $request)
    {
        $universitas = Universitas::where('id', auth()->user()->id_universitasUser)->get();
        $prodi = Prodi::where('id', auth()->user()->id_prodiUser)->get();
        $rpss = RPS::where('pengembang', auth()->user()->name)
            ->whereHas('mk')
            ->get();
        $rps_id = $rpss->pluck('id');
        $soals = Soal::all();
        $cpls = CPLMK::all();
        $mks = MK::all();
        $course = MK::all();
        foreach ($rpss as $rps) {
            $id_mk = $rps->kode_mk;
            $mk = MK::firstWhere('kode', $id_mk);
            $mks->push($mk);
        }
        $cpls = CPL::orderBy('aspek', 'desc')->get();

        return view('dosen.mutu.addMutu', compact('soals', 'rpss', 'cpls', 'mks', 'course', 'universitas', 'prodi'));
    }

    public function getMetodeByMapping(Request $request)
    {
        $data = DB::table('penilaian_metode as pm')
            ->join('metode_penilaian as mp', 'pm.metode_id', '=', 'mp.id')
            ->join('cpl_mk_cpmk_penilaian as cmcp', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
            ->where('cmcp.mk_kode', $request->kode_mk)
            ->where('cmcp.cpl_id', $request->cpl_id)
            ->where('cmcp.cpmk_id', $request->cpmk_id)
            ->select('mp.id', 'mp.nama', 'pm.bobot')
            ->get();

        return response()->json($data);
    }

    public function getJenisByMk(Request $request, $mk = null)
    {
        $mk = $request->kode_mk ?? $mk;

        $data = DB::table('cpl_mk_cpmk_penilaian as cmcp')
            ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
            ->join('metode_penilaian as mp', 'pm.metode_id', '=', 'mp.id')
            ->where('cmcp.mk_kode', $mk)
            ->select(
                'mp.id',
                'mp.nama',
                DB::raw('SUM(pm.bobot) as bobot')
            )
            ->groupBy('mp.id', 'mp.nama')
            ->orderBy('mp.nama')
            ->get();

        return response()->json($data);
    }

    public function getSoalByMkJenis(Request $request)
    {
        $kode_mk = $request->kode_mk;
        $jenis   = $request->jenis; // metode_penilaian.id

        $soals = DB::table('soals')
            ->leftJoin('cpmks', 'soals.cpmk', '=', 'cpmks.id')
            ->leftJoin('cpls', 'soals.cpl', '=', 'cpls.id')
            ->leftJoin('metode_penilaian', 'soals.jenis', '=', 'metode_penilaian.id')
            ->where('soals.kode_mk', $kode_mk)
            ->where('soals.jenis', $jenis)
            ->select(
                'soals.id',
                'soals.pertanyaan',
                'soals.bobotSoal',
                'cpmks.kode as kode_cpmk',
                'cpls.kode as kode_cpl',
                'metode_penilaian.nama as nama_metode'
            )
            ->orderBy('soals.id', 'asc')
            ->get();

        // Hitung total bobot
        $totalBobot = $soals->sum('bobotSoal');

        return response()->json([
            'soals' => $soals,
            'totalBobot' => $totalBobot
        ]);
    }
    public function addRawTS(Request $request)
    {
        $universitas = Universitas::where('id', auth()->user()->id_universitasUser)->get();
        $prodi = Prodi::where('id', auth()->user()->id_prodiUser)->get();
        $rpss = RPS::where('pengembang', auth()->user()->name)
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->whereHas('mk')
            ->get();
        $komponen = Komponen::groupBy('jenis')->get();
        $kurikulum = Kurikulum::query()
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->select('id', 'tahun')
            ->distinct()
            ->get();
        $cpls = CPL::orderBy('judul', 'asc')->get();
        $latestData = Soal::latest()->first();

        return view('dosen.TanpaSoal.addRawTS', compact(
            'universitas',
            'prodi',
            'rpss',
            'komponen',
            'kurikulum',
            'cpls',
            'latestData'
        ));
    }

    public function storeRawTS(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|exists:mks,kode',
            'jenis'   => 'required|exists:metode_penilaian,id',
            'cpl'     => 'required|array|min:1',
            'cpl.*'   => 'exists:cpls,id',
            'cpmk'    => 'required|array|min:1',
            'cpmk.*'  => 'exists:cpmks,id',
            // persentase_cpmk SENGAJA tidak divalidasi dari sini
            // karena diatur nanti saat download template di addMutu
        ]);

        DB::beginTransaction();

        try {
            $dosenId = auth()->id();
            $kodeMk  = $validated['kode_mk'];

            foreach ($validated['cpmk'] as $index => $cpmkId) {

                // Pastikan mapping bobot ada
                $bobotCpmk = DB::table('penilaian_metode as pm')
                    ->join('cpl_mk_cpmk_penilaian as cmcp', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
                    ->where('cmcp.mk_kode', $kodeMk)
                    ->where('cmcp.cpmk_id', $cpmkId)
                    ->where('pm.metode_id', $validated['jenis'])
                    ->value('pm.bobot') ?? 0;

                TanpaSoal::create([
                    'kode_mk'         => $kodeMk,
                    'metode_id'       => $validated['jenis'],
                    'nama_instrumen'  => MetodePenilaian::find($validated['jenis'])->nama,
                    'cpl_id'          => $validated['cpl'][$index],
                    'cpmk_id'         => $cpmkId,
                    'persentase_cpmk' => 0,       // ← sementara 0, diatur saat addMutu/download
                    'bobot_TS'        => 0,       // ← sementara 0, dihitung ulang di ExcelGabungan
                    'dosen_id'        => $dosenId,
                    'kurikulum_id'    => MK::where('kode', $kodeMk)->value('id_kurikulum'),
                    'status'          => 'Draft',
                ]);
            }

            DB::commit();

            return redirect()->route('dosen.tanpa-soal-list')
                ->with('success', 'Instrumen tanpa soal berhasil disimpan. Bobot akan dihitung otomatis saat download template penilaian.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function TanpaSoal(Request $request)
    {
        $universitas = Universitas::where('id', auth()->user()->id_universitasUser)->get();
        $prodi = Prodi::query()->where('id', auth()->user()->id_prodiUser)->get();
        $komponen = InstrumenPenilaian::all();
        $rpss = RPS::query()
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->where('pengembang', auth()->user()->name)->get();
        $rps_id = $rpss->pluck('id');
        $soals = Soal::all();
        $cpls = CPLMK::all();
        $mks = MK::all();
        $course = MK::all();
        foreach ($rpss as $rps) {
            $id_mk = $rps->kode_mk;
            $mk = MK::firstWhere('kode', $id_mk);
            $mks->push($mk);
        }
        $cpls = CPL::orderBy('aspek', 'desc')->get();
        $kode_mk = $request->input('kode_mk');
        $prodi_id = auth()->user()->id_prodiUser;
        $latestData = Soal::latest()->first();
        return view('dosen.mutu.addMutuTanpaSoal', compact('soals', 'rpss', 'cpls', 'mks', 'course', 'komponen', 'universitas', 'prodi', 'latestData'));
    }
    public function listTanpaSoal()
    {
        $tanpaSoalList = TanpaSoal::with(['mk', 'cpl', 'cpmk'])
            ->where('dosen_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        foreach ($tanpaSoalList->items() as $item) {

            $bobotCpmk = DB::table('penilaian_metode as pm')
                ->join('cpl_mk_cpmk_penilaian as cmcp', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
                ->where('cmcp.mk_kode', $item->kode_mk)
                ->where('cmcp.cpmk_id', $item->cpmk_id)
                ->where('pm.metode_id', $item->metode_id)
                ->value('pm.bobot') ?? 0;

            $item->bobot_final = ($item->persentase_cpmk / 100) * $bobotCpmk;

            $totalSoal = DB::table('soals')
                ->where('kode_mk', $item->kode_mk)
                ->where('cpmk', $item->cpmk_id)
                ->sum('persentase_cpmk');

            $totalTanpa = DB::table('tanpa_soal')
                ->where('kode_mk', $item->kode_mk)
                ->where('cpmk_id', $item->cpmk_id)
                ->sum('persentase_cpmk');

            $total = $totalSoal + $totalTanpa;

            $item->status_cpmk = $total == 100 ? 'Lengkap' : 'Belum';
            $item->total_cpmk  = $total;
        }

        return view('dosen.TanpaSoal.list-tanpa-soal', compact('tanpaSoalList'));
    }

    public function editTanpaSoal($id)
    {
        $instrumen = TanpaSoal::findOrFail($id);

        $rpss = RPS::where('pengembang', auth()->user()->name)
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->whereHas('mk')
            ->get();

        $cpls   = CPL::all();
        $metodes = MetodePenilaian::where('id_prodi', auth()->user()->id_prodiUser)->get();

        // CPMK sesuai MK yang sudah dipilih di instrumen
        $cpmks = CPMK::whereHas('mks', function ($q) use ($instrumen) {
            $q->where('kode', $instrumen->kode_mk);
        })->get();

        return view('dosen.TanpaSoal.editTS', compact('instrumen', 'rpss', 'cpls', 'metodes', 'cpmks'));
    }

    public function updateTanpaSoal(Request $request, $id)
    {
        $validated = $request->validate([
            'kode_mk'   => 'required|exists:mks,kode',
            'metode_id' => 'required|exists:metode_penilaian,id',
            'cpl_id'    => 'required|exists:cpls,id',
            'cpmk_id'   => 'required|exists:cpmks,id',
        ]);

        $instrumen = TanpaSoal::findOrFail($id);

        if ($instrumen->status === 'Valid') {
            return back()->with('failed', 'Data yang sudah disetujui tidak dapat diubah.');
        }

        $instrumen->update([
            'kode_mk'         => $validated['kode_mk'],
            'metode_id'       => $validated['metode_id'],
            'nama_instrumen'  => MetodePenilaian::find($validated['metode_id'])->nama,
            'cpl_id'          => $validated['cpl_id'],
            'cpmk_id'         => $validated['cpmk_id'],
            'persentase_cpmk' => 0,   // dihitung ulang saat download template
            'bobot_TS'        => 0,   // dihitung ulang saat download template
            'status'          => 'Draft',
        ]);

        return redirect()->route('dosen.tanpa-soal-list')
            ->with('success', 'Instrumen berhasil diperbarui. Bobot akan dihitung saat download template.');
    }

    public function deleteTanpaSoal($id)
    {
        $instrumen = TanpaSoal::findOrFail($id);

        if ($instrumen->status === 'Valid') {
            return back()->with('failed', 'Data yang sudah disetujui tidak dapat dihapus.');
        }

        $instrumen->delete();

        return redirect()->route('dosen.tanpa-soal-list')
            ->with('success', 'Instrumen berhasil dihapus.');
    }
    public function ajukanSoal($id)
    {
        $soal = Soal::findOrFail($id);

        if (!in_array($soal->status, ['Belum', 'Tolak'])) {
            return redirect()->back()->with('error', 'Soal ini sudah diajukan atau tidak dapat diajukan kembali.');
        }

        $soal->update(['status' => 'Menunggu']);

        return redirect()->route('dosen.soal-list')
            ->with('success', 'Soal berhasil diajukan. Menunggu validasi dari Penjamin Mutu.');
    }

    public function ajukanTanpaSoal($id)
    {
        $instrumen = TanpaSoal::findOrFail($id);

        if (!in_array($instrumen->status, ['Draft', 'Ditolak'])) {
            return redirect()->back()->with('error', 'Instrumen ini sudah diajukan atau tidak dapat diajukan kembali.');
        }

        $instrumen->update(['status' => 'Menunggu Validasi']);

        return redirect()->route('dosen.tanpa-soal-list')
            ->with('success', 'Instrumen berhasil diajukan. Menunggu validasi dari Penjamin Mutu.');
    }
    public function getInstrumenTanpaSoal(Request $request)
    {
        try {
            $kodeMk = $request->kode_mk;
            $metodeId = $request->jenis;

            if (!$kodeMk || !$metodeId) {
                return response()->json(['data' => []]);
            }

            $instrumen = TanpaSoal::with(['cpl', 'cpmk'])
                ->where('kode_mk', $kodeMk)
                ->where('metode_id', $metodeId)
                ->whereIn('status', ['Draft', 'Menunggu Validasi', 'Valid'])
                ->get();

            return response()->json([
                'data' => $instrumen->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama_instrumen' => $item->nama_instrumen,
                        'cpl_kode' => optional($item->cpl)->kode,
                        'cpmk_kode' => optional($item->cpmk)->kode,
                        'bobot' => (float) ($item->bobot_TS ?? 0),
                    ];
                })
            ]);
        } catch (\Throwable $e) {
            Log::error('Error getInstrumenTanpaSoal: ' . $e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan pada server.'
            ], 500);
        }
    }
    public function getMaxKriteriaBobotMKJenis($mk_kode)
    {
        $jenis = request('jenis');

        $MkPenilaianId = CplMkCpmkPenilaian::where('mk_kode', $mk_kode)->pluck('id');

        $bobotMK = PenilaianInstrumen::join('instrumen_penilaian', 'penilaian_instrumen.kriteria_id', '=', 'instrumen_penilaian.id')->whereIn('penilaian_instrumen.cpl_mk_cpmk_penilaian_id', $MkPenilaianId)->where('instrumen_penilaian.nama_kriteria', $jenis)->sum('bobot_metode');

        $maxBobotKriteriaInput = 100 - $bobotMK;

        if ($bobotMK >= 100) {
            return response()->json(['maxKriteriaBobotMKJenis' => 0]);
        } else {
            return response()->json(['maxKriteriaBobotMKJenis' => $maxBobotKriteriaInput]);
        }
    }
    public function getBobotByCpmk(Request $request)
    {
        $kodeMk = $request->kode_mk;
        $jenis = $request->jenis;
        $cpmkId = $request->cpmk_id;

        $bobot = DB::table('cpl_mk_cpmk_penilaian as cmcp')
            ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
            ->join('metode_penilaian as mp', 'mp.id', '=', 'pm.metode_id')
            ->where('cmcp.mk_kode', $kodeMk)
            ->where('cmcp.cpmk_id', $cpmkId)
            ->where('mp.nama', $jenis)
            ->value('pm.bobot');

        return response()->json([
            'bobot' => $bobot ?? 0
        ]);
    }
    public function Excel(Request $request)
    {
        $jenis    = $request->input('jenis');
        $kode_mk  = $request->input('kode_mk');
        $prodi    = $request->input('prodi');
        $univ     = $request->input('univ');
        $semester = $request->input('semester');

        $mk = DB::table('mks')->where('kode', $kode_mk)->first();
        $namaMK = $mk->nama ?? $kode_mk;

        $namaMetode = DB::table('metode_penilaian')
            ->where('id', $jenis)
            ->value('nama');

        // Ambil soal
        $soals = DB::table('soals')
            ->where('kode_mk', $kode_mk)
            ->where('jenis', $jenis)
            ->orderBy('id')
            ->get();

        if ($soals->isEmpty()) {
            return back()->with('error', 'Belum ada soal untuk MK ini.');
        }

        $bobotCpmkMap = DB::table('cpl_mk_cpmk_penilaian as cmcp')
            ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
            ->where('cmcp.mk_kode', $kode_mk)
            ->where('pm.metode_id', $jenis)
            ->pluck('pm.bobot', 'cmcp.cpmk_id');

        $jumlahSoalPerCpmk = $soals->groupBy('cpmk')->map->count();

        $soalsWithBobot = $soals->map(function ($soal) use ($bobotCpmkMap, $jumlahSoalPerCpmk) {
            $bobotCpmk = $bobotCpmkMap[$soal->cpmk] ?? 0;
            $jumlahSoal = $jumlahSoalPerCpmk[$soal->cpmk] ?? 1;
            $soal->bobotPerSoal = round($bobotCpmk / $jumlahSoal, 2);
            return $soal;
        });

        $bobotUjian = $bobotCpmkMap->sum();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'A1' => 'Universitas',
            'B1' => 'Tahun Akademik',
            'C1' => 'Angkatan',
            'D1' => 'NPM',
            'E1' => 'Nama',
            'F1' => 'Prodi',
            'G1' => 'Kode MK',
            'H1' => 'Nama MK',
            'I1' => 'Jenis',
            'J1' => 'Bobot Jenis (%)',
            'K1' => 'Nilai Total',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        $sheet->setCellValue('A2', $univ);
        $sheet->setCellValue('B2', $semester);
        $sheet->setCellValue('F2', $prodi);
        $sheet->setCellValue('G2', $kode_mk);
        $sheet->setCellValue('H2', $namaMK);
        $sheet->setCellValue('I2', $namaMetode);
        $sheet->setCellValue('J2', $bobotUjian);

        $columnIndex = 12;
        $no = 1;
        $totalBobotMetode = $bobotCpmkMap->sum();

        foreach ($soalsWithBobot as $soal) {

            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);

            $header = "Q{$no}/{$soal->id}/{$soal->bobotSoal}/{$soal->cpl}/{$soal->cpmk}";
            $sheet->setCellValue($columnLetter . '1', $header);

            $url = url('/dosen/soal/cetakSoal/' . $soal->id); // sesuaikan route kamu
            $sheet->getCell($columnLetter . '1')->getHyperlink()->setUrl($url);

            $sheet->getStyle($columnLetter . '1')->applyFromArray([
                'font' => [
                    'color' => ['rgb' => '0000FF'],
                    'underline' => true,
                ],
            ]);

            $validation = new DataValidation();
            $validation->setType(DataValidation::TYPE_WHOLE);
            $validation->setOperator(DataValidation::OPERATOR_BETWEEN);
            $validation->setAllowBlank(false);

            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);

            $validation->setFormula1(0);
            $validation->setFormula2(100);

            $validation->setErrorTitle('ERROR!');
            $validation->setError('Nilai wajib angka 0–100. Tidak boleh huruf atau lebih!');

            $validation->setPromptTitle('Input Nilai');
            $validation->setPrompt('Masukkan nilai 0 sampai 100');

            $sheet->setDataValidation($columnLetter . ':' . $columnLetter, $validation);

            $columnIndex++;
            $no++;
        }
        $formulaParts = [];
        $colIdx = 12;
        foreach ($soalsWithBobot as $soal) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $bobot = $soal->bobotSoal;
            $formulaParts[] = "({$colLetter}2*{$bobot})";
            $colIdx++;
        }

        $formula = '=(' . implode('+', $formulaParts) . ")/{$totalBobotMetode}";
        $sheet->setCellValue('K2', $formula);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(storage_path("app/public/template_penilaian.xlsx"));

        return response()->download(storage_path("app/public/template_penilaian.xlsx"));
    }
    public function ExcelTanpaSoal(Request $request)
    {
        $request->validate([
            'univ'     => 'required',
            'semester' => 'required',
            'prodi'    => 'required',
            'kode_mk'  => 'required',
            'jenis'    => 'required',
        ]);

        $kode_mk = $request->kode_mk;
        $jenis   = $request->jenis;

        $mk     = MK::where('kode', $kode_mk)->first();
        $namaMK = $mk->nama ?? $kode_mk;

        $totalBobotMetode = DB::table('cpl_mk_cpmk_penilaian as cmcp')
            ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
            ->where('cmcp.mk_kode', $kode_mk)
            ->where('pm.metode_id', $jenis)
            ->sum('pm.bobot');

        $instrumen = TanpaSoal::with(['cpl', 'cpmk'])
            ->where('kode_mk',   $kode_mk)
            ->where('metode_id', $jenis)
            ->where('status',    'Valid')                  
            ->get();

        if ($instrumen->isEmpty()) {
            return back()->with('error', 'Belum ada instrumen tanpa soal yang sudah divalidasi (Valid).');
        }

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $headers = [
            'Universitas',
            'Tahun Akademik',
            'Angkatan',
            'NPM',
            'Nama',
            'Prodi',
            'Kode MK',
            'Nama MK',
            'Jenis',
            'Bobot Jenis (%)',
            'Nilai Total'
        ];
        $sheet->fromArray($headers, null, 'A1');

        $sheet->setCellValue('A2', $request->univ);
        $sheet->setCellValue('B2', $request->semester);
        $sheet->setCellValue('F2', $request->prodi);
        $sheet->setCellValue('G2', $kode_mk);
        $sheet->setCellValue('H2', $namaMK);
        $sheet->setCellValue('I2', MetodePenilaian::find($jenis)->nama);
        $sheet->setCellValue('J2', $totalBobotMetode);

        $columnIndex  = 12;
        $formulaParts = [];

        foreach ($instrumen as $item) {
            $col   = Coordinate::stringFromColumnIndex($columnIndex);
            $bobot = (float) $item->bobot_TS;

            $headerText = "TS-{$item->id}|{$item->cpl_id}|{$item->cpmk_id}|{$bobot}|{$item->nama_instrumen}";
            $sheet->setCellValue("{$col}1", $headerText);

            $validation = new DataValidation();
            $validation->setType(DataValidation::TYPE_WHOLE);
            $validation->setOperator(DataValidation::OPERATOR_BETWEEN);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setFormula1(0);
            $validation->setFormula2(100);
            $validation->setErrorTitle('ERROR!');
            $validation->setError('Nilai wajib angka 0–100');
            $validation->setPromptTitle('Input Nilai');
            $validation->setPrompt('Masukkan nilai 0 sampai 100');
            $sheet->setDataValidation("{$col}:{$col}", $validation);

            $formulaParts[] = "({$col}2*{$bobot})";
            $columnIndex++;
        }

        if (!empty($formulaParts) && $totalBobotMetode > 0) {
            $formula = '=(' . implode('+', $formulaParts) . ")/{$totalBobotMetode}";
            $sheet->setCellValue('K2', $formula);
        }

        $fileName = 'Template_Tanpa_Soal_' . $kode_mk . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName);
    }

    public function mutuexport()
    {
        return Excel::download(new MutuExport, 'mutu.xlsx');
    }

    public function import()
    {
        $query = Mutu::query()
            ->with('mahasiswa')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->select('mutus.*', 'prodi.nama as nama_prodi', 'mks.nama as nama_mk');

        if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $query->where('prodi.id_fakultas', auth()->user()->id_fakultasUser);
        } else {
            $query->where('mutus.id_prodi', auth()->user()->id_prodiUser);
        }

        $mutus = $query->paginate(10);

        return view('dosen.mutu.importMutu', compact('mutus'));
    }

    public function import1()
    {
        $query = Mutu::query()
            ->with('mahasiswa')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')

            ->leftJoin('cpls', 'mutus.Cpl', '=', 'cpls.id')
            ->leftJoin('cpmks', 'mutus.Cpmk', '=', 'cpmks.id')

            ->select(
                'mutus.*',
                'prodi.nama as nama_prodi',
                'mks.nama as nama_mk',
                'cpls.kode as cpl_kode',       
                'cpls.judul as cpl_judul',      
                'cpmks.kode as cpmk_kode',      
                'cpmks.judul as cpmk_judul'     
            );
        if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $query->where('prodi.id_fakultas', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $query->where('mutus.id_prodi', auth()->user()->id_prodiUser);
        }

        $mutus = $query->paginate(10);

        return view('dosen.mutu.importTanpaSoal', compact('mutus'));
    }


    public function filter(Request $request)
    {
        $mutus = Mutu::query()
            ->with('mahasiswa')
            ->when($request->course, function ($query) use ($request) {
                $keyword = $request->course;
                return $query->where(function ($q) use ($keyword) {
                    $q->where('nama_mhs', 'like', '%' . $keyword . '%')
                        ->orWhere('Nama_mhs', 'like', '%' . $keyword . '%')
                        ->orWhereHas('mahasiswa', function ($sub) use ($keyword) {
                            $sub->where('Nama', 'like', '%' . $keyword . '%');
                        })
                        ->orWhereRaw(
                            'EXISTS (SELECT 1 FROM mahasiswa WHERE mahasiswa.NPM = COALESCE(NULLIF(mutus.npm, 0), mutus.NPM) AND mahasiswa.Nama LIKE ?)',
                            ['%' . $keyword . '%']
                        );
                });
            })
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->select('mutus.*', 'prodi.nama as nama_prodi', 'mks.nama as nama_mk');

        return view('dosen.mutu.importMutu', ['mutus' => $mutus->paginate(10)]);
    }

    public function filterSoal(Request $request)
    {
        $soals = Soal::query();
        $soals->when($request->kode_mk, function ($query) use ($request) {
            return $query->where('jenis', 'like', '%' . $request->kode_mk . '%');
        });
        return view('dosen.soal.list', ['soals' => $soals->paginate(25)]);
    }

    public function mutuimport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048',
        ]);

        try {
            DB::beginTransaction();

            Excel::import(new MutuImport, $request->file('file'));

            DB::commit();

            return redirect()->back()->with('success', 'Data nilai berhasil diimport dan disimpan ke database.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal import mutu: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function importTanpaSoal(Request $request)
    {
        try {
            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', "File harus format 'xslx'");
            }

            Excel::import(new ImportTanpaSoal, $request->file('file'));

            return redirect()->back()->with('success', "File imported successfully");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Ada kolom yang kosong. Silahkan cek kembali");
        }
    }

    public function list(Request $request)
    {
        $user     = auth()->user();
        $otoritas = $user->otoritas->otoritas;

        $query = DB::table('soals')
            ->leftJoin('mks', 'soals.kode_mk', '=', 'mks.kode')
            ->leftJoin('cpls', 'soals.cpl', '=', 'cpls.id')
            ->leftJoin('cpmks', 'soals.cpmk', '=', 'cpmks.id')
            ->leftJoin('metode_penilaian', 'soals.jenis', '=', 'metode_penilaian.id')
            ->leftJoin('prodi', 'soals.prodiId', '=', 'prodi.id')
            ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select(
                'soals.id',
                'soals.kode_mk',
                'soals.bobotSoal',
                'soals.status',
                'soals.komentar',
                'mks.nama as nama_mk',
                'metode_penilaian.nama as nama_kriteria',
                'cpls.kode as cpl_kode',
                'cpmks.kode as cpmk_kode'
            )
            ->where('soals.dosen', $user->name);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mks.nama', 'like', "%{$search}%")
                    ->orWhere('mks.kode', 'like', "%{$search}%")
                    ->orWhere('metode_penilaian.nama', 'like', "%{$search}%");
            });
        }

        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100])
            ? (int) $request->input('per_page')
            : 10;

        $soals = $query->orderBy('soals.created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        $filterData = $this->getFilterData($request);

        return view('dosen.soal.list', array_merge(
            ['soals' => $soals],
            $filterData
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mk'    => 'required|exists:mks,kode',
            'cpl'        => 'required|exists:cpls,id',
            'cpmk'       => 'required|exists:cpmks,id',
            'metode_id'  => 'required|exists:metode_penilaian,id',
            'minggu'     => 'required|integer|min:1|max:16',
            'pertanyaan' => 'required|string',
            'kurikulum'  => 'required|exists:kurikulums,id',
            'prodi'      => 'required|exists:prodi,id',
            // persentase_cpmk SENGAJA tidak divalidasi dari sini
            // karena diatur nanti saat download template di addMutu
        ]);

        DB::beginTransaction();

        try {
            // Pastikan mapping bobot ada (untuk validasi relasi)
            $bobotCpmk = DB::table('penilaian_metode as pm')
                ->join('cpl_mk_cpmk_penilaian as cmcp', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
                ->where('cmcp.mk_kode', $request->kode_mk)
                ->where('cmcp.cpmk_id', $request->cpmk)
                ->where('pm.metode_id', $request->metode_id)
                ->value('pm.bobot');

            if (!$bobotCpmk) {
                return back()->withInput()
                    ->with('error', 'Bobot metode tidak ditemukan. Pastikan mapping CPL-MK-CPMK-Penilaian sudah diatur.');
            }

            Soal::create([
                'kode_mk'         => $request->kode_mk,
                'minggu'          => $request->minggu,
                'jenis'           => $request->metode_id,
                'dosen'           => auth()->user()->name,
                'kurikulumId'     => $request->kurikulum,
                'pertanyaan'      => $request->pertanyaan,
                'status'          => 'Belum',
                'prodiId'         => $request->prodi,
                'bobotSoal'       => 0,       // ← sementara 0, dihitung ulang di ExcelGabungan
                'persentase_cpmk' => 0,       // ← sementara 0, diatur saat addMutu/download template
                'cpl'             => $request->cpl,
                'cpmk'            => $request->cpmk,
            ]);

            DB::commit();

            return redirect()->route('dosen.soal-list')
                ->with('success', 'Soal berhasil disimpan. Bobot akan dihitung otomatis saat download template penilaian.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function Edit($id)
    {
        $soal = Soal::findOrFail($id);

        $rpss = RPS::with('mk')
            ->where('pengembang', auth()->user()->name)
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->get();

        $metodes = MetodePenilaian::where('id_prodi', auth()->user()->id_prodiUser)->get();

        $kurikulum = Kurikulum::where('id_prodi', auth()->user()->id_prodiUser)->get();
        if ($kurikulum->isEmpty()) {
            $kurikulum = Kurikulum::all();
        }

        return view('dosen.soal.edit', compact('soal', 'rpss', 'metodes', 'kurikulum'));
    }
    public function Update(Request $request, $id)
    {
        $request->validate([
            'kode_mk'    => 'required|exists:mks,kode',
            'minggu'     => 'required|integer|min:1|max:16',
            'jenis'      => 'required|exists:metode_penilaian,id',
            'pertanyaan' => 'required|string',
            'kurikulum'  => 'required|exists:kurikulums,id',
        ]);

        DB::beginTransaction();
        try {
            $soal = Soal::findOrFail($id);

            $soal->update([
                'kode_mk'         => $request->kode_mk,
                'minggu'          => $request->minggu,
                'jenis'           => $request->jenis,
                'pertanyaan'      => $request->pertanyaan,
                'kurikulumId'     => $request->kurikulum,
                'status'          => 'Belum',
                'persentase_cpmk' => 0,   // dihitung ulang saat download template
                'bobotSoal'       => 0,   // dihitung ulang saat download template
            ]);

            DB::commit();
            return redirect()->route('dosen.soal-list')
                ->with('success', 'Soal berhasil diperbarui. Bobot akan dihitung saat download template.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function Delete($id)
    {
        $soal = Soal::findOrFail($id);

        if (method_exists($soal, 'cpmk')) {
            $soal->cpmk()->detach();
        }

        $soal->delete();

        return redirect()->route('dosen.soal-list')
            ->with('success', 'Data soal berhasil dihapus.');
    }

    public function print($id)
    {
        $ids = Crypt::decrypt($id);
        $soal = soal::findOrFail($ids);
        $mks = MK::all();
        $cpmks = collect();
        $soals = collect();
        $cpmk_soals = collect();
        foreach ($mks as $mk) {
            if ($soal->kode_mk == $mk->kode) {
                $soalss = Soal::where('kode_mk', $mk->kode)->where('jenis', $soal->jenis)->orderBy('id', 'asc')->get();
            }
        }

        foreach ($soalss as $s) $soals->push($s);
        foreach ($soals as $sl) {
            $temp = DB::table('cpmk_soals')->select(DB::raw('id_cpmk, id_soal'))->groupBy('id_cpmk')->orderBy('id_cpmk', 'asc')->get();
            $cpmk_s = $temp->where('id_soal', $sl->id);
            foreach ($cpmk_s as $cpmk) $cpmk_soals->push($cpmk);
        }
        foreach ($cpmk_soals as $c_s) {
            $cpmkss = CPMK::where('id', $c_s->id_cpmk)->get();
            foreach ($cpmkss as $cp) $cpmks->push($cp);
        }
        $mk = MK::where('kode', $soal->kode_mk)->first();
        $data = compact(
            'mk',
            'soal',
            'mks',
            'cpmks',
            'cpmk_soals'
        );
        $pdf = SnappyPdf::loadView('dosen.soal.print', $data);
        $pdf->setOption('enable-local-file-access', true);
        return $pdf->stream('soal.pdf');
    }

    public function getGabunganByMkJenis(Request $request)
    {
        $kodeMk   = $request->kode_mk;
        $metodeId = $request->jenis;

        if (!$kodeMk || !$metodeId) {
            return response()->json(['soals' => [], 'instrumens' => [], 'cpmks' => []]);
        }

        try {
            $soals = DB::table('soals')
                ->leftJoin('cpmks', 'soals.cpmk', '=', 'cpmks.id')
                ->leftJoin('cpls',  'soals.cpl',  '=', 'cpls.id')
                ->where('soals.kode_mk', $kodeMk)
                ->where('soals.jenis',   $metodeId)
                ->whereIn('soals.status', ['Valid', 'Menunggu', 'Tolak'])
                ->select(
                    'soals.id',
                    'soals.pertanyaan',
                    'soals.bobotSoal',
                    'soals.persentase_cpmk',
                    'soals.status',
                    'cpmks.id   as cpmk_id',
                    'cpmks.kode as kode_cpmk',
                    'cpls.kode  as kode_cpl'
                )
                ->orderBy('soals.id')
                ->get()
                ->map(function ($soal) {
                    $soal->can_select    = ($soal->status === 'Valid');
                    $soal->status_label  = match ($soal->status) {
                        'Valid'    => 'Tervalidasi',
                        'Menunggu' => 'Menunggu Validasi',
                        'Tolak'    => 'Ditolak',
                        default    => $soal->status,
                    };
                    return $soal;
                });

            $instrumens = TanpaSoal::with(['cpl', 'cpmk'])
                ->where('kode_mk',   $kodeMk)
                ->where('metode_id', $metodeId)
                ->whereIn('status', ['Valid', 'Menunggu Validasi', 'Ditolak', 'Draft'])
                ->get();

            $cpmks = DB::table('cpl_mk_cpmk_penilaian as cmcp')
                ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
                ->join('cpmks', 'cmcp.cpmk_id', '=', 'cpmks.id')
                ->where('cmcp.mk_kode',   $kodeMk)
                ->where('pm.metode_id',   $metodeId)
                ->select('cpmks.id', 'cpmks.kode', 'cpmks.judul')
                ->distinct()
                ->orderBy('cpmks.kode')
                ->get();

            return response()->json([
                'soals'      => $soals,
                'instrumens' => $instrumens->map(function ($item) {
                    $canSelect   = ($item->status === 'Valid');
                    $statusLabel = match ($item->status) {
                        'Valid'              => 'Tervalidasi',
                        'Menunggu Validasi'  => 'Menunggu Validasi',
                        'Ditolak'           => 'Ditolak',
                        'Draft'             => 'Draft',
                        default             => $item->status,
                    };
                    return [
                        'id'              => $item->id,
                        'nama_instrumen'  => $item->nama_instrumen,
                        'cpmk_id'         => $item->cpmk_id,
                        'cpmk_kode'       => optional($item->cpmk)->kode,
                        'cpl_kode'        => optional($item->cpl)->kode,
                        'bobot_TS'        => (float) $item->bobot_TS,
                        'persentase_cpmk' => (float) $item->persentase_cpmk,
                        'status'          => $item->status,
                        'status_label'    => $statusLabel,
                        'can_select'      => $canSelect,
                    ];
                }),
                'cpmks' => $cpmks,
            ]);
        } catch (\Throwable $e) {
            Log::error('getGabunganByMkJenis error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function ExcelGabungan(Request $request)
    {
        $request->validate([
            'univ'     => 'required',
            'semester' => 'required',
            'prodi'    => 'required',
            'kode_mk'  => 'required',
            'jenis'    => 'required',
        ]);

        $kodeMk   = $request->kode_mk;
        $metodeId = $request->jenis;

        $soalIds    = $request->input('soal_ids',   []);
        $tsIds      = $request->input('ts_ids',     []);
        $persenSoal = $request->input('persen_soal', []);
        $persenTs   = $request->input('persen_ts',  []);

        if (empty($soalIds) && empty($tsIds)) {
            return back()->with('error', 'Pilih minimal satu soal atau instrumen.');
        }

        $mk         = MK::where('kode', $kodeMk)->first();
        $namaMK     = $mk->nama ?? $kodeMk;
        $namaMetode = DB::table('metode_penilaian')->where('id', $metodeId)->value('nama') ?? '-';

        $totalBobotMetode = DB::table('cpl_mk_cpmk_penilaian as cmcp')
            ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
            ->where('cmcp.mk_kode', $kodeMk)
            ->where('pm.metode_id', $metodeId)
            ->sum('pm.bobot');

        $soals = collect();
        if (!empty($soalIds)) {
            $soals = DB::table('soals')
                ->leftJoin('cpmks', 'soals.cpmk', '=', 'cpmks.id')
                ->leftJoin('cpls',  'soals.cpl',  '=', 'cpls.id')
                ->whereIn('soals.id', $soalIds)
                ->select(
                    'soals.id',
                    'soals.pertanyaan',
                    'soals.cpmk as cpmk_id_raw',
                    'soals.cpl  as cpl_id_raw',
                    'cpmks.id   as cpmk_id',
                    'cpls.id    as cpl_id'
                )
                ->orderBy('soals.id')
                ->get();
        }

        $instrumens = collect();
        if (!empty($tsIds)) {
            $instrumens = TanpaSoal::with(['cpl', 'cpmk'])
                ->whereIn('id', $tsIds)
                ->orderBy('id')
                ->get();
        }

        $bobotCpmkMap = DB::table('cpl_mk_cpmk_penilaian as cmcp')
            ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
            ->where('cmcp.mk_kode', $kodeMk)
            ->where('pm.metode_id', $metodeId)
            ->pluck('pm.bobot', 'cmcp.cpmk_id');

        $itemPerCpmk = []; 

        foreach ($soals as $soal) {
            $cpmkId = $soal->cpmk_id ?? $soal->cpmk_id_raw;
            $itemPerCpmk[$cpmkId] = ($itemPerCpmk[$cpmkId] ?? 0) + 1;
        }
        foreach ($instrumens as $item) {
            $cpmkId = $item->cpmk_id;
            $itemPerCpmk[$cpmkId] = ($itemPerCpmk[$cpmkId] ?? 0) + 1;
        }

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $fixedHeaders = [
            'A1' => 'Universitas',
            'B1' => 'Tahun Akademik',
            'C1' => 'Angkatan',
            'D1' => 'NPM',
            'E1' => 'Nama',
            'F1' => 'Prodi',
            'G1' => 'Kode MK',
            'H1' => 'Nama MK',
            'I1' => 'Jenis',
            'J1' => 'Bobot Jenis (%)',
            'K1' => 'Nilai Total',
        ];
        foreach ($fixedHeaders as $cell => $text) {
            $sheet->setCellValue($cell, $text);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        $sheet->setCellValue('A2', $request->univ);
        $sheet->setCellValue('B2', $request->semester);
        $sheet->setCellValue('F2', $request->prodi);
        $sheet->setCellValue('G2', $kodeMk);
        $sheet->setCellValue('H2', $namaMK);
        $sheet->setCellValue('I2', $namaMetode);
        $sheet->setCellValue('J2', $totalBobotMetode);

        $columnIndex  = 12;   
        $formulaParts = [];

        foreach ($soals as $soal) {
            $cpmkId    = $soal->cpmk_id ?? $soal->cpmk_id_raw;
            $cplId     = $soal->cpl_id  ?? $soal->cpl_id_raw;
            $bobotCpmk = $bobotCpmkMap[$cpmkId] ?? 0;

            $persenManual = isset($persenSoal[$soal->id]) ? (float) $persenSoal[$soal->id] : 0;
            if ($persenManual > 0) {
                $persen = $persenManual;
            } else {
                $jumlahItem = $itemPerCpmk[$cpmkId] ?? 1;
                $persen     = $jumlahItem > 0 ? round(100 / $jumlahItem, 4) : 100;
            }

            $bobotFinal = round(($persen / 100) * $bobotCpmk, 4);

            $col = Coordinate::stringFromColumnIndex($columnIndex);

            $headerText = 'SOAL|' . $soal->id . '|' . $bobotFinal . '|' . $cplId . '|' . $cpmkId . '|' . $persen;
            $sheet->setCellValue($col . '1', $headerText);
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => ['color' => ['rgb' => '0000FF'], 'underline' => true],
            ]);
            $sheet->getCell($col . '1')->getHyperlink()->setUrl(url('/dosen/soal/cetakSoal/' . $soal->id));

            $this->addCellValidation($sheet, $col);
            $formulaParts[] = '(' . $col . '2*' . $bobotFinal . ')';
            $columnIndex++;
        }

        foreach ($instrumens as $item) {
            $cpmkId    = $item->cpmk_id;
            $bobotCpmk = $bobotCpmkMap[$cpmkId] ?? 0;

            $persenManual = isset($persenTs[$item->id]) ? (float) $persenTs[$item->id] : 0;
            if ($persenManual > 0) {
                $persen = $persenManual;
            } else {
                $jumlahItem = $itemPerCpmk[$cpmkId] ?? 1;
                $persen     = $jumlahItem > 0 ? round(100 / $jumlahItem, 4) : 100;
            }

            $bobotFinal = round(($persen / 100) * $bobotCpmk, 4);

            $col = Coordinate::stringFromColumnIndex($columnIndex);

            $headerText = 'TS|' . $item->id . '|' . $bobotFinal . '|' . $item->cpl_id . '|' . $cpmkId . '|' . $persen;
            $sheet->setCellValue($col . '1', $headerText);
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => ['color' => ['rgb' => '6f42c1']],
            ]);

            $this->addCellValidation($sheet, $col);
            $formulaParts[] = '(' . $col . '2*' . $bobotFinal . ')';
            $columnIndex++;
        }

        if (!empty($formulaParts) && $totalBobotMetode > 0) {
            $formula = '=(' . implode('+', $formulaParts) . ')/' . $totalBobotMetode;
            $sheet->setCellValue('K2', $formula);
        }

        $lastCol = Coordinate::stringFromColumnIndex($columnIndex - 1);
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->applyFromArray([
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAD3'],
            ]
        ]);

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Template_Penilaian_' . $kodeMk . '_' . $namaMetode . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }


    private function addCellValidation($sheet, string $col): void
    {
        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_WHOLE);
        $validation->setOperator(DataValidation::OPERATOR_BETWEEN);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setFormula1(0);
        $validation->setFormula2(100);
        $validation->setErrorTitle('ERROR!');
        $validation->setError('Nilai wajib angka 0–100. Tidak boleh huruf atau di luar batas!');
        $validation->setPromptTitle('Input Nilai');
        $validation->setPrompt('Masukkan nilai 0 sampai 100');
        $sheet->setDataValidation($col . ':' . $col, $validation);
    }
}
