@php
    use App\Support\AptikomTheme;

    // --- Ambil otoritas pengguna ---
    $userOtoritasObj = auth()->check() ? auth()->user()->otoritas : null;
    $userOtoritas = $userOtoritasObj->otoritas ?? 'Guest';
    $namaOtoritas = $userOtoritasObj->nama_otoritas ?? null;

    $displayOtoritas = ($userOtoritasObj && $namaOtoritas && trim($namaOtoritas) !== '' && trim($namaOtoritas) !== trim($userOtoritas))
        ? $userOtoritas . ' (' . trim($namaOtoritas) . ')'
        : $userOtoritas;
    $currentPrefix = '';

    // --- Prefix route berdasarkan otoritas ---
    $penjaminMutuPrefixes = [
        'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.',
        'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.',
        'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.',
    ];

    if (array_key_exists($userOtoritas, $penjaminMutuPrefixes)) {
        $currentPrefix = $penjaminMutuPrefixes[$userOtoritas];
    } elseif ($userOtoritas && $userOtoritas !== 'Guest') {
        $currentPrefix = str_replace(' ', '-', strtolower($userOtoritas)) . '.';
    } else {
        $currentPrefix = 'admin.';
    }

    // --- Warna tema: biru (Aptikom) / kuning (Non Aptikom) berdasarkan prodi user ---
    $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
    $themeColor = AptikomTheme::resolve(auth()->user(), $themeColor ?? null);
    $navbarLogoDark = AptikomTheme::isColorDark($themeColor);

    // --- Warna highlight menu aktif di sidebar sesuai tema prodi ---
    // Jika Aptikom: Biru (#007bff)
    // Jika Non-Aptikom: Emas/Kuning Gelap (#d97706) agar tidak biru dan terbaca di background putih
    $sidebarActiveColor = $isAptikom ? '#007bff' : '#d97706';
    $sidebarActiveBg = $isAptikom ? 'rgba(0, 123, 255, 0.1)' : 'rgba(255, 235, 59, 0.35)';

    // --- Warna badge otoritas berdasarkan Aptikom / Non-Aptikom ---
    $otoritasBadgeBg = $isAptikom ? '#e0f2fe' : '#fef3c7';
    $otoritasBadgeColor = $isAptikom ? '#0369a1' : '#b45309';
    $otoritasBadgeBorder = $isAptikom ? '#bae6fd' : '#fde68a';

    // --- Warna badge prodi di header navbar ---
    $prodiHeaderBadgeBg = $isAptikom ? 'rgba(255, 255, 255, 0.25)' : 'rgba(15, 23, 42, 0.12)';
    $prodiHeaderBadgeColor = $isAptikom ? '#ffffff' : '#0f172a';

    // --- Cek apakah halaman saat ini adalah dashboard ---
    $isDashboard = request()->routeIs('*.home', 'home', '*.dashboard', 'dashboard')
        || request()->is('*/dashboard')
        || request()->is('dashboard')
        || request()->is('/');

    // --- Tentukan judul halaman jika bukan di dashboard ---
    $pageTitle = trim($__env->yieldContent('page_title'));
    if (!$pageTitle) {
        $pageTitle = trim($__env->yieldContent('title'));
    }

    if (!$pageTitle) {
        $path = request()->path();

        // 1. Pemetaan spesifik (harus diperiksa paling atas agar tidak terpangkas oleh keyword 'cpmk', 'cpl', 'mk', 'bk')
        if (str_contains($path, 'pemetaan-cpl-cpmk-mk-semester') || str_contains($path, 'pemetaan_cpl_cpmk_mk_semester')) {
            $pageTitle = 'Pemetaan CPL - CPMK - MK Semester';
        } elseif (str_contains($path, 'cpl-cpmk-mk-profesi') || str_contains($path, 'pemetaan-cpl-cpmk-mk-profesi')) {
            $pageTitle = 'Pemetaan CPL - CPMK - MK - Profesi';
        } elseif (str_contains($path, 'pemetaan-cpl-cpmk-mk') || str_contains($path, 'pemetaan_cpl_cpmk_mk') || str_contains($path, 'cpl-cpmk-mk')) {
            $pageTitle = 'Pemetaan CPL - CPMK - MK';
        } elseif (str_contains($path, 'pemetaan-cpl-mk-cpmk') || str_contains($path, 'pemetaan_cpl_mk_cpmk') || str_contains($path, 'cpl-mk-cpmk')) {
            $pageTitle = 'Pemetaan CPL - MK - CPMK';
        } elseif (str_contains($path, 'pemetaan-mk-cpmk-subcpmk') || str_contains($path, 'pemetaan_mk_cpmk_subcpmk') || str_contains($path, 'mk-cpmk-subcpmk')) {
            $pageTitle = 'Pemetaan MK - CPMK - Sub CPMK';
        } elseif (str_contains($path, 'pemetaan-cpmk-profesi') || str_contains($path, 'profesi-cpmk') || str_contains($path, 'ProfesiCpmk') || str_contains($path, 'indexPemetaanCPMKProf')) {
            $pageTitle = 'Pemetaan CPMK - Profesi';
        } elseif (str_contains($path, 'pemetaan-cpl-bk-mk') || str_contains($path, 'pemetaan_cpl_bk_mk') || str_contains($path, 'cpl-bk-mk')) {
            $pageTitle = 'Pemetaan CPL - BK - MK';
        } elseif (str_contains($path, 'pemetaan-cpl-pl') || str_contains($path, 'pemetaan_cpl_pl') || str_contains($path, 'cpl-pl')) {
            $pageTitle = 'Pemetaan CPL - PL';
        } elseif (str_contains($path, 'pemetaan-cpl-bk') || str_contains($path, 'pemetaan_cpl_bk') || str_contains($path, 'cpl-bk')) {
            $pageTitle = 'Pemetaan CPL - BK';
        } elseif (str_contains($path, 'pemetaan-cpl-mk') || str_contains($path, 'pemetaan_cpl_mk') || str_contains($path, 'cpl-mk')) {
            $pageTitle = 'Pemetaan CPL - MK';
        } elseif (str_contains($path, 'pemetaan-bk-mk') || str_contains($path, 'pemetaan_bk_mk') || str_contains($path, 'bk-mk')) {
            $pageTitle = 'Pemetaan BK - MK';
        }

        // 2. Mata Kuliah Spesifik
        elseif (str_contains($path, 'susunan-mk') || str_contains($path, 'susunan_mk')) {
            $pageTitle = 'Susunan Mata Kuliah';
        } elseif (str_contains($path, 'organisasi-mk') || str_contains($path, 'organisasi_mk')) {
            $pageTitle = 'Organisasi Mata Kuliah';
        } elseif (str_contains($path, 'pemenuhan-cpl') || str_contains($path, 'pemenuhan_cpl')) {
            $pageTitle = 'Pemenuhan CPL';
        }

        // 3. Profil Lulusan & Profesi
        elseif (str_contains($path, 'indexProfilProfesi') || str_contains($path, 'ProfilLulusanprof') || str_contains($path, 'ProfilProfesi')) {
            $pageTitle = 'Profil Lulusan - Profesi';
        } elseif (str_contains($path, 'indexProfilMK') || str_contains($path, 'profil-mk')) {
            $pageTitle = 'Profil Lulusan - MK';
        } elseif (str_contains($path, 'indexListProfesi') || str_contains($path, 'list-profesi')) {
            $pageTitle = 'Daftar Profesi';
        } elseif (str_contains($path, 'indexListProfil') || str_contains($path, 'list-profil')) {
            $pageTitle = 'Daftar Profil Lulusan';
        } elseif (str_contains($path, 'kompetensi')) {
            $pageTitle = 'Profil Kompetensi';
        }

        // 4. Asesmen
        elseif (str_contains($path, 'asesmen-add') || str_contains($path, 'add-asesmen')) {
            $pageTitle = 'Asesmen';
        } elseif (str_contains($path, 'metode-penilaian')) {
            $pageTitle = 'Metode Penilaian';
        } elseif (str_contains($path, 'tahap-penilaian')) {
            $pageTitle = 'Tahap Penilaian';
        } elseif (str_contains($path, 'bobot-penilaian')) {
            $pageTitle = 'Bobot Penilaian';
        } elseif (str_contains($path, 'NA-MK') || str_contains($path, 'na-mk')) {
            $pageTitle = 'Nilai Akhir MK';
        } elseif (str_contains($path, 'NA-CPL') || str_contains($path, 'na-cpl')) {
            $pageTitle = 'Nilai Akhir CPL';
        } elseif (str_contains($path, 'asesmen')) {
            $pageTitle = 'Asesmen';
        }

        // 5. Visualisasi
        elseif (str_contains($path, 'visual-fakultas') || str_contains($path, 'visualisasi-fakultas')) {
            $pageTitle = 'Visualisasi CPL Per Fakultas';
        } elseif (str_contains($path, 'visual-program-studi') || str_contains($path, 'visualisasi-program-studi') || str_contains($path, 'visual-prodi')) {
            $pageTitle = 'Visualisasi CPL Per Program Studi';
        } elseif (str_contains($path, 'visual-mk') || str_contains($path, 'visualisasi-mk') || str_contains($path, 'visual-mahasiswaMataKuliah') || str_contains($path, 'hasilvisual-mahasiswaMataKuliah')) {
            $pageTitle = 'Visualisasi CPMK Per Mata Kuliah';
        } elseif (str_contains($path, 'visual-angkatan') || str_contains($path, 'visualisasi-angkatan') || str_contains($path, 'visual-mahasiswaAngkatan') || str_contains($path, 'hasilvisual-mahasiswaAngkatan') || str_contains($path, 'hasilvisualcpmk-angkatan')) {
            $pageTitle = 'Visualisasi CPL Per Angkatan';
        } elseif (str_contains($path, 'visual-mahasiswa') || str_contains($path, 'visualisasi-mahasiswa') || str_contains($path, 'visualisasi')) {
            $pageTitle = 'Visualisasi CPL Per Mahasiswa';
        }

        // 6. RPS & Soal & User & Kurikulum & General
        elseif (str_contains($path, 'validation') || str_contains($path, 'validasi')) {
            $pageTitle = 'Validasi RPS';
        } elseif (str_contains($path, 'rps')) {
            $pageTitle = 'Rencana Pembelajaran Semester (RPS)';
        } elseif (str_contains($path, 'add-cpmk')) {
            $pageTitle = 'Tambah CPMK';
        } elseif (str_contains($path, 'list-cpmk')) {
            $pageTitle = 'Daftar CPMK';
        } elseif (str_contains($path, 'kurikulum')) {
            $pageTitle = 'Daftar Kurikulum';
        } elseif (str_contains($path, 'add-user')) {
            $pageTitle = 'Tambah User';
        } elseif (str_contains($path, 'edit-user')) {
            $pageTitle = 'Edit User';
        } elseif (str_contains($path, 'list-user')) {
            $pageTitle = 'Daftar User';
        } elseif (str_contains($path, 'list-Jenis') || str_contains($path, 'list-jenis') || str_contains($path, 'Jenis') || str_contains($path, 'kriteria')) {
            $pageTitle = 'Kriteria Penilaian';
        } elseif (str_contains($path, 'import-mutu') || str_contains($path, 'importmutu')) {
            $pageTitle = 'Penilaian';
        } elseif (str_contains($path, 'konversi-nilai')) {
            $pageTitle = 'Penilaian';
        } elseif (str_contains($path, 'soal')) {
            $pageTitle = 'Daftar Soal';
        } elseif (str_contains($path, 'penilaian')) {
            $pageTitle = 'Penilaian';
        } elseif (str_contains($path, 'mahasiswa')) {
            $pageTitle = 'Daftar Mahasiswa';
        } elseif (str_contains($path, 'cpl')) {
            $pageTitle = 'CPL Prodi';
        } elseif (str_contains($path, 'bk')) {
            $pageTitle = 'Bahan Kajian (BK)';
        } elseif (str_contains($path, 'cpmk')) {
            $pageTitle = 'CPMK';
        } elseif (str_contains($path, 'profile')) {
            $pageTitle = 'Profile';
        } else {
            $segments = explode('/', $path);
            $lastSegment = end($segments);
            $pageTitle = ucwords(str_replace(['-', '_', '.'], ' ', $lastSegment));
        }
    }
