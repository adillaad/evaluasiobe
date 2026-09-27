{{-- 
    Partial: readListProfil.blade.php
    Diload via AJAX ke dalam #read.
    Tombol Edit → showProfil(id) → buka modal edit
    Tombol Delete → deleteProfil(id) → notif di #notif-wrapper
--}}
@php
    $selectedProdiId = request('prodi_id');
    if (!$selectedProdiId && request('kurikulum_id')) {
        $selectedProdiId = \Illuminate\Support\Facades\DB::table('kurikulums')->where('id', request('kurikulum_id'))->value('id_prodi');
    }
    if (!$selectedProdiId && auth()->check()) {
        $selectedProdiId = auth()->user()->id_prodiUser ?? (auth()->user()->prodi ? auth()->user()->prodi->id : null);
    }
    if ($selectedProdiId) {
        $isAptikom = (bool) \Illuminate\Support\Facades\DB::table('prodi')->where('id', $selectedProdiId)->value('is_aptikom');
    } else {
        $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
    }
@endphp

<style>
    .table-responsive table td.wrap-content {
        white-space: normal;
        word-wrap: break-word;
    }
    .table-responsive table td.profil-karir-col {
        white-space: normal;
        word-wrap: break-word;
        max-width: 260px;
    }
    .badge-pl-kode {
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        display: inline-block;
    }
    .kur-tabs-wrapper {
        background: #ffffff;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
    }
    .kur-nav-pills {
        gap: 8px;
    }
    .kur-nav-pills .nav-link {
        color: #475569 !important;
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        white-space: nowrap !important;
    }
    .kur-nav-pills .nav-link:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
    @if ($isAptikom)
        .kur-nav-pills .nav-link.active {
            background: linear-gradient(135deg, #006199 0%, #004c78 100%) !important;
            color: #ffffff !important;
            border-color: #006199 !important;
            box-shadow: 0 4px 12px rgba(0, 97, 153, 0.28) !important;
        }
    @else
        .kur-nav-pills .nav-link.active {
            background: linear-gradient(135deg, #76C0EC 0%, #4faae5 100%) !important;
            color: #ffffff !important;
            border-color: #76C0EC !important;
            box-shadow: 0 4px 12px rgba(118, 192, 236, 0.28) !important;
        }
    @endif
    .badge-pl-count {
        background-color: #e2e8f0;
        color: #475569;
        font-size: 11px;
        border-radius: 6px;
        padding: 2px 6px;
        margin-left: 6px;
        font-weight: 700;
    }
    .kur-nav-pills .nav-link.active .badge-pl-count {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    .btn-outline-primary {
        color: #0284c7 !important;
        border-color: #0284c7 !important;
        background-color: #ffffff !important;
    }
    .btn-outline-primary i {
        color: #0284c7 !important;
        transition: color 0.2s ease;
    }
    .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
        background-color: #0284c7 !important;
        border-color: #0284c7 !important;
        color: #ffffff !important;
    }
    .btn-outline-primary:hover i, .btn-outline-primary:focus i, .btn-outline-primary:active i {
        color: #ffffff !important;
    }

    .btn-outline-danger {
        color: #dc3545 !important;
        border-color: #dc3545 !important;
        background-color: #ffffff !important;
    }
    .btn-outline-danger i {
        color: #dc3545 !important;
        transition: color 0.2s ease;
    }
    .btn-outline-danger:hover, .btn-outline-danger:focus, .btn-outline-danger:active {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
    }
    .btn-outline-danger:hover i, .btn-outline-danger:focus i, .btn-outline-danger:active i {
        color: #ffffff !important;
    }
</style>

@php
    $kurikulumMap = collect();

    if (isset($kurikulums)) {
        foreach ($kurikulums as $k) {
            $kurikulumMap->put($k->id, (object)[
                'id' => $k->id,
                'tahun' => $k->tahun,
                'nama' => 'Kurikulum ' . $k->tahun
            ]);
        }
    }

    foreach ($listProfil as $pl) {
        if ($pl->kurikulum_id && !$kurikulumMap->has($pl->kurikulum_id)) {
            $tahun = $pl->kurikulum->tahun ?? 'N/A';
            $kurikulumMap->put($pl->kurikulum_id, (object)[
                'id' => $pl->kurikulum_id,
                'tahun' => $tahun,
                'nama' => 'Kurikulum ' . $tahun
            ]);
        }
    }

    $tabKurikulums = $kurikulumMap->sortByDesc('tahun')->values();
    $uncategorizedPls = $listProfil->filter(function($p) {
        return empty($p->kurikulum_id);
    });
@endphp

{{-- Kurikulum Navigation Container --}}
<div class="kur-tabs-wrapper mb-3">
    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
        <span class="fw-bold text-secondary text-uppercase small" style="letter-spacing: 0.5px;">
            <i class="mdi mdi-filter-variant me-1"></i> PILIH KURIKULUM:
        </span>
        <span class="text-muted small">Klik tab Kurikulum untuk memfilter data Profil Lulusan</span>
    </div>
    <ul class="nav nav-pills kur-nav-pills overflow-auto flex-nowrap pb-1" id="plKurTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-pl-all-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-pl-all" data-target="#tab-pl-all" type="button" role="tab" aria-controls="tab-pl-all" aria-selected="true">
                <i class="mdi mdi-grid me-1"></i> Semua Kurikulum
                <span class="badge-pl-count">{{ $listProfil->count() }}</span>
            </button>
        </li>
        @foreach ($tabKurikulums as $kur)
            @php
                $plInKur = $listProfil->where('kurikulum_id', $kur->id);
            @endphp
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-pl-kur-{{ $kur->id }}-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-pl-kur-{{ $kur->id }}" data-target="#tab-pl-kur-{{ $kur->id }}" type="button" role="tab" aria-controls="tab-pl-kur-{{ $kur->id }}" aria-selected="false">
                    Kurikulum {{ $kur->tahun }}
                    <span class="badge-pl-count">{{ $plInKur->count() }}</span>
                </button>
            </li>
        @endforeach
        @if ($uncategorizedPls->count() > 0)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-pl-kur-uncategorized-tab" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-pl-kur-uncategorized" data-target="#tab-pl-kur-uncategorized" type="button" role="tab" aria-controls="tab-pl-kur-uncategorized" aria-selected="false">
                    Tanpa Kurikulum
                    <span class="badge-pl-count">{{ $uncategorizedPls->count() }}</span>
                </button>
            </li>
        @endif
    </ul>
</div>

{{-- Tab Content Panes --}}
<div class="tab-content" id="plKurTabContent">
    {{-- TAB ALL KURIKULUM --}}
    <div class="tab-pane fade show active" id="tab-pl-all" role="tabpanel" aria-labelledby="tab-pl-all-tab">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle dataTable">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="12%">Kode PL</th>
                        <th width="28%">Profil Karir</th>
                        <th>Graduate Profile</th>
                        @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                            <th width="12%" class="text-center">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if ($listProfil->isEmpty())
                        <tr>
                            <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']) ? 5 : 4 }}" class="text-center text-muted py-4">
                                Tidak ada data
                            </td>
                        </tr>
                    @else
                        @foreach ($listProfil as $key => $profil)
                            <tr>
                                <td class="text-center fw-semibold text-secondary">{{ $key + 1 }}</td>
                                <td><span class="badge-pl-kode">{{ $profil->kode ?: 'PL-'.($key+1) }}</span></td>
                                <td class="profil-karir-col">
                                    <span class="fw-bold text-dark">{{ $profil->namaProfil ?: ($profil->jenis ?: '-') }}</span>
                                </td>
                                <td class="wrap-content">{{ ucfirst($profil->deskripsi) }}</td>

                                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <button type="button" class="btn btn-outline-primary btn-icons"
                                                onclick="showProfil({{ $profil->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                <i class="ti-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-icons" onclick="deleteProfil({{ $profil->id }})"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- PER KURIKULUM TABS --}}
    @foreach ($tabKurikulums as $kur)
        @php
            $plsInTab = $listProfil->where('kurikulum_id', $kur->id);
        @endphp
        <div class="tab-pane fade" id="tab-pl-kur-{{ $kur->id }}" role="tabpanel" aria-labelledby="tab-pl-kur-{{ $kur->id }}-tab">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle dataTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="12%">Kode PL</th>
                            <th width="28%">Profil Karir</th>
                            <th>Graduate Profile</th>
                            @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                <th width="12%" class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($plsInTab->isEmpty())
                            <tr>
                                <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']) ? 5 : 4 }}" class="text-center text-muted py-4">
                                    Tidak ada data untuk Kurikulum {{ $kur->tahun }}
                                </td>
                            </tr>
                        @else
                            @foreach ($plsInTab->values() as $key => $profil)
                                <tr>
                                    <td class="text-center fw-semibold text-secondary">{{ $key + 1 }}</td>
                                    <td><span class="badge-pl-kode">{{ $profil->kode ?: 'PL-'.($key+1) }}</span></td>
                                    <td class="profil-karir-col">
                                        <span class="fw-bold text-dark">{{ $profil->namaProfil ?: ($profil->jenis ?: '-') }}</span>
                                    </td>
                                    <td class="wrap-content">{{ ucfirst($profil->deskripsi) }}</td>

                                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" class="btn btn-outline-primary btn-icons"
                                                    onclick="showProfil({{ $profil->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                    <i class="ti-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-icons" onclick="deleteProfil({{ $profil->id }})"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    {{-- TAB UNCATEGORIZED --}}
    @if ($uncategorizedPls->count() > 0)
        <div class="tab-pane fade" id="tab-pl-kur-uncategorized" role="tabpanel" aria-labelledby="tab-pl-kur-uncategorized-tab">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle dataTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="12%">Kode PL</th>
                            <th width="28%">Profil Karir</th>
                            <th>Graduate Profile</th>
                            @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                <th width="12%" class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($uncategorizedPls->values() as $key => $profil)
                            <tr>
                                <td class="text-center fw-semibold text-secondary">{{ $key + 1 }}</td>
                                <td><span class="badge-pl-kode">{{ $profil->kode ?: 'PL-'.($key+1) }}</span></td>
                                <td class="profil-karir-col">
                                    <span class="fw-bold text-dark">{{ $profil->namaProfil ?: ($profil->jenis ?: '-') }}</span>
                                </td>
                                <td class="wrap-content">{{ ucfirst($profil->deskripsi) }}</td>

                                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <button type="button" class="btn btn-outline-primary btn-icons"
                                                onclick="showProfil({{ $profil->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                <i class="ti-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-icons" onclick="deleteProfil({{ $profil->id }})"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
