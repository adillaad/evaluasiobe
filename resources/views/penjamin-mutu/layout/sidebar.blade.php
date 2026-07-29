@php
    $routePrefix = [
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];
$userOtoritas = auth()->user()->otoritas->otoritas;
$currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp

<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        {{-- Dashboard --}}
        @if (in_array($userOtoritas, [
                'Penjamin Mutu Universitas',
                'Penjamin Mutu Fakultas',
                'Penjamin Mutu Program Studi',
                'Kepala Program Studi',
            ]))
            <li class="nav-item">
                <a class="nav-link" href={{ route($currentPrefix . 'home') }}>
                    <i class="mdi mdi-grid-large menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            {{-- Menu Kurikulum --}}
            <li class="nav-item nav-category">Kurikulum</li>
            @if ($userOtoritas == 'Kepala Program Studi')
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#kurikulum" aria-expanded="false"
                        aria-controls="kurikulum" onclick="document.getElementById('kur').click()">
                        <i class="menu-icon mdi mdi-library-books"></i>
                        <span class="menu-title">Kurikulum</span>
                    </a>
                    <div class="collapse" id="kurikulum" hidden="hidden">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a id="kur" class="nav-link"
                                    href="{{ route($currentPrefix . 'list-kurikulum') }}">Daftar Kurikulum</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            {{-- User Management --}}
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#user" aria-expanded="false" aria-controls="user">
                    <i class="menu-icon mdi mdi-account-circle-outline"></i>
                    <span class="menu-title">User</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="user">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route($currentPrefix . 'add-user') }}">Tambah
                                User</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route($currentPrefix . 'list-user') }}">Daftar
                                User</a></li>
                    </ul>
                </div>
            </li>

            {{-- Soal Pages --}}
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#soal" aria-expanded="false" aria-controls="soal">
                    <i class="menu-icon mdi mdi-comment-question-outline"></i>
                    <span class="menu-title">Soal Pages</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="soal">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route($currentPrefix . 'list-soal') }}">List
                                Soal</a></li>
                    </ul>
                </div>
            </li>

            {{-- Profil Lulusan --}}
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#profil" aria-expanded="false"
                    aria-controls="profil">
                    <i class="menu-icon mdi mdi-card-text-outline"></i>
                    <span class="menu-title">Profil Lulusan</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="profil">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'indexListProfil') }}">List Profil
                                Lulusan</a></li>
                        <li class="nav-item">
                            {{-- Pastikan nama route di sini sesuai dengan yang Anda buat di file web.php --}}
                            <a class="nav-link" href="{{ route($currentPrefix . 'indexProfilProfesi') }}">
                                List Profil Lulusan - Profesi
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'indexListProfesi') }}">List Profesi</a>
                        </li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'indexProfilMK') }}">Profil Lulusan-MK</a>
                        </li>
                        @if (optional(auth()->user()->prodi)->is_aptikom)
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ route($currentPrefix . 'indexPemetaanCPMKProf') }}">Pemetaan
                                    CPMK-Profesi</a>
                            </li>
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ route($currentPrefix . 'cpl-cpmk.cpl-cpmk-mk-profesi') }}">Pemetaan
                                    CPL-CPMK-MK-Profesi</a>
                            </li>
                        @endif
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'indexkompetensi') }}">List Profil
                                Kompetensi</a></li>
                    </ul>
                </div>
            </li>

            {{-- CPL Pages --}}
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#cpl" aria-expanded="false" aria-controls="cpl">
                    <i class="menu-icon mdi mdi-format-list-bulleted-type"></i>
                    <span class="menu-title">CPL</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="cpl">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'cpl.index') }}">CPL
                                Prodi</a></li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'cpl.cpl-pl') }}">Pemetaan CPL-PL</a></li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'cpl.cpl-bk') }}">Pemetaan CPL-BK</a></li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'cpl.cpl-mk') }}">Pemetaan CPL-MK</a></li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'cpl.cpl-bk-mk') }}">Pemetaan CPL-BK-MK</a></li>
                    </ul>
                </div>
            </li>

            {{-- CPMK Pages --}}
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#cpmk" aria-expanded="false"
                    aria-controls="cpmk">
                    <i class="menu-icon mdi mdi-target"></i>
                    <span class="menu-title">CPMK</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="cpmk">
                    <ul class="nav flex-column sub-menu">
                        @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route($currentPrefix . 'add-cpmk') }}">Tambah CPMK</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'list-cpmk') }}">Daftar CPMK</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- BK Pages --}}
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#bk" aria-expanded="false"
                    aria-controls="bk">
                    <i class="menu-icon mdi mdi-card-text-outline"></i>
                    <span class="menu-title">Bahan Kajian</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="bk">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'bk.index') }}">Bahan
                                Kajian</a></li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'bk.bk-mk') }}">Pemetaan BK-MK</a></li>
                    </ul>
                </div>
            </li>

            {{-- Mata Kuliah --}}
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#mk" aria-expanded="false"
                    aria-controls="mk">
                    <i class="menu-icon mdi mdi-collage"></i>
                    <span class="menu-title">Mata Kuliah</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="mk">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'mk.susunan-mk') }}">Susunan MK</a></li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'mk.organisasi-mk') }}">Organisasi MK</a></li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'mk.pemenuhan-cpl') }}">Pemenuhan CPL</a></li>
                    </ul>
                </div>
            </li>

            {{-- CPL-CPMK Pages --}}
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#cpl_cpmk" aria-expanded="false"
                    aria-controls="cpl_cpmk">
                    <i class="menu-icon mdi mdi-playlist-check"></i>
                    <span class="menu-title">Pemetaan CPL-CPMK</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="cpl_cpmk">
                    <ul class="nav flex-column sub-menu">
                        @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ route($currentPrefix . 'cpl-cpmk.cpl-cpmk-mk-add') }}">Tambah
                                    CPL-CPMK-MK</a>
                            </li>
                        @endif
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'cpl-cpmk.cpl-cpmk-mk') }}">Pemetaan CPL-CPMK-MK</a>
                        </li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'cpl-cpmk.cpl-cpmk-mk-semester') }}">Pemetaan
                                CPL-CPMK-MK Semester</a></li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'cpl-cpmk.cpl-mk-cpmk') }}">Pemetaan CPL-MK-CPMK</a>
                        </li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'cpl-cpmk.mk-cpmk-subcpmk') }}">Pemetaan MK-CPMK-Sub
                                CPMK</a></li>
                    </ul>
                </div>
            </li>

            {{-- Asesmen --}}
            <li class="nav-item nav-category">Asesmen</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#asesmen" aria-expanded="false"
                    aria-controls="asesmen">
                    <i class="menu-icon mdi mdi-card-text-outline"></i>
                    <span class="menu-title">Asesmen</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="asesmen">
                    <ul class="nav flex-column sub-menu">
                        @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ route($currentPrefix . 'asesmen.asesmen-add') }}">Tambah Asesmen</a>
                            </li>
                        @endif
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'asesmen.metode-penilaian') }}">Metode Penilaian</a>
                        </li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'asesmen.tahap-penilaian') }}">Tahap Penilaian</a>
                        </li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'asesmen.bobot-penilaian') }}">Bobot Penilaian</a>
                        </li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'asesmen.NA-MK') }}">Nilai Akhir MK</a></li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'asesmen.NA-CPL') }}">Nilai Akhir CPL</a></li>
                    </ul>
                </div>
            </li>

            {{-- RPS Management --}}
            <li class="nav-item nav-category">RPS</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#rps" aria-expanded="false"
                    aria-controls="rps">
                    <i class="menu-icon mdi mdi-file-document-outline"></i>
                    <span class="menu-title">RPS</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="rps">
                    <ul class="nav flex-column sub-menu">
                        @if (in_array($userOtoritas, [
                                'Kepala Program Studi',
                                'Penjamin Mutu Program Studi',
                                'Penjamin Mutu Fakultas',
                                'Penjamin Mutu Universitas',
                            ]))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route($currentPrefix . 'rps.list') }}">Daftar RPS</a>
                            </li>
                        @endif

                        @if ($userOtoritas == 'Kepala Program Studi')
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('kepala-program-studi.rps.validation.list') }}">Validasi RPS</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- Penilaian --}}
            <li class="nav-item nav-category">Penilaian</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#penilaian" aria-expanded="false"
                    aria-controls="penilaian">
                    <i class="menu-icon mdi mdi-file-check"></i>
                    <span class="menu-title">Penilaian</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="penilaian">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'penilaian.penilaian-dengan-soal') }}">Data
                                Penilaian</a>
                        </li>
                    </ul>
                </div>
            </li>
            {{-- Mahasiswa --}}
            <li class="nav-item nav-category">Mahasiswa</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#mahasiswa" aria-expanded="false"
                    aria-controls="mahasiswa">
                    <i class="menu-icon mdi mdi-account-group"></i>
                    <span class="menu-title">Mahasiswa</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="mahasiswa">
                    <ul class="nav flex-column sub-menu">
                        @if (in_array($userOtoritas, ['Dosen', 'Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ route($currentPrefix . 'mahasiswa.create') }}">Tambah
                                    Mahasiswa</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'mahasiswa.index') }}">Daftar Mahasiswa</a>
                        </li>
                    </ul>
                </div>
            </li>
            {{-- Visualisasi --}}
            <li class="nav-item nav-category">Visualisasi</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#visualisasi" aria-expanded="false"
                    aria-controls="visualisasi">
                    <i class="menu-icon mdi mdi-chart-arc"></i>
                    <span class="menu-title">Visualisasi</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="visualisasi">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'visualisasi.visual-mahasiswa') }}">Per Mahasiswa</a>
                        </li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'visualisasi.visual-mahasiswaAngkatan') }}">Per
                                Angkatan</a>
                        </li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{ route($currentPrefix . 'visualisasi.visual-mahasiswaMataKuliah') }}">Per Mata
                                Kuliah</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif
    </ul>
</nav>
