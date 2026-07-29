<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        {{-- Dashboard --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('mahasiswa.home') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        {{-- Transkrip Kompetensi --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('mahasiswa.transkrip-kompetensi') }}">
                <i class="mdi mdi-book-open-page-variant menu-icon"></i>
                <span class="menu-title">Transkrip Kompetensi</span>
            </a>
        </li>

        {{-- Pemetaan Profesi-CPMK --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('mahasiswa.pemetaan-cpmk-profesi') }}">
                <i class="mdi mdi-briefcase-check menu-icon"></i>
                <span class="menu-title">Pemetaan Profesi</span>
            </a>
        </li>

        {{-- Rekomendasi Mata Kuliah --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('mahasiswa.rekomendasi-mk') }}">
                <i class="mdi mdi-lightbulb-on menu-icon"></i>
                <span class="menu-title">Rekomendasi MK</span>
            </a>
        </li>
    </ul>
</nav>
