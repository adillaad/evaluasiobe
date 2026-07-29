<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Theme;
use App\Models\Universitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RegisterUniversitas;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\RateLimiter;
use App\Mail\UniversityRegistrationApproved;
use App\Mail\UniversityRegistrationRejected;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\UserOtoritas;
use Illuminate\Validation\Rule;

class RegisterUniversitasController extends Controller
{
    public function index()
    {
        // Jika user tidak login, langsung tampilkan halaman tamu.
        if (!auth()->check()) {
            return view('guest.register-universitas');
        }

        // Gunakan 'match' untuk mendapatkan nama route berdasarkan otoritas user.
        $routeName = match (auth()->user()->otoritas->otoritas) {
            'Admin'                       => 'admin.home',
            'Admin Universitas'           => 'admin-universitas.home',
            'Wakil Rektor'                => 'wakil-rektor.home',
            'Wakil Dekan'                 => 'wakil-dekan.home',
            'Kepala Program Studi'        => 'kepala-program.home',
            'Dosen'                       => 'dosen.home',
            'Penjamin Mutu Universitas'   => 'penjamin-mutu.universitas.home',
            'Penjamin Mutu Fakultas'      => 'penjamin-mutu.fakultas.home',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.home',
            default                       => null, // Untuk peran yang tidak terdaftar
        };

        // Jika peran ditemukan, redirect. Jika tidak, bisa arahkan ke halaman default.
        if ($routeName) {
            return redirect()->route($routeName);
        }
        
        // Fallback jika peran user ada tapi tidak terdaftar di atas (opsional)
        // Mungkin bisa diarahkan ke halaman login atau halaman error.
        return redirect('/login')->with('error', 'Otoritas tidak valid.');
    }

