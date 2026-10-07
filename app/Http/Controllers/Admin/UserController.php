<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\Universitas;
use App\Imports\UsersImport;
use App\Imports\DosenImport;
use App\Exports\DosenTemplateExport;
use App\Models\UserOtoritas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\UniversityFilterTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;

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
            'fakultas' => collect(),
            'prodi' => collect(),
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

        return view('admin.user.add', $data);
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'img' => ['nullable'],
            'otoritas' => ['required'],
            'prodi' => ['nullable'],
            'fakultas' => ['nullable'],
            'universitas' => ['nullable']
        ]);

        $prodiArray = array_values(array_filter((array) $request->input('prodi', [])));
        if (empty($prodiArray)) {
            $authUserProdiId = auth()->user()->id_prodiUser ?? auth()->user()->prodis->first()?->id;
            if ($authUserProdiId) {
                $prodiArray = [$authUserProdiId];
            } else {
                $firstProdi = Prodi::first();
                if ($firstProdi) {
                    $prodiArray = [$firstProdi->id];
                }
            }
        }

        $activeProdiId = $prodiArray[0] ?? null;
        $activeProdi = $activeProdiId ? Prodi::with('fakultas')->find($activeProdiId) : null;
        if (!$activeProdi) {
            $activeProdi = Prodi::with('fakultas')->first();
        }

        $idFakultas = $activeProdi ? $activeProdi->id_fakultas : (auth()->user()->id_fakultasUser ?? 1);
        $idUniversitas = ($activeProdi && $activeProdi->fakultas) ? $activeProdi->fakultas->id_universitas : (auth()->user()->id_universitasUser ?? 1);

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            DB::transaction(function () use ($request, $existingUser, $activeProdi, $idFakultas, $idUniversitas, $prodiArray) {
                $updateData = ['name' => $request->name];
                if (!empty($request->password)) {
                    $updateData['password'] = Hash::make($request->password);
                }
                if (!$existingUser->id_prodiUser && $activeProdi) {
                    $updateData['id_prodiUser'] = $activeProdi->id;
                    $updateData['id_fakultasUser'] = $idFakultas;
                    $updateData['id_universitasUser'] = $idUniversitas;
                }
                $existingUser->update($updateData);

                $otoritasArray = (array) $request->input('otoritas', []);
                $namaOtoritasArray = $request->input('nama_otoritas', []);
                foreach ($otoritasArray as $otoritas) {
                    if (!$existingUser->otoritas()->where('otoritas', $otoritas)->exists()) {
                        UserOtoritas::create([
                            'user_id' => $existingUser->id,
                            'otoritas' => $otoritas,
                            'nama_otoritas' => is_array($namaOtoritasArray) ? ($namaOtoritasArray[$otoritas] ?? null) : null,
                            'active' => !$existingUser->otoritas()->where('active', true)->exists()
                        ]);
                    }
                }

                $hasActiveProdi = $existingUser->prodis()->wherePivot('active', true)->exists();
                foreach (array_unique($prodiArray) as $pId) {
                    if (!$existingUser->prodis()->where('prodi_id', $pId)->exists()) {
                        $existingUser->prodis()->attach($pId, ['active' => !$hasActiveProdi]);
                        $hasActiveProdi = true;
                    }
                }
            });

            return redirect()->back()->with('success', 'User ' . $existingUser->name . ' (' . $request->email . ') berhasil ditambahkan!');
        }

        $img = $request->file('img');
        if ($img != null) {
            $imagePath = round(microtime(true) * 1000) . '-' . str_replace(' ', '-', $img->getClientOriginalName());
            $img->move(public_path('../public/assets/img/pp/'), $imagePath);
        } else {
            $imagePath = 'User-Profile.png';
        }

        $user = null;

        DB::transaction(function () use ($request, $imagePath, $activeProdi, $idFakultas, $idUniversitas, $prodiArray, &$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'img' => $imagePath,
                'id_prodiUser' => $activeProdi ? $activeProdi->id : null,
                'id_fakultasUser' => $idFakultas,
                'id_universitasUser' => $idUniversitas,
            ]);

            $otoritasArray = (array) $request->input('otoritas', []);
            $namaOtoritasArray = $request->input('nama_otoritas', []);
            $isFirst = true;
            foreach ($otoritasArray as $otoritas) {
                UserOtoritas::create([
                    'user_id' => $user->id,
                    'otoritas' => $otoritas,
                    'nama_otoritas' => is_array($namaOtoritasArray) ? ($namaOtoritasArray[$otoritas] ?? null) : null,
                    'active' => $isFirst
                ]);
                $isFirst = false;
            }

            $prodiSyncData = [];
            $isFirstProdi = true;
            foreach (array_unique($prodiArray) as $prodiId) {
                $prodiSyncData[$prodiId] = ['active' => $isFirstProdi];
                $isFirstProdi = false;
            }
            if (!empty($prodiSyncData)) {
                $user->prodis()->sync($prodiSyncData);
            }
        });

        event(new Registered($user));

        return redirect()->back()->with('success', 'User berhasil ditambahkan!');
    }

    public function list(Request $request)
    {
        $authUser = auth()->user();
        $authUnivId = $authUser->id_universitasUser ?? $authUser->fakultas?->id_universitas ?? $authUser->prodis->first()?->fakultas?->id_universitas;

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
            ->orderByRaw("FIELD(user_otoritas.otoritas, 'Admin', 'Admin Universitas', 'Wakil Rektor', 'Wakil Dekan', 'Dosen', 'Kepala Program Studi', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi')")
            ->whereNotNull('name')
            ->whereNotNull('email')
            ->where('users.id', '!=', auth()->id())
            ->distinct();

        // Untuk Admin Universitas
        if (($authUser->otoritas->otoritas ?? '') == 'Admin Universitas') {
            $query->whereHas('otoritas', function ($q) {
                $q->whereIn('otoritas', [
                    'Admin Universitas',
                    'Wakil Rektor',
                    'Wakil Dekan',
                    'Kepala Program Studi',
                    'Penjamin Mutu Universitas',
                    'Penjamin Mutu Fakultas',
                    'Penjamin Mutu Program Studi',
                    'Kepala Program Studi',
                    'Dosen'
                ]);
            });
            if ($authUnivId) {
                $query->where(function ($q) use ($authUnivId) {
                    $q->where('id_universitasUser', $authUnivId)
                      ->orWhereHas('prodis.fakultas', function ($sub) use ($authUnivId) {
                          $sub->where('fakultas.id_universitas', $authUnivId);
                      });
                });
            }
        }

        // Gunakan method dari trait untuk filter
        $query = $this->getFilteredQuery($query, $request);
        $users = $query->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        return view('admin.user.list', array_merge(
            ['users' => $users],
            $filterData
        ));
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
        User::where('id', $ids)->delete();

        return redirect()->route($this->getRouteByAuthority())->with('success', 'User successfully deleted!');
    }

    public function edit($id)
    {
        try {
            $userId = Crypt::decrypt($id);
            $user = User::with('prodis', 'fakultas', 'universitas')->findOrFail($userId);

            // Ambil semua ID prodi yang terhubung dengan user ini
            $userProdiIds = $user->prodis()->pluck('prodi_id')->toArray();

            $data = [
                'user' => $user,
                'userProdiIds' => $userProdiIds, // <-- BARU: Kirim semua ID prodi
                'allUniversitas' => Universitas::select('id', 'nama')->get(),
                'selectedUniversitas' => $user->universitas,
                // Pre-load fakultas berdasarkan universitas aktif user
                'allFakultas' => Fakultas::where('id_universitas', $user->id_universitasUser)->get(),
                'selectedFakultas' => $user->fakultas,
                // Pre-load prodi berdasarkan fakultas aktif user
                'allProdi' => Prodi::where('id_fakultas', $user->id_fakultasUser)->get(),
                'selectedProdi' => $user->prodi, // Prodi yang aktif
            ];

            return view('admin.user.edit', $data);
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
            // Pastikan validasi email unik mengabaikan user saat ini
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'img' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'otoritas' => ['required', 'array'],
            'prodi' => ['required', 'array'],
            'prodi.*' => ['exists:prodi,id'],
            // fakultas & universitas tidak perlu divalidasi karena hanya untuk filter di form
        ]);

        DB::transaction(function () use ($request, $user) {
            // 1. Dapatkan daftar prodi baru dari request
            $newProdiIds = $request->prodi;

            // 2. Tentukan prodi mana yang akan menjadi aktif
            $currentActiveProdiId = $user->id_prodiUser;
            $newActiveProdiId = null;

            if (in_array($currentActiveProdiId, $newProdiIds)) {
                // Jika prodi aktif lama masih ada di pilihan baru, pertahankan
                $newActiveProdiId = $currentActiveProdiId;
            } else {
                // Jika prodi aktif lama dihapus, jadikan prodi pertama di daftar baru sebagai aktif
                $newActiveProdiId = $newProdiIds[0];
            }

            // 3. Ambil data prodi aktif yang baru untuk sinkronisasi
            $activeProdi = Prodi::with('fakultas')->findOrFail($newActiveProdiId);

            // 4. Update data dasar di tabel 'users' (termasuk prodi aktif)
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'id_prodiUser' => $activeProdi->id,
                'id_fakultasUser' => $activeProdi->id_fakultas,
                'id_universitasUser' => $activeProdi->fakultas->id_universitas,
            ]);
            // (Logika update gambar bisa ditambahkan di sini jika perlu)

            // 5. Siapkan data untuk disinkronkan ke tabel pivot
            $prodiSyncData = [];
            foreach ($newProdiIds as $prodiId) {
                $prodiSyncData[$prodiId] = ['active' => ($prodiId == $newActiveProdiId)];
            }

            // 6. Sinkronkan data ke tabel pivot 'prodi_user'
            // 'sync' akan otomatis menambah, menghapus, dan memperbarui relasi
            $user->prodis()->sync($prodiSyncData);

            // 7. Sinkronkan data otoritas (logika lama, bisa disederhanakan dengan sync juga)
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
            if (auth()->user()->otoritas == 'Admin') {
                return redirect()->route($this->getRouteByAuthority())->with('error', "Terjadi kesalahan, silahkan periksa kembali data dalam excel anda!, " . $e);
            } else if (auth()->user()->otoritas == 'Admin Universitas') {
                return redirect()->back()->with('error', "Terjadi kesalahan, silahkan periksa kembali data dalam excel anda!, " . $e);
            }
        }
    }

    private function getRouteByAuthority(): string
    {
        $routes = [
            'Admin' => 'admin.list-user',
            'Admin Universitas' => 'admin-universitas.list-user',
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }

    private function getAddUserRoute(): string
    {
        $routes = [
            'Admin' => 'admin.add-user',
            'Admin Universitas' => 'admin-universitas.add-user',
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

            Excel::import(new DosenImport($prodiId), $request->file('excel_file'));
            return redirect()->back()->with('success', 'Data Dosen berhasil diimport dari Excel dengan password default Unilajaya!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimport data Dosen: ' . $e->getMessage());
        }
    }

    public function downloadTemplateDosen()
    {
        return Excel::download(new DosenTemplateExport, 'template_import_dosen.xlsx');
    }
}