@endphp
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
<style>
body, html, input, button, select, textarea, p, h1, h2, h3, h4, h5, h6, a, span, table, td, th, div, label, li, ul, ol {
    font-family: 'Poppins', sans-serif !important;
}
span[class*="mdi-"], i[class*="mdi-"], span[class*="mdi"], i[class*="mdi"], .mdi { font-family: "Material Design Icons" !important; }
span[class*="feather-"], i[class*="feather-"], span[class*="feather"], i[class*="feather"], .feather { font-family: "feather" !important; }
span[class*="ti-"], i[class*="ti-"], span[class*="ti"], i[class*="ti"], .ti { font-family: "themify" !important; }
i[class*="ti-trash"], span[class*="ti-trash"], .ti-trash, .btn-icons i.ti-trash, .btn i.ti-trash, .btn-danger i { font-family: "Material Design Icons" !important; }
i[class*="ti-trash"]:before, span[class*="ti-trash"]:before, .ti-trash:before, .btn-icons i.ti-trash:before, .btn i.ti-trash:before, .btn-danger i:before { content: "\F1C0" !important; font-family: "Material Design Icons" !important; }
span[class*="icon-"], i[class*="icon-"] { font-family: "Simple-Line-Icons" !important; }
span[class*="bi-"], i[class*="bi-"], span[class*="bi"], i[class*="bi"], .bi { font-family: "bootstrap-icons" !important; }
span[class*="fa-"], i[class*="fa-"], span[class*="fa"], i[class*="fa"], .fa { font-family: FontAwesome !important; }

