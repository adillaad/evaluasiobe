<?php

namespace App\Http\Controllers\Dosen;

use PDF;
use App\Models\MK;
use App\Models\CPL;
use App\Models\RPS;
use App\Models\User;
use App\Models\CPLMK;
use App\Models\CPMK;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Traits\UniversityFilterTrait;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\CplMkCpmkPenilaian;
use App\Models\MetodePenilaian;
use App\Models\PenilaianMetode;
use App\Models\RpsValidation; 
use App\Models\Pustaka;
use Illuminate\Validation\ValidationException;


class RPScontroller extends Controller
{
    use UniversityFilterTrait;
    public function Add()
    {
        $prodis = Prodi::query()
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->select('prodi.*')->get();

        $mks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->join('kurikulums', 'mks.id_kurikulum', '=', 'kurikulums.id')
            ->where('mks.id_prodi', auth()->user()->id_prodiUser)
            ->select('mks.*')->get();

        return view('dosen.RPS.add', compact('prodis', 'mks'));
    } 
    
    public function List(Request $request)
    {
        $user = auth()->user();
        $otoritas = $user->otoritas->otoritas;
        $query = RPS::query()
            ->with(['mk', 'prodi.fakultas', 'latestValidation']);
     
        if ($otoritas == 'Wakil Rektor') {
            $query->whereHas('prodi.fakultas', function ($q) use ($user) {
                $q->where('id_universitas', $user->id_universitasUser);
            });
        } elseif ($otoritas == 'Wakil Dekan') {
            $query->whereHas('prodi', function ($q) use ($user) {
                $q->where('id_fakultas', $user->id_fakultasUser);
            });
        } elseif ($otoritas == 'Kepala Program Studi') {
            $query->where('id_prodi', $user->id_prodiUser);
        } elseif ($otoritas == 'Dosen') {
            $query->where('id_prodi', $user->id_prodiUser)
                  ->where('pengembang', $user->name);
        }
    
        $rpss = $query->orderBy('created_at', 'desc')->get();
        $prodis = Prodi::where('id', $user->id_prodiUser)->get();
        $mks    = MK::where('id_prodi', $user->id_prodiUser)->get();
        $users  = User::where('id_prodiUser', $user->id_prodiUser)
            ->whereHas('otoritas', function ($q) {
                $q->where('otoritas', 'Dosen');
            })->get();
    
        return view('dosen.RPS.list', compact('rpss', 'prodis', 'mks', 'users'));
    }

    public function getPustakaByMk($kode_mk)
    { 
        $userProdiId = auth()->user()->id_prodiUser;
        $pustakas = Pustaka::where('id_prodi', $userProdiId)
                            ->where('kode_mk', $kode_mk) // <-- HANYA where() ini saja
                            ->select('id', 'kode_pustaka', 'judul','penulis', 'penerbit', 'tahun') 
                            ->orderBy('judul', 'asc') 
                            ->get(); 
        return response()->json($pustakas);
    }

    public function getPustaka(Request $request)
    {
        $userProdiId = auth()->user()->id_prodiUser;
        $rpsId  = $request->query('rpsId');     // untuk edit
        $sifat  = $request->query('sifat');     // utama | pendukung
        $kodeMk = $request->query('kodeMk');    // untuk create

        // === MODE EDIT: ambil dari pivot rps_pustaka ===
        if ($rpsId && $sifat) {
            $data = DB::table('rps_pustaka')
                ->join('pustakas', 'pustakas.id', '=', 'rps_pustaka.pustaka_id')
                ->where('rps_pustaka.rps_id', $rpsId)
                ->where('rps_pustaka.sifat', $sifat)
                ->where('pustakas.id_prodi', $userProdiId)
                ->select(
                    'pustakas.id',
                    'pustakas.kode_pustaka',
                    'pustakas.judul',
                    'pustakas.penulis',
                    'pustakas.penerbit',
                    'pustakas.tahun'
                )
                ->orderBy('pustakas.judul', 'asc')
                ->get();
            return response()->json($data);
        }

        // === MODE CREATE: ambil dari master pustakas by MK ===
        if ($kodeMk) {
            $data = Pustaka::where('id_prodi', $userProdiId)
                ->where('kode_mk', $kodeMk)
                ->select('id', 'kode_pustaka', 'judul', 'penulis', 'penerbit', 'tahun')
                ->orderBy('judul', 'asc')
                ->get();
            return response()->json($data);
        }
        return response()->json([]);
    }

