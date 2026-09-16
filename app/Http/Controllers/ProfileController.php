<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOtoritas;
use App\Models\UserTtd;
use App\Services\UserContextService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $primaryProdiId = $user->primary_prodi_id ?? $user->id_prodiUser;
        $isSecondaryProdi = $primaryProdiId && (int)$user->id_prodiUser !== (int)$primaryProdiId;

        // Ambil semua otoritas user
        $profiles = UserOtoritas::where('user_id', Auth::id())
            ->orderByRaw("FIELD(otoritas, 'Admin', 'Admin Universitas', 'Wakil Rektor', 'Wakil Dekan', 'Kepala Program Studi' ,'Dosen', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi')")
            ->get()
            ->unique('otoritas') // Pastikan tidak ada duplikat otoritas yang sama
            ->values();

        // BARU: Ambil semua prodi milik user
        $userProdis = $user->prodis()->withPivot('active')->get();

        // Mapping otoritas ke path view
        $layoutPaths = [
            'Admin' => 'admin.template',
            'Admin Universitas' => 'admin.template',
            'Wakil Rektor' => 'dosen.template',
            'Wakil Dekan' => 'dosen.template',
            'Dosen' => 'dosen.template',
            'Kepala Program Studi' => 'penjamin-mutu.template',
            'Penjamin Mutu Universitas' => 'penjamin-mutu.template',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.template',
            'Penjamin Mutu Prodi' => 'penjamin-mutu.template',
        ];

        // Pilih view berdasarkan otoritas aktif
        $activeOtoritas = $profiles->where('active', true)->first();
        $layout = $layoutPaths[$activeOtoritas->otoritas ?? 'Dosen'] ?? 'penjamin-mutu.template';

        return view('components.profile', compact('user', 'profiles', 'userProdis', 'layout', 'isSecondaryProdi'));
    }

    public function switchOtoritas(Request $request)
    {
        $user = Auth::user();
        $primaryProdiId = $user->primary_prodi_id ?? $user->id_prodiUser;
        $isSecondaryProdi = $primaryProdiId && (int)$user->id_prodiUser !== (int)$primaryProdiId;

        if ($isSecondaryProdi) {
            return redirect()->back()->with('error', 'Saat berada di prodi pengampu, Anda hanya memiliki otoritas sebagai Dosen.');
        }

        // Non-aktifkan semua otoritas user
        UserOtoritas::where('user_id', Auth::id())->update(['active' => false]);

        // Aktifkan otoritas yang dipilih
        $targetOtoritas = UserOtoritas::where('user_id', Auth::id())->findOrFail($request->otoritas_id);
        $targetOtoritas->update(['active' => true]);

        // Simpan role terakhir yang dipilih ke session
        session(['last_primary_otoritas_id_' . Auth::id() => $targetOtoritas->id]);

        $routes = [
            'Admin' => 'admin.home',
            'Admin Universitas' => 'admin-universitas.home',
            'Wakil Rektor' => 'wakil-rektor.home',
            'Wakil Dekan' => 'wakil-dekan.home',
            'Kepala Program Studi' => 'kepala-program-studi.home',
            'Dosen' => 'dosen.home',
            'Penjamin Mutu' => 'penjamin-mutu.universitas.home',
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.home',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.home',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.home',
            'Mahasiswa' => 'mahasiswa.home',
        ];

        if (isset($routes[$targetOtoritas->otoritas]) && \Illuminate\Support\Facades\Route::has($routes[$targetOtoritas->otoritas])) {
            return redirect()->route($routes[$targetOtoritas->otoritas])->with('success', 'Berhasil beralih ke peran ' . $targetOtoritas->otoritas);
        }

        return redirect()->back()->with('success', 'Berhasil beralih ke peran ' . $targetOtoritas->otoritas);
    }

    public function switchProdi(Request $request, UserContextService $contextService)
    {
        $request->validate(['prodi_id' => 'required|integer|exists:prodi,id']);

        try {
            $contextService->switchActiveProdi(auth()->user(), $request->prodi_id);
            if ($request->has('redirect_to')) {
                return redirect($request->redirect_to)->with('success', 'Prodi aktif berhasil diperbarui!');
            }
            
            $userOtoritas = optional(auth()->user()->otoritas)->otoritas;
            $routes = [
                'Admin' => 'admin.home',
                'Admin Universitas' => 'admin-universitas.home',
                'Wakil Rektor' => 'wakil-rektor.home',
                'Wakil Dekan' => 'wakil-dekan.home',
                'Kepala Program Studi' => 'kepala-program-studi.home',
                'Dosen' => 'dosen.home',
                'Penjamin Mutu' => 'penjamin-mutu.universitas.home',
                'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.home',
                'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.home',
                'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.home',
                'Mahasiswa' => 'mahasiswa.home',
            ];

            if (isset($routes[$userOtoritas])) {
                return redirect()->route($routes[$userOtoritas])->with('success', 'Prodi aktif berhasil diperbarui!');
            }

            return redirect()->back()->with('success', 'Prodi aktif berhasil diperbarui!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    
    public function uploadTtd(Request $request)
    {
        $request->validate([
            'file_ttd' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ], [
            'file_ttd.required' => 'File tanda tangan wajib diunggah.',
            'file_ttd.image' => 'File harus berupa gambar.',
            'file_ttd.mimes' => 'Format file harus PNG, JPG, atau JPEG.',
            'file_ttd.max' => 'Ukuran file maksimal 2 MB.',
        ]);

        $user = Auth::user();

        try {
            $file = $request->file('file_ttd');

            $folderPath = public_path('assets/img/ttd');

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $filename = 'ttd_user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($folderPath, $filename);

            $relativePath = 'assets/img/ttd/' . $filename;

            // nonaktifkan semua ttd lama milik user
            UserTtd::where('user_id', $user->id)->update([
                'is_active' => 0,
            ]);

            // simpan ttd baru sebagai aktif
            UserTtd::create([
                'user_id' => $user->id,
                'file_ttd' => $relativePath,
                'is_active' => 1,
            ]);

            return back()->with('success_ttd', 'Tanda tangan berhasil diunggah.');
        } catch (\Throwable $e) {
            return back()->with('error_ttd', 'Tanda tangan gagal diunggah.');
        }
    }

    public function disconnectGoogle()
    {
        if (! config('services.google.login_enabled')) {
            return redirect('/profile')->with('error', 'Fitur Google tidak tersedia.');
        }

        $user = Auth::user();
        $user->google_id = null;
        $user->save();

        return redirect('/profile')->with('success', 'Hubungan dengan akun Google berhasil diputuskan.');
    }

    public function password(Request $request,  $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'old_password' => 'required',
            'password' => [
                'required', 'different:old_password',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
            'confirm_password' => 'required|same:password'
        ], [
            'password.regex' => 'Kata sandi harus mengandung huruf besar, huruf kecil, angka, dan simbol',
            'confirm_password.same' => 'Konfirmasi kata sandi tidak sesuai',
            'old_password.required' => 'Kata sandi lama wajib diisi',
            'password.required' => 'Kata sandi baru wajib diisi',
            'confirm_password.required' => 'Konfirmasi kata sandi wajib diisi'
        ]);

        if (Hash::check($request->old_password, $user->password)) {
            $user->fill([
                'password' => Hash::make($request->password)
            ])->save();

            $request->session()->flash('success', 'Kata sandi berhasil diubah');
            return redirect('/profile');
        } else {
            $request->session()->flash('error', 'Kata sandi lama tidak sesuai');
            return redirect('/profile');
        }
    }

    public function pp(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $img = $request->file('img');
        if ($user->img != 'User-Profile.png') {
            File::delete(public_path('../public/assets/img/pp/' . $user->img));
        }
        
        $imagePath = round(microtime(true) * 1000) . '-' . str_replace(' ', '-', $img->getClientOriginalName());
        $image = Image::make($img)->fit(200);
        $image->save(public_path('../public/assets/img/pp/') . $imagePath, 100);
        $user->fill([
            'img' => $imagePath
        ])->save();

        return redirect('/profile')->with('success', 'Foto profil berhasil diperbarui.');

    }

    public function name(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z., ]+$/']
        ]);

        $user->fill([
            'name' => $request->name
        ])->save();

        return redirect('/profile')->with('success', 'Foto profil berhasil diperbarui.');

    }
}
