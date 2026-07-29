<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RpsPublicController;
use App\Http\Controllers\Admin\MkController;
use App\Http\Controllers\Admin\CplController;
use App\Http\Controllers\Admin\RpsController;
use App\Http\Controllers\Admin\CpmkController;
use App\Http\Controllers\Admin\SoalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CplmkController;
use App\Http\Controllers\Admin\ProdiController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Admin\FakultasController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KurikulumController;
use App\Http\Controllers\Dosen\ActivitiesController;
use App\Http\Controllers\Admin\UniversitasController;
use App\Http\Controllers\Dosen\VisualisasiController;
use App\Http\Controllers\PenjaminMutu\ProfilController;
use App\Http\Controllers\RegisterUniversitasController;
use App\Http\Controllers\RegisterMahasiswaController;
use App\Http\Controllers\Dosen\RPScontroller as RPSdosen;
use App\Http\Controllers\PenjaminMutu\ProfilCplController;
use App\Http\Controllers\Dosen\CPMKcontroller as CPMKdosen;
use App\Http\Controllers\Dosen\RubrikController as RubrikDosen;
use App\Http\Controllers\Dosen\SoalController as soalDosen;
use App\Http\Controllers\PenjaminMutu\BKController as BKPM;
use App\Http\Controllers\PenjaminMutu\MKController as MKPM;
use App\Http\Controllers\Dosen\CPLMKcontroller as CPLMKdosen;
use App\Http\Controllers\PenjaminMutu\CPLController as CPLPM;
use App\Http\Controllers\PenjaminMutu\SoalController as SoalPM;
use App\Http\Controllers\PenjaminMutu\UserController as UserPM;
use App\Http\Controllers\Dosen\KomponenController as Jenisdosen;
use App\Http\Controllers\Dosen\DashboardController as DashboardDosen;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PenjaminMutu\AsesmenController as AsesmenPM;
use App\Http\Controllers\PenjaminMutu\CPLCPMKController as CPLCPMKPM;
use App\Http\Controllers\PenjaminMutu\DashboardController as DashboardPM;
use App\Http\Controllers\PenjaminMutu\ProfilPdfController;
use App\Http\Controllers\PenjaminMutu\VisualisasiController as PenjaminMutuVisualisasiController;
use App\Http\Controllers\PenjaminMutu\RpsController as RpsPM;
use App\Http\Controllers\PenjaminMutu\RubrikController as RubrikPM;
use App\Http\Controllers\SocialiteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/admin/print-rps/{id}', [RpsDosen::class, 'print']);

// Landing Pages
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::resource('berita', BeritaController::class)->only(['index', 'show'])->parameters(['berita' => 'berita']);

Route::controller(RpsPublicController::class)->group(function(){
    Route::get('/rps','index')->name('rps.index');
    Route::get('rps/download/{id}','download')->name('rps.download');
});

Route::controller(RegisterUniversitasController::class)->group(function () {
    Route::get('register', 'index')->name('register-universitas.index');
    Route::post('register', 'store')->name('register-universitas.store');
});

Route::controller(RegisterMahasiswaController::class)->group(function () {
    Route::get('/register-mahasiswa', 'index')->name('register-mahasiswa.index');
    Route::post('/register-mahasiswa', 'store')->name('register-mahasiswa.store');
});


// ROUTE UNTUK LOGIN (UNTUK TAMU)
Route::get('/login/google', [SocialiteController::class, 'redirectToGoogle'])->name('google.login');

