@php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';
@endphp 
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route($currentPrefix . 'home') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#rps" aria-expanded="false" aria-controls="rps">
                <i class="menu-icon mdi mdi-view-headline"></i>
                <span class="menu-title">RPS</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="rps">
                <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Dosen')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'rps-add') }}">Tambah RPS</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'rps-list') }}">Daftar RPS</a>
                    </li>
                </ul>
            </div>
        </li> -->

        <li class="nav-item">
            <a class="nav-link" href="{{ route($currentPrefix . 'rps-list') }}">
                <i class="menu-icon mdi mdi-view-headline"></i>
                <span class="menu-title">RPS</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#cpl" aria-expanded="false" aria-controls="cpl">
                <i class="menu-icon mdi mdi-checkbox-multiple-blank"></i>
                <span class="menu-title">CPLMK</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="cpl">
                <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Dosen')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'cplmk-add') }}">Tambah CPLMK</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'cplmk-list') }}">Daftar CPLMK</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#cpmk" aria-expanded="false" aria-controls="cpmk">
                <i class="menu-icon mdi mdi-message-text"></i>
                <span class="menu-title">CPMK</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="cpmk">
                <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Dosen')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'cpmk-add') }}">Tambah CPMK</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'cpmk-list') }}">Daftar CPMK</a>
                    </li>
                </ul>
            </div>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#activities" aria-expanded="false"
                aria-controls="activities">
                <i class="menu-icon mdi mdi-checkbox-multiple-blank-outline"></i>
                <span class="menu-title">Activities</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="activities">
                <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Dosen')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'activities-add') }}">Tambah Activities</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'activities-list') }}">Daftar Activities</a>
                    </li>
                </ul>
            </div>
        </li> -->
        
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#komponen" aria-expanded="false"
                aria-controls="komponen">
                <i class="menu-icon mdi mdi-message-text-outline"></i>
                <span class="menu-title">Komponen</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="komponen">
                <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Dosen')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'Jenis-add') }}">Tambah Komponen</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'Jenis-list') }}">Lihat Komponen</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#rubrik" aria-expanded="false" aria-controls="rubrik">
                <i class="menu-icon mdi mdi-book-open"></i>
                <span class="menu-title">Rubrik Penilaian</span>
                <i class="menu-arrow"></i>
            </a>

            <div class="collapse" id="rubrik">
                <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Dosen')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'rubrik-add') }}">Tambah Rubrik</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'rubrik-list') }}">Daftar Rubrik</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#soal" aria-expanded="false" aria-controls="soal">
                <i class="menu-icon mdi mdi-pen"></i>
                <span class="menu-title">Soal</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="soal">
                <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Dosen')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'soal-addRaw') }}">Tambah Soal</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'soal-list') }}">Daftar Soal</a>
                    </li>
                </ul>
            </div>
        </li>
         <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#tanpa-soal" aria-expanded="false"
                aria-controls="tanpa-soal">
                <i class="menu-icon mdi mdi-file-document-outline"></i>
                <span class="menu-title">Tanpa Soal</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="tanpa-soal">
                <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Dosen')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'addRawTS') }}">Tambah Tanpa
                                Soal</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'tanpa-soal-list') }}">Daftar Tanpa
                            Soal</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#excel" aria-expanded="false"
                aria-controls="excel">
                <i class="menu-icon mdi mdi-file-document"></i>
                <span class="menu-title">Penilaian</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="excel">
                <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Dosen')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'add-mutu') }}">Download Template</a>
                        </li>
                    @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'import-mutu') }}">Import Nilai</a>
                        </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#mahasiswa" aria-expanded="false" aria-controls="mahasiswa">
                <i class="menu-icon mdi mdi-account-group"></i>
                <span class="menu-title">Mahasiswa</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="mahasiswa">
                <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Dosen')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'mahasiswa.create') }}">Tambah Mahasiswa</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'mahasiswa.index') }}">Daftar Mahasiswa</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#visualisasi" aria-expanded="false"
                aria-controls="visualisasi">
                <i class="menu-icon mdi mdi-chart-arc"></i>
                <span class="menu-title">Visualisasi</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="visualisasi">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'visual-mahasiswa') }}">Per Mahasiswa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'visual-mahasiswaAngkatan') }}">Per Angkatan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'visual-mahasiswaMataKuliah') }}">Per Mata Kuliah</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#profil" aria-expanded="false"
                aria-controls="profil">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Profil Lulusan</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="profil">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'indexListProfil') }}">List Profil Lulusan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($currentPrefix . 'indexkompetensi') }}">List Profil
                            Kompetensi</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>