.btn-icons {
    width: 35px !important;
    height: 35px !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 6px !important;
}
.btn-icons i {
    font-size: 15px !important;
    color: #ffffff !important;
    margin: 0 !important;
    line-height: 1 !important;
}

.btn-icon-text {
    padding: 8px 18px !important;
    height: 38px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    line-height: 1 !important;
}
.btn-icon-text svg {
    stroke: #ffffff !important;
}

/* Fix DataTables Controls Layout & Structure */
.dataTables_wrapper {
    position: relative !important;
    width: 100% !important;
    clear: both !important;
}
.dataTables_wrapper > .table-responsive {
    width: 100% !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
    margin-bottom: 1rem !important;
    clear: both !important;
    border: none !important;
}
.dataTables_wrapper > .table-responsive > table.dataTable {
    width: 100% !important;
    margin-bottom: 0 !important;
}
.dataTables_wrapper .dataTables_length {
    float: left !important;
    margin-bottom: 0.75rem !important;
}
.dataTables_wrapper .dataTables_length label {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    white-space: nowrap !important;
    font-size: 13.5px !important;
    color: #475569 !important;
    margin-bottom: 0 !important;
}
.dataTables_wrapper .dataTables_length select {
    width: auto !important;
    display: inline-block !important;
    padding: 4px 10px !important;
    height: 34px !important;
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    margin: 0 4px !important;
}
.dataTables_wrapper .dataTables_filter {
    float: right !important;
    text-align: right !important;
    margin-bottom: 0.75rem !important;
}
.dataTables_wrapper .dataTables_filter label {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    white-space: nowrap !important;
    font-size: 13.5px !important;
    color: #475569 !important;
    margin-bottom: 0 !important;
}
.dataTables_wrapper .dataTables_filter input {
    height: 34px !important;
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 4px 10px !important;
}
.dataTables_wrapper .dataTables_info {
    float: left !important;
    margin-top: 0.75rem !important;
    font-size: 13.5px !important;
    color: #475569 !important;
}
.dataTables_wrapper .dataTables_paginate {
    float: right !important;
    text-align: right !important;
    margin-top: 0.75rem !important;
}
        /* UNIFIED GLOBAL FORM INPUT SYSTEM */
        .form-control,
        .form-select,
        select.form-control,
        input[type="text"].form-control,
        input[type="email"].form-control,
        input[type="password"].form-control,
        input[type="number"].form-control,
        input[type="date"].form-control,
        input[type="url"].form-control,
        input[type="search"].form-control,
        .select2-container--default .select2-selection--single,
        .select2-container--bootstrap .select2-selection--single {
            height: 40px !important;
            min-height: 40px !important;
            padding: 8px 14px !important;
            font-size: 14px !important;
            font-family: 'Poppins', sans-serif !important;
            color: #1e293b !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            box-shadow: none !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
            line-height: 1.4 !important;
        }

        /* Select2 Single Alignment Fixes */
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
            line-height: 22px !important;
            padding-left: 0 !important;
            color: #1e293b !important;
            font-size: 14px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow,
        .select2-container--bootstrap .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
            top: 0 !important;
            right: 8px !important;
        }

        /* Select2 Multiple Alignment Fixes */
        .select2-container--default .select2-selection--multiple,
        .select2-container--bootstrap .select2-selection--multiple {
            min-height: 40px !important;
            height: auto !important;
            padding: 4px 8px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            background-color: #ffffff !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e2e8f0 !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 4px !important;
            padding: 2px 8px !important;
            font-size: 13px !important;
            color: #1e293b !important;
            margin-top: 3px !important;
        }

        /* Focus State for all inputs & Select2 */
        .form-control:focus,
        .form-select:focus,
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--open .select2-selection--single,
        .select2-container--open .select2-selection--multiple {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
            outline: 0 !important;
        }

        /* Form Labels Uniformity */
        .form-label, label {
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #334155 !important;
            margin-bottom: 6px !important;
        }

        /* Textarea Uniformity */
        textarea.form-control {
            height: auto !important;
            min-height: 90px !important;
            padding: 10px 14px !important;
            line-height: 1.5 !important;
        }

        /* Small Form Inputs (.form-control-sm, .form-select-sm) */
        .form-control-sm,
        .form-select-sm {
            height: 34px !important;
            min-height: 34px !important;
            padding: 4px 10px !important;
            font-size: 13px !important;
            border-radius: 6px !important;
        }

        /* Fix Navbar Date Picker Layout */
        .navbar-date-picker {
            display: inline-flex !important;
            align-items: center !important;
            background-color: #ffffff !important;
            border-radius: 8px !important;
            padding: 0 12px !important;
            height: 38px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden !important;
        }
        .navbar-date-picker .input-group-addon,
        .navbar-date-picker .input-group-prepend,
        .navbar-date-picker .input-group-text,
        .navbar-date-picker .calendar-icon {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin-right: 8px !important;
            color: #3b82f6 !important;
            font-size: 15px !important;
            display: flex !important;
            align-items: center !important;
        }
        .navbar-date-picker input.form-control,
        .navbar-date-picker input {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            height: 100% !important;
            min-height: unset !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            box-shadow: none !important;
            width: 95px !important;
            cursor: default !important;
        }
