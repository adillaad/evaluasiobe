<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\Models\MK;
use App\Models\CPL;
use App\Models\RPS;
use App\Models\CPMK;
use App\Models\User;
use App\Models\CPLMK;
use App\Models\Prodi;
use App\Models\Activity;
use App\Models\Fakultas;
use App\Imports\RPSsImport;
use App\Models\Universitas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\UniversityFilterTrait;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RpsController extends Controller
{
    use UniversityFilterTrait;

    public function create()
    {
        $mks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('fakultas.id_universitas', auth()->user()->id_universitasUser)
            ->select('mks.*')->get();

        $prodis = Prodi::query()
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('fakultas.id_universitas', auth()->user()->id_universitasUser)
            ->select('prodi.*')->get();

        $users = User::query()
            ->join('user_otoritas', 'users.id', '=', 'user_otoritas.user_id')
            ->where('user_otoritas.otoritas', 'Dosen')
            ->where('id_universitasUser', auth()->user()->id_universitasUser)
            ->select('users.*')
            ->get();

        $kaprodis = User::query()
            ->join('user_otoritas', 'users.id', '=', 'user_otoritas.user_id')
            ->where('user_otoritas.otoritas', 'Kepala Program Studi')
            ->where('id_universitasUser', auth()->user()->id_universitasUser)
            ->select('users.*')
            ->get();

        return view('admin.rps.add', compact('mks', 'users', 'prodis', 'kaprodis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor' => ['required', 'regex:/^[0-9\/]+$/'],
            'id_prodi' => 'required',
            'kode_mk' => 'required',
            'semester' => ['required', 'integer', 'digits:1'],
            'pengembang' => 'required',
            'koordinator' => 'required',
            'dosen' => 'required',
            'kaprodi' => ['required', 'string', 'regex:/^[a-zA-Z., ]+$/', 'max:255'],
            'materi_mk' => 'required',
            'kontrak' => 'required',
            'pustaka_utama' => 'required',
            'pustaka_pendukung' => 'nullable',
        ]);

        // Cari MK berdasarkan kode
        $mk = MK::where('kode', $request->kode_mk)->firstOrFail();

        // Tambahkan pengecekan jika MK tidak ditemukan
        if (!$mk) {
            return redirect()->back()->with('error', 'Mata kuliah tidak ditemukan!');
        }

        if ($request->pustaka_pendukung == null) {
            $p = 'Tidak ada'; // Jika pustaka pendukung kosong, set 'Tidak ada'
        } else {
            $p = $request->pustaka_pendukung; // Jika ada, ambil dari input
        }

        $bobot = $mk->bobot_teori + $mk->bobot_praktikum;
        if ($bobot == 1 && $mk->bobot_teori < $mk->bobot_praktikum) {
            $t = 'Lecture, group discussion, task, and practicum'; // Jika bobot 1 dan teori lebih sedikit
        } elseif ($bobot == 3) {
            $t = 'Lecture, group discussion, task, and practicum'; // Jika bobot 3
        } else {
            $t = 'Lecture, group discussion, and task'; // Untuk bobot lainnya
        }

        // Menghitung waktu berdasarkan bobot mata kuliah
        $w = '"Lectures: ' . $bobot . 'x 50 = 150 minutes per week. 
Exercises and Assignments: ' . $bobot . 'x 60 = 180 minutes per week. 
Private study: ' . $bobot . 'x 60 = 180 minutes per week."';

        try {
            RPS::create([
                'nomor' => $request->nomor,
                'id_prodi' => $request->id_prodi,
                'kode_mk' => $request->kode_mk,
                'semester' => $request->semester,
                'pengembang' => $request->pengembang,
                'koordinator' => $request->koordinator,
                'dosen' => $request->dosen,
                'kaprodi' => $request->kaprodi,
                'tipe' => $t, // Menyimpan tipe berdasarkan bobot mata kuliah
                'id_kurikulum' => $mk->id_kurikulum, // id Kurikulum diambil dari mata kuliah yang dipilih
                'waktu' => $w, // Menyimpan perhitungan waktu berdasarkan bobot
                'kontrak' => $request->kontrak,
                'media' => 'e-learning (virtual class), LCD, whiteboard, and websites',
                'pustaka_utama' => $request->pustaka_utama,
                'pustaka_pendukung' => $p, // Menyimpan pustaka pendukung, jika ada
                'syarat_ujian' => 'A student must have attended at least 80% of the lectures to sit in the exams.',
                'syarat_studi' => '"Trial, either midterm or semester test,
                Tasks, including individual or group assignments to be completed within a certain timeframe, and team project
                Quizzes, held on face-to-face, once before midterm exam and once after midterm exam, with a short answer form.
                Assessment is done using benchmark assessment, with the aim of measuring the level of student understanding related to the target and class rank."',
                'materi_mk' => $request->materi_mk,
            ]);

            return redirect()->route($this->getRouteByAuthority())->with('success', 'New RPS successfully added!');
        } catch (\Exception $e) {
            // Check if it's a duplicate entry error
            if (
                strpos($e->getMessage(), 'Duplicate entry') !== false &&
                strpos($e->getMessage(), 'kode_mk') !== false
            ) {
                return redirect()->back()->with('error', 'RPS untuk mata kuliah ini sudah ada!');
            }

            // For other errors
            return redirect()->back()->with('error', 'Gagal menambahkan RPS. Silakan coba lagi.');
        }
    }

    public function list(Request $request)
    {
        $query = RPS::query()
            ->with(['prodi', 'prodi.fakultas'])
            ->join('prodi', 'rpss.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('rpss.*');

        // Filter berdasarkan otoritas user
        if (auth()->user()->otoritas->otoritas === 'Admin Universitas') {
            $query->where('universitas.id', auth()->user()->id_universitasUser);
        }

        // Gunakan method dari trait untuk filter
        $query = $this->getFilteredQuery($query, $request);
        $rpss = $query->orderBy('rpss.created_at', 'desc')->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        return view('admin.rps.list', array_merge(
            ['rpss' => $rpss],
            $filterData
        ));
    }

    public function edit($kode)
    {
        $rps = RPS::where('kode_mk', $kode)->firstOrFail();

        $prodis = Prodi::query()
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('fakultas.id_universitas', auth()->user()->id_universitasUser)
            ->select('prodi.*')->get();

        $kaprodis = User::query()
            ->join('user_otoritas', 'users.id', '=', 'user_otoritas.user_id')
            ->where('user_otoritas.otoritas', 'Kepala Program Studi')
            ->where('id_universitasUser', auth()->user()->id_universitasUser)
            ->select('users.*')
            ->get();

        $mks = MK::join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('fakultas.id_universitas', auth()->user()->id_universitasUser)
            ->select('mks.*')
            ->get();

        $users = User::query()
            ->join('user_otoritas', 'users.id', '=', 'user_otoritas.user_id')
            ->where('user_otoritas.otoritas', 'Dosen')
            ->where('id_universitasUser', auth()->user()->id_universitasUser)
            ->select('users.*')
            ->get();
        return view('admin.rps.edit', compact('rps', 'prodis', 'kaprodis', 'users', 'mks'));
    }

    public function update(Request $request, $kode)
    {
        $request->validate([
            'nomor' => ['required', 'regex:/^[0-9\/]+$/'],
            'id_prodi' => 'required',
            'semester' => ['required', 'integer', 'digits:1'],
            'kode_mk' => 'required',
            'pengembang' => 'required',
            'koordinator' => 'nullable',
            'dosen' => 'required',
            'kaprodi' => ['required', 'string', 'regex:/^[a-zA-Z., ]+$/', 'max:255'],
            'tipe' => ['required', 'regex:/^[a-zA-Z., ]+$/', 'string'],
            'waktu' => 'required',
            'kontrak' => 'required',
            'materi_mk' => 'required',
            'syarat_ujian' => ['required', 'regex:/^[0-9a-zA-Z.,% ]+$/'],
            'syarat_studi' => 'required',
            'media' => 'required',
            'pustaka_pendukung' => 'nullable',
            'pustaka_utama' => 'required',
        ]);
        if ($request->pustaka_pendukung == null) {
            $p = 'Tidak ada';
        } else {
            $p = $request->pustaka_pendukung;
        }

        $mk = MK::where('kode', $request->kode_mk)->firstOrFail();
        $rps = RPS::where('kode_mk', $kode)->firstOrFail();
        $rps->update([
            'nomor' => $request->nomor,
            'id_prodi' => $request->id_prodi,
            'semester' => $request->semester,
            'kode_mk' => $request->kode_mk,
            'pengembang' => $request->pengembang,
            'koordinator' => $request->koordinator,
            'dosen' => $request->dosen,
            'kaprodi' => $request->kaprodi,
            'tipe' => $request->tipe,
            'kurikulum' => $mk->id_kurikulum, // diambil dari mk
            'waktu' => $request->waktu,
            'kontrak' => $request->kontrak,
            'materi_mk' => $request->materi_mk,
            'syarat_ujian' => $request->syarat_ujian,
            'syarat_studi' => $request->syarat_studi,
            'media' => $request->media,
            'pustaka_utama' => $request->pustaka_utama,
            'pustaka_pendukung' => $p,
        ]);
        return redirect()->route($this->getRouteByAuthority())->with('success', 'RPS successfully updated!');
    }

    public function delete($id)
    {
        $ids = Crypt::decrypt($id);
        RPS::where('id', $ids)->delete();
        return redirect()->route($this->getRouteByAuthority())->with('success', 'RPS successfully deleted!');
    }

    public function print($id)
    {
        $ids = Crypt::decrypt($id);
        $rps = RPS::findOrFail($ids);
        $mks = MK::all();
        // $rps = RPS::where('kode_mk', $kode)->firstOrFail();
        $activities = Activity::all();
        $pengetahuans = collect();
        $keterampilans = collect();
        $umums = collect();
        $cplmks = DB::table('mk_cpl')
        ->whereIn('mk_kode', $rps->kode_mk)
        ->get();
        foreach ($cplmks as $cplmk) {
            $pengetahuan = CPL::where('aspek', 'Pengetahuan')->where('id', $cplmk->id_cpl)->get();
            $keterampilan = CPL::where('aspek', 'Keterampilan')->where('id', $cplmk->id_cpl)->get();
            $umum = CPL::where('aspek', 'Umum')->where('id', $cplmk->id_cpl)->get();
            foreach ($keterampilan as $k) $keterampilans->push($k);
            foreach ($pengetahuan as $p) $pengetahuans->push($p);
            foreach ($umum as $u) $umums->push($u);
        }
        $cpls = CPL::all();
        $cpmks = DB::table('cpmk_mk')
            ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
            ->where('mk_kode', $rps->kode_mk)
            ->select('cpmks.*', 'cpmk_mk.mk_kode')
            ->get();
        $sikaps = CPL::where('aspek', 'Sikap')->where('id_kurikulum', $rps->id_kurikulum)->get();

        $data = compact(
            'rps',
            'activities',
            'mks',
            'cplmks',
            'cpls',
            'cpmks',
            'sikaps',
            'umums',
            'pengetahuans',
            'keterampilans'
        );

        // $pdf = PDF::loadView('admin.rps.print', $data)->setOrientation('landscape');
        // $pdf->setOption('enable-local-file-access', true);
        // return $pdf->stream('rps.pdf');
        return view('admin.rps.print', $data);
    }


    public function create_wfile(Request $request)
    {
        try {
            $excel = $request->file('excel');
            $excelPath = round(microtime(true) * 1000) . '-' . str_replace(' ', '-', $excel->getClientOriginalName());
            $excel->move(public_path('../public/assets/excel/'), $excelPath);
            Excel::import(new RPSsImport, public_path('../public/assets/excel/' . $excelPath));
            $file = new Filesystem;
            $file->cleanDirectory('../public/assets/excel/');
            return redirect()->route($this->getRouteByAuthority())->with('success', 'RPS successfully added!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Terjadi kesalahan, silahkan periksa kembali data dalam excel anda!, " . $e);
        }
    }

    private function getRouteByAuthority(): string
    {
        $routes = [
            'Admin' => 'admin.list-rps',
            'Admin Universitas' => 'admin-universitas.list-rps',
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }
}
