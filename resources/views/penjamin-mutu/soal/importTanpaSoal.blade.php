{{-- @php
$routePrefix = [
    'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
    'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
    'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
];

$userOtoritas = auth()->user()->otoritas->otoritas;
$currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}
@extends('penjamin-mutu.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @php
        $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
        $isUnivLevel = in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Admin Universitas', 'Wakil Rektor']);
        $isFacultyLevel = in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan']);
        $isProdiLevel = in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen']);
    @endphp

    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form id="filterFormTS" action="{{ route($currentPrefix . 'penilaian.filter') }}" method="get" class="mb-3">
                    @csrf
                    <input type="hidden" name="type" value="tanpa-soal">
                    <div class="d-flex flex-column gap-3">
                        {{-- 1. Baris Filter Dropdown (Di Atas) --}}
                        <div class="d-flex align-items-end flex-wrap gap-2">
                            {{-- Filter Fakultas (Khusus Level Universitas) --}}
                            @if ($isUnivLevel && isset($faculties) && $faculties->isNotEmpty())
                                <div style="min-width: 150px;">
                                    <label for="fakultas_id" class="form-label fw-bold small mb-1">Fakultas</label>
                                    <select name="fakultas_id" id="fakultas_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua Fakultas --</option>
                                        @foreach ($faculties as $fac)
                                            <option value="{{ $fac->id }}" {{ request('fakultas_id') == $fac->id ? 'selected' : '' }}>
                                                {{ $fac->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Prodi (Level Universitas & Fakultas) --}}
                            @if (($isUnivLevel || $isFacultyLevel) && isset($programs) && $programs->isNotEmpty())
                                <div style="min-width: 150px;">
                                    <label for="prodi_id" class="form-label fw-bold small mb-1">Program Studi</label>
                                    <select name="prodi_id" id="prodi_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua Prodi --</option>
                                        @foreach ($programs as $prog)
                                            <option value="{{ $prog->id }}" {{ request('prodi_id') == $prog->id ? 'selected' : '' }}>
                                                {{ $prog->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Kurikulum --}}
                            @if (isset($kurikulums) && $kurikulums->isNotEmpty())
                                <div style="min-width: 140px;">
                                    <label for="kurikulum_id" class="form-label fw-bold small mb-1">Kurikulum</label>
                                    <select name="kurikulum_id" id="kurikulum_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua Kurikulum --</option>
                                        @foreach ($kurikulums as $kur)
                                            <option value="{{ $kur->id }}" {{ request('kurikulum_id') == $kur->id ? 'selected' : '' }}>
                                                {{ $kur->tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Tahun Ajaran --}}
                            @if (isset($tahunAjarans) && $tahunAjarans->isNotEmpty())
                                <div style="min-width: 140px;">
                                    <label for="tahun_ajaran_id" class="form-label fw-bold small mb-1">Tahun Ajaran</label>
                                    <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua TA --</option>
                                        @foreach ($tahunAjarans as $ta)
                                            <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                                {{ $ta->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Mata Kuliah --}}
                            @if (isset($mks) && $mks->isNotEmpty())
                                <div style="min-width: 170px;">
                                    <label for="mk_kode" class="form-label fw-bold small mb-1">Mata Kuliah</label>
                                    <select name="mk_kode" id="mk_kode" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua MK --</option>
                                        @foreach ($mks as $mk)
                                            <option value="{{ $mk->kode }}" {{ request('mk_kode') == $mk->kode ? 'selected' : '' }}>
                                                {{ $mk->nama }} ({{ $mk->kode }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Metode Penilaian --}}
                            @if (isset($metodePenilaians) && $metodePenilaians->isNotEmpty())
                                <div style="min-width: 150px;">
                                    <label for="metode_id" class="form-label fw-bold small mb-1">Metode Penilaian</label>
                                    <select name="metode_id" id="metode_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                        <option value="">-- Semua Metode --</option>
                                        @foreach ($metodePenilaians as $mp)
                                            <option value="{{ $mp->id }}" {{ request('metode_id') == $mp->id ? 'selected' : '' }}>
                                                {{ $mp->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Tombol Reset --}}
                            @if (request('course') || request('fakultas_id') || request('prodi_id') || request('kurikulum_id') || request('tahun_ajaran_id') || request('mk_kode') || request('metode_id'))
                                <div>
                                    <a href="{{ route($currentPrefix . 'penilaian.penilaian-tanpa-soal') }}" class="btn btn-secondary btn-sm px-4 fw-semibold d-flex align-items-center justify-content-center" style="height: 38px;">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- 2. Input Pencarian (Di Bawah) --}}
                        <div class="d-flex align-items-end gap-2">
                            <div style="max-width: 350px; width: 100%;">
                                <label for="course" class="form-label fw-bold small mb-1">Cari Mahasiswa</label>
                                <input id="course" name="course" type="text" class="form-control form-control-sm instant-search" style="height: 38px;"
                                    placeholder="Ketik nama atau NPM..."
                                    value="{{ request('course') }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                @if ($isUnivLevel)
                                    <th>Fakultas</th>
                                    <th>Prodi</th>
                                @elseif ($isFacultyLevel)
                                    <th>Prodi</th>
                                @endif
                                <th>Tahun Ajaran</th>
                                <th>Angkatan</th>
                                <th>Nama</th>
                                <th>NPM</th>
                                <th>Nama Mata Kuliah</th>
                                <th>Metode Penilaian</th>
                                <th>Nilai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($mutus->isEmpty())
                                <tr>
                                    <td colspan="{{ $isUnivLevel ? 10 : ($isFacultyLevel ? 9 : 8) }}" class="text-center text-muted py-4">Tidak ada data penilaian</td>
                                </tr>
                            @else
                                @php $groupIndex = 0; @endphp
                                @foreach ($mutus as $groupKey => $items)
                                        @php
                                            $groupIndex++;
                                            $first = $items->first();
                                            $metodeName = $first->Jenis ?? '-';
                                            $nilaiMetode = $first->Nilai ?? $items->avg('nilaiSoal') ?? 0;
                                            $taLabel = isset($first->ta_tahun) 
                                                ? ($first->ta_tahun . ($first->ta_semester ? ' - ' . $first->ta_semester : '')) 
                                                : ($first->tahunAjaran?->tahun ? ($first->tahunAjaran->tahun . ' - ' . $first->tahunAjaran->jenis_semester) : ($first->tahun ?? '-'));

                                            // Hitung Skor CPMK Mahasiswa untuk Mata Kuliah ini
                                            $npmMhs = $first->npm ?? $first->NPM;
                                            $courseKode = $first->Course;

                                            // Ambil seluruh record mutus mahasiswa ini pada mata kuliah ini
                                            $allMhsCourseMutus = \Illuminate\Support\Facades\DB::table('mutus')
                                                ->where(function($q) use ($npmMhs) {
                                                    $q->where('npm', $npmMhs)->orWhere('NPM', $npmMhs);
                                                })
                                                ->where('Course', $courseKode)
                                                ->get();

                                            $kmId = $first->konversi_metode_id;
                                            $mappedCpmkIds = collect();
                                            if ($kmId) {
                                                $mappedCpmkIds = \Illuminate\Support\Facades\DB::table('konversi_cpmk_metode')
                                                    ->where('konversi_metode_id', $kmId)
                                                    ->pluck('cpmk_id')
                                                    ->unique();
                                            }

                                            $cpmkScoresList = collect();
                                            $mhsDummy = new \App\Models\Mahasiswa();

                                            if ($mappedCpmkIds->isNotEmpty()) {
                                                foreach ($mappedCpmkIds as $cId) {
                                                    $cpmkDetail = \Illuminate\Support\Facades\DB::table('cpmks')->where('id', $cId)->first();
                                                    
                                                    // Dapatkan seluruh konversi_metode_id yang terikat ke CPMK ini pada MK ini
                                                    $allKmIdsForCpmk = \Illuminate\Support\Facades\DB::table('konversi_cpmk_metode as kcm')
                                                        ->join('konversi_metode as km', 'kcm.konversi_metode_id', '=', 'km.id')
                                                        ->join('penilaian_konversi as pk', 'km.penilaian_konversi_id', '=', 'pk.id')
                                                        ->where('pk.mk_kode', $courseKode)
                                                        ->where(function($q) use ($first) {
                                                            if (!empty($first->tahun_ajaran_id)) {
                                                                $q->where('pk.tahun_ajaran_id', $first->tahun_ajaran_id);
                                                            }
                                                        })
                                                        ->where('kcm.cpmk_id', $cId)
                                                        ->pluck('km.id')
                                                        ->unique();

                                                    if ($allKmIdsForCpmk->isEmpty()) {
                                                        $allKmIdsForCpmk = \Illuminate\Support\Facades\DB::table('konversi_cpmk_metode as kcm')
                                                            ->join('konversi_metode as km', 'kcm.konversi_metode_id', '=', 'km.id')
                                                            ->join('penilaian_konversi as pk', 'km.penilaian_konversi_id', '=', 'pk.id')
                                                            ->where('pk.mk_kode', $courseKode)
                                                            ->where('kcm.cpmk_id', $cId)
                                                            ->pluck('km.id')
                                                            ->unique();
                                                    }

                                                    if ($allKmIdsForCpmk->isEmpty()) {
                                                        $allKmIdsForCpmk = collect([$kmId]);
                                                    }

                                                    // Filter seluruh record mutus mahasiswa ini yang masuk ke CPMK tersebut
                                                    $matchingRecs = $allMhsCourseMutus->whereIn('konversi_metode_id', $allKmIdsForCpmk);

                                                    if ($matchingRecs->isNotEmpty()) {
                                                        $cpmkScore = $mhsDummy->calcWeightedScore($matchingRecs);
                                                    } else {
                                                        $cpmkScore = (float)$nilaiMetode;
                                                    }

                                                    $cpmkScoresList->push([
                                                        'kode' => $cpmkDetail->kode ?? ('CPMK-' . $cId),
                                                        'score' => $cpmkScore,
                                                    ]);
                                                }
                                            }
                                        @endphp
                                        {{-- Main Row Per Mahasiswa + Mata Kuliah + Metode + Tahun Ajaran --}}
                                        <tr class="table-light border-top">
                                            @if ($isUnivLevel)
                                                <td class="fw-semibold">{{ $first->nama_fakultas ?? '-' }}</td>
                                                <td class="fw-semibold">{{ $first->nama_prodi }}</td>
                                            @elseif ($isFacultyLevel)
                                                <td class="fw-semibold">{{ $first->nama_prodi }}</td>
                                            @endif
                                            <td class="fw-semibold small">{{ $taLabel }}</td>
                                            <td>{{ $first->angkatan }}</td>
                                            <td class="fw-semibold">{{ $first->nama_mhs }}</td>
                                            <td class="fw-semibold">{{ $first->npm }}</td>
                                            <td class="fw-semibold">{{ $first->nama_mk }}</td>
                                            <td class="fw-semibold">
                                        
                                                    {{ $metodeName }}
                                                
                                            </td>
                                            <td class="fw-semibold">
                                                    {{ number_format((float)$nilaiMetode, 1) }}
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary py-0 px-2 text-nowrap" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGroupTanpaSoal{{ $groupIndex }}" aria-expanded="false">
                                                    <i class="mdi mdi-chevron-down me-1"></i> Detail ({{ $cpmkScoresList->count() }})
                                                </button>
                                            </td>
                                        </tr>

                                         {{-- Collapsible Rows Per CPMK --}}
                                         <tr class="p-0 border-0">
                                             <td colspan="{{ $isUnivLevel ? 10 : ($isFacultyLevel ? 9 : 8) }}" class="p-0 border-0">
                                                 <div class="collapse" id="collapseGroupTanpaSoal{{ $groupIndex }}">
                                                     <div class="p-3 bg-light border-bottom border-secondary border-2">
                                                         <div class="text-muted fw-semibold mb-2" style="font-size: 0.9rem;">Hasil Score CPMK</div>
                                                         <div class="d-flex justify-content-between align-items-center text-secondary fw-bold border-bottom pb-1 mb-2" style="font-size: 0.85rem;">
                                                             <div>CPMK</div>
                                                             <div class="pe-3">Score CPMK</div>
                                                         </div>
                                                         <div class="d-flex flex-column gap-2">
                                                             @if ($cpmkScoresList->isEmpty())
                                                                 <div class="text-muted small">Tidak ada CPMK yang dipetakan langsung untuk metode konversi ini.</div>
                                                             @else
                                                                 @foreach ($cpmkScoresList as $cpmkItem)
                                                                     @php
                                                                         $cpmkLabel = $cpmkItem['kode'];
                                                                     @endphp
                                                                     <div class="d-flex justify-content-between align-items-center" style="font-size: 0.95rem;">
                                                                         <div>
                                                                             <span class="fw-semibold text-dark">{{ $cpmkLabel }}</span>
                                                                         </div>
                                                                         <div class="fw-bold text-dark pe-3" style="font-size: 0.95rem;">
                                                                             {{ number_format((float)$cpmkItem['score'], 2) }}
                                                                         </div>
                                                                     </div>
                                                                 @endforeach
                                                             @endif
                                                         </div>
                                                     </div>
                                                 </div>
                                             </td>
                                         </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($mutus->hasPages())
                <div class="card-footer bg-white border-top py-2 px-3">
                    {{ $mutus->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('filterFormTS');
                if (!form) return;

                // Auto submit on dropdown select change
                const dropdowns = form.querySelectorAll('.filter-auto-submit');
                dropdowns.forEach(select => {
                    select.addEventListener('change', function() {
                        form.submit();
                    });
                });

                // Instant typing search (DOM instant filter + 600ms debounced server submit)
                const searchInput = form.querySelector('.instant-search');
                const tableRows = document.querySelectorAll('table tbody tr');

                if (searchInput) {
                    let timer = null;

                    // Restore focus and cursor position after page reload if search value exists
                    const val = searchInput.value;
                    if (val) {
                        searchInput.focus();
                        searchInput.setSelectionRange(val.length, val.length);
                    }

                    searchInput.addEventListener('input', function() {
                        const filterText = searchInput.value.toLowerCase().trim();

                        // 1. Filter rows in current table instantly (0ms UI feedback)
                        tableRows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            if (!filterText || text.includes(filterText)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        // 2. Submit form after 600ms pause in typing for full DB search
                        clearTimeout(timer);
                        timer = setTimeout(function() {
                            form.submit();
                        }, 600);
                    });
                }
            });
        </script>
    @endpush
@endsection