    public function print($id)
    {
        $ids = Crypt::decrypt($id);
        $rps = RPS::with([
            'prodi.fakultas.universitas',
            'mk',
            'pustakaUtama',
            'pustakaPendukung',
            'pengembangUser.activeTtd',
            'koordinatorUser.activeTtd',
            'kaprodiUser.activeTtd',
            'latestValidation', 
        ])->findOrFail($ids);
        $mks = MK::all();
        $activities = Activity::all();
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
        $instrumens = DB::table('penilaian_metode as pm')
            ->join('cpl_mk_cpmk_penilaian as cmc', 'cmc.id', '=', 'pm.cpl_mk_cpmk_penilaian_id')
            ->join('metode_penilaian as mp', 'mp.id', '=', 'pm.metode_id')
            ->where('cmc.mk_kode', $rps->kode_mk)
            ->select('mp.nama as nama')
            ->distinct()
            ->orderBy('mp.nama')
            ->get();
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

        // Ambil file TTD user
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
        return view('admin.rps.print', $data);
    }
    
    public function Store(Request $request)
    {   
        $validatedData = $request->validate([
            'matakuliah' => 'required|exists:mks,kode', 
            'pengembang' => 'required',
            'koordinator' => 'nullable',
            'dosen' => 'required',
            'dosen_anggota1' => 'required|different:dosen',
            'dosen_anggota2' => 'nullable|different:dosen|different:dosen_anggota1',
            'media_software' => 'required',
            'media_hardware' => 'required',
            'batas_kelulusan_mhs' => 'required|numeric|min:0|max:100',
            'batas_kelulusan_mk' => 'required|numeric|min:0|max:100',
            'pustaka_utama' => 'required|array|min:1',
            'pustaka_utama.*' => 'required',   
             
            'new_pustaka_judul.*' => 'required_if:pustaka_utama.*,tambah_baru|nullable|string|max:255',
            'new_pustaka_penulis.*' => 'required_if:pustaka_utama.*,tambah_baru|nullable|string|max:255',
            'new_pustaka_penerbit.*' => 'nullable|string|max:100',
            'new_pustaka_tahun.*' => 'required_if:pustaka_utama.*,tambah_baru|nullable|integer|digits:4',

            'pustaka_pendukung' => 'nullable|array',
            'pustaka_pendukung.*' => 'nullable',

            'new_judul_pendukung.*' => 'required_if:pustaka_pendukung.*,tambah_baru|nullable|string|max:255',
            'new_penulis_pendukung.*' => 'required_if:pustaka_pendukung.*,tambah_baru|nullable|string|max:255',
            'new_penerbit_pendukung.*' => 'nullable|string|max:100',
            'new_tahun_pendukung.*' => 'required_if:pustaka_pendukung.*,tambah_baru|nullable|integer|digits:4',
        ]); 
        return DB::transaction(function () use ($request, $validatedData) {

        $mk = MK::where('kode', $validatedData['matakuliah'])->firstOrFail();
        $semesterMk = $mk->semester;
        $lastRps = RPS::where('id_prodi', auth()->user()->id_prodiUser)->orderBy('nomor', 'desc')->first();
        $nomor = $lastRps ? $lastRps->nomor + 1 : 1;
        $lastVersi = RPS::where('kode_mk', $validatedData['matakuliah'])
            ->where('id_kurikulum', $mk->id_kurikulum)
            ->orderBy('versi', 'desc')
            ->value('versi');
        $versi = $lastVersi ? $lastVersi + 1 : 1;
        $kaprodi = User::whereHas('otoritas', function($q){
            $q->where('otoritas', 'Kepala Program Studi');
        })->where('id_prodiUser', auth()->user()->id_prodiUser)->value('name');
        $rps = RPS::create([
            'nomor' => $nomor,
            'versi' => $versi, 
            'status' => 'draft',
            'kode_mk' => $validatedData['matakuliah'], 
            'semester' => $semesterMk,
            'id_kurikulum' => $mk->id_kurikulum,
            'pengembang' => $validatedData['pengembang'],
            'koordinator' => $validatedData['koordinator'],
            'dosen' => $validatedData['dosen'],
            'dosen_anggota1' => $validatedData['dosen_anggota1'],
            'dosen_anggota2' => $validatedData['dosen_anggota2'],
            'kaprodi' => $kaprodi,
            'media_software' => $validatedData['media_software'],
            'media_hardware' => $validatedData['media_hardware'],
            'batas_kelulusan_mhs' => $validatedData['batas_kelulusan_mhs'],
            'batas_kelulusan_mk' => $validatedData['batas_kelulusan_mk'],
            'id_prodi' => auth()->user()->id_prodiUser, 
            'pustaka_utama' => null, 
            'pustaka_pendukung' => null, 
        ]); 
        if ($request->has('pustaka_utama')) {
            foreach ($request->pustaka_utama as $index => $pustakaValue) {
                $pustakaIdToAttach = null;
                if ($pustakaValue === 'tambah_baru') {
                    $judul = $request->new_judul_utama[$index];
                    $penulis = $request->new_penulis_utama[$index];
                    $penerbit = $request->new_penerbit_utama[$index];
                    $tahun = $request->new_tahun_utama[$index]; 
                    $penulisParts = explode(' ', $penulis);
                    $namaAkhir = end($penulisParts);
                    $kodePenulis = strtoupper(substr($namaAkhir, 0, 3)); 
                    $kodeTahun = substr($tahun, -2); 
                    $kodePustaka = "[{$kodePenulis}{$kodeTahun}]"; 
                    $deskripsiLengkap = "{$kodePustaka} {$judul}. {$penulis}. {$penerbit}. {$tahun}";
                    $newPustaka = Pustaka::create([
                        'id_prodi' => auth()->user()->id_prodiUser,
                        'kode_mk' => $validatedData['matakuliah'],  
                        'judul' => $judul,
                        'penulis' => $penulis,
                        'penerbit' => $penerbit,
                        'tahun' => $tahun,
                        'kode_pustaka' => $kodePustaka,
                        'deskripsi_lengkap' => $deskripsiLengkap,
                    ]);
                    $pustakaIdToAttach = $newPustaka->id;
                }  
                else {
                    $pustakaIdToAttach = $pustakaValue;
                }
                if ($pustakaIdToAttach) { 
                    $rps->pustakas()->attach($pustakaIdToAttach, ['sifat' => 'utama']);
                }
            }
        } 
        if ($request->has('pustaka_pendukung')) {
            foreach ($request->pustaka_pendukung as $index => $pustakaValue) {
                $pustakaIdToAttach = null;
                if ($pustakaValue === 'tambah_baru') {
                    $judul = $request->new_judul_pendukung[$index] ?? null;
                    $penulis = $request->new_penulis_pendukung[$index] ?? null;
                    $penerbit = $request->new_penerbit_pendukung[$index] ?? '-';
                    $tahun = $request->new_tahun_pendukung[$index] ?? null;
                    if ($judul && $penulis && $tahun) {
                        $penulisParts = explode(' ', $penulis);
                        $namaAkhir = end($penulisParts);
                        $kodePenulis = strtoupper(substr($namaAkhir, 0, 3));
                        $kodeTahun = substr($tahun, -2);
                        $kodePustaka = "[{$kodePenulis}{$kodeTahun}]";
                        $deskripsiLengkap = "{$kodePustaka} {$judul}. {$penulis}. {$penerbit}. {$tahun}";
                        $newPustaka = Pustaka::create([
                            'id_prodi' => auth()->user()->id_prodiUser,
                            'kode_mk' => $validatedData['matakuliah'],
                            'judul' => $judul,
                            'penulis' => $penulis,
                            'penerbit' => $penerbit,
                            'tahun' => $tahun,
                            'kode_pustaka' => $kodePustaka,
                            'deskripsi_lengkap' => $deskripsiLengkap,
                        ]);
                        $pustakaIdToAttach = $newPustaka->id;
                    }
                } else {
                    $pustakaIdToAttach = $pustakaValue;
                }
                if ($pustakaIdToAttach) {
                    $rps->pustakas()->attach($pustakaIdToAttach, ['sifat' => 'pendukung']);
                }
            }
        }
        $prefix = str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.';
        return redirect()->route($prefix . 'rps-list')->with('success', 'New RPS successfully added!');
        });
    }
    

