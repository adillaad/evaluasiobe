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

@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h4 class="card-title mb-0 me-auto">CPL Program Studi</h4>
                    @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-success text-white btn-icon-text" data-bs-toggle="modal" data-bs-target="#importCplModal">
                                <i class="ti-upload me-1"></i>
                                <span>Import CPL Prodi</span>
                            </button>
                            <a href="{{ route($currentPrefix . 'cpl.create') }}" class="btn btn-primary btn-icon-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Tambah CPL Prodi</span>
                            </a>
                        </div>
                    @endif
                </div>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                    :kurikulums="$kurikulums"
                    :showKurikulum="true"
                />
                <style>
                    .table-cpl-prodi {
                        width: 100% !important;
                    }
                    .table-cpl-prodi th {
                        font-weight: 600 !important;
                        font-size: 13px !important;
                    }
                    .table-cpl-prodi td {
                        vertical-align: middle !important;
                        white-space: normal !important;
                        word-wrap: break-word !important;
                        word-break: break-word !important;
                    }
                    .badge-cpl-kode {
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
                    .badge-cpl-count {
                        background-color: #e2e8f0;
                        color: #475569;
                        font-size: 11px;
                        border-radius: 6px;
                        padding: 2px 6px;
                        margin-left: 6px;
                        font-weight: 700;
                    }
                    .kur-nav-pills .nav-link.active .badge-cpl-count {
                        background-color: rgba(255, 255, 255, 0.25) !important;
                        color: #ffffff !important;
                    }
                </style>

                <div class="table-responsive mt-3">
                    <table class="table table-hover dataTable table-cpl-prodi align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 120px;">Kode CPL</th>
                                <th style="min-width: 300px; max-width: 550px;">Deskripsi CPL</th>
                                @if ($userOtoritas != 'Penjamin Mutu Program Studi')
                                    <th style="width: 200px;">Prodi</th>
                                @endif
                                @if (!in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi']))
                                    <th style="width: 200px;">Fakultas</th>
                                @endif
                                @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                                    <th style="width: 100px;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpls as $cpl)
                                <tr>
                                    <td class="fw-semibold text-secondary">{{ $loop->iteration }}</td>
                                    <td><span class="badge-cpl-kode">{{ $cpl->kode }}</span></td>
                                    <td style="line-height: 1.5; color: #334155;">{{ $cpl->judul }}</td>
                                    @if ($userOtoritas != 'Penjamin Mutu Program Studi')
                                        <td>{{ $cpl->prodi->nama }}</td>
                                    @endif
                                    @if (!in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi']))
                                        <td>{{ $cpl->prodi->fakultas->nama }}</td>
                                    @endif
                                    @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <a href="{{ route($currentPrefix . 'cpl.edit', encrypt($cpl->id)) }}"
                                                    class="btn btn-outline-primary btn-icons" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                    <i class="ti-pencil"></i>
                                                </a>
                                                <form action="{{ route($currentPrefix . 'cpl.delete', encrypt($cpl->id)) }}"
                                                    method="post" class="d-inline m-0 p-0">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-outline-danger btn-icons"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                        onclick="return confirm('Hapus CPL {{ $cpl->kode }}?')">
                                                        <i class="ti-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Import CPL Program Studi --}}
    @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
        <div class="modal fade" id="importCplModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-sm rounded-3">
                    <form action="{{ route($currentPrefix . 'cpl.import-excel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header py-3 px-4 bg-light border-bottom text-start">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded-2">
                                    <i class="ti-upload fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title fs-6 fw-bold mb-0 text-dark">Import CPL Program Studi</h5>
                                    <span class="text-muted small">Impor data CPL dari file Excel</span>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="alert alert-info p-3 small border-0 bg-info bg-opacity-10 text-info-emphasis rounded-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <i class="ti-info-alt me-1"></i> Format data CPL harus sesuai dengan template Excel yang disediakan.
                                </div>
                                <a href="{{ route($currentPrefix . 'cpl.download-template') }}" class="btn btn-sm btn-outline-success btn-icon-text text-nowrap">
                                    <i class="ti-download me-1"></i>
                                    <span>Download Template Excel</span>
                                </a>
                            </div>

                            <div class="mb-3">
                                <label for="id_kurikulum_import" class="form-label small fw-bold text-dark mb-1">Kurikulum (Opsional)</label>
                                <select name="id_kurikulum" id="id_kurikulum_import" class="form-select form-select-sm">
                                    <option value="">-- Otomatis Deteksi dari Excel / Gunakan Kurikulum Terkini --</option>
                                    @foreach ($kurikulums as $k)
                                        <option value="{{ $k->id }}">Kurikulum {{ $k->tahun }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size: 0.75rem;"><i class="ti-info-circle me-1"></i> Jika kolom <i>Tahun Kurikulum</i> di file Excel terisi, sistem akan otomatis menggunakan tahun tersebut.</small>
                            </div>

                            <div class="mb-3">
                                <label for="excel_file" class="form-label small fw-bold text-dark mb-1">Pilih File Excel (.xlsx, .xls, .csv) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                            </div>
                        </div>
                        <div class="modal-footer py-2 px-4 bg-light border-top justify-content-between">
                            <button type="button" class="btn btn-light btn-sm text-secondary px-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success text-white btn-sm px-4 fw-bold shadow-sm">
                                <i class="ti-upload me-1"></i> Upload & Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection
