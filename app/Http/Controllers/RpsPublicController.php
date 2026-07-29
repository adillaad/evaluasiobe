<?php

namespace App\Http\Controllers;

use App\Models\RPS;
use App\Models\MK; // Tambahkan
use App\Models\CPL; // Tambahkan
use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\Activity; // Tambahkan
use App\Models\Universitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Contracts\Encryption\DecryptException;

class RpsPublicController extends Controller
{ 
    // public function index(Request $request) 
    // { 
    //      $universitas = Universitas::orderBy('nama')->get();
        
    //      $fakultas = collect();
    //      if ($request->filled('universitas_id')) {
    //          $fakultas = Fakultas::where('id_universitas', $request->universitas_id)->orderBy('nama')->get();
    //      }
         
    //      $prodi = collect();
    //      if ($request->filled('fakultas_id')) {
    //          $prodi = Prodi::where('id_fakultas', $request->fakultas_id)->orderBy('nama')->get();
    //      }
  
    //      $query = RPS::where('status', 'published');
  
    //      $query->whereIn('id', function ($subQuery) {
    //          $subQuery->select(DB::raw('MAX(id)'))
    //              ->from('rpss')
    //              ->where('status', 'published')
    //              ->groupBy('kode_mk', 'id_kurikulum');
    //      });
  
    //      if ($request->filled('search')) {
    //          $search = $request->search;
    //          $query->whereHas('mk', function ($q) use ($search) {
    //              $q->where('nama', 'like', "%{$search}%")
    //                ->orWhere('kode_mk', 'like', "%{$search}%");
    //          });
    //      }
  
    //      if ($request->filled('prodi_id')) {
    //          $query->whereHas('mk', fn($q) => $q->where('id_prodi', $request->prodi_id));
    //      } elseif ($request->filled('fakultas_id')) {
    //          $query->whereHas('mk.prodi', fn($q) => $q->where('id_fakultas', $request->fakultas_id));
    //      } elseif ($request->filled('universitas_id')) {
    //          $query->whereHas('mk.prodi.fakultas', fn($q) => $q->where('id_universitas', $request->universitas_id));
    //      }
  
    //      $rpss = $query->with('mk.prodi')->latest()->paginate(10);
 
    //      return view('guest.rps.index', compact('rpss', 'universitas', 'fakultas', 'prodi'));
    // }

    // public function index(Request $request)
    // { 
    //     $query = RPS::where('status', 'published');
    //     $query->whereIn('id', function ($sub) {
    //         $sub->select(DB::raw('MAX(id)'))
    //             ->from('rpss')
    //             ->where('status', 'published')
    //             ->groupBy('kode_mk', 'id_kurikulum');
    //     }); 
        
    //     $rpss = $query->with('mk.prodi')
    //                 ->latest()
    //                 ->paginate(10);
    //     return view('guest.rps.index', compact('rpss'));
    // }
    
