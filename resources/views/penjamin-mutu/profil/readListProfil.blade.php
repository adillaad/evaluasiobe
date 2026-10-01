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
