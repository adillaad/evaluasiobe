@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
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

                    <div class="d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route($currentPrefix . 'bk.index') }}" class="d-flex align-items-center">
                            <select name="kurikulum_id" class="form-select rounded-3 font-13 ps-3 py-2 border-primary fw-medium" style="min-width: 260px; width: auto; padding-right: 2.75rem !important; background-position: right 0.85rem center;" onchange="this.form.submit()">
                                <option value="">Filter Semua Kurikulum</option>
                                @foreach ($kurikulums as $kurikulum)
                                    <option value="{{ $kurikulum->id }}"
                                        {{ request('kurikulum_id') == $kurikulum->id ? 'selected' : '' }}>
                                        Kurikulum {{ $kurikulum->tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover align-middle w-100 bk-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th class="text-center" style="width: 120px;">Kode BK</th>
                                <th>Nama Bahan Kajian</th>
                                <th class="text-center" style="width: 150px;">Kurikulum</th>
                                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                    <th class="text-center" style="width: 120px;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $section = 0; @endphp
                            @forelse ($bks as $rumpun => $items)
                                @php $section++; @endphp
                                <tr class="table-light">
                                    <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']) ? 5 : 4 }}" class="py-2.5 px-3 fw-bold text-dark">
                                        {{ chr(64 + $section) }}. {{ $rumpun ?: 'Unassigned / Lainnya' }}
                                    </td>
                                </tr>
                                @foreach ($items as $index => $bk)
                                    <tr>
                                        <td class="text-center font-weight-medium text-muted">{{ $index + 1 }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-2.5 py-1.5 font-12 rounded-2 fw-bold">
                                                {{ $bk->kode }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $bk->nama }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-2.5 py-1.5 font-12 rounded-2 fw-semibold">
                                                Kurikulum {{ $bk->kurikulum->tahun ?? '-' }}
                                            </span>
                                        </td>
                                        @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <button type="button" 
                                                            class="btn btn-warning btn-icons btn-edit-bk"
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
                                                        <button type="submit" class="btn btn-danger btn-icons" data-bs-toggle="tooltip" title="Hapus">
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
                                    <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']) ? 5 : 4 }}" class="text-center text-muted py-4">
                                        <i class="mdi mdi-file-table-outline font-32 d-block mb-1"></i>
                                        Belum ada data Bahan Kajian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
    });
</script>
@endpush