// ROUTE UNTUK CALLBACK TUNGGAL (UNTUK SEMUA)
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::middleware(['auth'])->group(function () {
        // ROUTE UNTUK CONNECT (UNTUK USER LOGIN)
        Route::get('/connect/google', [SocialiteController::class, 'connectToGoogle'])->name('google.connect');

        // PROFILE
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/edit-password/{id}', 'password');
            Route::put('/edit-pp/{id}', 'pp');
            Route::put('/edit-name/{id}', 'name');
            Route::post('/upload-ttd', 'uploadTtd')->name('profile.upload-ttd');
            Route::post('/switch-otoritas', 'switchOtoritas')->name('switch-otoritas');
            Route::post('/switch-prodi', 'switchProdi')->name('profile.switch-prodi');
            Route::post('/auth/google/disconnect', 'disconnectGoogle')->name('google.disconnect');
        });

        // SESSION HANDLING (Untuk redirect ke dashboard sesuai role)
        Route::middleware(['other'])->name('dashboard')->get('/dashboard', fn() => view('dashboard'));

        Route::controller(FilterController::class)->group(function () {
            Route::get('get-faculties/{universitas_id}', 'getFaculties')->name('get-faculties');
            Route::get('get-programs/{fakultas_id}', 'getPrograms')->name('get-programs');
        });

        // Define roles that will use common routes
        $adminRoles = ['Admin', 'Admin Universitas'];

        // Define academic roles that use the common routes
        $academicRoles = ['Wakil Rektor', 'Wakil Dekan', 'Dosen'];

        // Common routes for admin roles
        $adminCommonRoutes = function () {
            // DASHBOARD
            Route::controller(DashboardController::class)->group(function () {
                Route::get('dashboard', 'index')->name('home');
                Route::get('dashboard-chart/{id}', 'chart')->name('chart');
                Route::get('dashboard-card/{id}', 'card')->name('card');
            });

            // USER management
            Route::controller(UserController::class)->group(function () {
                Route::get('add-user', 'create')->name('add-user');
                Route::post('add-user', 'store')->name('store-user');
                Route::get('list-user', 'list')->name('list-user');
                Route::put('reset-user/{id}', 'reset')->name('reset-user');
                Route::delete('delete-user/{id}', 'delete')->name('delete-user');
                Route::get('edit-user/{id}', 'edit')->name('edit-user');
                Route::put('edit-user/{id}', 'update')->name('update-user');
                Route::post('add-user-wfile', 'create_wfile')->name('store-user-wfile');
                Route::get('get-fakultas/{universitas_id}', 'getFakultas');
                Route::get('get-prodi/{fakultas_id}', 'getProdi');
            });

            Route::get('list-kurikulum', [KurikulumController::class, 'list'])->name('list-kurikulum');
            Route::get('list-cpl', [CplController::class, 'list'])->name('list-cpl');
            Route::get('list-cplmk', [CplmkController::class, 'list'])->name('list-cplmk');
            Route::get('list-mk', [MkController::class, 'list'])->name('list-mk');
            Route::get('list-rps', [RpsController::class, 'list'])->name('list-rps');
            Route::get('list-cpmk', [CpmkController::class, 'list'])->name('list-cpmk');

            // SOAL management
            Route::controller(SoalController::class)->group(function () {
                Route::get('list-soal', 'list')->name('list-soal');
                Route::get('print-soal/{id}', 'print')->name('print-soal');
                Route::get('soal-chart/{id}', 'chart_soal')->name('chart_soal');
                Route::get('summary-soal', 'summary')->name('summary-soal');
            });

            Route::controller(FakultasController::class)->group(function () {
                Route::get('add-fakultas', 'Add')->name('add-fakultas');
                Route::post('add-fakultas', 'Store')->name('store-fakultas');
                Route::get('list-fakultas', 'List')->name('list-fakultas');
                Route::delete('delete-fakultas/{id}', 'Delete')->name('delete-fakultas');
            });

            Route::controller(ProdiController::class)->group(function () {
                Route::get('add-Prodi', 'Add')->name('add-prodi');
                Route::post('add-Prodi', 'Store')->name('store-prodi');
                Route::get('list-Prodi', 'List')->name('list-prodi');
                Route::delete('delete-Prodi/{id}', 'Delete')->name('delete-prodi');
            });
        };

        // Common academic routes for Wakil Rektor, Wakil Dekan, Kepala Program Studi, and Dosen
        $academicCommonRoutes = function () {
            // Dashboard routes
            Route::controller(DashboardDosen::class)->group(function () {
                Route::get('dashboard', 'list')->name('home');
                Route::get('dashboard-chart', 'chart')->name('chart');
            });

            Route::get('/get-pustaka-by-mk/{kode_mk}', [RPSdosen::class, 'getPustakaByMk'])->name('get-pustaka-by-mk');
            
            // RPS routes
            Route::controller(RPSdosen::class)->group(function () {
                Route::get('rps/list-rps', 'List')->name('rps-list');
                Route::get('rps/print-rps/{id}', 'Print')->name('rps-print');
            });

            // CPLMK routes
            Route::controller(CPLMKdosen::class)->group(function () {
                Route::get('cplmk/list-cplmk', 'List')->name('cplmk-list');
            });

            // CPMK routes
            Route::controller(CPMKdosen::class)->group(function () {
                Route::get('cpmk/list-cpmk', 'List')->name('cpmk-list');
            });

            // Activities routes
            Route::controller(ActivitiesController::class)->group(function () {
                Route::get('activities/list-activity', 'List')->name('activities-list');
                Route::get('/activities-data', 'getActivitiesData')->name('activities-data');
            });

            // Jenis routes
            Route::controller(Jenisdosen::class)->group(function () {
                Route::get('Jenis/list-Jenis', 'List')->name('Jenis-list');
            });

            Route::controller(RubrikDosen::class)->group(function () {
                Route::get('rubrik/list-rubrik', 'List')->name('rubrik-list');

                // opsional tapi kepake buat role non-dosen
                Route::get('rubrik/preview/{id}', 'Preview')->name('rubrik-preview');
                Route::get('rubrik/download/{id}', 'download')->name('rubrik-download');
            });

            // Soal routes
            Route::controller(soalDosen::class)->group(function () {
                Route::get('soal/list-soal', 'list')->name('soal-list');
                Route::get('print-soal/{id}', 'print');
                Route::get('soal/cetakSoal/{id}', 'cetakSoal')->name('cetakSoal');
                //Data import/export
                Route::get('exportmutu', 'mutuexport')->name('exportmutu');
                Route::post('importmutu', 'mutuimport')->name('importmutu');
                Route::post('importTanpaSoal', 'importTanpaSoal')->name('importTanpaSoal');
                Route::get('/tanpa-soal', [App\Http\Controllers\Dosen\SoalController::class, 'listTanpaSoal'])->name('tanpa-soal-list');

                Route::get('TanpaSoal', 'import1')->name('TanpaSoal');
                Route::get('import-mutu', 'import')->name('import-mutu');
                Route::get('filter', 'filter')->name('filter');
                Route::get('filterSoal', 'filterSoal')->name('filterSoal');
                Route::get('excel', 'excel')->name('excel');
                Route::get('ExcelTanpaSoal', 'ExcelTanpaSoal')->name('ExcelTanpaSoal');
                Route::get('getCPLBykode_mk', 'getCPLBykode_mk')->name('getCPLBykode_mk');
                // Route::get('getCPMKBykode_mk', 'getCPMKBykode_mk')->name('getCPMKBykode_mk');

            });

            // Visualization routes
            Route::controller(VisualisasiController::class)->group(function () {
                // Mahasiswa visualization
                Route::get('visual-mahasiswa', 'index')->name('visual-mahasiswa');
                // Route::get('getAngkatanByUniversitas', 'getAngkatanByUniversitas')->name('getAngkatanByUniversitas');
                // Route::get('getProdiByUniversitas', 'getProdiByUniversitas')->name('getProdiByUniversitas');
                Route::get('getNpmByAngkatan', 'getNpmByAngkatan')->name('getNpmByAngkatan');
                Route::get('getPemetaanCpl', 'getPemetaanCpl')->name('getPemetaanCpl');
                Route::post('hasilvisual-mahasiswa', 'hasilVisualMahasiswa')->name('hasilvisual-mahasiswa');
                Route::any('hasilvisualcpmk-mahasiswa', 'hasilVisualCpmkMahasiswa')->name('hasilvisualcpmk-mahasiswa');

                // Angkatan visualization
                Route::get('visual-mahasiswaAngkatan', 'indexAngkatan')->name('visual-mahasiswaAngkatan');
                Route::post('hasilvisual-mahasiswaAngkatan', 'hasilVisualMahasiswaAngkatan')->name('hasilvisual-mahasiswaAngkatan');
                Route::get('getAngkatanByProdiUniversitas', 'getAngkatanByProdiUniversitas')->name('getAngkatanByProdiUniversitas');
                Route::any('hasilvisualcpmk-angkatan', 'hasilVisualCpmkAngkatan')->name('hasilvisualcpmk-angkatan');
                Route::get('getAllAngkatanCpmk', 'getAllAngkatanCpmk')->name('getAllAngkatanCpmk');
                Route::get('getAllNpmByAngkatan', 'getAllNpmByAngkatan')->name('getAllNpmByAngkatan');

                // Mata Kuliah visualization
                Route::get('visual-mahasiswaMataKuliah', 'indexMataKuliah')->name('visual-mahasiswaMataKuliah');
                Route::get('getCourseByProdi', 'getCourseByProdi')->name('getCourseByProdi');
                Route::post('hasilvisual-mahasiswaMataKuliah', 'hasilVisualMahasiswaMataKuliah')->name('hasilvisual-mahasiswaMataKuliah');
                Route::get('getNamaByNpm', 'getNamaByNpm')->name('getNamaByNpm');

                // generate PDF
                Route::post('/generate-pdf-visualisasi-mahasiswa', 'generatePDFhasilVisualMahasiswa')->name('generate-pdfVisualMahasiswa');
                Route::post('/generate-pdf-visualisasi-angkatan', 'generatePDFhasilVisualAngkatan')->name('generate-pdfVisualAngkatan');
                Route::post('/generate-pdf-visualisasi-matakuliah', 'generatePDFhasilVisualMataKuliah')->name('generate-pdfVisualMataKuliah');
                Route::post('/generate-pdf-visualisasi-cpmk-mahasiswa', 'generatePDFhasilVisualCPMKMahasiswa')->name('generate-pdfVisualCPMKMahasiswa');
                Route::post('/generate-pdf-visualisasi-cpmk-angkatan', 'generatePDFhasilVisualCPMKAngkatan')->name('generate-pdfVisualCPMKAngkatan');
            });

            // Profile routes
            Route::controller(ProfilController::class)->group(function () {
                Route::get('indexListProfil', 'indexListProfil')->name('indexListProfil');
                Route::get('readListProfil', 'readListProfil')->name('readListProfil');
                Route::get('index-profil-mk', [ProfilController::class, 'indexProfilMK'])->name('indexProfilMK');
                Route::get('generate-pdf-profil-mk', [ProfilPdfController::class, 'generatePDFProfilMK'])->name('generatePDFProfilMK');
            });

            Route::controller(ProfilCplController::class)->group(function () {
                Route::get('indexkompetensi', 'indexkompetensi')->name('indexkompetensi'); 
                Route::get('readListProfilCpl', 'readListProfilCpl')->name('readListProfilCpl');
            });
        };

        // Register admin role routes
        foreach ($adminRoles as $role) {
            $prefix = strtolower(str_replace(' ', '-', $role));
            Route::middleware("cekrole:$role")->prefix($prefix)->name("$prefix.")->group(function () use ($adminCommonRoutes, $prefix) {
                $adminCommonRoutes();

                if ($prefix === 'admin') {
                    // Admin-specific routes
                    Route::resource('berita', BeritaController::class)->parameters(['berita' => 'berita']);

                    // REGISTRASI UNIVERSITAS
                    Route::controller(RegisterUniversitasController::class)->group(function () {
                        Route::get('list-registrasi-universitas', 'list')->name('list-registrasi-universitas');
                        Route::get('show-registrasi-universitas/{id}', 'show')->name('show-registrasi-universitas');
                        Route::get('edit-registrasi-universitas/{id}', 'edit')->name('edit-registrasi-universitas');
                        Route::get('view-pdf/{id}', 'viewPdf')->name('view.pdf');
                        Route::put('register-universitas/{id}/approve', 'approve')->name('register-universitas.approve');
                        Route::put('register-universitas/{id}/reject', 'reject')->name('register-universitas.reject');
                    });

                    Route::controller(UniversitasController::class)->group(function () {
                        Route::get('add-universitas', 'Add')->name('add-universitas');
                        Route::post('add-universitas', 'Store')->name('store-universitas');
                        Route::get('list-Universitas', 'List')->name('list-universitas');
                        Route::delete('delete-Universitas/{id}', 'Delete')->name('delete-universitas');
                    });
                } else if ($prefix === 'admin-universitas') {
                    // Admin Universitas specific routes
                    Route::prefix('theme')->name('theme.')->group(function () {
                        Route::get('edit', [ThemeController::class, 'edit'])->name('edit');
                        Route::post('update', [ThemeController::class, 'update'])->name('update');
                    });

                    // Kurikulum routes
                    Route::controller(KurikulumController::class)->group(function () {
                        Route::post('add-kurikulum', 'store')->name('add-kurikulum');
                        Route::put('update-kurikulum/{kurikulum}', 'update')->name('update-kurikulum');
                        Route::delete('delete-kurikulum/{kurikulum}', 'delete')->name('delete-kurikulum');
                        Route::get('enter-edit-mode/{kurikulum}', 'enterEditMode')->name('enter-edit-mode');
                        Route::get('cancel-edit', 'cancelEdit')->name('cancel-edit');
                    });

                    // CPL
                    Route::controller(CplController::class)->group(function () {
                        Route::get('add-cpl', 'create')->name('add-cpl');
                        Route::post('add-cpl', 'store')->name('store-cpl');
                        Route::get('list-cpl', 'list')->name('list-cpl');
                        Route::get('edit-cpl/{id}', 'edit')->name('edit-cpl');
                        Route::put('edit-cpl/{id}', 'update')->name('update-cpl');
                        Route::delete('delete-cpl/{id}', 'delete')->name('delete-cpl');
                        Route::get('/get-prodi/{fakultas_id}', 'getProdi');
                        Route::get('/get-kurikulum/{prodi_id}', 'getKurikulum');
                    });

                    Route::controller(MkController::class)->group(function () {
                        Route::get('add-mk', 'create')->name('add-mk');
                        Route::post('add-mk', 'store')->name('store-mk');
                        Route::get('edit-mk/{kode}', 'edit')->name('edit-mk');
                        Route::put('edit-mk/{kode}', 'update')->name('update-mk');
                        Route::delete('delete-mk/{id}', 'delete')->name('delete-mk');

                        Route::get('get-prodi/{fakultas_id}', 'getProdi')->name('get-prodi');
                        Route::get('get-kurikulum/{prodi_id}', 'getKurikulum')->name('get-kurikulum');
                        Route::get('get-mk-prasyarat/{prodi_id}', 'getMkPrasyarat')->name('get-mk-prasyarat');
                    });

                    Route::controller(CplmkController::class)->group(function () {
                        Route::get('add-cplmk', 'create')->name('add-cplmk');
                        Route::post('add-cplmk', 'store')->name('store-cplmk');
                        Route::get('edit-cplmk/{id}', 'edit')->name('edit-cplmk');
                        Route::put('edit-cplmk/{id}', 'update')->name('update-cplmk');
                        Route::delete('delete-cplmk/{id}', 'delete')->name('delete-cplmk');
                    });

                    Route::controller(RpsController::class)->group(function () {
                        Route::get('add-rps', 'create')->name('add-rps');
                        Route::post('add-rps', 'store')->name('store-rps');
                        // Route::get('edit-rps/{id}', 'edit')->name('edit-rps');
                        // Route::put('edit-rps/{id}', 'update')->name('update-rps');
                        Route::delete('delete-rps/{id}', 'delete')->name('delete-rps');
                        Route::post('add-rps-wfile', 'create_wfile')->name('add-rps-wfile');
                    });

                    Route::controller(CpmkController::class)->group(function () {
                        Route::get('add-cpmk', 'create')->name('add-cpmk');
                        Route::post('add-cpmk', 'store')->name('store-cpmk');
                        Route::get('edit-cpmk/{id}', 'edit')->name('edit-cpmk');
                        Route::put('edit-cpmk/{id}', 'update')->name('update-cpmk');
                        Route::delete('delete-cpmk/{id}', 'delete')->name('delete-cpmk');
                        Route::get('/get-cpl-by-kurikulum/{kurikulum_id}','getCplbyKurkulum')->name('getCplByKurikulum');
                    });
                }
            });
        }

        // Register academic role routes
        foreach ($academicRoles as $role) {
            $prefix = strtolower(str_replace(' ', '-', $role));
            Route::middleware("cekrole:$role")->prefix($prefix)->name("$prefix.")->group(function () use ($academicCommonRoutes, $prefix) {
                $academicCommonRoutes();

                if($prefix != 'dosen') {
                    Route::resource('mahasiswa', MahasiswaController::class)->only(['index']);
                } else {
                    Route::controller(RPSdosen::class)->group(function () {
                        Route::get('rps/add-rps', 'Add')->name('rps-add');
                        Route::post('rps/add-rpsStep1', 'StoreRpsStep1');
                        Route::get('rps/addRpsStep2', 'AddRpsStep2')->name('addRpsStep2');
                        Route::post('rps/add-rpsStep2', 'StoreRpsStep2');
                        Route::get('rps/addRpsStep3', 'AddRpsStep3')->name('addRpsStep3');
                        Route::post('rps/add-rpsStep3', 'StoreRpsStep3');
                        Route::get('rps/addRpsStep4', 'AddRpsStep4')->name('addRpsStep4');
                        Route::post('rps/add-rpsStep4', 'StoreRpsStep4');
    
                        Route::post('rps/add-rps', 'Store')->name('rps-store'); 
                        Route::put('rps/edit-rps/{id}', 'Update')->name('rps-update');
                        Route::delete('rps/delete-rps/{id}', 'Delete');
                        Route::get('rps/pustaka', 'getPustaka')->name('pustaka-get');
                        Route::post('rps/submit-validation/{id}', 'submitValidation')->name('rps-submit-validation');
                    });

                    Route::controller(CPLMKdosen::class)->group(function () {
                        Route::get('cplmk/add-cplmk', 'Add')->name('cplmk-add');
                        Route::post('cplmk/add-cplmk', 'Store')->name('cplmk-store');
                        Route::delete('cplmk/delete-cplmk/{id}', 'Delete')->name('cplmk-delete');
                    });
                    
                    Route::controller(CPMKdosen::class)->group(function () {
                        Route::get('cpmk/add-cpmk', 'Add')->name('cpmk-add');
                        Route::post('cpmk/add-cpmk', 'Store')->name('cpmk-store');
                        Route::get('cpmk/edit-cpmk/{id}', 'Edit')->name('cpmk-edit');;
                        Route::put('cpmk/edit-cpmk/{id}', 'Update');
                        Route::delete('cpmk/delete-cpmk/{id}', 'Delete')->name('cpmk-delete');
                        Route::delete('/sub-cpmk/delete/{id}', 'hapusSubCpmk')->name('sub-cpmk-delete');
                    });

                    Route::controller(ActivitiesController::class)->group(function () {
                        Route::get('rps/list-rps/detail/{id}','listByRps')->name('rps-detail');
                        Route::get('activities/add-activity', 'Add')->name('activities-add');
                        Route::post('activities/add-activity', 'Store')->name('activities-store');
                        Route::post('activities/add-activity-wfile', 'create_wfile')->name('activities-wfile');
                        Route::get('activities/edit-activity/{id}', 'Edit')->name('activity-edit');
                        // Route::put('activities/edit-activity/{id}', 'Update');
                        Route::put('activities/edit-activity/{id}', 'Update')->name('activity-update');
                        Route::delete('activities/delete-activity/{id}', 'Delete')->name('activity-delete');
                        Route::get('/activities-data', 'getActivitiesData')->name('activities-data');
                        Route::get('/sub-cpmk-by-cpmk/{cpmkId}', 'getSubCpmkByCpmk')->name('subcpmk-by-cpmk');
                    });

                    Route::controller(Jenisdosen::class)->group(function () {
                        Route::get('Jenis/add-Jenis', 'Add')->name('Jenis-add');
                        Route::post('Jenis/add-Jenis', 'Store')->name('Jenis-store');
                        Route::delete('Jenis/delete-Jenis/{id}', 'Delete')->name('Jenis-delete');
                    });

                    Route::controller(RubrikDosen::class)->group(function () {
                        Route::get('rubrik/add-rubrik', 'Add')->name('rubrik-add');
                        Route::post('rubrik/add-rubrik', 'Store')->name('rubrik-store');

                        // kalau nanti ada upload rubrik per metode
                        Route::post('rubrik/upload/{id}', 'Upload')->name('rubrik-upload');
                        Route::get('rubrik/jenis/{mkKode}', 'getJenisRubrikByMk')->name('rubrik-jenis');
                        Route::post('rubrik-preview', 'previewRubrik')->name('rubrik.preview');
                        Route::get('rubrik/list-rubrik', 'List')->name('rubrik-list');
                        Route::get('rubrik/download/{id}', 'download')->name('rubrik-download');
                        Route::delete('rubrik/delete/{id}', 'destroy')->name('rubrik-delete');
                    });

                    Route::controller(soalDosen::class)->group(function () {
                        Route::get('soal/add-soal', 'Add')->name('soal-add');
                        Route::post('soal/add-soal', 'Store')->name('soal-store');
                        Route::get('soal/addRaw-soal', 'addRaw')->name('soal-addRaw');
                        Route::post('soal/addRaw-soal', 'Store')->name('rawSoal-store');
                        Route::get('soal/edit-soal/{id}', 'Edit');
                        Route::put('soal/edit-soal/{id}', 'Update');
                        Route::delete('soal/delete-soal/{id}', 'Delete')->name('soal-delete');
                        Route::post('soal/ajukan/{id}', 'ajukanSoal')->name('soal-ajukan');
                        Route::post('tanpa-soal/ajukan/{id}', 'ajukanTanpaSoal')->name('tanpa-soal-ajukan');

    
                        // Route lama (tetap ada di baru)
                        Route::get('add-mutu', 'New')->name('add-mutu');
                        Route::get('/add-raw-tanpa-soal', [App\Http\Controllers\Dosen\SoalController::class, 'addRawTS'])->name('addRawTS');
                        Route::get('getMaxBobotMKJenis/{mk}', 'getMaxKriteriaBobotMKJenis')->name('getMaxBobotMKJenis');
                        Route::get('getCPMKBykode_mk/{cpl}', 'getCPMKBykode_mk')->name('getCPMKBykode_mk');
                        Route::get('getJenisKriteria/{cpl}', 'getJenisKriteria')->name('getJenisKriteria');
                        Route::get('getJenisSoalByKriteria/{jenis}', 'getJenisSoalByKriteria')->name('getJenisSoalByKriteria');
                        Route::get('getJenisSoalByMk/{mk}', 'getJenisSoalByMk')->name('getJenisSoalByMk');

                        // Route lama (tidak ada di baru)
                        Route::get('add-TanpaSoal', 'TanpaSoal')->name('add-TanpaSoal');

                        // Route tambahan (hanya ada di baru)
                        Route::get('tanpa-soal/edit/{id}', 'editTanpaSoal')->name('tanpa-soal.edit');
                        Route::put('tanpa-soal/update/{id}', 'updateTanpaSoal')->name('tanpa-soal.update');
                        Route::delete('tanpa-soal/delete/{id}', 'deleteTanpaSoal')->name('tanpa-soal.delete');
                        Route::post('/store-raw-tanpa-soal', [App\Http\Controllers\Dosen\SoalController::class, 'storeRawTS'])->name('storeRawTS');
                        Route::get('addMutuTanpaSoal', 'TanpaSoal')->name('addMutuTanpaSoal');
                        Route::get('getMetodeByMk', 'getMetodeByMk')->name('getMetodeByMk');
                        Route::get('getCPMKByMetode', 'getCPMKByMetode')->name('getCPMKByMetode');
                        Route::get('/getBobotByCpmk', [SoalController::class, 'getBobotByCpmk'])->name('getBobotByCpmk');
                        Route::get('/tanpa-soal', [App\Http\Controllers\Dosen\SoalController::class, 'listTanpaSoal'])->name('tanpa-soal-list');
                        Route::get('/getPersentaseCpmk', [App\Http\Controllers\Dosen\SoalController::class, 'getPersentaseCpmk'])->name('dosen.getPersentaseCpmk');
                        Route::get('getJenisByMk/{mk?}', 'getJenisByMk')->name('getJenisByMk');
                        Route::get('getSoalByMkJenis', [App\Http\Controllers\Dosen\SoalController::class, 'getSoalByMkJenis'])->name('getSoalByMkJenis');
                        Route::get('getGabunganByMkJenis', 'getGabunganByMkJenis')->name('getGabunganByMkJenis');
                        Route::get('excelGabungan', 'ExcelGabungan')->name('excelGabungan');
                        Route::get('getInstrumenTanpaSoal', 'getInstrumenTanpaSoal')->name('getInstrumenTanpaSoal');
                        Route::get('ExcelTanpaSoal', 'ExcelTanpaSoal')->name('ExcelTanpaSoal');
                    });

                    Route::resource('mahasiswa', MahasiswaController::class);
                }
            });

            
        }

        // Definisikan roles untuk level akses
        $roles = ['universitas', 'fakultas', 'program-studi'];

        foreach ($roles as $role) {
            Route::middleware([$role])->prefix('penjamin-mutu')->name('penjamin-mutu.')->group(function () use ($role) {
                Route::prefix($role)->name($role . '.')->group(function () use ($role) {
                    if($role != 'program-studi') {
                        Route::resource('mahasiswa', MahasiswaController::class)->only(['index']);
                    } else {
                        Route::resource('mahasiswa', MahasiswaController::class);
                    }

                    // Dashboard
                    Route::get('dashboard', [DashboardPM::class, 'list'])->name('home');
                    Route::get('dashboard-chart', [DashboardPM::class, 'chart'])->name('chart');

                    // Soal
                Route::get('list-soal', [SoalPM::class, 'list'])->name('list-soal');
                Route::post('soal/pesan-mk/{kode_mk}', [SoalPM::class, 'pesanMK'])->name('soal-pesan-mk');

                Route::get('soal/detail/{kode_mk}', [SoalPM::class, 'detail'])->name('soal-detail');
                Route::post('soal/validasi-mk/{kode_mk}', [SoalPM::class, 'validasiMK'])->name('soal-validasi-mk');
                Route::post('soal/tolak-mk/{kode_mk}', [SoalPM::class, 'tolakMK'])->name('soal-tolak-mk');
                Route::get('print-soal/{id}', [SoalPM::class, 'print'])->name('print-soal');
                Route::get('soal/cetakSoal/{id}', [SoalPM::class, 'cetakSoal'])->name('cetakSoal');
                // Route lama dipertahankan untuk kompatibilitas
                Route::post('validasi-soal/{id}', [SoalPM::class, 'validasi'])->name('validasi-soal');
                Route::post('tolak-soal/{id}', [SoalPM::class, 'tolak_validasi'])->name('tolak-soal');

                    Route::get('add-user', [UserPM::class, 'create'])->name('add-user');
                    Route::post('add-user', [UserPM::class, 'store'])->name('store-user');
                    Route::get('list-user', [UserPM::class, 'list'])->name('list-user');
                    Route::put('reset-user/{id}', [UserPM::class, 'reset'])->name('reset-user');
                    Route::delete('delete-user/{id}', [UserPM::class, 'delete'])->name('delete-user');
                    Route::get('edit-user/{id}', [UserPM::class, 'edit'])->name('edit-user');
                    Route::put('edit-user/{id}', [UserPM::class, 'update'])->name('update-user');
                    Route::post('add-wfile-user', [UserPM::class, 'create_wfile'])->name('add-user-wfile');
                    Route::get('get-fakultas/{universitas_id}', [UserPM::class, 'getFakultas']);
                    Route::get('get-prodi/{fakultas_id}', [UserPM::class, 'getProdi']);

                    Route::get('indexListProfil', [ProfilController::class, 'indexListProfil'])->name('indexListProfil');
                    Route::get('indexProfilProfesi', [ProfilController::class, 'indexProfilProfesi'])->name('indexProfilProfesi');
                    Route::get('readListProfil', [ProfilController::class, 'readListProfil'])->name('readListProfil');
                    Route::get('readListProfilProf', [ProfilController::class, 'readListProfilProf'])->name('readListProfilProf');
                    Route::get('indexPemetaanCPMKProf', [ProfilController::class, 'indexPemetaanCPMKProf'])->name('indexPemetaanCPMKProf');
                    Route::get('add-profesi-cpmks', [ProfilController::class, 'addProfesiCpmk'])->name('profesi-cpmk-add');
                    Route::post('store-profesi-cpmk', [ProfilController::class, 'storeProfesiCPMK'])->name('profesi-cpmk-store');
                    Route::get('edit-profesi-cpmk/{id}', [ProfilController::class, 'editProfesiCpmk'])->name('profesi-cpmk-edit');
                    Route::put('update-profesi-cpmk/{id}', [ProfilController::class, 'updateProfesiCpmk'])->name('profesi-cpmk-update');
                    Route::get('get-profesi-by-cpmk/{id}', [ProfilController::class, 'getProfesiByCpmk']);
                    Route::delete('delete-profesi-cpmk/{cpmk}/{profesi}', [ProfilController::class, 'deleteProfesiCpmk'])->name('profesi-cpmk-delete');
                    Route::get('add-profesi', [ProfilController::class, 'createListProfesi'])->name('profesi-add');
                    Route::post('store-profesi', [ProfilController::class, 'storeListProfesi'])->name('profesi-store');
                    Route::get('indexListProfesi', [ProfilController::class, 'indexListProfesi'])->name('indexListProfesi');
                    Route::get('readListProfesi', [ProfilController::class, 'readListProfesi'])->name('readListProfesi');
                    Route::get('createListProfesi', [ProfilController::class, 'createListProfesi'])->name('createListProfesi');
                    Route::get('cpl-cpmk-mk-profesi', [ProfilPdfController::class, 'cplCpmkMkProfesi'])->name('cpl-cpmk.cpl-cpmk-mk-profesi');
                    Route::get('generate-pdf-cpl-cpmk-mk-profesi', [ProfilPdfController::class, 'generatePdfCplCpmkMkProfesi'])->name('generate-pdf-cpl-cpmk-mk-profesi');
                    Route::get('/print-cpl-cpmk-mk-profesi', [ProfilPdfController::class, 'printCplCpmkMkProfesi'])->name('print-cpl-cpmk-mk-profesi');
                    Route::get('/print-profesi-cpmk', [ProfilPdfController::class, 'printProfesiCpmk'])->name('print-profesi-cpmk');
                    Route::get('evaluasi-cpmk', [ProfilController::class, 'evaluasiCpmk'])->name('evaluasi-cpmk');
                    Route::get('generate-pdf-profesi-cpmk', [ProfilPdfController::class, 'generatePDFProfesiCpmk'])->name('generate-pdf-profesi-cpmk');
                    Route::get('/print-profil-mk', [ProfilPdfController::class, 'printProfilMK'])->name('printProfilMK');
                    Route::get('showProfesi/{id}', [ProfilController::class, 'showProfesi'])->name('showProfesi');
                    Route::put('updateProfesi/{id}', [ProfilController::class, 'updateProfesi'])->name('updateProfesi');
                    Route::delete('deleteProfesi/{id}', [ProfilController::class, 'deleteProfesi'])->name('deleteProfesi');
                    Route::get('index-profil-mk', [ProfilController::class, 'indexProfilMK'])->name('indexProfilMK');
                    Route::get('generate-pdf-profil-mk', [ProfilPdfController::class, 'generatePDFProfilMK'])->name('generatePDFProfilMK');
                    Route::get('indexkompetensi', [ProfilCplController::class, 'indexkompetensi'])->name('indexkompetensi');

                    // Profil cpl
                    Route::get('indexListProfilCpl', [ProfilCplController::class, 'indexListProfilCpl'])->name('indexListProfilCpl');
                    Route::get('readListProfilCpl', [ProfilCplController::class, 'readListProfilCpl'])->name('readListProfilCpl');

                    Route::get('createListProfil', [ProfilController::class, 'createListProfil'])->name('createListProfil');
                    Route::get('storeListProfil', [ProfilController::class, 'storeListProfil'])->name('storeListProfil');
                    Route::get('storeListProfilAptikom', [ProfilController::class, 'storeListProfilAptikom'])->name('storeListProfilAptikom');
                    Route::get('showProfil/{id}', [ProfilController::class, 'showProfil']);
                    Route::get('updateProfil/{id}', [ProfilController::class, 'updateProfil']);
                    Route::get('updateProfilAptikom/{id}', [ProfilController::class, 'updateProfilAptikom']);
                    Route::get('deleteProfil/{id}', [ProfilController::class, 'deleteProfil']);

                    if ($role === 'program-studi') {
                        // Penjamin Mutu Program Studi -> Akses Penuh
                        Route::controller(CpmkController::class)->group(function () {
                            Route::get('list-cpmk', 'list')->name('list-cpmk');
                            Route::get('add-cpmk', 'create')->name('add-cpmk');
                            Route::post('add-cpmk', 'store')->name('store-cpmk');
                            Route::get('edit-cpmk/{id}', 'edit')->name('edit-cpmk');
                            Route::put('edit-cpmk/{id}', 'update')->name('update-cpmk');
                            Route::delete('delete-cpmk/{id}', 'delete')->name('delete-cpmk');
                        });
                    }else {
                        // Penjamin Mutu Universitas & Fakultas -> Hanya List
                        Route::get('list-cpmk', [CpmkController::class, 'list'])->name('list-cpmk');
                    }
                    

                    //PROFIL CPL
                    Route::get('createListProfilCpl', [ProfilCplController::class, 'createListProfilCpl'])->name('createListProfilCpl');
                    Route::get('storeListProfilCpl', [ProfilCplController::class, 'storeListProfilCpl'])->name('storeListProfilCpl');
                    Route::get('showProfilCpl/{id}', [ProfilCplController::class, 'showProfilCpl']);
                    Route::get('updateProfilCpl/{id}', [ProfilCplController::class, 'updateProfilCpl']);
                    Route::get('deleteProfilCpl/{id}', [ProfilCplController::class, 'deleteProfilCpl']);

                    // CPL Pages
                    Route::prefix('cpl')->name('cpl.')->group(function () {
                        Route::get('cpl-prodi', [CPLPM::class, 'index'])->name('index');
                        Route::get('add-cpl', [CPLPM::class, 'create'])->name('create');
                        Route::post('add-cpl', [CPLPM::class, 'store'])->name('store');
                        Route::get('edit/{id}', [CPLPM::class, 'edit'])->name('edit');
                        Route::put('edit/{id}', [CPLPM::class, 'update'])->name('update');
                        Route::delete('delete/{id}', [CPLPM::class, 'delete'])->name('delete');

                        Route::get('add-cpl-pl', [CPLPM::class, 'addCPLPL'])->name('cpl-pl-add');
                        Route::post('add-cpl-pl', [CPLPM::class, 'storeCPLPL'])->name('cpl-pl-store');

                        Route::get('pemetaan-cpl-pl', [CPLPM::class, 'indexCPLPL'])->name('cpl-pl');
                        Route::get('pemetaan-cpl-bk', [CPLPM::class, 'indexCPLBK'])->name('cpl-bk');
                        Route::get('add-cpl-bk', [CPLPM::class, 'addCPLBK'])->name('cpl-bk-add');
                        Route::post('add-cpl-bk', [CPLPM::class, 'storeCPLBK'])->name('cpl-bk-store');
                        Route::get('pemetaan-cpl-mk', [CPLPM::class, 'indexCPLMK'])->name('cpl-mk');
                        Route::get('add-cpl-mk', [CPLPM::class, 'addCPLMK'])->name('cpl-mk-add');
                        Route::post('add-cpl-mk', [CPLPM::class, 'storeCPLMK'])->name('cpl-mk-store');
                        Route::get('pemetaan-cpl-bk-mk', [CPLPM::class, 'indexCPLBKMK'])->name('cpl-bk-mk');
                    });

                    // BK Pages
                    Route::prefix('bk')->name('bk.')->group(function () {
                        Route::get('bahan-kajian', [BKPM::class, 'index'])->name('index');
                        Route::get('pemetaan-bk-mk', [BKPM::class, 'indexBKMK'])->name('bk-mk');
                        Route::get('add-bk-mk', [BKPM::class, 'addBKMK'])->name('bk-mk-add');
                        Route::get('get-mk-by-bk/{bk}', [BKPM::class, 'getMkByBk'])->name('mk-by-mk');
                        Route::post('add-bk-mk', [BKPM::class, 'storeBKMK'])->name('bk-mk-store');
                        Route::get('bk-add', [BKPM::class, 'addBK'])->name('bk-add');
                        Route::post('bk-store', [BKPM::class, 'storeBK'])->name('bk-store');
                    });

                    // MK Pages
                    Route::prefix('mk')->name('mk.')->group(function () {
                        Route::get('susunan-mk', [MKPM::class, 'susunanMK'])->name('susunan-mk');
                        Route::get('add-mk', [MKPM::class, 'create'])->name('create');
                        Route::post('add-mk', [MKPM::class, 'store'])->name('store');
                        Route::get('edit-mk/{kode}', [MKPM::class, 'edit'])->name('edit');
                        Route::put('edit-mk/{kode}', [MKPM::class, 'update'])->name('update');
                        Route::delete('delete-mk/{kode}', [MKPM::class, 'delete'])->name('delete');

                        Route::get('organisasi-mk', [MKPM::class, 'organisasiMK'])->name('organisasi-mk');
                        Route::get('pemenuhan-cpl', [MKPM::class, 'pemenuhanCPL'])->name('pemenuhan-cpl');
                    });

                    // CPL CPMK Pages
                    Route::prefix('cpl-cpmk')->name('cpl-cpmk.')->group(function () {
                        Route::get('add-cpl-cpmk-mk', [CPLCPMKPM::class, 'addCPLCPMKMK'])->name('cpl-cpmk-mk-add');
                        Route::get('get-cpmk-by-mk/{mk}', [CPLCPMKPM::class, 'getCPMKByMK'])->name('cpmk-by-mk');
                        Route::post('add-cpl-cpmk-mk', [CPLCPMKPM::class, 'storeCPLCPMKMK'])->name('cpl-cpmk-mk-store');

                        Route::get('pemetaan-cpl-cpmk-mk', [CPLCPMKPM::class, 'indexCPLCPMKMK'])->name('cpl-cpmk-mk');
                        Route::get('pemetaan-cpl-cpmk-mk-semester', [CPLCPMKPM::class, 'indexCPLCPMKMKSMT'])->name('cpl-cpmk-mk-semester');
                        Route::get('pemetaan-cpl-mk-cpmk', [CPLCPMKPM::class, 'indexCPLMKCPMK'])->name('cpl-mk-cpmk');
                        Route::get('pemetaan-mk-cpmk-subcpmk', [CPLCPMKPM::class, 'indexMKCPMKSubCPMK'])->name('mk-cpmk-subcpmk');

                        Route::get('add-cpmk-mk-subcpmk', [CPLCPMKPM::class, 'addCPMKMKSUBCPMK'])->name('cpmk-mk-subcpmk-add');
                        Route::get('get-subcpmk-by-mk/{mk}', [CPLCPMKPM::class, 'getSUBCPMKByMK'])->name('sub-cpmk-by-mk');
                        Route::post('add-cpmk-mk-subcpmk', [CPLCPMKPM::class, 'storeCPMKMKSUBCPMK'])->name('cpmk-mk-subcpmk-store');
                        Route::get('get-cpmk-by-kurikulum/{kurikulum_id}', [CPLCPMKPM::class, 'getCpmkByKurikulum'])->name('cpmk-by-kurikulum');
                        Route::post('add-subcpmk', [CPLCPMKPM::class, 'storeSubCpmk'])->name('subCpmk-store');
                    });

                    // Asesmen
                    Route::prefix('asesmen')->name('asesmen.')->group(function () {
                        Route::get('add-asesmen', [AsesmenPM::class, 'addAsesmen'])->name('asesmen-add');
                        Route::get('get-cpl-by-mk/{mk}', [AsesmenPM::class, 'getCplByMk'])->name('get-cpl-by-mk');
                        Route::get('get-cpmk-by-cpl/{cpl}', [AsesmenPM::class, 'getCpmkByCpl'])->name('get-cpmk-by-cpl');
                        Route::post('add-asesmen', [AsesmenPM::class, 'storeAsesmen'])->name('asesmen-store');
                        Route::get('getMaxBobotCPL/{cplId}', [AsesmenPM::class, 'getMaxInputKriteriaCPL'])->name('get-max-bobot-cpl');
                        Route::get('getMaxBobotMK/{mkKode}', [AsesmenPM::class, 'getMaxInputKriteriaMK'])->name('get-max-bobot-mk');

                        Route::get('metode-penilaian', [AsesmenPM::class, 'metodePenilaian'])->name('metode-penilaian');
                        Route::get('add-metode-penilaian', [AsesmenPM::class, 'addMetodePenilaian'])->name('metode-penilaian-add');
                        Route::post('store-metode-penilaian', [AsesmenPM::class, 'storeMetodePenilaian'])->name('metode-penilaian-store');

                        Route::get('tahap-penilaian', [AsesmenPM::class, 'tahapPenilaian'])->name('tahap-penilaian');
                        Route::get('add-instrumen-penilaian', [AsesmenPM::class, 'addInstrumenPenilaian'])->name('instrumen-penilaian-add');
                        Route::post('store-instrumen-penilaian', [AsesmenPM::class, 'storeInstrumenPenilaian'])->name('instrumen-penilaian-store');

                        Route::get('bobot-penilaian', [AsesmenPM::class, 'bobotPenilaian'])->name('bobot-penilaian');
                        Route::get('NA-MK', [AsesmenPM::class, 'NA_MK'])->name('NA-MK');
                        Route::get('NA-CPL', [AsesmenPM::class, 'NA_CPL'])->name('NA-CPL');
                    });
                    // RPS Pages
                    Route::prefix('rps')->name('rps.')->group(function () use ($role) {
                        Route::get('rps-list', [RpsPM::class, 'index'])->name('list');
                    
                        if ($role === 'program-studi') {
                            Route::get('validation', [RpsPM::class, 'validationList'])->name('validation.list');
                            Route::post('validation/approve/{id}', [RpsPM::class, 'approve'])->name('validation.approve');
                            Route::post('validation/reject/{id}', [RpsPM::class, 'reject'])->name('validation.reject');
                        }
                    });
                    
                    // Rubrik Penilaian Pages 
                    Route::get('rubrik/list-rubrik', [RubrikPM::class, 'index'])->name('rubrik-list');
                    Route::get('rubrik/download/{id}', [RubrikPM::class, 'download'])->name('rubrik-download');

                    // Penilaian Pages
                    Route::prefix('penilaian')->name('penilaian.')->group(function () {
                        Route::get('PenilaianDenganSoal', [SoalPM::class, 'import'])->name('penilaian-dengan-soal');
                        Route::get('PenilaianTanpaSoal', [SoalPM::class, 'import1'])->name('penilaian-tanpa-soal');
                        Route::get('filter', [SoalPM::class, 'filter'])->name('filter');
                    });
                    Route::prefix('visualisasi')->name('visualisasi.')->group(function () {
                        // Visualisasi spesifik / mahasiswa
                        Route::get('visual-mahasiswa', [PenjaminMutuVisualisasiController::class, 'index'])->name('visual-mahasiswa');
                        // Route::get('getProdiByUniversitas', [PenjaminMutuVisualisasiController::class, 'getProdiByUniversitas'])->name('getProdiByUniversitas');
                        // Route::get('getAngkatanByUniversitas', [PenjaminMutuVisualisasiController::class, 'getAngkatanByUniversitas'])->name('getAngkatanByUniversitas');
                        Route::get('getNpmByAngkatan', [PenjaminMutuVisualisasiController::class, 'getNpmByAngkatan'])->name('getNpmByAngkatan');
                        Route::get('getPemetaanCpl', [PenjaminMutuVisualisasiController::class, 'getPemetaanCpl'])->name('getPemetaanCpl');
                        Route::post('hasilvisual-mahasiswa', [PenjaminMutuVisualisasiController::class, 'hasilVisualMahasiswa'])->name('hasilvisual-mahasiswa');

                        Route::any('hasilvisualcpmk-mahasiswa', [PenjaminMutuVisualisasiController::class, 'hasilVisualCpmkMahasiswa'])->name('hasilvisualcpmk-mahasiswa');

                        // Visualisasi  Angkatan Prodi
                        Route::get('visual-mahasiswaAngkatan', [PenjaminMutuVisualisasiController::class, 'indexAngkatan'])->name('visual-mahasiswaAngkatan');
                        Route::post('hasilvisual-mahasiswaAngkatan', [PenjaminMutuVisualisasiController::class, 'hasilVisualMahasiswaAngkatan'])->name('hasilvisual-mahasiswaAngkatan');
                        Route::get('getAngkatanByProdiUniversitas', [PenjaminMutuVisualisasiController::class, 'getAngkatanByProdiUniversitas'])->name('getAngkatanByProdiUniversitas');

                        Route::any('hasilvisualcpmk-angkatan', [PenjaminMutuVisualisasiController::class, 'hasilVisualCpmkAngkatan'])->name('hasilvisualcpmk-angkatan');
                        Route::get('getAllAngkatanCpmk', [PenjaminMutuVisualisasiController::class, 'getAllAngkatanCpmk'])->name('getAllAngkatanCpmk');
                        Route::get('getAllNpmByAngkatan', [PenjaminMutuVisualisasiController::class, 'getAllNpmByAngkatan'])->name('getAllNpmByAngkatan');


                        // Visualisasi Mata Kuliah 
                        Route::get('visual-mahasiswaMataKuliah', [PenjaminMutuVisualisasiController::class, 'indexMataKuliah'])->name('visual-mahasiswaMataKuliah');
                        Route::get('getCourseByProdi', [PenjaminMutuVisualisasiController::class, 'getCourseByProdi'])->name('getCourseByProdi');

                        Route::post('hasilvisual-mahasiswaMataKuliah', [PenjaminMutuVisualisasiController::class, 'hasilVisualMahasiswaMataKuliah'])->name('hasilvisual-mahasiswaMataKuliah');
                        Route::get('getNamaByNpm', [PenjaminMutuVisualisasiController::class, 'getNamaByNpm'])->name('getNamaByNpm');

                        // generate PDF
                        Route::post('/generate-pdf-visualisasi-mahasiswa', [PenjaminMutuVisualisasiController::class, 'generatePDFhasilVisualMahasiswa'])->name('generate-pdfVisualMahasiswa');
                        Route::post('/generate-pdf-visualisasi-angkatan', [PenjaminMutuVisualisasiController::class,'generatePDFhasilVisualAngkatan'])->name('generate-pdfVisualAngkatan');
                        Route::post('/generate-pdf-visualisasi-matakuliah', [PenjaminMutuVisualisasiController::class, 'generatePDFhasilVisualMataKuliah'])->name('generate-pdfVisualMataKuliah');
                        Route::post('/generate-pdf-visualisasi-cpmk-mahasiswa', [PenjaminMutuVisualisasiController::class, 'generatePDFhasilVisualCPMKMahasiswa'])->name('generate-pdfVisualCPMKMahasiswa');
                        Route::post('/generate-pdf-visualisasi-cpmk-angkatan', [PenjaminMutuVisualisasiController::class,'generatePDFhasilVisualCPMKAngkatan'])->name('generate-pdfVisualCPMKAngkatan');
                    });
                });
            });
        }

        Route::middleware('cekrole:Kepala Program Studi')->group(function () {
            $role = 'Kepala Program Studi';
            $prefix = strtolower(str_replace(' ', '-', $role));
            Route::prefix($prefix)->name("$prefix.")->group(function () use ($prefix) {
                Route::resource('mahasiswa', MahasiswaController::class);

                // Dashboard
                Route::get('dashboard', [DashboardPM::class, 'list'])->name('home');
                Route::get('dashboard-chart', [DashboardPM::class, 'chart'])->name('chart');

                // Rubrik Penilaian Pages
                Route::get('rubrik/list-rubrik', [RubrikPM::class, 'index'])->name('rubrik-list');
                Route::get('rubrik/download/{id}', [RubrikPM::class, 'download'])->name('rubrik-download');

                Route::controller(KurikulumController::class)->group(function () {
                    Route::get('list-kurikulum', 'list')->name('list-kurikulum');
                    Route::post('add-kurikulum', 'store')->name('add-kurikulum');
                    Route::put('update-kurikulum/{kurikulum}', 'update')->name('update-kurikulum');
                    Route::delete('delete-kurikulum/{kurikulum}', 'delete')->name('delete-kurikulum');
                    Route::get('enter-edit-mode/{kurikulum}', 'enterEditMode')->name('enter-edit-mode');
                    Route::get('cancel-edit', 'cancelEdit')->name('cancel-edit');
                });
               // Soal
                Route::post('soal/pesan-mk/{kode_mk}', [SoalPM::class, 'pesanMK'])->name('soal-pesan-mk');
                Route::get('soal/detail/{kode_mk}', [SoalPM::class, 'detail'])->name('soal-detail');
                Route::post('soal/validasi-mk/{kode_mk}', [SoalPM::class, 'validasiMK'])->name('soal-validasi-mk');
                Route::post('soal/tolak-mk/{kode_mk}', [SoalPM::class, 'tolakMK'])->name('soal-tolak-mk');
                Route::get('list-soal', [SoalPM::class, 'list'])->name('list-soal');
                Route::post('validasi-soal/{id}', [SoalPM::class, 'validasi'])->name('validasi-soal');
                Route::post('tolak-soal/{id}', [SoalPM::class, 'tolak_validasi'])->name('tolak-soal');
                Route::get('print-soal/{id}', [SoalPM::class, 'print'])->name('print-soal');
                Route::get('soal/cetakSoal/{id}', [SoalPM::class, 'cetakSoal'])->name('cetakSoal');
                

                Route::get('add-user', [UserPM::class, 'create'])->name('add-user');
                Route::post('add-user', [UserPM::class, 'store'])->name('store-user');
                Route::get('list-user', [UserPM::class, 'list'])->name('list-user');
                Route::put('reset-user/{id}', [UserPM::class, 'reset'])->name('reset-user');
                Route::delete('delete-user/{id}', [UserPM::class, 'delete'])->name('delete-user');
                Route::get('edit-user/{id}', [UserPM::class, 'edit'])->name('edit-user');
                Route::put('edit-user/{id}', [UserPM::class, 'update'])->name('update-user');
                Route::post('add-wfile-user', [UserPM::class, 'create_wfile'])->name('add-user-wfile');
                Route::get('get-fakultas/{universitas_id}', [UserPM::class, 'getFakultas']);
                Route::get('get-prodi/{fakultas_id}', [UserPM::class, 'getProdi']);

                Route::get('indexListProfil', [ProfilController::class, 'indexListProfil'])->name('indexListProfil');
                Route::get('readListProfil', [ProfilController::class, 'readListProfil'])->name('readListProfil');
                Route::get('indexListProfesiCpmk', [ProfilController::class, 'indexListProfesiCpmk'])->name('indexListProfesiCpmk');
                Route::get('add-profesi-cpmks', [ProfilController::class, 'addProfesiCpmk'])->name('profesi-cpmk-add');
                Route::post('store-profesi-cpmk', [ProfilController::class, 'storeProfesiCPMK'])->name('profesi-cpmk-store');
                Route::get('add-profesi', [ProfilController::class, 'createListProfesi'])->name('profesi-add');
                Route::get('createListProfesi', [ProfilController::class, 'createListProfesi'])->name('createListProfesi');
                Route::post('store-profesi', [ProfilController::class, 'storeListProfesi'])->name('profesi-store');
Route::get('readListProfesi', [ProfilController::class, 'readListProfesi'])->name('readListProfesi');
            Route::get('readListProfilProf', [ProfilController::class, 'readListProfilProf'])->name('readListProfilProf');


                // Profil cpl
                Route::get('indexListProfilCpl', [ProfilCplController::class, 'indexListProfilCpl'])->name('indexListProfilCpl');
                Route::get('readListProfilCpl', [ProfilCplController::class, 'readListProfilCpl'])->name('readListProfilCpl');
                Route::get('indexkompetensi', [ProfilCplController::class, 'indexkompetensi'])->name('indexkompetensi');
                Route::get('readListProfilCpl', [ProfilCplController::class, 'readListProfilCpl'])->name('readListProfilCpl');
                Route::get('generate-pdf-cpl-cpmk-mk-profesi', [ProfilPdfController::class, 'generatePdfCplCpmkMkProfesi'])->name('generate-pdf-cpl-cpmk-mk-profesi');
                Route::get('showProfesi/{id}', [ProfilController::class, 'showProfesi'])->name('showProfesi');
                Route::put('updateProfesi/{id}', [ProfilController::class, 'updateProfesi'])->name('updateProfesi');
                Route::delete('deleteProfesi/{id}', [ProfilController::class, 'deleteProfesi'])->name('deleteProfesi');
                Route::get('/print-cpl-cpmk-mk-profesi', [ProfilPdfController::class, 'printCplCpmkMkProfesi'])->name('print-cpl-cpmk-mk-profesi');

                // Route::get('createListProfil', [ProfilController::class, 'createListProfil'])->name('createListProfil');
                // Route::get('storeListProfil', [ProfilController::class, 'storeListProfil'])->name('storeListProfil');
                // Route::get('showProfil/{id}', [ProfilController::class, 'showProfil']);
                // Route::get('updateProfil/{id}', [ProfilController::class, 'updateProfil']);
                // Route::get('deleteProfil/{id}', [ProfilController::class, 'deleteProfil']);
                Route::get('createListProfil', [ProfilController::class, 'createListProfil'])->name('createListProfil');
                Route::get('storeListProfil', [ProfilController::class, 'storeListProfil'])->name('storeListProfil');
                Route::get('storeListProfilAptikom', [ProfilController::class, 'storeListProfilAptikom'])->name('storeListProfilAptikom');
                Route::get('showProfil/{id}', [ProfilController::class, 'showProfil']);
                Route::get('updateProfil/{id}', [ProfilController::class, 'updateProfil']);
                Route::get('updateProfilAptikom/{id}', [ProfilController::class, 'updateProfilAptikom']);
                Route::get('deleteProfil/{id}', [ProfilController::class, 'deleteProfil']);
                Route::get('cpl-cpmk-mk-profesi', [ProfilPdfController::class, 'cplCpmkMkProfesi'])->name('cpl-cpmk.cpl-cpmk-mk-profesi');
                Route::get('indexProfilProfesi', [ProfilController::class, 'indexProfilProfesi'])->name('indexProfilProfesi');
                Route::get('generate-pdf-profesi-cpmk', [ProfilPdfController::class, 'generatePDFProfesiCpmk'])->name('generate-pdf-profesi-cpmk');
                Route::get('index-profil-mk', [ProfilController::class, 'indexProfilMK'])->name('indexProfilMK');
                Route::get('generate-pdf-profil-mk', [ProfilPdfController::class, 'generatePDFProfilMK'])->name('generatePDFProfilMK');


                // Profesi
            Route::get('indexListProfil', [ProfilController::class, 'indexListProfil'])->name('indexListProfil');
            Route::get('readListProfil', [ProfilController::class, 'readListProfil'])->name('readListProfil');
            Route::get('indexPemetaanCPMKProf', [ProfilController::class, 'indexPemetaanCPMKProf'])->name('indexPemetaanCPMKProf');
            Route::get('add-profesi-cpmks', [ProfilController::class, 'addProfesiCpmk'])->name('profesi-cpmk-add');
            Route::post('store-profesi-cpmk', [ProfilController::class, 'storeProfesiCPMK'])->name('profesi-cpmk-store');
            Route::get('edit-profesi-cpmk/{id}', [ProfilController::class, 'editProfesiCpmk'])->name('profesi-cpmk-edit');
            Route::put('update-profesi-cpmk/{id}', [ProfilController::class, 'updateProfesiCpmk'])->name('profesi-cpmk-update');
            Route::delete('delete-profesi-cpmk/{cpmk}/{profesi}', [ProfilController::class, 'deleteProfesiCpmk'])->name('profesi-cpmk-delete');
            Route::get('indexListProfesi', [ProfilController::class, 'indexListProfesi'])->name('indexListProfesi');
            Route::get('/print-profil-mk', [ProfilPdfController::class, 'printProfilMK'])->name('printProfilMK');
            Route::get('/print-profesi-cpmk', [ProfilPdfController::class, 'printProfesiCpmk'])->name('print-profesi-cpmk');

                // PROFIL CPL

                Route::get('createListProfilCpl', [ProfilCplController::class, 'createListProfilCpl'])->name('createListProfilCpl');
                Route::get('storeListProfilCpl', [ProfilCplController::class, 'storeListProfilCpl'])->name('storeListProfilCpl');
                Route::get('showProfilCpl/{id}', [ProfilCplController::class, 'showProfilCpl']);
                Route::get('updateProfilCpl/{id}', [ProfilCplController::class, 'updateProfilCpl']);
                Route::get('deleteProfilCpl/{id}', [ProfilCplController::class, 'deleteProfilCpl']);

                // CPL Pages
                Route::prefix('cpl')->name('cpl.')->group(function () {
                    Route::get('cpl-prodi', [CPLPM::class, 'index'])->name('index');
                    Route::get('add-cpl', [CPLPM::class, 'create'])->name('create');
                    Route::post('add-cpl', [CPLPM::class, 'store'])->name('store');
                    Route::get('edit/{id}', [CPLPM::class, 'edit'])->name('edit');
                    Route::put('edit/{id}', [CPLPM::class, 'update'])->name('update');
                    Route::delete('delete/{id}', [CPLPM::class, 'delete'])->name('delete');
                    
                    Route::get('add-cpl-pl', [CPLPM::class, 'addCPLPL'])->name('cpl-pl-add');
                    Route::post('add-cpl-pl', [CPLPM::class, 'storeCPLPL'])->name('cpl-pl-store');
                    
                    Route::get('pemetaan-cpl-pl', [CPLPM::class, 'indexCPLPL'])->name('cpl-pl');
                    Route::get('pemetaan-cpl-bk', [CPLPM::class, 'indexCPLBK'])->name('cpl-bk');
                    Route::get('add-cpl-bk', [CPLPM::class, 'addCPLBK'])->name('cpl-bk-add');
                    Route::post('add-cpl-bk', [CPLPM::class, 'storeCPLBK'])->name('cpl-bk-store');
                    Route::get('pemetaan-cpl-mk', [CPLPM::class, 'indexCPLMK'])->name('cpl-mk');
                    Route::get('add-cpl-mk', [CPLPM::class, 'addCPLMK'])->name('cpl-mk-add');
                    Route::post('add-cpl-mk', [CPLPM::class, 'storeCPLMK'])->name('cpl-mk-store');
                    Route::get('pemetaan-cpl-bk-mk', [CPLPM::class, 'indexCPLBKMK'])->name('cpl-bk-mk');
                });

                Route::controller(CpmkController::class)->group(function () {
                    Route::get('list-cpmk', 'list')->name('list-cpmk');
                    Route::get('add-cpmk', 'create')->name('add-cpmk');
                    Route::get('/get-cpl-by-kurikulum/{kurikulum_id}','getCplbyKurkulum')->name('getCplByKurikulum');
                    Route::post('add-cpmk', 'store')->name('store-cpmk');
                    Route::get('edit-cpmk/{id}', 'edit')->name('edit-cpmk');
                    Route::put('edit-cpmk/{id}', 'update')->name('update-cpmk');
                    Route::delete('delete-cpmk/{id}', 'delete')->name('delete-cpmk');
                });

                // BK Pages
                Route::prefix('bk')->name('bk.')->group(function () {
                    Route::get('bahan-kajian', [BKPM::class, 'index'])->name('index');
                    Route::get('bk-add', [BKPM::class, 'addBK'])->name('bk-add');
                    Route::post('bk-store', [BKPM::class, 'storeBK'])->name('bk-store');
                    Route::get('pemetaan-bk-mk', [BKPM::class, 'indexBKMK'])->name('bk-mk');
                    Route::get('add-bk-mk', [BKPM::class, 'addBKMK'])->name('bk-mk-add');
                    Route::get('get-mk-by-bk/{bk}', [BKPM::class, 'getMkByBk'])->name('mk-by-mk');
                    Route::post('add-bk-mk', [BKPM::class, 'storeBKMK'])->name('bk-mk-store');
                });

                // MK Pages
                Route::prefix('mk')->name('mk.')->group(function () {
                    Route::get('susunan-mk', [MKPM::class, 'susunanMK'])->name('susunan-mk');
                    Route::get('add-mk', [MKPM::class, 'create'])->name('create');
                    Route::post('add-mk', [MKPM::class, 'store'])->name('store');
                    Route::get('edit-mk/{kode}', [MKPM::class, 'edit'])->name('edit');
                    Route::put('edit-mk/{kode}', [MKPM::class, 'update'])->name('update');
                    Route::delete('delete-mk/{kode}', [MKPM::class, 'delete'])->name('delete');

                    Route::get('organisasi-mk', [MKPM::class, 'organisasiMK'])->name('organisasi-mk');
                    Route::get('pemenuhan-cpl', [MKPM::class, 'pemenuhanCPL'])->name('pemenuhan-cpl');
                });

                // CPL CPMK Pages
                Route::prefix('cpl-cpmk')->name('cpl-cpmk.')->group(function () {
                    Route::get('add-cpl-cpmk-mk', [CPLCPMKPM::class, 'addCPLCPMKMK'])->name('cpl-cpmk-mk-add');
                    Route::get('get-cpmk-by-mk/{mk}', [CPLCPMKPM::class, 'getCPMKByMK'])->name('cpmk-by-mk');
                    Route::post('add-cpl-cpmk-mk', [CPLCPMKPM::class, 'storeCPLCPMKMK'])->name('cpl-cpmk-mk-store');

                    Route::get('pemetaan-cpl-cpmk-mk', [CPLCPMKPM::class, 'indexCPLCPMKMK'])->name('cpl-cpmk-mk');
                    Route::get('pemetaan-cpl-cpmk-mk-semester', [CPLCPMKPM::class, 'indexCPLCPMKMKSMT'])->name('cpl-cpmk-mk-semester');
                    Route::get('pemetaan-cpl-mk-cpmk', [CPLCPMKPM::class, 'indexCPLMKCPMK'])->name('cpl-mk-cpmk');
                    Route::get('pemetaan-mk-cpmk-subcpmk', [CPLCPMKPM::class, 'indexMKCPMKSubCPMK'])->name('mk-cpmk-subcpmk');

                    Route::get('add-cpmk-mk-subcpmk', [CPLCPMKPM::class, 'addCPMKMKSUBCPMK'])->name('cpmk-mk-subcpmk-add');
                    Route::get('get-subcpmk-by-mk/{mk}', [CPLCPMKPM::class, 'getSUBCPMKByMK'])->name('sub-cpmk-by-mk');
                    Route::post('add-cpmk-mk-subcpmk', [CPLCPMKPM::class, 'storeCPMKMKSUBCPMK'])->name('cpmk-mk-subcpmk-store');
                    Route::get('get-cpmk-by-kurikulum/{kurikulum_id}', [CPLCPMKPM::class, 'getCpmkByKurikulum'])->name('cpmk-by-kurikulum');
                    Route::post('add-subcpmk', [CPLCPMKPM::class, 'storeSubCpmk'])->name('subCpmk-store');
                });

                // Asesmen
                Route::prefix('asesmen')->name('asesmen.')->group(function () {
                    Route::get('add-asesmen', [AsesmenPM::class, 'addAsesmen'])->name('asesmen-add');
                    Route::get('get-cpl-by-mk/{mk}', [AsesmenPM::class, 'getCplByMk'])->name('get-cpl-by-mk');
                    Route::get('get-cpmk-by-cpl/{cpl}', [AsesmenPM::class, 'getCpmkByCpl'])->name('get-cpmk-by-cpl');
                    Route::post('add-asesmen', [AsesmenPM::class, 'storeAsesmen'])->name('asesmen-store');
                    Route::get('getMaxBobotCPL/{cplId}', [AsesmenPM::class, 'getMaxInputKriteriaCPL'])->name('get-max-bobot-cpl');
                    Route::get('getMaxBobotMK/{mkKode}', [AsesmenPM::class, 'getMaxInputKriteriaMK'])->name('get-max-bobot-mk');

                    Route::get('metode-penilaian', [AsesmenPM::class, 'metodePenilaian'])->name('metode-penilaian');
                    Route::get('add-metode-penilaian', [AsesmenPM::class, 'addMetodePenilaian'])->name('metode-penilaian-add');
                    Route::post('store-metode-penilaian', [AsesmenPM::class, 'storeMetodePenilaian'])->name('metode-penilaian-store');

                    Route::get('tahap-penilaian', [AsesmenPM::class, 'tahapPenilaian'])->name('tahap-penilaian');
                    Route::get('add-instrumen-penilaian', [AsesmenPM::class, 'addInstrumenPenilaian'])->name('instrumen-penilaian-add');
                    Route::post('store-instrumen-penilaian', [AsesmenPM::class, 'storeInstrumenPenilaian'])->name('instrumen-penilaian-store');

                    Route::get('bobot-penilaian', [AsesmenPM::class, 'bobotPenilaian'])->name('bobot-penilaian');
                    Route::get('NA-MK', [AsesmenPM::class, 'NA_MK'])->name('NA-MK');
                    Route::get('NA-CPL', [AsesmenPM::class, 'NA_CPL'])->name('NA-CPL');
                });

                // RPS Pages
                Route::prefix('rps')->name('rps.')->group(function() {
                    // 1. Route untuk menu "Daftar RPS" (memanggil method index di RpsPM)
                    Route::get('rps-list', [RpsPM::class, 'index'])->name('list');

                    // 2. Route untuk menu "Validasi RPS"
                    Route::get('validation', [RpsPM::class, 'validationList'])->name('validation.list');

                    // 3. Route untuk AKSI validasi
                    Route::post('validation/approve/{id}', [RpsPM::class, 'approve'])->name('validation.approve');
                    Route::post('validation/reject/{id}', [RpsPM::class, 'reject'])->name('validation.reject');
                });

                // Rubrik Penilaian Pages 
                // Route::prefix('rubrik')->name('rubrik.')->group(function() {
                //     Route::get('list-rubrik', [RubrikPM::class, 'index'])->name('rubrik-list');
                //     Route::get('download/{id}', [RubrikPM::class, 'download'])->name('rubrik-download');
                // });

                // Penilaian Pages
                Route::prefix('penilaian')->name('penilaian.')->group(function () {
                    Route::get('PenilaianDenganSoal', [SoalPM::class, 'import'])->name('penilaian-dengan-soal');
                    Route::get('PenilaianTanpaSoal', [SoalPM::class, 'import1'])->name('penilaian-tanpa-soal');
                    Route::get('filter', [SoalPM::class, 'filter'])->name('filter');
                });
                Route::prefix('visualisasi')->name('visualisasi.')->group(function () {
                    // Visualisasi spesifik / mahasiswa
                    Route::get('visual-mahasiswa', [PenjaminMutuVisualisasiController::class, 'index'])->name('visual-mahasiswa');
                    // Route::get('getProdiByUniversitas', [PenjaminMutuVisualisasiController::class, 'getProdiByUniversitas'])->name('getProdiByUniversitas');
                    // Route::get('getAngkatanByUniversitas', [PenjaminMutuVisualisasiController::class, 'getAngkatanByUniversitas'])->name('getAngkatanByUniversitas');
                    Route::get('getNpmByAngkatan', [PenjaminMutuVisualisasiController::class, 'getNpmByAngkatan'])->name('getNpmByAngkatan');
                    Route::get('getPemetaanCpl', [PenjaminMutuVisualisasiController::class, 'getPemetaanCpl'])->name('getPemetaanCpl');
                    Route::post('hasilvisual-mahasiswa', [PenjaminMutuVisualisasiController::class, 'hasilVisualMahasiswa'])->name('hasilvisual-mahasiswa');

                    Route::any('hasilvisualcpmk-mahasiswa', [PenjaminMutuVisualisasiController::class, 'hasilVisualCpmkMahasiswa'])->name('hasilvisualcpmk-mahasiswa');

                    // Visualisasi  Angkatan Prodi
                    Route::get('visual-mahasiswaAngkatan', [PenjaminMutuVisualisasiController::class, 'indexAngkatan'])->name('visual-mahasiswaAngkatan');
                    Route::post('hasilvisual-mahasiswaAngkatan', [PenjaminMutuVisualisasiController::class, 'hasilVisualMahasiswaAngkatan'])->name('hasilvisual-mahasiswaAngkatan');
                    Route::get('getAngkatanByProdiUniversitas', [PenjaminMutuVisualisasiController::class, 'getAngkatanByProdiUniversitas'])->name('getAngkatanByProdiUniversitas');

                    Route::any('hasilvisualcpmk-angkatan', [PenjaminMutuVisualisasiController::class, 'hasilVisualCpmkAngkatan'])->name('hasilvisualcpmk-angkatan');
                    Route::get('getAllAngkatanCpmk', [PenjaminMutuVisualisasiController::class, 'getAllAngkatanCpmk'])->name('getAllAngkatanCpmk');
                    Route::get('getAllNpmByAngkatan', [PenjaminMutuVisualisasiController::class, 'getAllNpmByAngkatan'])->name('getAllNpmByAngkatan');


                    // Visualisasi Mata Kuliah 
                    Route::get('visual-mahasiswaMataKuliah', [PenjaminMutuVisualisasiController::class, 'indexMataKuliah'])->name('visual-mahasiswaMataKuliah');
                    Route::get('getCourseByProdi', [PenjaminMutuVisualisasiController::class, 'getCourseByProdi'])->name('getCourseByProdi');

                    Route::post('hasilvisual-mahasiswaMataKuliah', [PenjaminMutuVisualisasiController::class, 'hasilVisualMahasiswaMataKuliah'])->name('hasilvisual-mahasiswaMataKuliah');
                    Route::get('getNamaByNpm', [PenjaminMutuVisualisasiController::class, 'getNamaByNpm'])->name('getNamaByNpm');

                    // generate PDF
                    Route::post('/generate-pdf-visualisasi-mahasiswa', [PenjaminMutuVisualisasiController::class, 'generatePDFhasilVisualMahasiswa'])->name('generate-pdfVisualMahasiswa');
                    Route::post('/generate-pdf-visualisasi-angkatan', [PenjaminMutuVisualisasiController::class,'generatePDFhasilVisualAngkatan'])->name('generate-pdfVisualAngkatan');
                    Route::post('/generate-pdf-visualisasi-matakuliah', [PenjaminMutuVisualisasiController::class, 'generatePDFhasilVisualMataKuliah'])->name('generate-pdfVisualMataKuliah');
                    Route::post('/generate-pdf-visualisasi-cpmk-mahasiswa', [PenjaminMutuVisualisasiController::class, 'generatePDFhasilVisualCPMKMahasiswa'])->name('generate-pdfVisualCPMKMahasiswa');
                    Route::post('/generate-pdf-visualisasi-cpmk-angkatan', [PenjaminMutuVisualisasiController::class,'generatePDFhasilVisualCPMKAngkatan'])->name('generate-pdfVisualCPMKAngkatan');
                });
            });
        });
        
        // ROUTE MAHASISWA
        Route::middleware(['auth', 'cekrole:Mahasiswa'])
            ->prefix('mahasiswa')
            ->name('mahasiswa.')
            ->group(function () {
                Route::get('/dashboard', [\App\Http\Controllers\Mahasiswa\DashboardController::class, 'index'])
                    ->name('home');
                Route::get('/transkrip-kompetensi', [\App\Http\Controllers\Mahasiswa\TranskripKompetensiController::class, 'index'])
                    ->name('transkrip-kompetensi');
                Route::get('/pemetaan-cpmk-profesi', [\App\Http\Controllers\Mahasiswa\PemetaanCpmkProfesiController::class, 'index'])
                    ->name('pemetaan-cpmk-profesi');
                Route::get('/rekomendasi-mk', [\App\Http\Controllers\Mahasiswa\RekomendasiMkController::class, 'index'])
                    ->name('rekomendasi-mk');
 
                // PRINT HTML (untuk window.print)
                Route::get('/transkrip-kompetensi/print', [\App\Http\Controllers\Mahasiswa\TranskripKompetensiController::class, 'printTranskrip'])
                    ->name('transkrip-kompetensi.print');
 
                // DOWNLOAD PDF
                Route::get('/transkrip-kompetensi/pdf', [\App\Http\Controllers\Mahasiswa\TranskripKompetensiController::class, 'downloadTranskripPdf'])
                    ->name('transkrip-kompetensi.pdf');
 
                Route::post('/transkrip-kompetensi/data', [\App\Http\Controllers\Mahasiswa\TranskripKompetensiController::class, 'getCompetencyData'])
                    ->name('get-competency-data');
 
                Route::get('/pemetaan-cpmk-profesi/pdf', [\App\Http\Controllers\Mahasiswa\PemetaanCpmkProfesiController::class, 'pdf'])
                    ->name('pemetaan-cpmk-profesi.pdf');
            });
 
    }
); // <--- Ini penutup Auth Group utama
 
require __DIR__ . '/auth.php';