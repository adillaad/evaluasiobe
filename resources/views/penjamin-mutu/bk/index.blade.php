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

    $allBkItemsCollection = isset($allBkItems) ? $allBkItems : collect();
    $kurikulumMap = collect();
    foreach ($kurikulums as $k) {
        $kurikulumMap->put($k->id, (object)[
            'id' => $k->id,
            'tahun' => $k->tahun,
            'nama' => 'Kurikulum ' . $k->tahun
        ]);
    }

    foreach ($allBkItemsCollection as $bkItem) {
        if ($bkItem->kurikulum_id && !$kurikulumMap->has($bkItem->kurikulum_id)) {
            $tahun = $bkItem->kurikulum->tahun ?? 'N/A';
            $kurikulumMap->put($bkItem->kurikulum_id, (object)[
                'id' => $bkItem->kurikulum_id,
                'tahun' => $tahun,
                'nama' => 'Kurikulum ' . $tahun
            ]);
        }
    }

    $tabKurikulums = $kurikulumMap->sortByDesc('tahun')->values();
    $uncategorizedBks = $allBkItemsCollection->filter(function($b) {
        return empty($b->kurikulum_id);
    });
@endphp

@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

    <style>
        .btn-outline-primary {
            color: #2664F5 !important;
            border-color: #2664F5 !important;
            background-color: #ffffff !important;
        }
        .btn-outline-primary i {
            color: #2664F5 !important;
            transition: color 0.2s ease;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            background-color: #2664F5 !important;
            border-color: #2664F5 !important;
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
                background: linear-gradient(135deg, #2664F5 0%, #1d52cc 100%) !important;
                color: #ffffff !important;
                border-color: #2664F5 !important;
                box-shadow: 0 4px 12px rgba(38, 100, 245, 0.28) !important;
            }
        @endif
        .badge-bk-count {
            background-color: #e2e8f0;
            color: #475569;
            font-size: 11px;
            border-radius: 6px;
            padding: 2px 6px;
            margin-left: 6px;
            font-weight: 700;
        }
        .kur-nav-pills .nav-link.active .badge-bk-count {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
    </style>

    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']))
        <div class="container-fluid mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="card-title fw-bold mb-3">Tambah Bahan Kajian</h4>
                    <form action="{{ route($currentPrefix . 'bk.bk-store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="nama" class="fw-semibold text-dark mb-1">Nama Bahan Kajian :</label>
                                    <input type="text" value="{{ old('nama') }}" name="nama" id="nama" class="form-control rounded-3"
                                        placeholder="Ketik Nama Bahan Kajian" required>
                                    @error('nama')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="kurikulum_id" class="fw-semibold text-dark mb-1">Kurikulum :</label>
                                    <select name="kurikulum_id" id="kurikulum_id" class="form-select rounded-3" required>
                                        <option value="">-- Pilih Kurikulum --</option>
                                        @foreach ($kurikulums as $kurikulum)
                                            <option value="{{ $kurikulum->id }}"
                                                {{ old('kurikulum_id') == $kurikulum->id ? 'selected' : '' }}>
                                                Kurikulum {{ $kurikulum->tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kurikulum_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label for="rumpun_select_add" class="fw-semibold text-dark mb-1">Rumpun BK :</label>
                                    <select name="rumpun_select" id="rumpun_select_add" class="form-select rounded-3 rumpun-select-toggle" data-target="#new_rumpun_box_add">
                                        <option value="">-- Pilih Rumpun yang Sudah Ada --</option>
                                        @if (isset($existingRumpuns))
                                            @foreach ($existingRumpuns as $rumpunOption)
                                                <option value="{{ $rumpunOption }}" {{ old('rumpun') == $rumpunOption ? 'selected' : '' }}>
                                                    {{ $rumpunOption }}
                                                </option>
                                            @endforeach
                                        @endif
                                        <option value="__NEW__" class="fw-bold text-primary">+ Tambah Rumpun Baru...</option>
                                    </select>
                                    
                                    <div id="new_rumpun_box_add" class="mt-2 d-none">
                                        <input type="text" name="rumpun_new" class="form-control rounded-3" placeholder="Ketik Nama Rumpun Baru...">
                                    </div>
                                    @error('rumpun')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary rounded-3 px-4 font-weight-bold shadow-sm">
                                <i class="mdi mdi-plus-circle me-1"></i> Tambah Bahan Kajian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>    
    @endif

    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="card-title fw-bold mb-1">List Bahan Kajian</h4>
                        <p class="text-muted small mb-0">Daftar Bahan Kajian (BK) dikelompokkan berdasarkan Rumpun.</p>
                    </div>

                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']))
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-success text-white btn-icon-text" data-bs-toggle="modal" data-bs-target="#importBkModal">
                                <i class="ti-upload me-1"></i>
                                <span>Import Bahan Kajian</span>
                            </button>
                        </div>
                    @endif
                </div>

                <x-filter-form :showKurikulum="true" :kurikulums="$kurikulums" />

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 mt-2">
                    {{-- Kiri: Badge status filter jika ada --}}
                    <div>
                        @if (request('search') || request('kurikulum_id'))
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="text-muted small"><i class="ti-filter me-1"></i>Filter aktif:</span>
                                @if (request('kurikulum_id'))
                                    @php $selKur = $kurikulums->firstWhere('id', request('kurikulum_id')); @endphp
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1.5 rounded-2 font-12 fw-medium">
                                        Kurikulum: {{ $selKur ? 'Kurikulum ' . $selKur->tahun : request('kurikulum_id') }}
                                    </span>
                                @endif
                                @if (request('search'))
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1.5 rounded-2 font-12 fw-medium">
                                        Pencarian: "{{ request('search') }}"
                                    </span>
                                @endif
                                <a href="{{ Request::route() ? route(Request::route()->getName()) : url()->current() }}" class="btn btn-link btn-sm text-danger text-decoration-none p-0 ms-2 font-12">
                                    <i class="ti-close font-10 me-1"></i>Hapus Filter
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Kanan: Kolom Search persis seperti referensi gambar --}}
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2 m-0" id="bkSearchForm">
                            @if (request('kurikulum_id'))
                                <input type="hidden" name="kurikulum_id" value="{{ request('kurikulum_id') }}">
                            @endif
                            <label for="table_search_input" class="mb-0 text-dark font-14" style="font-weight: 500;">Search:</label>
                            <div class="position-relative">
                                <input type="text" 
                                       id="table_search_input" 
                                       name="search" 
                                       class="form-control form-control-sm rounded-2 search-input-box" 
                                       style="width: 220px; height: 36px; border: 1px solid #b0c4de; font-size: 13.5px; padding-right: {{ request('search') ? '30px' : '10px' }};" 
                                       placeholder="" 
                                       value="{{ request('search') }}">
                                @if (request('search'))
                                    <a href="{{ route(Request::route()->getName(), request()->except('search', 'page')) }}" 
                                       class="position-absolute end-0 top-50 translate-middle-y text-muted text-decoration-none pe-2" 
                                       title="Hapus pencarian">
                                        <i class="ti-close font-11"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive mt-1">
                    <table class="table table-bordered table-hover align-middle w-100 bk-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th class="text-center" style="width: 120px;">Kode BK</th>
                                <th>Nama Bahan Kajian</th>
                                <th class="text-center" style="width: 150px;">Kurikulum</th>
                                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']))
                                    <th class="text-center" style="width: 120px;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $runningNo = $bks->firstItem() ?? 1;
                                $sectionCounter = 0;
                            @endphp
                            @forelse ($groupedBks as $rumpun => $items)
                                @php
                                    $sectionCounter++;
                                    $letter = $rumpunLetters[$rumpun] ?? chr(64 + $sectionCounter);
                                @endphp
                                <tr class="table-light bk-rumpun-header">
                                    <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']) ? 5 : 4 }}" class="py-2.5 px-3 fw-bold text-dark">
                                        {{ $letter }}. {{ $rumpun ?: 'Unassigned / Lainnya' }}
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border font-11 ms-2 px-2 py-0.5 rounded-pill fw-normal">
                                            {{ count($items) }} BK
                                        </span>
                                    </td>
                                </tr>
                                @foreach ($items as $index => $bk)
                                    <tr class="bk-item-row">
                                        <td class="text-center font-weight-medium text-muted">{{ $runningNo++ }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-2.5 py-1.5 font-12 rounded-2 fw-bold">
                                                {{ $bk->kode }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $bk->nama }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-2.5 py-1.5 font-12 rounded-2 fw-semibold">
                                                {{ $bk->kurikulum->tahun ?? '-' }}
                                            </span>
                                        </td>
                                        @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']))
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <button type="button" 
                                                            class="btn btn-outline-primary btn-icons btn-edit-bk"
                                                            data-id="{{ $bk->id }}"
                                                            data-nama="{{ $bk->nama }}"
                                                            data-kurikulum="{{ $bk->kurikulum_id }}"
                                                            data-rumpun="{{ $bk->rumpun }}"
                                                            data-bs-toggle="tooltip"
                                                            title="Edit Bahan Kajian">
                                                        <i class="ti-pencil"></i>
                                                    </button>
                                                    <form action="{{ route($currentPrefix . 'bk.bk-delete', $bk->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus Bahan Kajian {{ $bk->kode }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-icons" data-bs-toggle="tooltip" title="Hapus">
                                                            <i class="ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']) ? 5 : 4 }}" class="text-center text-muted py-5">
                                        <i class="mdi mdi-database-search-outline font-36 d-block mb-2 text-secondary"></i>
                                        @if (request('search'))
                                            <div class="fw-semibold text-dark font-15">Tidak ada Bahan Kajian yang cocok dengan kata kunci "{{ request('search') }}".</div>
                                            <p class="small text-muted mb-3">Coba gunakan kata kunci lain atau hapus filter.</p>
                                            <a href="{{ Request::route() ? route(Request::route()->getName(), request()->except('search', 'page')) : url()->current() }}" class="btn btn-sm btn-outline-primary rounded-2 px-3">
                                                <i class="ti-reload me-1"></i> Bersihkan Kata Kunci
                                            </a>
                                        @else
                                            <div class="fw-semibold text-dark font-15">Belum ada data Bahan Kajian.</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Kontrol Pagination --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3 pt-3 border-top">
                    <div class="text-muted small">
                        Menampilkan <strong>{{ $bks->firstItem() ?? 0 }}</strong> – <strong>{{ $bks->lastItem() ?? 0 }}</strong> dari <strong>{{ $bks->total() }}</strong> Bahan Kajian
                        @if (request('search') || request('kurikulum_id'))
                            <span class="text-secondary">(difilter)</span>
                        @endif
                    </div>
                    <div class="bk-pagination-wrapper">
                        {{ $bks->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT BAHAN KAJIAN -->
    <div class="modal fade" id="editBkModal" tabindex="-1" aria-labelledby="editBkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editBkModalLabel">Edit Bahan Kajian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editBkForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label fw-semibold text-dark">Nama Bahan Kajian :</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_kurikulum_id" class="form-label fw-semibold text-dark">Kurikulum :</label>
                            <select name="kurikulum_id" id="edit_kurikulum_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Kurikulum --</option>
                                @foreach ($kurikulums as $kurikulum)
                                    <option value="{{ $kurikulum->id }}">Kurikulum {{ $kurikulum->tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_rumpun_select" class="form-label fw-semibold text-dark">Rumpun BK :</label>
                            <select name="rumpun_select" id="edit_rumpun_select" class="form-select rounded-3 rumpun-select-toggle" data-target="#edit_new_rumpun_box">
                                <option value="">-- Pilih Rumpun yang Sudah Ada --</option>
                                @if (isset($existingRumpuns))
                                    @foreach ($existingRumpuns as $rumpunOption)
                                        <option value="{{ $rumpunOption }}">{{ $rumpunOption }}</option>
                                    @endforeach
                                @endif
                                <option value="__NEW__" class="fw-bold text-primary">+ Tambah Rumpun Baru...</option>
                            </select>
                            
                            <div id="edit_new_rumpun_box" class="mt-2 d-none">
                                <input type="text" name="rumpun_new" id="edit_rumpun_new" class="form-control rounded-3" placeholder="Ketik Nama Rumpun Baru...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 font-weight-bold shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Import Bahan Kajian --}}
    @if (in_array($userOtoritas, ['Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Program Studi']))
        <div class="modal fade" id="importBkModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-sm rounded-3">
                    <form action="{{ route($currentPrefix . 'bk.import-excel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header py-3 px-4 bg-light border-bottom text-start">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded-2">
                                    <i class="ti-upload fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title fs-6 fw-bold mb-0 text-dark">Import Bahan Kajian</h5>
                                    <span class="text-muted small">Impor data Bahan Kajian dari file Excel</span>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="alert alert-info p-3 small border-0 bg-info bg-opacity-10 text-info-emphasis rounded-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <i class="ti-info-alt me-1"></i> Format data Bahan Kajian harus sesuai dengan template Excel.
                                </div>
                                <a href="{{ route($currentPrefix . 'bk.download-template') }}" class="btn btn-sm btn-outline-success btn-icon-text text-nowrap">
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

    <style>
        .bk-table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .bk-table th {
            border-bottom: 2px solid #e2e8f0 !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .bk-table tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        }

        .bk-table tbody td {
            border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .bk-table tbody tr:hover td {
            background-color: rgba(2, 132, 199, 0.03) !important;
        }

        .bk-pagination-wrapper .pagination {
            margin-bottom: 0 !important;
            gap: 3px;
        }

        .bk-pagination-wrapper .page-link {
            color: #475569;
            border-radius: 6px !important;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }

        .bk-pagination-wrapper .page-link:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .bk-pagination-wrapper .page-item.active .page-link {
            background: linear-gradient(135deg, #2664F5 0%, #1d52cc 100%) !important;
            border-color: #2664F5 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(38, 100, 245, 0.25) !important;
        }

        @if ($isAptikom)
            .bk-pagination-wrapper .page-item.active .page-link {
                background: linear-gradient(135deg, #006199 0%, #004c78 100%) !important;
                border-color: #006199 !important;
                box-shadow: 0 2px 6px rgba(0, 97, 153, 0.25) !important;
            }
            .bk-pagination-wrapper .page-link:hover {
                color: #004c78;
            }
        @endif
    </style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle Rumpun Baru Textbox
        $(document).on('change', '.rumpun-select-toggle', function() {
            const targetSelector = $(this).data('target');
            const targetBox = $(targetSelector);

            if ($(this).val() === '__NEW__') {
                targetBox.removeClass('d-none');
                targetBox.find('input').focus();
            } else {
                targetBox.addClass('d-none');
                targetBox.find('input').val('');
            }
        });

        // Edit Modal Trigger
        $('.btn-edit-bk').on('click', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const kurikulumId = $(this).data('kurikulum');
            const rumpun = $(this).data('rumpun');

            const currentPrefix = "{{ $currentPrefix }}";
            const updateUrl = `/${currentPrefix.replace(/\./g, '/')}bk/bk-update/${id}`;

            $('#editBkForm').attr('action', updateUrl);
            $('#edit_nama').val(nama);
            $('#edit_kurikulum_id').val(kurikulumId);

            const rumpunSelect = $('#edit_rumpun_select');
            const newRumpunBox = $('#edit_new_rumpun_box');
            const newRumpunInput = $('#edit_rumpun_new');

            // Check if rumpun exists in select options
            let existsInOptions = false;
            rumpunSelect.find('option').each(function() {
                if ($(this).val() === rumpun) {
                    existsInOptions = true;
                }
            });

            if (existsInOptions) {
                rumpunSelect.val(rumpun);
                newRumpunBox.addClass('d-none');
                newRumpunInput.val('');
            } else if (rumpun) {
                rumpunSelect.val('__NEW__');
                newRumpunBox.removeClass('d-none');
                newRumpunInput.val(rumpun);
            } else {
                rumpunSelect.val('');
                newRumpunBox.addClass('d-none');
                newRumpunInput.val('');
            }

            $('#editBkModal').modal('show');
        });

        // Instant Live Filter pada tabel halaman aktif (dan tekan Enter untuk query database)
        $('#table_search_input').on('input', function() {
            const term = $(this).val().toLowerCase().trim();

            if (term === '') {
                $('.bk-rumpun-header, .bk-item-row').show();
                return;
            }

            $('.bk-rumpun-header').each(function() {
                const rumpunRow = $(this);
                const rumpunItems = rumpunRow.nextUntil('.bk-rumpun-header');
                let matched = 0;

                rumpunItems.each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.indexOf(term) !== -1) {
                        $(this).show();
                        matched++;
                    } else {
                        $(this).hide();
                    }
                });

                if (matched > 0) {
                    rumpunRow.show();
                } else {
                    rumpunRow.hide();
                }
            });
        });
    });

    function switchKurikulumTab(targetId) {
        var tabBtn = document.getElementById(targetId + '-tab');
        if (tabBtn) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                var tab = bootstrap.Tab.getOrCreateInstance(tabBtn);
                tab.show();
            } else if (typeof $ !== 'undefined' && $.fn.tab) {
                $(tabBtn).tab('show');
            } else {
                tabBtn.click();
            }
        }
    }
</script>
@endpush

