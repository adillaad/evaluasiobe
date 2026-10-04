@php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Dosen' => ['prefix' => 'dosen.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';

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

    <style>
        @if ($isAptikom)
            /* ── APTIKOM Theme (Ocean Blue #006199 Accent) ── */
            .cpl-nav-pills .nav-link.active {
                background: linear-gradient(135deg, #006199 0%, #004c78 100%) !important;
                color: #ffffff !important;
                border-color: #006199 !important;
                box-shadow: 0 4px 12px rgba(0, 97, 153, 0.28) !important;
            }
            .text-primary-accent {
                color: #006199 !important;
            }
        @else
            /* ── NON-APTIKOM Theme (Royal Blue #2664F5 Accent) ── */
            .cpl-nav-pills .nav-link.active {
                background: linear-gradient(135deg, #2664F5 0%, #1d52cc 100%) !important;
                color: #ffffff !important;
                border-color: #2664F5 !important;
                box-shadow: 0 4px 12px rgba(38, 100, 245, 0.28) !important;
            }
            .text-primary-accent {
                color: #2664F5 !important;
            }
        @endif
        .cpl-tabs-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 14px 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }
        .cpl-nav-pills {
            gap: 8px;
        }
        .cpl-nav-pills .nav-link {
            color: #475569 !important;
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
        }
        .cpl-nav-pills .nav-link:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
        .cpl-nav-pills .nav-link.active .badge-cpmk-count {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
        .badge-cpmk-count {
            background-color: #e2e8f0;
            color: #475569;
            font-size: 11px;
            border-radius: 6px;
            padding: 2px 6px;
            margin-left: 6px;
            font-weight: 700;
        }
        .table-pemetaan {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .table-pemetaan th {
            background-color: #f8fafc !important;
            color: #334155 !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-bottom: 2px solid #cbd5e1 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-pemetaan td {
            vertical-align: middle !important;
            line-height: 1.6 !important;
            padding: 12px 14px !important;
            font-size: 13.5px !important;
            color: #1e293b !important;
            white-space: normal !important;
            word-break: break-word !important;
        }
        .table-pemetaan tbody tr:hover {
            background-color: #f8fafc !important;
        }
        .code-pink {
            background-color: #fce4ec;
            color: #d81b60;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .code-cpl {
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
        }
    </style>

    <div class="container-fluid mb-4">
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti-check-box me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('failed'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti-alert me-1"></i> {{ session('failed') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Top Navigation & Header --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Kelola Sub CPMK</h3>
                <p class="text-muted mb-0 small">Kelola data Sub Capaian Pembelajaran Mata Kuliah (Tambah, Edit, & Hapus)</p>
            </div>
            <div>
                <a href="{{ route($currentPrefix . 'cpl-cpmk.mk-cpmk-subcpmk') }}" class="btn btn-outline-secondary btn-icon-text">
                    <i class="ti-arrow-left me-1"></i> Kembali ke Pemetaan
                </a>
            </div>
        </div>

        {{-- Form Tambah Sub CPMK Baru --}}
        @if(in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold mb-0 text-primary">
                        <i class="ti-plus me-1"></i> Tambah Sub CPMK Baru
                    </h5>
                    <button type="button" class="btn btn-success text-white font-weight-bold d-inline-flex align-items-center gap-1 shadow-sm" style="background-color: #10ac84 !important; border-color: #10ac84 !important; color: #ffffff !important;" data-bs-toggle="modal" data-bs-target="#importSubCpmkModal">
                        <i class="ti-import me-1 text-white"></i>
                        <span style="color: #ffffff !important;">Import Sub CPMK</span>
                    </button>
                </div>
                <div class="card-body">
                    <form action="{{ route($currentPrefix . 'cpl-cpmk.subCpmk-store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="kurikulum_id" class="fw-bold">Kurikulum :</label>
                                <select name="kurikulum_id" id="kurikulum_id" class="form-select" required>
                                    <option value="">-- Pilih Kurikulum --</option>
                                    @foreach ($kurikulums as $kurikulum)
                                        <option value="{{ $kurikulum->id }}" {{ old('kurikulum_id') == $kurikulum->id ? 'selected' : '' }}>
                                            {{ $kurikulum->tahun }}
                                        </option>    
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cpl_id" class="fw-bold">CPL :</label>
                                <select name="cpl_id" id="cpl_id" class="form-select" required>
                                    <option value="">-- Pilih CPL --</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cpmk_id" class="fw-bold">CPMK :</label>
                                <input type="hidden" name="cpmk_kode" id="cpmk_kode">
                                <select name="cpmk_id" id="cpmk_id" class="form-select" required>
                                    <option value="">-- Pilih CPMK --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="uraian" class="fw-bold">Uraian Sub CPMK :</label>
                            <textarea name="uraian" id="uraian" class="form-control" style="height: 90px" placeholder="Masukkan Uraian Sub CPMK" required>{{ old('uraian') }}</textarea>
                            @error('uraian')
                                <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="ti-save me-1"></i> Simpan Sub CPMK
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Filter Kurikulum --}}
        @if(isset($kurikulums))
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body py-2">
                    <x-filter-form :showKurikulum="true" :kurikulums="$kurikulums" />
                </div>
            </div>
        @endif

        {{-- Tabel List Sub CPMK --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h5 class="card-title fw-bold mb-0 text-dark">Daftar Sub CPMK</h5>
                    <span class="badge bg-primary rounded-pill">{{ $subCpmks->total() }} Sub CPMK</span>
                </div>

                {{-- Tab Filter Per CPL --}}
                @if(isset($cpls) && $cpls->isNotEmpty())
                    <div class="cpl-tabs-wrapper mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                            <span class="fw-bold text-secondary text-uppercase small" style="letter-spacing: 0.5px;">
                                <i class="mdi mdi-filter-variant me-1"></i> Pilih CPL:
                            </span>
                            <span class="text-muted small">Klik tab CPL untuk memfilter data Sub CPMK</span>
                        </div>
                        <ul class="nav nav-pills cpl-nav-pills overflow-auto flex-nowrap pb-1">
                            <li class="nav-item">
                                <a class="nav-link {{ !request('cpl_id') ? 'active' : '' }}" 
                                   href="{{ request()->fullUrlWithQuery(['cpl_id' => null, 'page' => null]) }}">
                                   <i class="mdi mdi-grid me-1"></i> Semua CPL
                                   <span class="badge-cpmk-count">{{ $subCpmks->total() }}</span>
                                </a>
                            </li>
                            @foreach ($cpls as $cplItem)
                                <li class="nav-item">
                                    <a class="nav-link {{ request('cpl_id') == $cplItem->id ? 'active' : '' }}" 
                                       href="{{ request()->fullUrlWithQuery(['cpl_id' => $cplItem->id, 'page' => null]) }}">
                                       {{ $cplItem->kode }}
                                       <span class="badge-cpmk-count">{{ $cplItem->sub_cpmks_count ?? 0 }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle table-pemetaan mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th style="width: 140px;">Kode Sub CPMK</th>
                                <th>Uraian Sub CPMK</th>
                                <th style="width: 200px;">CPMK Induk</th>
                                <th style="width: 120px;">CPL Terkait</th>
                                <th style="width: 120px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subCpmks as $index => $sub)
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $subCpmks->firstItem() + $index }}</td>
                                    <td class="align-middle">
                                        <span class="text-dark" style="font-size: 0.85rem;">{{ $sub->kode }}</span>
                                    </td>
                                    <td style="word-wrap:break-word; white-space:normal;">
                                        {{ $sub->uraian }}
                                    </td>
                                    <td>
                                        @if($sub->cpmk)
                                            <div class="fw-bold small text-dark">{{ $sub->cpmk->kode }}</div>
                                            <div class="text-muted text-truncate small" style="max-width: 180px;" title="{{ $sub->cpmk->judul }}">
                                                {{ $sub->cpmk->judul }}
                                            </div>
                                        @else
                                            <span class="text-muted italic small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($sub->cpmk && $sub->cpmk->cpl)
                                            <span class="code-cpl">{{ $sub->cpmk->cpl->kode }}</span>
                                        @else
                                            <span class="text-muted italic small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary p-1 px-2" 
                                                    title="Edit Sub CPMK"
                                                    onclick="openEditSubCpmkModal({{ $sub->id }}, '{{ addslashes($sub->kode) }}', '{{ addslashes($sub->uraian) }}')">
                                                    <i class="ti-pencil me-1"></i>
                                                </button>
                                                <form action="{{ route($currentPrefix . 'cpl-cpmk.subCpmk-destroy', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Sub-CPMK {{ $sub->kode }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger p-1 px-2" title="Hapus Sub CPMK">
                                                        <i class="ti-trash me-1"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <em>Belum ada data Sub CPMK. Silakan tambah Sub CPMK baru menggunakan form di atas.</em>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination Footer --}}
            @if ($subCpmks->hasPages())
                <div class="card-footer bg-white py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="small text-muted">
                        Menampilkan <strong>{{ $subCpmks->firstItem() }}</strong> - <strong>{{ $subCpmks->lastItem() }}</strong> dari <strong>{{ $subCpmks->total() }}</strong> Sub CPMK
                    </div>
                    <div>
                        {{ $subCpmks->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Edit Sub CPMK -->
    <div class="modal fade" id="editSubCpmkModal" tabindex="-1" aria-labelledby="editSubCpmkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditSubCpmk" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="editSubCpmkModalLabel">Edit Sub CPMK</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_kode_sub" class="form-label fw-bold">Kode Sub CPMK</label>
                            <input type="text" class="form-control" id="edit_kode_sub" name="kode" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_uraian_sub" class="form-label fw-bold">Uraian Sub CPMK</label>
                            <textarea class="form-control" id="edit_uraian_sub" name="uraian" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="ti-save me-1"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const kurikulumElement = document.getElementById("kurikulum_id");
            const cplSelect = document.getElementById("cpl_id");
            const cpmkSelect = document.getElementById("cpmk_id");
            const hiddenCpmkKode = document.getElementById("cpmk_kode");

            function getPrefixUrl() {
                const userOtoritas = @json($userOtoritas);
                if (userOtoritas === 'Kepala Program Studi') return '/kepala-program-studi/cpl-cpmk';
                if (userOtoritas === 'Penjamin Mutu Universitas') return '/penjamin-mutu/universitas/cpl-cpmk';
                if (userOtoritas === 'Penjamin Mutu Fakultas') return '/penjamin-mutu/fakultas/cpl-cpmk';
                return '/penjamin-mutu/program-studi/cpl-cpmk';
            }

            function fetchCplByKurikulum() {
                const kurikulumId = kurikulumElement ? kurikulumElement.value : null;
                if (cplSelect) cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
                if (cpmkSelect) cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';

                if (!kurikulumId) return;

                if (cplSelect) cplSelect.innerHTML = '<option>Loading CPL...</option>';

                $.ajax({
                    url: `${getPrefixUrl()}/get-cpl-by-kurikulum/${kurikulumId}`,
                    method: 'GET',
                    success: function (data) {
                        if (!cplSelect) return;
                        cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
                        if (data.cpls && data.cpls.length > 0) {
                            data.cpls.forEach(cpl => {
                                cplSelect.innerHTML += `<option value="${cpl.id}">${cpl.kode} - ${cpl.deskripsi || ''}</option>`;
                            });
                        } else {
                            cplSelect.innerHTML = '<option value="" disabled>Tidak ada data CPL untuk kurikulum yang dipilih.</option>';
                        }
                    },
                    error: function () {
                        if (cplSelect) cplSelect.innerHTML = '<option value="" disabled>Gagal memuat data CPL.</option>';
                    }
                });
            }

            function fetchCpmkByCpl() {
                const cplId = cplSelect ? cplSelect.value : null;
                if (cpmkSelect) cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
                if (hiddenCpmkKode) hiddenCpmkKode.value = '';

                if (!cplId) return;

                if (cpmkSelect) cpmkSelect.innerHTML = '<option>Loading CPMK...</option>';

                $.ajax({
                    url: `${getPrefixUrl()}/get-cpmk-by-cpl/${cplId}`,
                    method: 'GET',
                    success: function (data) {
                        if (!cpmkSelect) return;
                        cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
                        if (data.cpmks && data.cpmks.length > 0) {
                            data.cpmks.forEach(cpmk => {
                                cpmkSelect.innerHTML += `<option value="${cpmk.id}">${cpmk.kode} - ${cpmk.judul}</option>`;
                            });
                        } else {
                            cpmkSelect.innerHTML = '<option value="" disabled>Tidak ada data CPMK untuk CPL yang dipilih.</option>';
                        }
                    },
                    error: function () {
                        if (cpmkSelect) cpmkSelect.innerHTML = '<option value="" disabled>Gagal memuat data CPMK.</option>';
                    }
                });
            }

            if (kurikulumElement) {
                kurikulumElement.addEventListener("change", fetchCplByKurikulum);
            }

            if (cplSelect) {
                cplSelect.addEventListener("change", fetchCpmkByCpl);
            }

            if (cpmkSelect) {
                cpmkSelect.addEventListener('change', function () {
                    const selectedText = this.options[this.selectedIndex]?.textContent;
                    const kode = selectedText?.split(" - ")[0] ?? '';
                    if (hiddenCpmkKode) hiddenCpmkKode.value = kode;
                });
            }
        });

        function openEditSubCpmkModal(id, kode, uraian) {
            const routeTemplate = "{{ route($currentPrefix . 'cpl-cpmk.subCpmk-update', ':id') }}";
            const actionUrl = routeTemplate.replace(':id', id);
            $('#formEditSubCpmk').attr('action', actionUrl);
            $('#edit_kode_sub').val(kode);
            $('#edit_uraian_sub').val(uraian);
            
            var editModal = new bootstrap.Modal(document.getElementById('editSubCpmkModal'));
            editModal.show();
        }
    </script>

    <!-- Modal Import Sub CPMK -->
    <div class="modal fade" id="importSubCpmkModal" tabindex="-1" aria-labelledby="importSubCpmkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="importSubCpmkModalLabel">
                        <i class="ti-import me-1 text-success"></i> Import Sub CPMK dari Excel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route($currentPrefix . 'cpl-cpmk.import-excel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="ti-info-alt me-1"></i> Gunakan template Excel resmi agar format kolom sesuai (Tahun Kurikulum, Kode MK, Kode CPMK, Uraian Sub CPMK). Kode Sub CPMK akan disesuaikan otomatis oleh sistem.
                        </div>
                        <div class="mb-3">
                            <label for="excel_file_subcpmk" class="form-label fw-bold">Pilih File Excel (.xlsx, .xls, .csv):</label>
                            <input type="file" class="form-control" id="excel_file_subcpmk" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <a href="{{ route($currentPrefix . 'cpl-cpmk.download-template') }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti-download me-1"></i> Download Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm"><i class="ti-upload me-1"></i> Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