    public function store(Request $request)
    {
        // Rate Limiting
        if (RateLimiter::tooManyAttempts('register-universitas:' . $request->ip(), 5)) {
            return redirect()->back()->withErrors(['error' => 'Terlalu banyak percobaan. Coba lagi nanti.']);
        }

        RateLimiter::hit('register-universitas:' . $request->ip(), 60);

        // Cari entri sebelumnya dengan email yang sama
        $existingRegistration = RegisterUniversitas::where('email', $request->email)->first();

        if ($existingRegistration) {
            // Hapus entri sebelumnya jika statusnya rejected
            if ($existingRegistration->status === 'rejected') {
                $existingRegistration->delete();
            }
        }

        $messages = [
            'gelar_depan.string' => 'Gelar depan harus berupa teks',
            'gelar_depan.max' => 'Gelar depan maksimal 50 karakter',

            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nama_lengkap.string' => 'Nama lengkap harus berupa teks',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',

            'gelar_belakang.required' => 'Gelar belakang wajib diisi',
            'gelar_belakang.string' => 'Gelar belakang harus berupa teks',
            'gelar_belakang.max' => 'Gelar belakang maksimal 50 karakter',

            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus Pria atau Wanita',

            'nomor_telepon.required' => 'Nomor telepon wajib diisi',
            'nomor_telepon.regex' => 'Format nomor telepon tidak valid, harus dimulai dengan 08',

            'nama_universitas.required' => 'Nama universitas wajib diisi',
            'nama_universitas.string' => 'Nama universitas harus berupa teks',
            'nama_universitas.max' => 'Nama universitas maksimal 255 karakter',
            'nama_universitas.unique' => 'Universitas ini sudah terdaftar atau dalam proses pendaftaran',

            'nama_fakultas.required' => 'Nama fakultas wajib diisi',
            'nama_fakultas.string' => 'Nama fakultas harus berupa teks',
            'nama_fakultas.max' => 'Nama fakultas maksimal 255 karakter',

            'nama_prodi.required' => 'Nama prodi wajib diisi',
            'nama_prodi.string' => 'Nama prodi harus berupa teks',
            'nama_prodi.max' => 'Nama prodi maksimal 255 karakter',
            'nama_prodi.regex' => 'Format nama prodi tidak valid. Gunakan format: Gelar - Nama Prodi. Contoh: S1 - Ilmu Komputer',

            'is_aptikom.required' => 'Status Aptikom wajib dipilih',
            'is_aptikom.in' => 'Status Aptikom tidak valid',

            'posisi.required' => 'Posisi wajib diisi',
            'posisi.string' => 'Posisi harus berupa teks',
            'posisi.max' => 'Posisi maksimal 255 karakter',

            'nomor_telepon_universitas.required' => 'Nomor telepon universitas wajib diisi',
            'nomor_telepon_universitas.regex' => 'Format nomor telepon universitas tidak valid',

            'alamat_kontak_universitas.required' => 'Alamat kontak universitas wajib diisi',
            'alamat_kontak_universitas.string' => 'Alamat kontak universitas harus berupa teks',

            'website.required' => 'Website wajib diisi',
            'website.url' => 'Format website tidak valid. Contoh format yang benar: http://contoh.com',

            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email ini sudah terdaftar atau dalam proses pendaftaran',

            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',

            'surat_tugas.required' => 'Surat tugas wajib diupload',
            'surat_tugas.file' => 'Surat tugas harus berupa file',
            'surat_tugas.mimes' => 'Format surat tugas harus PDF',
            'surat_tugas.max' => 'Ukuran surat tugas maksimal 2MB'
        ];

        $request->validate([
            'gelar_depan' => 'nullable|string|max:50',
            'nama_lengkap' => 'required|string|max:255',
            'gelar_belakang' => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:Pria,Wanita',
            'nomor_telepon' => 'required|regex:/^08[1-9][0-9]{6,11}$/',
            'nama_universitas' => [
                'required',
                'string',
                'max:255',
                // Pastikan nama universitas tidak ada dalam proses pendaftaran aktif
                Rule::unique('register_universitas')->where(function ($query) {
                    return $query->whereIn('status', ['pending', 'approved']);
                }),
                Rule::unique('universitas', 'nama'),
            ],
            'nama_fakultas' => 'required|string|max:255|regex:/^[\p{L}\p{N}\s\-\&\.\,\']+$/u',
            'nama_prodi' => 'required|string|max:255|regex:/^[\p{L}0-9\.\-\/\s]+ \- [\p{L}0-9\s]+$/u',
            'is_aptikom' => 'required|in:0,1',
            'posisi' => 'required|string|max:255',
            'nomor_telepon_universitas' => 'required|regex:/^[0-9]{6,15}$/',
            'alamat_kontak_universitas' => 'required|string',
            'website' => 'required|url',
            'email' => [
                'required',
                'email',
                // Pastikan email tidak ada dalam proses pendaftaran aktif
                Rule::unique('register_universitas')->where(function ($query) {
                    return $query->whereIn('status', ['pending', 'approved']);
                }),
                Rule::unique('users', 'email'),
            ],
            'password' => 'required|min:8|confirmed',
            'surat_tugas' => 'required|file|mimes:pdf|max:2048',
        ], $messages);

        // Hash password before saving
        $hashedPassword = Hash::make($request->password);

        // Upload surat tugas
        $suratTugasPath = $request->file('surat_tugas')->store('surat_tugas', 'public');

        // Hitung jumlah percobaan
        $attempts = $existingRegistration ? $existingRegistration->registration_attempts + 1 : 1;

        // Simpan data ke database
        RegisterUniversitas::create([
            'gelar_depan' => $request->gelar_depan,
            'nama_lengkap' => $request->nama_lengkap,
            'gelar_belakang' => $request->gelar_belakang,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nomor_telepon' => $request->nomor_telepon,
            'nama_universitas' => $request->nama_universitas,
            'nama_fakultas' => $request->nama_fakultas,
            'nama_prodi' => $request->nama_prodi,
            'is_aptikom' => (int) $request->is_aptikom,
            'posisi' => $request->posisi,
            'nomor_telepon_universitas' => $request->nomor_telepon_universitas,
            'alamat_kontak_universitas' => $request->alamat_kontak_universitas,
            'website' => $request->website,
            'email' => $request->email,
            'password' => $hashedPassword,
            'surat_tugas' => $suratTugasPath,
            'registration_attempts' => $attempts
        ]);

        return redirect()->back()->with('success', 'Pendaftaran berhasil dikirim. Kami akan menghubungi Anda melalui Email. Jika tidak ditemukan di kotak masuk, mohon periksa folder spam/junk email Anda.');
    }

    public function list()
    {
        $registerUniversitass = RegisterUniversitas::all();
        return view('register-universitas.list', compact('registerUniversitass'));
    }

    public function show($id)
    {
        $ids = Crypt::decrypt($id);
        $register = RegisterUniversitas::findOrFail($ids);
        return view('register-universitas.show', compact('register'));
    }

    public function viewPdf($id)
    {
        $registerId = Crypt::decrypt($id);
        $register = RegisterUniversitas::findOrFail($registerId);

        $relativePath = ltrim(str_replace(['storage/', 'public/'], '', (string) $register->surat_tugas), '/');
        $path = storage_path('app/public/' . $relativePath);

        if (!is_file($path)) {
            abort(404, 'File surat tugas tidak ditemukan.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
        ]);
    }