@media (min-width: 992px) {
  .page-body-wrapper {
    position: relative !important;
  }
  #sidebar, .sidebar {
    position: fixed !important;
    top: 75px !important;
    left: 0 !important;
    bottom: 0 !important;
    width: 220px !important;
    height: calc(100vh - 75px) !important;
    overflow-y: auto !important;
    z-index: 11 !important;
    border-right: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
  }
  #sidebar::-webkit-scrollbar, .sidebar::-webkit-scrollbar {
    width: 4px;
  }
  #sidebar::-webkit-scrollbar-thumb, .sidebar::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.15);
    border-radius: 4px;
  }
  .main-panel {
    margin-left: 220px !important;
    width: calc(100% - 220px) !important;
  }
  body.sidebar-icon-only .main-panel,
  .sidebar-icon-only .main-panel {
    margin-left: 70px !important;
    width: calc(100% - 70px) !important;
  }
  body.sidebar-icon-only #sidebar,
  body.sidebar-icon-only .sidebar,
  .sidebar-icon-only #sidebar,
  .sidebar-icon-only .sidebar {
    width: 70px !important;
  }
}
.page-body-wrapper {
  padding-top: 75px !important;
}
.navbar {
  height: 75px !important;
  background-color: {{ $themeColor }} !important;
}
.navbar-brand-wrapper {
  height: 75px !important;
  background-color: #ffffff !important;
  border-right: 1px solid #e2e8f0 !important;
  box-shadow: none !important;
}
.navbar-menu-wrapper {
  height: 75px !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
  background-color: {{ $themeColor }} !important;
  box-shadow: none !important;
  border: none !important;
}

