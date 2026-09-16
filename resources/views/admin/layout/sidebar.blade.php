{{-- @php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';
@endphp --}}
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    @if (in_array($userOtoritas, ['Admin', 'Admin Universitas']))
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route($currentPrefix . 'home') }}">
                    <i class="mdi mdi-grid-large menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route($currentPrefix . 'daftar-akun-prodi') }}">
                    <i class="mdi mdi-school menu-icon"></i>
                    <span class="menu-title">Daftar Akun Prodi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route($currentPrefix . 'tahun-ajaran.index') }}">
                    <i class="mdi mdi-calendar-clock menu-icon"></i>
                    <span class="menu-title">Tahun Ajaran</span>
                </a>
            </li>
            @if ($userOtoritas === 'Admin')
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#berita" aria-expanded="false"
                        aria-controls="berita">
                        <i class="menu-icon mdi mdi-newspaper"></i>
                        <span class="menu-title">Berita</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="berita">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link"href="{{ route($currentPrefix . 'berita.create') }}">Tambah Berita</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route($currentPrefix . 'berita.index') }}">Daftar Berita</a>
                            </li>
                            
                        </ul>
                    </div>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" href="{{ route($currentPrefix . 'list-user') }}">
                    <i class="menu-icon mdi mdi-account-circle-outline"></i>
                    <span class="menu-title">User</span>
                </a>
            </li>
            @if ($userOtoritas === 'Admin')
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#registrasi-universitas" aria-expanded="false"
                        aria-controls="registrasi-universitas" onclick="document.getElementById('registrasi').click()">
                        <i class="menu-icon mdi mdi-account-plus"></i>
                        <span class="menu-title">Registrasi Universitas</span>
                    </a>
                    <div class="collapse" id="registrasi-universitas" hidden="hidden">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a id="registrasi" class="nav-link" href="{{ route($currentPrefix . 'list-registrasi-universitas') }}">
                                    Daftar Registrasi Universitas
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#kurikulum" aria-expanded="false"
                    aria-controls="kurikulum" onclick="document.getElementById('kur').click()">
                    <i class="menu-icon mdi mdi-library-books"></i>
                    <span class="menu-title">Kurikulum</span>
                </a>
                <div class="collapse" id="kurikulum" hidden="hidden">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a id="kur" class="nav-link" href="{{ route($currentPrefix . 'list-kurikulum') }}">Daftar Kurikulum</a>
                        </li>
                    </ul>
                </div>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#cpl" aria-expanded="false" aria-controls="cpl">
                    <i class="menu-icon mdi mdi-format-list-bulleted-type"></i>
                    <span class="menu-title">CPL</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="cpl">
                    <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Admin Universitas')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'add-cpl') }}">Tambah CPL</a>
                        </li>
                    @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'list-cpl') }}">Daftar CPL</a>
                        </li>
                    </ul>
                </div>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#mk" aria-expanded="false"
                    aria-controls="mk">
                    <i class="menu-icon mdi mdi-collage"></i>
                    <span class="menu-title">MK</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="mk">
                    <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Admin Universitas')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'add-mk') }}">Tambah MK</a>
                        </li>
                    @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'list-mk') }}">Daftar MK</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#cplmk" aria-expanded="false"
                    aria-controls="cplmk">
                    <i class="menu-icon mdi mdi-playlist-check"></i>
                    <span class="menu-title">CPLMK</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="cplmk">
                    <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Admin Universitas')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'add-cplmk') }}">Tambah CPLMK</a>
                        </li>
                    @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'list-cplmk') }}">Daftar CPLMK</a>
                        </li>
                    </ul>
                </div>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#rps" aria-expanded="false"
                    aria-controls="rps">
                    <i class="menu-icon mdi mdi-file-outline"></i>
                    <span class="menu-title">RPS</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="rps">
                    <ul class="nav flex-column sub-menu">
                    <!--@if ($userOtoritas == 'Admin Universitas')-->
                    <!--    <li class="nav-item">-->
                    <!--        <a class="nav-link" href="{{ route($currentPrefix . 'add-rps') }}">Tambah RPS</a>-->
                    <!--    </li>-->
                    <!--@endif-->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'list-rps') }}">Daftar RPS</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#cpmk" aria-expanded="false"
                    aria-controls="cpmk">
                    <i class="menu-icon mdi mdi-target"></i>
                    <span class="menu-title">CPMK</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="cpmk">
                    <ul class="nav flex-column sub-menu">
                    @if ($userOtoritas == 'Admin Universitas')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'add-cpmk') }}">Tambah CPMK</a>
                        </li>
                    @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'list-cpmk') }}">Daftar CPMK</a>
                        </li>
                    </ul>
                </div>
            @if ($userOtoritas == 'Admin')
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#univ" aria-expanded="false"
                        aria-controls="univ">
                        <i class="menu-icon mdi mdi-city"></i>
                        <span class="menu-title">Universitas</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="univ">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route($currentPrefix . 'add-universitas') }}">
                                    Tambah Universitas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route($currentPrefix . 'list-universitas') }}">
                                    Daftar Universitas
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#fakultas" aria-expanded="false"
                    aria-controls="fakultas">
                    <i class="menu-icon mdi mdi-city"></i>
                    <span class="menu-title">Fakultas</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="fakultas">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'add-fakultas') }}">Tambah Fakultas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'list-fakultas') }}">Daftar Fakultas</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#prodi" aria-expanded="false"
                    aria-controls="prodi">
                    <i class="menu-icon mdi mdi-clipboard"></i>
                    <span class="menu-title">Prodi</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="prodi">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'add-prodi') }}">Tambah Prodi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'list-prodi') }}">Daftar Prodi</a>
                        </li>
                    </ul>
                </div>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#soal" aria-expanded="false"
                    aria-controls="soal">
                    <i class="menu-icon mdi mdi-comment-question-outline"></i>
                    <span class="menu-title">Soal</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="soal">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'list-soal') }}">Daftar Soal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route($currentPrefix . 'summary-soal') }}">Summary Soal</a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    @endif
</nav>