    public function Update(Request $request, $id)
    { 
        $validatedData = $request->validate([ 
            'pengembang' => 'required',
            'koordinator' => 'nullable',
            'dosen' => 'required',
            'dosen_anggota1' => 'required|different:dosen',
            'dosen_anggota2' => 'nullable|different:dosen|different:dosen_anggota1',
            'media_software' => 'required',
            'media_hardware' => 'required',
            'batas_kelulusan_mhs' => 'required|numeric|min:0|max:100',
            'batas_kelulusan_mk' => 'required|numeric|min:0|max:100',
            'pustaka_utama' => 'required|array|min:1',
            'pustaka_utama.*' => 'required',  
            'new_pustaka_judul.*' => 'nullable|string|max:255',
            'new_pustaka_penulis.*' => 'nullable|string|max:255',
            'new_pustaka_penerbit.*' => 'nullable|string|max:100',
            'new_pustaka_tahun.*' => 'nullable|integer|digits:4',
            'pustaka_pendukung' => 'nullable|array',
            'pustaka_pendukung.*' => 'nullable',
            'new_pustaka_judul_pendukung.*' => 'nullable|string|max:255',
            'new_pustaka_penulis_pendukung.*' => 'nullable|string|max:255',
            'new_pustaka_penerbit_pendukung.*' => 'nullable|string|max:100',
            'new_pustaka_tahun_pendukung.*' => 'nullable|integer|digits:4',
        ]); 
        $rps = RPS::findOrFail($id); 
        $mk = $rps->mk;
        $rps->update([ 
            'semester' => $mk->semester,
            'pengembang' => $validatedData['pengembang'],
            'koordinator' => $validatedData['koordinator'],
            'dosen' => $validatedData['dosen'],
            'dosen_anggota1' => $validatedData['dosen_anggota1'],
            'dosen_anggota2' => $validatedData['dosen_anggota2'],
            'media_software' => $validatedData['media_software'],
            'media_hardware' => $validatedData['media_hardware'],
            'batas_kelulusan_mhs' => $validatedData['batas_kelulusan_mhs'],
            'batas_kelulusan_mk' => $validatedData['batas_kelulusan_mk'], 
            'pustaka_utama' => null,
            'pustaka_pendukung' => null,
        ]);
        $syncData = [];
        if ($request->has('pustaka_utama')) {
            foreach ($request->pustaka_utama as $index => $pustakaValue) { 
                $pustakaId = null; 
                if ($pustakaValue === 'tambah_baru') { 
                    $listJudul = $request->new_pustaka_judul ?? [];
                    $listPenulis = $request->new_pustaka_penulis ?? [];
                    $listPenerbit = $request->new_pustaka_penerbit ?? [];
                    $listTahun = $request->new_pustaka_tahun ?? []; 
                    $judul = $listJudul[$index] ?? 'Tanpa Judul';
                    $penulis = $listPenulis[$index] ?? 'Tanpa Penulis';
                    $penerbit = $listPenerbit[$index] ?? '-';
                    $tahun = $listTahun[$index] ?? date('Y'); 
                    $penulisParts = explode(' ', $penulis);
                    $namaAkhir = end($penulisParts) ?: 'XXX';
                    $kodePenulis = strtoupper(substr($namaAkhir, 0, 3));
                    $kodeTahun = substr($tahun, -2);
                    $kodePustaka = "[{$kodePenulis}{$kodeTahun}]";
                    $deskripsiLengkap = "{$kodePustaka} {$judul}. {$penulis}. {$penerbit}. {$tahun}"; 
                    $newPustaka = Pustaka::create([
                        'id_prodi' => auth()->user()->id_prodiUser,
                        'kode_mk' => $rps->kode_mk,
                        'judul' => $judul,
                        'penulis' => $penulis,
                        'penerbit' => $penerbit,
                        'tahun' => $tahun,
                        'kode_pustaka' => $kodePustaka,
                        'deskripsi_lengkap' => $deskripsiLengkap,
                    ]); 
                    $pustakaId = $newPustaka->id;
                } else { 
                    $pustakaId = $pustakaValue;
                } 
                if ($pustakaId) {
                    $syncData[$pustakaId] = ['sifat' => 'utama'];
                }
            }
        } 
        if ($request->has('pustaka_pendukung')) {
            foreach ($request->pustaka_pendukung as $index => $pustakaValue) { 
                $pustakaId = null; 
                if ($pustakaValue === 'tambah_baru') {
                    $judul = ($request->new_pustaka_judul_pendukung[$index] ?? null);
                    $penulis = ($request->new_pustaka_penulis_pendukung[$index] ?? null);
                    $penerbit = ($request->new_pustaka_penerbit_pendukung[$index] ?? '-');
                    $tahun = ($request->new_pustaka_tahun_pendukung[$index] ?? null); 
                    if ($judul && $penulis && $tahun) {
                        $penulisParts = explode(' ', $penulis);
                        $namaAkhir = end($penulisParts) ?: 'XXX';
                        $kodePenulis = strtoupper(substr($namaAkhir, 0, 3));
                        $kodeTahun = substr($tahun, -2);
                        $kodePustaka = "[{$kodePenulis}{$kodeTahun}]";
                        $deskripsiLengkap = "{$kodePustaka} {$judul}. {$penulis}. {$penerbit}. {$tahun}"; 
                        $newPustaka = Pustaka::create([
                            'id_prodi' => auth()->user()->id_prodiUser,
                            'kode_mk' => $rps->kode_mk,
                            'judul' => $judul,
                            'penulis' => $penulis,
                            'penerbit' => $penerbit,
                            'tahun' => $tahun,
                            'kode_pustaka' => $kodePustaka,
                            'deskripsi_lengkap' => $deskripsiLengkap,
                        ]); 
                        $pustakaId = $newPustaka->id;
                    }
                } else {
                    $pustakaId = $pustakaValue;
                } 
                if ($pustakaId) {
                    $syncData[$pustakaId] = ['sifat' => 'pendukung'];
                }
            }
        } 
        $rps->pustakas()->sync($syncData); 
        return redirect()->back()->with('success', 'RPS successfully updated!');
    }
    