/* Sidebar Styling based on Prodi Theme - Remove default template blue */
.sidebar .nav .nav-item .nav-link,
.sidebar .nav .nav-item .nav-link .menu-title,
.sidebar .nav .nav-item .nav-link .menu-icon,
.sidebar .nav .nav-item .nav-link i,
.sidebar .nav .nav-item .nav-link .menu-arrow,
.sidebar .nav .nav-item .sub-menu .nav-link,
.sidebar .nav.sub-menu .nav-item .nav-link {
    color: #334155 !important;
}

/* Hover State */
.sidebar .nav .nav-item .nav-link:hover,
.sidebar .nav .nav-item .nav-link:hover .menu-title,
.sidebar .nav .nav-item .nav-link:hover .menu-icon,
.sidebar .nav .nav-item .nav-link:hover i,
.sidebar .nav .nav-item .nav-link:hover .menu-arrow {
    color: {{ $sidebarActiveColor }} !important;
}

/* Active State */
.sidebar .nav .nav-item.active > .nav-link,
.sidebar .nav .nav-item.active > .nav-link .menu-title,
.sidebar .nav .nav-item.active > .nav-link .menu-icon,
.sidebar .nav .nav-item.active > .nav-link i,
.sidebar .nav .nav-item.active > .nav-link .menu-arrow,
.sidebar .nav .nav-item .nav-link.active,
.sidebar .nav .nav-item .nav-link.active .menu-title,
.sidebar .nav .nav-item .nav-link.active .menu-icon,
.sidebar .nav .nav-item .nav-link.active i,
.sidebar .nav .nav-item .nav-link.active .menu-arrow,
.sidebar .nav .nav-item .sub-menu .nav-item .nav-link.active,
.sidebar .nav .nav-item .sub-menu .nav-item.active > .nav-link,
.sidebar .nav.sub-menu .nav-item .nav-link.active {
    color: {{ $sidebarActiveColor }} !important;
    font-weight: 600 !important;
}

.sidebar .nav .nav-item.active > .nav-link,
.sidebar .nav .nav-item .nav-link.active {
    background-color: {{ $sidebarActiveBg }} !important;
    border-radius: 8px !important;
}

.sidebar .nav .nav-item.active > .nav-link::before,
.sidebar .nav .nav-item .nav-link.active::before {
    background-color: {{ $sidebarActiveColor }} !important;
}

.sidebar .nav .nav-category {
    color: #64748b !important;
    font-weight: 700 !important;
}

/* Sidebar Submenu Text Overflow & Bullet Alignment Fix */
.sidebar .nav.sub-menu {
    padding-left: 2rem !important;
    padding-right: 1rem !important;
}

.sidebar .nav.sub-menu .nav-item .nav-link {
    font-size: 12.5px !important;
    line-height: 1.35 !important;
    padding: 7px 12px 7px 1.8rem !important;
    white-space: normal !important;
    word-break: break-word !important;
    overflow: visible !important;
    text-overflow: clip !important;
    height: auto !important;
    min-height: 34px !important;
    position: relative !important;
}

.sidebar .nav.sub-menu .nav-item .nav-link::before {
    left: 0.65rem !important;
}

/* MODERN NAVBAR & USER DROPDOWN SYSTEM */
.navbar.default-layout {
    box-shadow: 0 4px 25px -4px rgba(15, 23, 42, 0.08) !important;
    transition: all 0.3s ease !important;
}

.navbar .navbar-brand-wrapper {
    box-shadow: 1px 0 0 0 #f1f5f9 !important;
    transition: all 0.3s ease !important;
}

.navbar .navbar-brand img {
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.navbar .navbar-brand:hover img {
    transform: scale(1.04) !important;
}

/* User Avatar Nav Trigger */
.navbar .user-dropdown .nav-link,
.navbar .user-dropdown-mobile .nav-link {
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
}

.navbar .user-dropdown .nav-link img,
.navbar .user-dropdown-mobile .nav-link img {
    width: 42px !important;
    height: 42px !important;
    border-radius: 50% !important;
    border: 2.5px solid rgba(255, 255, 255, 0.95) !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12) !important;
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    object-fit: cover !important;
}

.navbar .user-dropdown .nav-link:hover img,
.navbar .user-dropdown-mobile .nav-link:hover img {
    transform: translateY(-1px) scale(1.05) !important;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.18) !important;
}

