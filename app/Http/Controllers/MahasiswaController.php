<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Traits\UniversityFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MahasiswaController extends Controller
{
    use UniversityFilterTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Method getTemplate() Anda untuk mendapatkan data dasar seperti peran pengguna
        $data = $this->getTemplate();

        // Trait dipanggil untuk menyiapkan data dropdown filter (universitas, fakultas, prodi)
        // Trait ini juga akan mengunci dropdown sesuai peran, yang bagus untuk UX.
        $filterData = $this->getFilterData($request);

        // Query dasar untuk Mahasiswa
        $mahasiswaQuery = Mahasiswa::query()
            ->join('prodi', 'mahasiswa.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select(
                'mahasiswa.*',
                'prodi.nama as prodi_nama',
                'fakultas.nama as fakultas_nama',
                'universitas.nama as universitas_nama'
            );

        // LAPISAN 1: Filter WAJIB berdasarkan peran pengguna (default)
        // Ini adalah lapisan keamanan utama untuk membatasi data.
        if (in_array($data['userOtoritas'], ['Penjamin Mutu Universitas', 'Wakil Rektor', 'Admin Universitas'])) {
            $mahasiswaQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($data['userOtoritas'], ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $mahasiswaQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($data['userOtoritas'], ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $mahasiswaQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        // Admin, tidak ada filter yang diterapkan di sini.
        // =========================================================================

        // LAPISAN 2: Filter OPSIONAL dari input form (menggunakan Trait)
        // Trait ini akan menambahkan filter di atas data yang sudah dibatasi oleh peran.
        $mahasiswaQuery = $this->getFilteredQuery($mahasiswaQuery, $request);

        // Eksekusi query final dan kirim semua data ke view
        $mahasiswas = $mahasiswaQuery->orderBy('npm', 'asc')->get();
        
        // Gabungkan semua data yang dibutuhkan oleh view
        $viewData = array_merge($data, $filterData, compact('mahasiswas'));

        return view('mahasiswa.index', $viewData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = $this->getTemplate();
        return view('mahasiswa.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'npm.required' => 'NPM wajib diisi',
            'npm.numeric' => 'NPM harus berupa angka',
            'npm.min' => 'NPM minimal 9 karakter',
            'npm.unique' => 'NPM sudah terdaftar',

            'nama.required' => 'Nama lengkap wajib diisi',
            'nama.string' => 'Nama lengkap harus berupa teks',
            'nama.max' => 'Nama lengkap maksimal 255 karakter',

            'angkatan.required' => 'Angkatan wajib diisi',
            'angkatan.numeric' => 'Angkatan harus berupa angka',
            'angkatan.min' => 'Angkatan minimal 4 karakter',
        ];
        // dd($request->all());

        $request->validate([
            'npm' => ['required', 'numeric', 'min:9'],
            'nama' => ['required', 'string', 'max:255'],
            'angkatan' => ['required', 'numeric', 'min:4'],
        ], $messages);

        try {
            Mahasiswa::create([
                'NPM' => $request->npm,
                'Nama' => $request->nama,
                'angkatan' => $request->angkatan,
                'id_prodi' => auth()->user()->id_prodiUser,
            ]);
            return redirect($this->getRedirectRoute())->with('success', 'Mahasiswa berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->route('dosen.mahasiswa.create')->with('error', 'Mahasiswa gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->getTemplate();
        $mahasiswa = Mahasiswa::findOrFail($id);

        return view('mahasiswa.edit', array_merge($data, compact('mahasiswa')));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $messages = [
            'npm.required' => 'NPM wajib diisi',
            'npm.numeric' => 'NPM harus berupa angka',
            'npm.min' => 'NPM minimal 9 karakter',
            'npm.unique' => 'NPM sudah terdaftar',
            'nama.required' => 'Nama lengkap wajib diisi',
            'nama.string' => 'Nama lengkap harus berupa teks',
            'nama.max' => 'Nama lengkap maksimal 255 karakter',
            'angkatan.required' => 'Angkatan wajib diisi',
            'angkatan.numeric' => 'Angkatan harus berupa angka',
        ];

        $request->validate([
            'npm' => ['required', 'numeric', 'min:9'],
            'nama' => ['required', 'string', 'max:255'],
            'angkatan' => ['required', 'numeric'],
        ], $messages);

        try {
            $mahasiswa = Mahasiswa::findOrFail($id);
            $mahasiswa->update([
                'NPM' => $request->npm,
                'Nama' => $request->nama,
                'angkatan' => $request->angkatan,
            ]);
            return redirect($this->getRedirectRoute())->with('success', 'Data mahasiswa berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data mahasiswa: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $mahasiswa = Mahasiswa::findOrFail($id);
            $mahasiswa->delete();
            return redirect($this->getRedirectRoute())->with('success', 'Mahasiswa berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus mahasiswa: ' . $e->getMessage());
        }
    }

    private function getRedirectRoute()
    {
        $userOtoritas = auth()->user()->otoritas->otoritas;

        $routes = [
            'Dosen' => 'dosen.mahasiswa.index',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.mahasiswa.index',
        ];

        return route($routes[$userOtoritas] ?? 'mahasiswa.index');
    }

    private function getTemplate()
    {
        $userOtoritas = auth()->user()->otoritas->otoritas;

        $templates = [
            'Wakil Rektor' => 'dosen.template',
            'Wakil Dekan' => 'dosen.template',
            'Dosen' => 'dosen.template',
            'Admin' => 'admin.template',
            'Admin Universitas' => 'admin.template',
            'Kepala Program Studi' => 'penjamin-mutu.template',
            'Penjamin Mutu Universitas' => 'penjamin-mutu.template',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.template',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.template',
        ];

        return [
            'userOtoritas' => $userOtoritas,
            'template' => $templates[$userOtoritas] ?? 'mahasiswa.index',
            'otoritas' => array_keys($templates),
        ];
    }
}