    public function index(Request $request)
    {
        $search = trim($request->search);

        $query = RPS::where('status', 'published');

        $query->whereIn('id', function ($sub) {
            $sub->select(DB::raw('MAX(id)'))
                ->from('rpss')
                ->where('status', 'published')
                ->groupBy('kode_mk', 'id_kurikulum');
        });

        if (!empty($search)) {
            $query->whereHas('mk', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $rpss = $query->with('mk.prodi.fakultas.universitas')
                    ->latest()
                    ->paginate(10)
                    ->appends($request->query());

        return view('guest.rps.index', compact('rpss'));
    }


    public function download($encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $rps = RPS::with([
                'mk.prodi.fakultas.universitas',
                'pustakaUtama',
                'pustakaPendukung',
                'pengembangUser.activeTtd',
                'koordinatorUser.activeTtd',
                'kaprodiUser.activeTtd',
            ])->findOrFail($id);
            $mks = MK::all(); 
            $activities = Activity::where('id_rps', $id)->get();  
            $cpl_prodi = CPL::join('mk_cpl', 'cpls.id', '=', 'mk_cpl.cpl_id')
                ->where('mk_cpl.mk_kode', $rps->kode_mk)
                ->select('cpls.*')
                ->get();
            $cpmks = DB::table('cpmk_mk')
                ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
                ->where('cpmk_mk.mk_kode', $rps->kode_mk)
                ->select('cpmks.*', 'cpmk_mk.mk_kode')
                ->get();
            $sub_cpmks = DB::table('sub_cpmk')->get();
            $cpl_ids_from_cpmk = $cpmks->pluck('cpl_id')->unique();
            $supporting_cpls = CPL::whereIn('id', $cpl_ids_from_cpmk)->get();
            $all_cpls_for_view = $cpl_prodi->merge($supporting_cpls)->unique('id');
            // === ambil metode penilaian yg dipakai pada MK ini ===
            $instrumens = DB::table('penilaian_metode as pm')
                ->join('cpl_mk_cpmk_penilaian as cmc', 'cmc.id', '=', 'pm.cpl_mk_cpmk_penilaian_id')
                ->join('metode_penilaian as mp', 'mp.id', '=', 'pm.metode_id')
                ->where('cmc.mk_kode', $rps->kode_mk)
                ->select('mp.nama as nama')
                ->distinct()
                ->orderBy('mp.nama')
                ->get();
            
            // === ambil bobot per CPMK per metode ===
            $penilaian = DB::table('penilaian_metode as pm')
                ->join('cpl_mk_cpmk_penilaian as cmc', 'cmc.id', '=', 'pm.cpl_mk_cpmk_penilaian_id')
                ->join('cpmks', 'cpmks.id', '=', 'cmc.cpmk_id')
                ->join('metode_penilaian as mp', 'mp.id', '=', 'pm.metode_id')
                ->where('cmc.mk_kode', $rps->kode_mk)
                ->select('cpmks.kode as cpmk_kode', 'mp.nama as nama', 'pm.bobot as bobot')
                ->get();
            
            $penilaianMap = [];
            foreach ($penilaian as $p) {
                $penilaianMap[$p->cpmk_kode][$p->nama] = $p->bobot;
            } 

            $showTtdPengembang = in_array($rps->status, ['pending', 'published']);

            $showTtdKoordinator = $rps->status === 'published';

            $showTtdKaprodi = $rps->status === 'published';

            // ambil file ttd user
            $pengembangTtd = optional(optional($rps->pengembangUser)->activeTtd)->file_ttd;
            $koordinatorTtd = optional(optional($rps->koordinatorUser)->activeTtd)->file_ttd;
            $kaprodiTtd = optional(optional($rps->kaprodiUser)->activeTtd)->file_ttd;

            $data = compact(
                'rps',
                'activities',
                'mks',
                'cpmks',
                'sub_cpmks',
                'cpl_prodi',
                'all_cpls_for_view',
                'instrumens',
                'penilaianMap',

                'showTtdPengembang',
                'showTtdKoordinator',
                'showTtdKaprodi',
                'pengembangTtd',
                'koordinatorTtd',
                'kaprodiTtd'
            );
            $pdf = SnappyPdf::loadView('pdf.rpsDownload', $data)
                ->setPaper('a4')
                ->setOrientation('landscape')
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-top', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('disable-smart-shrinking', true)
                ->setOption('no-outline', true)
                ->setOption('print-media-type', true)
                ->setOption('disable-smart-shrinking', true)
                ->setOption('enable-local-file-access', true)
                ->setOption('load-error-handling', 'ignore')
                ->setOption('load-media-error-handling', 'ignore')
                ->setOption('image-quality', 60)
                ->setOption('dpi', 150);

            $mkNama = $rps->mk->nama ?? 'Mata Kuliah';
            $filename = "RPS - {$mkNama}.pdf";
            
            return $pdf->download($filename);

        } catch (DecryptException $e) {
            abort(404, 'Link tidak valid.');
        }
    }
}