/* Navbar Dropdown Menu Card */
.navbar .navbar-dropdown {
    border-radius: 18px !important;
    border: 1px solid rgba(226, 232, 240, 0.9) !important;
    box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.15), 0 0 1px rgba(15, 23, 42, 0.1) !important;
    padding: 0 0 8px 0 !important;
    min-width: 285px !important;
    margin-top: 12px !important;
    overflow: hidden !important;
    background: #ffffff !important;
    animation: navbarDropdownFadeIn 0.2s ease-out forwards !important;
}

@keyframes navbarDropdownFadeIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

.navbar .navbar-dropdown .dropdown-header {
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%) !important;
    padding: 20px 18px 16px 18px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    margin-bottom: 6px !important;
}

.navbar .navbar-dropdown .dropdown-header img {
    width: 48px !important;
    height: 48px !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12) !important;
    border: 2px solid #ffffff !important;
    object-fit: cover !important;
}

.navbar .navbar-dropdown .dropdown-item {
    padding: 10px 16px !important;
    margin: 3px 8px !important;
    border-radius: 10px !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    color: #334155 !important;
    display: flex !important;
    align-items: center !important;
    transition: all 0.18s ease !important;
    width: auto !important;
}

.navbar .navbar-dropdown .dropdown-item:hover {
    background-color: #f1f5f9 !important;
    color: #0284c7 !important;
    transform: translateX(3px) !important;
}

.navbar .navbar-dropdown .dropdown-item i.dropdown-item-icon {
    font-size: 18px !important;
    color: #0284c7 !important;
    transition: transform 0.18s ease !important;
}

.navbar .navbar-dropdown .dropdown-item:hover i.dropdown-item-icon {
    transform: scale(1.15) !important;
}

/* Logout Button Special Styling */
.navbar .navbar-dropdown form .dropdown-item:hover {
    background-color: #fef2f2 !important;
    color: #ef4444 !important;
}

.navbar .navbar-dropdown form .dropdown-item:hover i.dropdown-item-icon {
    color: #ef4444 !important;
}

/* Modern Datepicker Pill Widget */
.navbar-date-picker {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 30px !important;
    padding: 3px 6px 3px 12px !important;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04) !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}

.navbar-date-picker:hover {
    border-color: #94a3b8 !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
}