    public function approve($encryptedId)
    {
        try {
            DB::beginTransaction();

            $id = decrypt($encryptedId);
            $register = RegisterUniversitas::findOrFail($id);

            Log::info("Starting approval process for university: " . $register->nama_universitas);

            Log::info('Mengecek status pendaftaran saat ini: ' . $register->status);
            if ($register->status === 'approved') {
                return redirect()->back()->with('failed', 'Pendaftaran sudah disetujui sebelumnya.');
            }

            // Validasi email unik
            Log::info('Mengecek apakah email sudah ada di tabel users: ' . $register->email);
            if (User::where('email', $register->email)->exists()) {
                return redirect()->back()->with('failed', 'Email sudah terdaftar dalam sistem.');
            }

            Log::info("Creating university record");
            $universitas = Universitas::create([
                'nama' => $register->nama_universitas,
            ]);

            // Membuat tema
            Theme::create([
                'universitas_id' => $universitas->id,
            ]);

            $fakultas = Fakultas::create([
                'nama' => $register->nama_fakultas,
                'id_universitas' => $universitas->id
            ]);

            Log::info("Creating prodi record");
            $prodi = Prodi::create([
                'nama' => $register->nama_prodi,
                'id_fakultas' => $fakultas->id,
                'is_aptikom' => (bool) $register->is_aptikom,
            ]);

            // Format nama dengan pengecekan gelar
            $name = trim(($register->gelar_depan ? $register->gelar_depan . ' ' : '') .
                $register->nama_lengkap .
                ($register->gelar_belakang ? ', ' . $register->gelar_belakang : ''));

            // Membuat user
            Log::info("Creating user record");
            $user = User::create([
                'name' => $name,
                'email' => $register->email,
                'password' => $register->password,
                'img' => 'User-Profile.png',
                'id_universitasUser' => $universitas->id,
                'id_fakultasUser' => $fakultas->id,
                'id_prodiUser' => $prodi->id,
            ]);

            Log::info("Creating UserOtoritas record");
            UserOtoritas::create([
                'user_id' => $user->id,
                'otoritas' => 'Admin Universitas',
                'active' => true  // Set sebagai otoritas aktif karena ini otoritas pertama
            ]);

            // Update status
            Log::info("Updating RegisterUniversitas record");
            $register->update([
                'status' => 'approved',
                'user_id' => $user->id
            ]);

            // Log aktivitas
            Log::info("Pendaftaran Universitas Disetujui: " . $register->nama_universitas . " oleh " . auth()->user()->name);

            // Mengirim email
            try {
                Log::info("Attempting to send email to: " . $register->email);

                $emailData = [
                    'name' => $name,
                    'email' => $register->email,
                    'otoritas' => 'Admin Universitas',
                    'nama_universitas' => $register->nama_universitas
                ];
                Log::info("Email data prepared:", $emailData);

                Mail::to($register->email)->send(new UniversityRegistrationApproved($register, $emailData));
                Log::info("Email berhasil terkirim");
            } catch (\Exception $mailException) {
                Log::error("Email error details:", [
                    'message' => $mailException->getMessage(),
                    'file' => $mailException->getFile(),
                    'line' => $mailException->getLine()
                ]);
                return redirect()->back()->with('warning', 'Pendaftaran berhasil, tetapi email gagal dikirim: ' . $mailException->getMessage());
            }

            DB::commit();
            Log::info("Approval process completed successfully");

            return redirect()->back()->with('success', 'Pendaftaran berhasil disetujui dan email telah dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in approval process: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('failed', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $register = RegisterUniversitas::findOrFail($id);

            $request->validate(['reason' => 'required|string|max:1000']);

            if ($register->status === 'approved') {
                return redirect()
                    ->route('admin.list-registrasi-universitas')
                    ->with('failed', 'Tidak dapat menolak pendaftaran yang sudah disetujui.'); // Menggunakan 'failed' agar konsisten
            }

            // 1. Selesaikan dan commit transaksi database TERLEBIH DAHULU
            DB::transaction(function () use ($register, $request) {
                $register->status = 'rejected';
                $register->rejected_reason = $request->reason;
                $register->rejected_at = now();
                $register->rejected_by = auth()->id();
                $register->save();

                Log::info("Pendaftaran Universitas Ditolak", [
                    'university' => $register->nama_universitas,
                    'rejected_by' => auth()->user()->name,
                    'reason' => $request->reason
                ]);
            });

            // 2. Kirim email SETELAH transaksi database
            try {
                $name = trim(($register->gelar_depan ? $register->gelar_depan . ' ' : '') .
                    $register->nama_lengkap .
                    ($register->gelar_belakang ? ', ' . $register->gelar_belakang : ''));

                Mail::to($register->email)->send(new UniversityRegistrationRejected($register, $name, $request->reason));

            } catch (\Exception $mailException) {
                Log::error('Email penolakan gagal terkirim', ['error' => $mailException->getMessage()]);
                // Redirect dengan pesan warning, tapi data penolakan sudah aman di database
                return redirect()
                    ->route('admin.list-registrasi-universitas')
                    ->with('warning', 'Pendaftaran berhasil ditolak, tetapi notifikasi email gagal dikirim.');
            }

            return redirect()
                ->route('admin.list-registrasi-universitas')
                ->with('success', 'Pendaftaran telah berhasil ditolak.');

        } catch (\Exception $e) {
            // Blok catch utama jika terjadi error tak terduga
            Log::error('Error saat menolak pendaftaran universitas: ' . $e->getMessage());

            // Tambahkan pesan error untuk user
            return redirect()
                ->route('admin.list-registrasi-universitas')
                ->with('failed', 'Terjadi kesalahan sistem saat mencoba menolak pendaftaran.');
        }
    }
}