    public function show($id)
    { 
        $rps = Rps::with(['mk', 'prodi.fakultas', 'cpmks', 'activities'])->findOrFail($id);
        return view('dosen.rps.detail', compact('rps'));
    }

    public function Delete($id)
    {
        RPS::where('id', $id)->delete();
        return redirect('/dosen/rps/list-rps')->with('success', 'RPS successfully deleted!');
    }

    // public function submitValidation($id)
    // {
    //     $rps = RPS::where('id', $id)
    //               ->where('pengembang', auth()->user()->name) 
    //               ->whereIn('status', ['draft', 'rejected'])
    //               ->firstOrFail(); 
    //     $rps->status = 'pending';
    //     $rps->submitted_at = now();
    //     $rps->save(); 
    //     return redirect()->back()->with('success', 'The RPS has been successfully submitted for validation.');
    // }

    public function submitValidation($id)
    {
        $rps = RPS::with(['activities'])
            ->where('id', $id)
            ->where('pengembang', auth()->user()->name)
            ->whereIn('status', ['draft', 'rejected'])
            ->firstOrFail();

        $validationRps = $this->getRpsKelengkapanData($rps);

        if (!$validationRps['is_complete']) {
            $messages = [];

            if ($validationRps['missing_cpmk']->isNotEmpty()) {
                $messages[] = 'CPMK yang belum dimasukkan ke aktivitas mingguan: ' .
                    $validationRps['missing_cpmk']->pluck('kode')->implode(', ');
            }

            if ($validationRps['missing_asesmen']->isNotEmpty()) {
                $messages[] = 'Bentuk asesmen yang belum dimasukkan ke aktivitas mingguan: ' .
                    $validationRps['missing_asesmen']->implode(', ');
            }

            return redirect()->back()
                ->with('error_list', $messages)
                ->with('rps_mk', $rps->mk->nama);
        }

        $rps->update([
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return redirect()->back()->with('success', 'RPS berhasil diajukan untuk validasi.');
    }

    private function getRpsKelengkapanData(RPS $rps): array
    {
        $rps->loadMissing(['activities']);

        // Semua CPMK yang seharusnya ada pada MK ini
        $allCpmks = CplMkCpmkPenilaian::with('cpmk')
            ->where('mk_kode', $rps->kode_mk)
            ->get()
            ->pluck('cpmk')
            ->filter()
            ->unique('id')
            ->values();

        // Semua asesmen yang seharusnya ada pada MK ini
        $allAsesmen = CplMkCpmkPenilaian::with('penilaianMetode.metode')
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

        // CPMK yang sudah dipakai di aktivitas mingguan
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

        // Asesmen yang sudah dipakai di aktivitas mingguan
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
} 