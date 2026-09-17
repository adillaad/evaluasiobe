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
@endphp

@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

    <style>
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
                <div class="card-header bg-white py-3">
                    <h5 class="card-title fw-bold mb-0 text-primary">
                        <i class="ti-plus me-1"></i> Tambah Sub CPMK Baru
                    </h5>
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
            <div class="card mb-3 shadow-sm">
                <div class="card-body py-2">
                    <x-filter-form :showKurikulum="true" :kurikulums="$kurikulums" />
                </div>
            </div>
        @endif

        {{-- Tabel List Sub CPMK --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title fw-bold mb-0 text-dark">Daftar Sub CPMK</h5>
                <span class="badge bg-primary rounded-pill">{{ $subCpmks->total() }} Sub CPMK</span>
            </div>

            {{-- Tab Filter Per CPL --}}
            @if(isset($cpls) && $cpls->isNotEmpty())
                <div class="px-3 pt-3 border-bottom bg-white">
                    <ul class="nav nav-tabs border-0 flex-nowrap overflow-auto" style="scrollbar-width: thin;">
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 {{ !request('cpl_id') ? 'active fw-bold text-primary border-bottom border-primary border-2' : 'text-secondary' }}" 
                               href="{{ request()->fullUrlWithQuery(['cpl_id' => null, 'page' => null]) }}">
                               Semua CPL
                            </a>
                        </li>
                        @foreach ($cpls as $cplItem)
                            <li class="nav-item">
                                <a class="nav-link px-3 py-2 {{ request('cpl_id') == $cplItem->id ? 'active fw-bold text-primary border-bottom border-primary border-2' : 'text-secondary' }}" 
                                   href="{{ request()->fullUrlWithQuery(['cpl_id' => $cplItem->id, 'page' => null]) }}">
                                   {{ $cplItem->kode }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
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
                                    <td>
                                        <span class="code-pink">{{ $sub->kode }}</span>
                                    </td>
                                    <td style="word-wrap:break-word; white-space:normal;">
                                        {{ $sub->uraian }}
                                    </td>
                                    <td>
                                        @if($sub->cpmk)
                                            <div class="fw-bold small">{{ $sub->cpmk->kode }}</div>
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
                                                <button type="button" class="btn btn-sm btn-outline-warning p-1 px-2" 
                                                    title="Edit Sub CPMK"
                                                    onclick="openEditSubCpmkModal({{ $sub->id }}, '{{ addslashes($sub->kode) }}', '{{ addslashes($sub->uraian) }}')">
                                                    <i class="ti-pencil me-1"></i> Edit
                                                </button>
                                                <form action="{{ route($currentPrefix . 'cpl-cpmk.subCpmk-destroy', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Sub-CPMK {{ $sub->kode }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger p-1 px-2" title="Hapus Sub CPMK">
                                                        <i class="ti-trash me-1"></i> Hapus
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
@endsection
