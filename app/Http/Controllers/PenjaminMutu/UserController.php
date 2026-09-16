<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Models\User;

use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\Universitas;
use App\Imports\UsersImport;
use App\Models\UserOtoritas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\UniversityFilterTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Encryption\DecryptException;

class UserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    use UniversityFilterTrait;

    public function create()
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas;

        $data = [
            'universitas' => Universitas::select('id', 'nama')->distinct()->get(),
            'fakultas' => collect(), // Initialize empty collection by default
            'prodi' => collect(), // Initialize empty collection by default
        ];

        // Handle pre-selected values based on user role
        switch ($userOtoritas) {
            case 'Admin Universitas':
            case 'Penjamin Mutu Universitas':
                // Pre-load fakultas for the user's university
                $data['fakultas'] = Fakultas::select('id', 'nama')
                    ->where('id_universitas', $user->id_universitasUser)
                    ->distinct()
                    ->get();
                break;

            case 'Penjamin Mutu Fakultas':
                // Pre-load fakultas and prodi for the user's faculty
                $data['fakultas'] = Fakultas::select('id', 'nama')
                    ->where('id_universitas', $user->id_universitasUser)
                    ->distinct()
                    ->get();
                $data['prodi'] = Prodi::select('id', 'nama')
                    ->where('id_fakultas', $user->id_fakultasUser)
                    ->distinct()
                    ->get();
                break;

            case 'Penjamin Mutu Program Studi':
            case 'Kepala Program Studi':
                // Pre-load all selections for the user's program
                $data['fakultas'] = Fakultas::select('id', 'nama')
                    ->where('id_universitas', $user->id_universitasUser)
                    ->distinct()
                    ->get();
                $data['prodi'] = Prodi::select('id', 'nama')
                    ->where('id_fakultas', $user->id_fakultasUser)
                    ->distinct()
                    ->get();
                break;
        }

        return view('penjamin-mutu.user.add', $data);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z., ]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'string', 
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/'
            ],
            'img' => ['nullable'],
            'otoritas' => ['required', 'array'],
            'otoritas.*' => ['required', 'string', 'max:255'],
            'nama_otoritas' => ['nullable', 'array'],
            'nama_otoritas.*' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z., ]+$/'],
            'prodi' => ['required', 'array'],
            'prodi.*' => ['exists:prodi,id'],
            'fakultas' => ['required'],
            'universitas' => ['required']
        ], [
            // Custom messages
            'password.regex' => 'Password Harus Mengandung Huruf Besar, Huruf Kecil, Angka, dan Simbol',
        ]);

        // Ambil prodi pertama dari array untuk dijadikan prodi aktif default
        $activeProdiId = $request->prodi[0];
        $activeProdi = Prodi::with('fakultas')->find($activeProdiId);

        $img = $request->file('img');
        if ($img != null) {
            $imagePath = round(microtime(true) * 1000) . '-' . str_replace(' ', '-', $img->getClientOriginalName());
            $img->move(public_path('../public/assets/img/pp/'), $imagePath);
        } else {
            $imagePath = 'User-Profile.png';
        }

        $user = null;

        DB::transaction(function () use ($request, $imagePath, $activeProdi, &$user) {
            // Buat user dengan prodi aktif pertama
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'img' => $imagePath,
                // DIUBAH: Isi kolom lama dengan data dari prodi aktif pertama
                'id_prodiUser' => $activeProdi->id,
                'id_fakultasUser' => $activeProdi->id_fakultas,
                'id_universitasUser' => $activeProdi->fakultas->id_universitas,
            ]);

            // Handle multiple otoritas (tidak ada perubahan)
            $otoritasArray = $request->otoritas;
            $namaOtoritasArray = $request->nama_otoritas ?? [];
            $isFirst = true;
            foreach ($otoritasArray as $otoritas) {
                UserOtoritas::create([
                    'user_id' => $user->id,
                    'otoritas' => $otoritas,
                    'nama_otoritas' => $namaOtoritasArray[$otoritas] ?? null,
                    'active' => $isFirst
                ]);
                $isFirst = false;
            }

            // BARU: Handle multiple prodi, mirip seperti otoritas
            $prodiArray = $request->prodi;
            $isFirstProdi = true;
            foreach ($prodiArray as $prodiId) {
                $user->prodis()->attach($prodiId, ['active' => $isFirstProdi]);
                $isFirstProdi = false;
            }
        });

        event(new Registered($user));

        return redirect()->back()->with('success', 'User berhasil ditambahkan!');
    }

    public function list(Request $request)
    {
        $query = User::query()
            ->select('users.*')
            ->with(['otoritas', 'prodis'])
            ->leftJoin('user_otoritas', 'users.id', '=', 'user_otoritas.user_id')
            ->leftJoin('prodi', 'users.id_prodiUser', '=', 'prodi.id')
            ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->leftJoin('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->orderByRaw('
                COALESCE(universitas.id, users.id_universitasUser) ASC,
                COALESCE(fakultas.id, users.id_fakultasUser) ASC,
                COALESCE(prodi.id, users.id_prodiUser) ASC
            ')
            ->orderByRaw("FIELD(user_otoritas.otoritas, 'Admin', 'Admin Universitas', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen')")
            ->whereNotNull(['name', 'email', 'id_universitasUser'])
            ->where('users.id', '!=', auth()->id())
            ->distinct();

        $availableDosen = collect();

        // Untuk Admin Universitas
        if (auth()->user()->otoritas->otoritas == 'Penjamin Mutu Universitas') {
            $query->whereHas('otoritas', function ($q) {
                $q->whereIn('otoritas', [
                    'Penjamin Mutu Fakultas',
                    'Penjamin Mutu Program Studi',
                    'Wakil Rektor',
                    'Wakil Dekan',
                    'Kepala Program Studi',
                    'Dosen'
                ]);
            })
                ->where('id_universitasUser', auth()->user()->id_universitasUser);
        } elseif (auth()->user()->otoritas->otoritas == 'Penjamin Mutu Fakultas') {
            $query->whereHas('otoritas', function ($q) {
                $q->whereIn('otoritas', [
                    'Penjamin Mutu Program Studi',
                    'Wakil Dekan',
                    'Kepala Program Studi',
                    'Dosen'
                ]);
            })
                ->where('id_fakultasUser', auth()->user()->id_fakultasUser);
        } elseif (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $kaprodiProdiId = auth()->user()->id_prodiUser;

            $query->whereHas('otoritas', function ($q) {
                $q->whereIn('otoritas', [
                    'Kepala Program Studi',
                    'Dosen'
                ]);
            })
                ->where(function ($q) use ($kaprodiProdiId) {
                    $q->where('id_prodiUser', $kaprodiProdiId)
                      ->orWhereHas('prodis', function ($p) use ($kaprodiProdiId) {
                          $p->where('prodi.id', $kaprodiProdiId);
                      });
                });

            // Ambil daftar seluruh dosen yang ada di universitas tetapi belum masuk ke prodi Kaprodi
            $availableDosen = User::whereHas('otoritas', function ($q) {
                    $q->whereIn('otoritas', ['Dosen', 'Kepala Program Studi']);
                })
                ->where('id_universitasUser', auth()->user()->id_universitasUser)
                ->whereDoesntHave('prodis', function ($p) use ($kaprodiProdiId) {
                    $p->where('prodi.id', $kaprodiProdiId);
                })
                ->where(function($q) use ($kaprodiProdiId) {
                    $q->where('id_prodiUser', '!=', $kaprodiProdiId)
                      ->orWhereNull('id_prodiUser');
                })
                ->orderBy('name', 'asc')
                ->get();
        }


        // Gunakan method dari trait untuk filter
        $query = $this->getFilteredQuery($query, $request);
        $users = $query->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        return view('penjamin-mutu.user.list', array_merge(
            ['users' => $users, 'availableDosen' => $availableDosen],
            $filterData
        ));
    }

    public function assignDosen(Request $request)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas;

        if (!in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Admin', 'Admin Universitas'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $kaprodiProdiId = $user->id_prodiUser;
        if (!$kaprodiProdiId) {
            return redirect()->back()->with('error', 'Prodi pengampu tidak terdefinisi.');
        }

        $targetUser = User::findOrFail($request->user_id);

        // Ensure primary_prodi_id is preserved for targetUser
        if (!$targetUser->primary_prodi_id && $targetUser->id_prodiUser) {
            $targetUser->update(['primary_prodi_id' => $targetUser->id_prodiUser]);
        }

        // Pastikan target user memiliki rekaman otoritas 'Dosen'
        if (!$targetUser->otoritas()->where('otoritas', 'Dosen')->exists()) {
            UserOtoritas::create([
                'user_id' => $targetUser->id,
                'otoritas' => 'Dosen',
                'nama_otoritas' => 'Dosen',
                'active' => false, // Otoritas Dosen akan aktif saat mengakses prodi pengampu
            ]);
        }

        // Simpan relasi dosen-prodi (tanpa duplikasi)
        if (!$targetUser->prodis()->where('prodi_id', $kaprodiProdiId)->exists()) {
            $isFirst = ($targetUser->prodis()->count() === 0);
            $targetUser->prodis()->attach($kaprodiProdiId, ['active' => $isFirst]);

            if ($isFirst || !$targetUser->id_prodiUser) {
                $prodi = Prodi::find($kaprodiProdiId);
                $targetUser->update([
                    'id_prodiUser' => $kaprodiProdiId,
                    'id_fakultasUser' => $prodi ? $prodi->id_fakultas : $targetUser->id_fakultasUser,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Dosen ' . $targetUser->name . ' berhasil ditambahkan ke prodi pengampu!');
    }

    public function reset($id)
    {
        $ids = Crypt::decrypt($id);
        $reset = User::findOrFail($ids);
        $password = 'unilajaya';
        $reset->update(['password' => Hash::make($password)]);

        return redirect()->route($this->getRouteByAuthority())->with('success', 'Password successfully reset!');
    }

    public function delete($id)
    {
        $ids = Crypt::decrypt($id);
        $user = User::findOrFail($ids);
        $userOtoritas = auth()->user()->otoritas->otoritas;

        if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi'])) {
            $kaprodiProdiId = auth()->user()->id_prodiUser;

            // Lepas relasi prodi_user untuk prodi Kaprodi ini
            $user->prodis()->detach($kaprodiProdiId);

            // Jika id_prodiUser aktif milik user adalah prodi ini, alihkan ke prodi lain atau null
            if ($user->id_prodiUser == $kaprodiProdiId) {
                $nextProdi = $user->prodis()->first();
                if ($nextProdi) {
                    $user->update([
                        'id_prodiUser' => $nextProdi->id,
                        'id_fakultasUser' => $nextProdi->id_fakultas,
                    ]);
                    DB::table('prodi_user')
                        ->where('user_id', $user->id)
                        ->where('prodi_id', $nextProdi->id)
                        ->update(['active' => true]);
                } else {
                    $user->update(['id_prodiUser' => null]);
                }
            }

            return redirect()->route($this->getRouteByAuthority())->with('success', 'Dosen ' . $user->name . ' berhasil dihapus dari daftar dosen pengampu prodi ini!');
        }

        $user->delete();

        return redirect()->route($this->getRouteByAuthority())->with('success', 'User successfully deleted!');
    }

    public function edit($id)
    {
        try {
            $userId = Crypt::decrypt($id);
            // Eager load relasi untuk efisiensi
            $user = User::with('prodis', 'fakultas', 'universitas')->findOrFail($userId);

            // BARU: Ambil semua ID prodi yang terhubung dengan user ini
            $userProdiIds = $user->prodis()->pluck('prodi_id')->toArray();
            
            // Ambil data otoritas yang spesifik untuk user ini
            $userOtoritasData = $user->otoritas()->pluck('nama_otoritas', 'otoritas')->toArray();

            $data = [
                'user' => $user,
                'userProdiIds' => $userProdiIds, // <-- DIKIRIM KE VIEW
                'userOtoritasData' => $userOtoritasData,
                'allUniversitas' => Universitas::where('id', $user->id_universitasUser)->get(),
                'selectedUniversitas' => $user->universitas,
                'allFakultas' => Fakultas::where('id_universitas', $user->id_universitasUser)->get(),
                'selectedFakultas' => $user->fakultas,
                'allProdi' => Prodi::where('id_fakultas', $user->id_fakultasUser)->get(),
                'selectedProdi' => $user->prodi,
            ];

            return view('penjamin-mutu.user.edit', $data);
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', 'Invalid encrypted ID');
        }
    }

    public function update(Request $request, $id)
    {
        $userId = Crypt::decrypt($id);
        $user = User::findOrFail($userId);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z., ]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'img' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'otoritas' => ['required', 'array'],
            'prodi' => ['required', 'array'],
            'prodi.*' => ['exists:prodi,id'],
        ]);

        DB::transaction(function () use ($request, $user) {
            // 1. Dapatkan daftar prodi baru dari request
            $newProdiIds = $request->prodi;

            // 2. Tentukan prodi mana yang akan menjadi aktif
            $currentActiveProdiId = $user->id_prodiUser;
            $newActiveProdiId = in_array($currentActiveProdiId, $newProdiIds) ? $currentActiveProdiId : $newProdiIds[0];

            // 3. Ambil data prodi aktif yang baru untuk sinkronisasi
            $activeProdi = Prodi::with('fakultas')->findOrFail($newActiveProdiId);
            
            // 4. Update data dasar di tabel 'users'
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'id_prodiUser' => $activeProdi->id,
                'id_fakultasUser' => $activeProdi->id_fakultas,
                'id_universitasUser' => $activeProdi->fakultas->id_universitas,
            ]);
            // (Logika update gambar bisa ditambahkan di sini)

            // 5. Siapkan data untuk sinkronisasi pivot prodi
            $prodiSyncData = [];
            foreach ($newProdiIds as $prodiId) {
                $prodiSyncData[$prodiId] = ['active' => ($prodiId == $newActiveProdiId)];
            }
            $user->prodis()->sync($prodiSyncData);

            // 6. Sinkronkan data otoritas
            $user->otoritas()->delete(); // Hapus semua otoritas lama
            $otoritasArray = $request->otoritas;
            $namaOtoritasArray = $request->nama_otoritas ?? [];
            $isFirstOtoritas = true;
            foreach ($otoritasArray as $otoritas) {
                UserOtoritas::create([
                    'user_id' => $user->id,
                    'otoritas' => $otoritas,
                    'nama_otoritas' => $namaOtoritasArray[$otoritas] ?? null,
                    'active' => $isFirstOtoritas,
                ]);
                $isFirstOtoritas = false;
            }
        });

        return redirect()->route($this->getRouteByAuthority())->with('success', 'User berhasil diperbarui!');
    }

    public function create_wfile(Request $request)
    {
        try {
            $excel = $request->file('excel');
            $excelPath = round(microtime(true) * 1000) . '-' . str_replace(' ', '-', $excel->getClientOriginalName());
            $excel->move(public_path('../public/assets/excel/'), $excelPath);
            Excel::import(new UsersImport, public_path('../public/assets/excel/' . $excelPath));
            $file = new Filesystem;
            $file->cleanDirectory('../public/assets/excel/');

            return redirect()->route($this->getRouteByAuthority())->with('success', 'User successfully edited!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Terjadi kesalahan, silahkan periksa kembali data dalam excel anda!, " . $e);
        }
    }

    // untuk mendapatkan route berdasarkan otoritas
    private function getRouteByAuthority(): string
    {
        $routes = [
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.list-user',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.list-user',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.list-user',
            'Kepala Program Studi'          => 'kepala-program-studi.list-user'
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }

    private function getAddUserRoute(): string
    {
        $routes = [
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.add-user',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.add-user',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.add-user'
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.add-user';
    }

    public function getFakultas($universitas_id)
    {
        $fakultas = Fakultas::where('id_universitas', $universitas_id)
            ->select('id', 'nama')
            ->get();
        return response()->json($fakultas);
    }

    public function getProdi($fakultas_id)
    {
        $prodi = Prodi::where('id_fakultas', $fakultas_id)
            ->select('id', 'nama')
            ->get();
        return response()->json($prodi);
    }

    public function importDosen(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $user = auth()->user();
            $prodiId = $user->id_prodiUser;
            if (!$prodiId && $user->prodis()->exists()) {
                $prodiId = $user->prodis()->first()->id;
            }

            Excel::import(new \App\Imports\DosenImport($prodiId), $request->file('excel_file'));
            return redirect()->back()->with('success', 'Data Dosen berhasil diimport dari Excel dengan password default Unilajaya!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimport data Dosen: ' . $e->getMessage());
        }
    }

    public function downloadTemplateDosen()
    {
        return Excel::download(new \App\Exports\DosenTemplateExport, 'template_import_dosen.xlsx');
    }
}
