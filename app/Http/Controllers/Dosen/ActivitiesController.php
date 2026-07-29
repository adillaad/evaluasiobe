<?php

namespace App\Http\Controllers\Dosen;

use App\Models\RPS;
use App\Models\CPMK;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Imports\ActivitiesImport;
use App\Http\Controllers\Controller;
use App\Traits\UniversityFilterTrait;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class ActivitiesController extends Controller
{
    use UniversityFilterTrait;

    public function List()
    {
        $filterData = $this->getFilterData(request());
        return view('dosen.Activities.list', [
            'universities' => $filterData['universities'],
            'faculties' => $filterData['faculties'],
            'programs' => $filterData['programs']
        ]);
    }

    public function getActivitiesData(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $cpmks = DB::table('cpmks')->select('id','judul')->get();
        $subCpmks = DB::table('sub_cpmk')->select('id','uraian')->get();
        $query = Activity::query()
            ->join('prodi', 'activities.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->orderby('id', 'asc')
            ->select('activities.*');

        if ($otoritas == 'Wakil Rektor') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if ($otoritas == 'Wakil Dekan') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif ($otoritas == 'Kepala Program Studi') {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        } elseif ($otoritas == 'Dosen') {
            $query->join('rpss', 'activities.id_rps', '=', 'rpss.id')
                ->where('prodi.id', auth()->user()->id_prodiUser)
                ->where('rpss.pengembang', auth()->user()->name)
                ->distinct();
        } 
        $query = $this->getFilteredQuery($query, new Request([
            'universitas_id' => $request->input('universitas_id'),
            'fakultas_id' => $request->input('fakultas_id'),
            'prodi_id' => $request->input('prodi_id')
        ]));  
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('mk_nama', function ($activity) {
                return $activity->rps->mk->nama ?? '-';
            })
            ->addColumn('rps_nomor', function ($activity) {
                return $activity->rps->nomor ?? '-';
            })
            ->addColumn('indikator', function ($activity) {
                return $activity->indikator;
            })
            ->addColumn('sub_cpmk', function ($activity) use ($cpmks, $subCpmks) {
                // normalisasi aman (sub_cpmk & id_cpmk bisa string JSON)
                $subArr = $activity->sub_cpmk;
                if (!is_array($subArr)) {
                    $subArr = json_decode($subArr ?? '[]', true);
                    $subArr = is_array($subArr) ? $subArr : [];
                }
                $idCpmkArr = $activity->id_cpmk;
                if (!is_array($idCpmkArr)) {
                    $idCpmkArr = json_decode($idCpmkArr ?? '[]', true);
                    $idCpmkArr = is_array($idCpmkArr) ? $idCpmkArr : [];
                }
                $subHtml = '<ol type="a" style="padding-left:1.2rem;margin-bottom:0;">';
                foreach ($subArr as $sub_id) {
                    $sub_id_int = (int)$sub_id;
                    if ($sub_id_int === 0) {
                        // fallback ke CPMK
                        foreach ($idCpmkArr as $cpmkId) {
                            $cpmkIdInt = (int)$cpmkId;
                            $judul = optional($cpmks->firstWhere('id', $cpmkIdInt))->judul
                                ?? "CPMK ID $cpmkIdInt tdk ditemukan";
                            $subHtml .= '<li>' . e($judul) . '</li>';
                        }
                    } else {
                        $uraian = optional($subCpmks->firstWhere('id', $sub_id_int))->uraian
                            ?? "Sub-CPMK ID $sub_id_int tdk ditemukan";
                        $subHtml .= '<li>' . e($uraian) . '</li>';
                    }
                }
                if (empty($subArr)) {
                    $subHtml .= '<li><em>--</em></li>';
                }
                $subHtml .= '</ol>';
                return $subHtml;
            })
            ->addColumn('action', function ($activity) {
                $actionButtons = '';
                if (auth()->user()->otoritas->otoritas == 'Dosen') {
                    $actionButtons = '
                    <div class="d-flex">
                        <div>
                            <a href="' . route('dosen.activity-edit', ['id' => $activity->id]) . '"
                                class="btn btn-warning me-2 btn-icon-text p-2">
                                Edit
                                <i class="ti-pencil btn-icon-append"></i>
                            </a>
                        </div>
                        <form action="' . route('dosen.activity-delete', ['id' => $activity->id]) . '" method="post">
                            ' . csrf_field() . '
                            ' . method_field('delete') . '
                            <button type="submit" class="btn btn-danger btn-icon-text p-2"
                                onclick="return confirm(\'Are you sure to delete this ?\')">
                                Delete
                                <i class="ti-trash btn-icon-append"></i>
                            </button>
                        </form>
                    </div>';
                }
                return $actionButtons;
            })
            ->rawColumns(['action', 'indikator', 'sub_cpmk'])
            ->make(true);
    }

    // public function listByRps($id)
    // {
    //     $rps = RPS::with(['mk', 'activities'])->findOrFail($id);

    //     $allWeeks = range(1, 16);
    //     $usedWeeks = $rps->activities->pluck('minggu')->toArray();
    //     $availableWeeks = array_diff($allWeeks, $usedWeeks);
    //     $cpmks = DB::table('cpmk_mk')
    //     ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
    //     ->where('cpmk_mk.mk_kode', $rps->kode_mk) 
    //     ->select('cpmks.id', 'cpmks.kode', 'cpmks.judul')
    //     ->distinct()
    //     ->get();
    //     $cpmk_ids = $cpmks->pluck('id'); 
    //     $sub_cpmks = DB::table('sub_cpmk')->whereIn('cpmk_id', $cpmk_ids)->get();
    //     $instrumen = DB::table('metode_penilaian')
    //         ->where('id_prodi', auth()->user()->id_prodiUser)
    //         ->orderBy('nama')
    //         ->get();
    //     $pustakas = DB::table('pustakas')
    //         ->where('id_prodi', $rps->id_prodi)
    //         ->where(function($query) use ($rps) {
    //             $query->where('kode_mk', $rps->kode_mk) ;     
    //         })
    //         ->get();
    //     return view('dosen.Activities.list_by_rps', compact('rps','cpmks', 'instrumen','availableWeeks','sub_cpmks','pustakas'));
    // }

    public function listByRps($id)
    {
        $rps = RPS::with(['mk', 'activities'])->findOrFail($id);

        $allWeeks = range(1, 16);
        $usedWeeks = $rps->activities->pluck('minggu')->toArray();
        $availableWeeks = array_diff($allWeeks, $usedWeeks);

        $cpmks = DB::table('cpmk_mk')
            ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
            ->where('cpmk_mk.mk_kode', $rps->kode_mk)
            ->select('cpmks.id', 'cpmks.kode', 'cpmks.judul')
            ->distinct()
            ->get();

        $cpmk_ids = $cpmks->pluck('id');

        $sub_cpmks = DB::table('sub_cpmk')->whereIn('cpmk_id', $cpmk_ids)->get();

        $instrumen = DB::table('metode_penilaian')
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->orderBy('nama')
            ->get();

        $pustakas = DB::table('pustakas')
            ->where('id_prodi', $rps->id_prodi)
            ->where(function($query) use ($rps) {
                $query->where('kode_mk', $rps->kode_mk);
            })
            ->get();

        $validationRps = $this->getRpsKelengkapanData($rps);

        return view('dosen.Activities.list_by_rps', compact(
            'rps',
            'cpmks',
            'instrumen',
            'availableWeeks',
            'sub_cpmks',
            'pustakas',
            'validationRps'
        ));
    }

    private function getRpsKelengkapanData(RPS $rps): array
    {
        $rps->loadMissing(['activities']);

        $allCpmks = \App\Models\CplMkCpmkPenilaian::with('cpmk')
            ->where('mk_kode', $rps->kode_mk)
            ->get()
            ->pluck('cpmk')
            ->filter()
            ->unique('id')
            ->values();

        $allAsesmen = \App\Models\CplMkCpmkPenilaian::with('penilaianMetode.metode')
            ->where('mk_kode', $rps->kode_mk)
            ->get()
            ->flatMap(function ($item) {
                return $item->penilaianMetode->pluck('metode');
            })
            ->filter()
            ->pluck('nama')
            ->map(fn ($nama) => trim($nama))
            ->filter()
            ->unique()
            ->values();

        $usedCpmkIds = $rps->activities
            ->flatMap(function ($activity) {
                $ids = $activity->id_cpmk;

                if (!is_array($ids)) {
                    $ids = json_decode($ids ?? '[]', true);
                    $ids = is_array($ids) ? $ids : [];
                }

                return $ids;
            })
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $usedAsesmen = $rps->activities
            ->flatMap(function ($activity) {
                $asesmen = $activity->getRawOriginal('bentuk_asesmen');

                // kalau JSON array
                $decoded = json_decode($asesmen, true);
                if (is_array($decoded)) {
                    return $decoded;
                }

                // kalau string biasa
                if (!empty($asesmen)) {
                    return [$asesmen];
                }

                return [];
            })
            ->filter()
            ->map(fn ($nama) => trim($nama))
            ->unique()
            ->values();

        $missingCpmk = $allCpmks
            ->filter(fn ($cpmk) => !$usedCpmkIds->contains((int) $cpmk->id))
            ->values();

        $missingAsesmen = $allAsesmen
            ->filter(fn ($asesmen) => !$usedAsesmen->contains($asesmen))
            ->values();

        return [
            'total_cpmk'      => $allCpmks->count(),
            'used_cpmk'       => $usedCpmkIds->count(),
            'missing_cpmk'    => $missingCpmk,

            'total_asesmen'   => $allAsesmen->count(),
            'used_asesmen'    => $usedAsesmen->count(),
            'missing_asesmen' => $missingAsesmen,

            'is_complete'     => $missingCpmk->isEmpty() && $missingAsesmen->isEmpty(),
        ];
    }
    
    public function getSubCpmkByCpmk($cpmkId)
    {
        // asumsi: tabel sub_cpmk punya kolom cpmk_id (relasi ke cpmks.id)
        // dan punya kolom: id, kode, uraian
        $subs = DB::table('sub_cpmk')
            ->where('cpmk_id', $cpmkId)
            ->select('id', 'kode', 'uraian')
            ->orderBy('kode')
            ->get();
        return response()->json($subs);
    }

    public function Add()
    {
        $rpss = RPS::where('pengembang', auth()->user()->name)
                    ->where('rpss.id_prodi', auth()->user()->id_prodiUser)
                    ->whereHas('mk')
                    ->get(); 
        $mk_kodes = $rpss->pluck('kode_mk'); 
        $cpmks = DB::table('cpmk_mk')
            ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
            ->whereIn('cpmk_mk.mk_kode', $mk_kodes)
            ->select('cpmks.id', 'cpmks.kode', 'cpmks.judul')
            ->distinct()
            ->get();
        $cpmk_ids = $cpmks->pluck('id'); 
        $sub_cpmks = DB::table('sub_cpmk')
                    ->whereIn('cpmk_id', $cpmk_ids)
                    ->get();  
        $instrumen = DB::table('metode_penilaian')
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->orderBy('nama')
            ->get();
        return view('dosen.Activities.add', compact('rpss', 'cpmks','instrumen','sub_cpmks'));
    }

    public function Edit($id)
    {
        $activity = Activity::findOrFail($id);
        $rpss = RPS::where('pengembang', auth()->user()->name)
                    ->where('prodi.id', auth()->user()->id_prodiUser)
                    ->whereHas('mk')
                    ->get();
        return view('dosen.Activities.edit', compact('activity', 'rpss'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'minggu' => 'required|integer|min:1|max:16',
            'id_cpmk' => 'required|array',
            'sub_cpmk' => 'nullable|array',
            'sub_cpmk.*' => 'nullable',
            'indikator' => 'required|array',
            'materi' => 'required|array',
            'id_rps' => 'required|exists:rpss,id',
            'bentuk_asesmen' => 'required|array|min:1',
            'bentuk_asesmen.*' => 'required|string',
            'metode_deskripsi' => 'required|array',
            'metode_deskripsi.*' => 'required|string',
            'metode_kategori' => 'required|array',
            'metode_waktu' => 'required|array',
            'pustaka' => 'nullable|array',
            'pustaka.*' => 'nullable|string'
        ]);
        $idCpmk = array_values(array_filter($request->id_cpmk ?? []));
        $subCpmk = array_values(array_filter($request->sub_cpmk ?? []));
        $bentukAsesmen = array_values(array_filter($request->bentuk_asesmen ?? []));
        if (count($subCpmk) === 0) {
            $subCpmk = [0];
        }
        $metodeArray = [];
        if ($request->has('metode_deskripsi')) {
            foreach ($request->metode_deskripsi as $key => $deskripsi) {
                if (!empty($deskripsi)) {
                    $metodeArray[] = [
                        'deskripsi' => $deskripsi,
                        'kategori'  => $request->metode_kategori[$key] ?? null,
                        'waktu'     => $request->metode_waktu[$key] ?? null,
                    ];
                }
            }
        }

        $pustakaArray = [];
        if ($request->has('pustaka')) {
            $pustakaArray = array_values(array_filter($request->pustaka));
        }
        $dataMetodeLengkap = [
            'detail_metode' => $metodeArray,
            'pustaka'       => $pustakaArray,
        ];
        $activity = new Activity();
        $activity->minggu = $request->minggu;
        $activity->id_cpmk = $idCpmk;
        $activity->sub_cpmk = $subCpmk;
        $activity->materi = $request->materi;
        $activity->kegiatan_luring = $request->kegiatan_luring ?? [];
        $activity->kegiatan_daring = $request->kegiatan_daring ?? [];
        $activity->indikator = $request->indikator;
        $activity->bentuk_asesmen = $bentukAsesmen;
        $activity->kriteria = $request->kriteria ?? null;
        $activity->bobot = $request->bobot ?? null;
        $activity->id_rps = $request->id_rps;
        $activity->id_prodi = auth()->user()->id_prodiUser;
        $activity->metode = $dataMetodeLengkap;
        $activity->save();
        return redirect()->route('dosen.rps-detail', ['id' => $request->id_rps])
                ->with('success', 'New Activities successfully added!');
    }

 
    public function Update(Request $request, $id)
    {
        $request->validate([
            'sub_cpmk' => 'nullable|array',
            'sub_cpmk.*' => 'nullable',
            'indikator' => 'required|array',
            'materi' => 'required|array',
            'id_rps' => 'required|exists:rpss,id',
            'bentuk_asesmen' => 'required|array|min:1',
            'bentuk_asesmen.*' => 'required|string',
            'metode_deskripsi' => 'required|array',
            'metode_deskripsi.*' => 'required|string',
            'metode_kategori' => 'required|array',
            'metode_waktu' => 'required|array',
            'pustaka' => 'nullable|array',
            'pustaka.*' => 'nullable|string'
        ]);
        $idCpmk = array_values(array_filter($request->id_cpmk ?? []));
        $subCpmk = array_values(array_filter($request->sub_cpmk ?? []));
        $bentukAsesmen = array_values(array_filter($request->bentuk_asesmen ?? []));
        if (count($subCpmk) === 0) {
            $subCpmk = [0];
        }
        $metodeArray = [];
        if ($request->has('metode_deskripsi')) {
            foreach ($request->metode_deskripsi as $key => $deskripsi) {
                if (!empty($deskripsi)) {
                    $metodeArray[] = [
                        'deskripsi' => $deskripsi,
                        'kategori'  => $request->metode_kategori[$key] ?? null,
                        'waktu'     => $request->metode_waktu[$key] ?? null,
                    ];
                }
            }
        }
        $pustakaArray = [];
        if ($request->has('pustaka')) {
            $pustakaArray = array_values(array_filter($request->pustaka));
        }
        $dataMetodeLengkap = [
            'detail_metode' => $metodeArray,
            'pustaka'       => $pustakaArray,
        ];
        $activity = Activity::findOrFail($id);
        $activity->update([
            'id_cpmk' => $idCpmk,
            'sub_cpmk' => $subCpmk,
            'materi' => $request->materi,
            'kegiatan_luring' => $request->kegiatan_luring ?? [],
            'kegiatan_daring' => $request->kegiatan_daring ?? [],
            'bentuk_asesmen' => $bentukAsesmen,
            'metode' => $dataMetodeLengkap,
            'kriteria' => $request->kriteria ?? null,
            'bobot' => $request->bobot ?? null,
            'id_rps' => $request->id_rps,
            'indikator' => $request->indikator
        ]);
        return redirect()->route('dosen.rps-detail', ['id' => $request->id_rps])
                ->with('success', 'Activity successfully updated!');
    }

    public function Delete($id)
    {
        $activity = Activity::find($id);
        $id_rps = $activity->id_rps;
        $activity->delete(); 
        return redirect()->route('dosen.rps-detail', ['id' => $id_rps])
                   ->with('success', 'Activity successfully deleted!');
    }
}