.navbar-date-picker input {
    border: none !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #334155 !important;
    background: transparent !important;
}
</style>
<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-center flex-row"
    style="background-color: {{ $themeColor }}; height: 75px;">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center"
        style="background-color: #ffffff !important; border-right: 1px solid #e2e8f0; height: 75px;">
        <div>
            <a class="navbar-brand brand-logo d-flex align-items-center justify-content-center px-3" href="{{ route($currentPrefix . 'home') }}">
                <x-app-logo :dark="false" />
            </a>
            <span class="navbar-brand brand-logo-mini">
                <x-app-logo :dark="false" style="max-height:39px;" />
            </span>
        </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-between navbar-themed-section px-4"
        style="background-color: {{ $themeColor }}; height: 75px;">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item d-none d-lg-block ms-0">
                @if ($isDashboard)
                    <div class="d-flex flex-column justify-content-center">
                        <h1 class="welcome-text themed-text mb-0" style="font-size: 1.15rem; font-weight: 500; line-height: 1.2;">
                            Selamat Datang, <span class="themed-text text-capitalize" style="font-weight: 600;">
                                {{ auth()->user()->name }}
                                @if ($userOtoritas === 'Admin Universitas')
                                    <a class="text-decoration-none ms-1" href="{{ route('admin-universitas.theme.edit') }}">
                                        <i class="typcn typcn-cog themed-text" style="font-size: 18px; vertical-align: middle;"></i>
                                    </a>
                                @endif
                            </span>
                        </h1>
                        <p class="welcome-role themed-text mb-0" style="font-size: 0.8rem; opacity: 0.88; margin-top: 3px;">
                            {{ $displayOtoritas }}
                            @if (auth()->check() && auth()->user()->prodi)
                                <span class="ms-1 px-2.5 py-0.5 rounded-pill fw-semibold" style="font-size: 0.75rem; background-color: {{ $prodiHeaderBadgeBg }}; color: {{ $prodiHeaderBadgeColor }} !important; display: inline-block;">
                                    {{ auth()->user()->prodi->nama }}
                                </span>
                            @endif
                        </p>
                    </div>
                @else
                    <div class="d-flex align-items-center">
                        <h1 class="welcome-text themed-text mb-0" style="font-size: 1.2rem; font-weight: 600; letter-spacing: -0.2px;">
                            {{ $pageTitle }}
                        </h1>
                    </div>
                @endif
            </li>
        </ul>
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item d-none d-lg-block">
                <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
                    <span class="input-group-addon input-group-prepend border-right">
                        <span class="icon-calendar input-group-text calendar-icon"></span>
                    </span>
                    <input style="background-color:white" disabled="disabled" type="text" class="form-control">
                </div>
            </li>
            {{-- User dropdown untuk desktop --}}
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                <a class="nav-link" id="UserDropdown" href="#" aria-expanded="false">
                    <img class="img-xs rounded-circle" src="{{ asset('/assets/img/pp/' . auth()->user()->img) }}"
                        alt="Profile image">
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <div class="dropdown-header text-center">
                        <img class="img-md rounded-circle" src="{{ asset('/assets/img/pp/' . auth()->user()->img) }}"
                            alt="Profile image" style="width:41px; height:40px;">
                        <div class="mt-2 mb-1">
                            <span class="badge" style="background-color: {{ $otoritasBadgeBg }}; color: {{ $otoritasBadgeColor }}; border: 1px solid {{ $otoritasBadgeBorder }}; font-weight: 500; font-size: 11px; padding: 4px 10px; border-radius: 12px; display: inline-block; white-space: normal; max-width: 100%;">
                                {{ $displayOtoritas }}
                            </span>
                        </div>
                        <p class="mb-1 font-weight-semibold">{{ auth()->user()->name }}</p>
                        <p class="fw-light text-muted mb-0">{{ auth()->user()->email }}</p>
                        @if (auth()->check())
                            @php
                                $userOtoritasList = auth()->user()->getOtoritasListForCurrentProdi();
                                $activeOtoritasId = optional(auth()->user()->otoritas)->id;
                            @endphp
                            @if ($userOtoritasList->count() > 1)
                                <div class="mt-2 text-center">
                                    <form method="POST" action="{{ route('switch-otoritas') }}" class="d-inline-flex align-items-center justify-content-center m-0">
                                        @csrf
                                        <span class="text-secondary small font-weight-bold me-2" style="font-size: 0.82rem; color: #475569 !important;">Role:</span>
                                        <select class="form-select form-select-sm d-inline-block text-primary font-weight-bold bg-white border shadow-xs" name="otoritas_id" onchange="this.form.submit()" style="font-size: 0.82rem; width: auto; max-width: 220px; height: 32px; padding-left: 12px !important; padding-right: 32px !important; border-radius: 8px; cursor: pointer; color: #0284c7 !important; border-color: #cbd5e1 !important;" title="Klik untuk alih role">
                                            @foreach ($userOtoritasList as $userOtoritasItem)
                                                <option value="{{ $userOtoritasItem->id }}" {{ ($userOtoritasItem->active || $userOtoritasItem->id == $activeOtoritasId) ? 'selected' : '' }}>
                                                    {{ $userOtoritasItem->otoritas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            @elseif (auth()->user()->prodi)
                                <p class="text-muted mb-0 small mt-1 font-weight-bold">
                                    Prodi: <span class="text-primary">{{ auth()->user()->prodi->nama }}</span>
                                </p>
                            @endif
                        @endif
                    </div>
                    <a href="{{ route('profile') }}" class="dropdown-item"><i
                            class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>My Profile</a>
                    @if (auth()->check() && auth()->user()->prodis && auth()->user()->prodis->count() > 1)
                        <a href="{{ route('gate.menu') }}" class="dropdown-item">
                            <i class="dropdown-item-icon mdi mdi-swap-horizontal text-primary me-2"></i>Beralih Prodi
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item"
                            onclick="event.preventDefault(); this.closest('form').submit();" style="color:black">
                            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </li>
            {{-- User dropdown untuk mobile --}}
            <li class="nav-item dropdown d-block d-lg-none user-dropdown-mobile">
                <a class="nav-link" id="UserDropdownMobile" href="#" aria-expanded="false">
                    <img class="img-xs rounded-circle" src="{{ asset('/assets/img/pp/' . auth()->user()->img) }}"
                        alt="Profile image">
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdownMobile">
                    <div class="dropdown-header text-center">
                        <img class="img-md rounded-circle" src="{{ asset('/assets/img/pp/' . auth()->user()->img) }}"
                            alt="Profile image" style="width:41px; height:40px;">
                        <div class="mt-2 mb-1">
                            <span class="badge" style="background-color: {{ $otoritasBadgeBg }}; color: {{ $otoritasBadgeColor }}; border: 1px solid {{ $otoritasBadgeBorder }}; font-weight: 500; font-size: 11px; padding: 4px 10px; border-radius: 12px; display: inline-block; white-space: normal; max-width: 100%;">
                                {{ $displayOtoritas }}
                            </span>
                        </div>
                        <p class="mb-1 font-weight-semibold">{{ auth()->user()->name }}</p>
                        <p class="fw-light text-muted mb-0">{{ auth()->user()->email }}</p>
                        @if (auth()->check())
                            @php
                                $userOtoritasList = auth()->user()->getOtoritasListForCurrentProdi();
                                $activeOtoritasId = optional(auth()->user()->otoritas)->id;
                            @endphp
                            @if ($userOtoritasList->count() > 1)
                                <div class="mt-2 text-center">
                                    <form method="POST" action="{{ route('switch-otoritas') }}" class="d-inline-flex align-items-center justify-content-center m-0">
                                        @csrf
                                        <span class="text-secondary small font-weight-bold me-2" style="font-size: 0.82rem; color: #475569 !important;">Role:</span>
                                        <select class="form-select form-select-sm d-inline-block text-primary font-weight-bold bg-white border shadow-xs" name="otoritas_id" onchange="this.form.submit()" style="font-size: 0.82rem; width: auto; max-width: 220px; height: 32px; padding-left: 12px !important; padding-right: 32px !important; border-radius: 8px; cursor: pointer; color: #0284c7 !important; border-color: #cbd5e1 !important;" title="Klik untuk alih role">
                                            @foreach ($userOtoritasList as $userOtoritasItem)
                                                <option value="{{ $userOtoritasItem->id }}" {{ ($userOtoritasItem->active || $userOtoritasItem->id == $activeOtoritasId) ? 'selected' : '' }}>
                                                    {{ $userOtoritasItem->otoritas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            @elseif (auth()->user()->prodi)
                                <p class="text-muted mb-0 small mt-1 font-weight-bold">
                                    Prodi: <span class="text-primary">{{ auth()->user()->prodi->nama }}</span>
                                </p>
                            @endif
                        @endif
                    </div>
                    <a href="{{ route('profile') }}" class="dropdown-item"><i
                            class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>My Profile</a>
                    @if (auth()->check() && auth()->user()->prodis && auth()->user()->prodis->count() > 1)
                        <a href="{{ route('gate.menu') }}" class="dropdown-item">
                            <i class="dropdown-item-icon mdi mdi-swap-horizontal text-primary me-2"></i>Beralih Prodi
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item"
                            onclick="event.preventDefault(); this.closest('form').submit();" style="color:black">
                            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
@push('scripts')
    <script>
        // Utility functions for color handling
        const colorUtils = {
            // Convert hex color to RGB
            hexToRgb(hex) {
                const r = parseInt(hex.slice(1, 3), 16);
                const g = parseInt(hex.slice(3, 5), 16);
                const b = parseInt(hex.slice(5, 7), 16);
                return {
                    r,
                    g,
                    b
                };
            },

            // Check if color is dark using YIQ formula
            isColorDark(hexColor) {
                const {
                    r,
                    g,
                    b
                } = this.hexToRgb(hexColor);
                const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return yiq < 128;
            }
        };

        // Function to update text colors based on background
        function updateNavbarTextColors(color) {
            const themedSections = document.querySelectorAll('.navbar-themed-section');
            const textColor = colorUtils.isColorDark(color) ? '#FFFFFF' : '#000000';

            themedSections.forEach(section => {
                const themedTexts = section.querySelectorAll('.themed-text');
                themedTexts.forEach(element => {
                    element.style.color = textColor;
                });
            });
        }

        // Convert RGB color to hex
        function rgbToHex(rgb) {
            const rgbValues = rgb.match(/\d+/g);
            const r = parseInt(rgbValues[0]);
            const g = parseInt(rgbValues[1]);
            const b = parseInt(rgbValues[2]);

            return '#' + [r, g, b].map(x => {
                const hex = x.toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('');
        }

        // Function to update theme colors
        function onThemeColorChange(newColor) {
            const themedSections = document.querySelectorAll('.navbar-themed-section');
            themedSections.forEach(section => {
                section.style.backgroundColor = newColor;
            });
            updateNavbarTextColors(newColor);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Get the current background color from the navbar
            const navbarSection = document.querySelector('.navbar-themed-section');
            if (navbarSection) {
                const backgroundColor = window.getComputedStyle(navbarSection).backgroundColor;
                const hexColor = rgbToHex(backgroundColor);
                updateNavbarTextColors(hexColor);
            }

            // Only set up color input listener if we're on the theme edit page
            const colorInput = document.getElementById('theme_color');
            if (colorInput) {
                colorInput.addEventListener('input', function(e) {
                    const selectedColor = e.target.value;
                    requestAnimationFrame(() => {
                        onThemeColorChange(selectedColor);
                    });
                });
            }

            // Fungsi reusable untuk menangani dropdown
            const setupDropdown = (triggerId) => {
                const trigger = document.getElementById(triggerId);
                if (!trigger) return; // Keluar jika elemen tidak ada

                const dropdownMenu = trigger.nextElementSibling;

                // Event saat trigger di-klik
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Toggle (tampilkan/sembunyikan) menu dropdown
                    dropdownMenu.classList.toggle('show');
                });
            };

            // Menangani event klik di luar dropdown untuk menutupnya
            document.addEventListener('click', function(e) {
                // Cari semua dropdown yang sedang aktif
                const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
                openDropdowns.forEach(dropdown => {
                    // Dapatkan trigger dari dropdown menu
                    const trigger = dropdown.previousElementSibling;
                    // Tutup jika klik terjadi di luar area trigger DAN di luar area menu itu sendiri
                    if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
            });

            // Terapkan fungsi pada kedua dropdown (desktop dan mobile)
            setupDropdown('UserDropdown');
            setupDropdown('UserDropdownMobile');
        });
    </script>
@endpush
