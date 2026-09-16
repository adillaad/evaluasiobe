@extends(in_array($userOtoritas ?? auth()->user()->otoritas->otoritas, ['Admin Universitas', 'Admin', 'Wakil Rektor']) ? 'admin.template' : 'penjamin-mutu.template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-1">Daftar Tahun Ajaran</h4>
                        <p class="text-muted small mb-0">Kelola daftar tahun ajaran dan semester perkuliahan.</p>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTahunAjaranModal">
                        <i class="mdi mdi-plus-circle me-1"></i> Tambah Tahun Ajaran
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th style="width: 8%" class="text-center">No</th>
                                <th>Tahun</th>
                                <th>Jenis Semester</th>
                                <th>Tahun Ajaran</th>
                                <th style="width: 18%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tahunAjarans as $index => $item)
                                <tr>
                                    <td class="text-center">{{ ($tahunAjarans->currentPage() - 1) * $tahunAjarans->perPage() + $index + 1 }}</td>
                                    <td>{{ $item->tahun }}</td>
                                    <td>
                                        <span class="badge {{ $item->jenis_semester == 'Ganjil' ? 'bg-info' : 'bg-warning' }}">
                                            {{ $item->jenis_semester }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $item->label }}</strong></td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <button class="btn btn-warning btn-sm py-1 px-2 text-white" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                                <i class="mdi mdi-pencil me-1"></i> Edit
                                            </button>
                                            <form action="{{ route($currentPrefix . 'tahun-ajaran.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm py-1 px-2">
                                                    <i class="mdi mdi-delete me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content text-start">
                                                    <form method="POST" action="{{ route($currentPrefix . 'tahun-ajaran.update', $item->id) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header py-2 px-3 bg-light">
                                                            <h5 class="modal-title fs-6 fw-bold" id="editModalLabel{{ $item->id }}">Edit Tahun Ajaran</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-3">
                                                            <div class="form-group mb-3">
                                                                <label for="tahun{{ $item->id }}" class="form-label fw-semibold">Tahun (Awal)</label>
                                                                <input type="number" class="form-control" id="tahun{{ $item->id }}" name="tahun" value="{{ $item->tahun }}" min="2000" max="2100" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label for="jenis_semester{{ $item->id }}" class="form-label fw-semibold">Jenis Semester</label>
                                                                <select class="form-control" id="jenis_semester{{ $item->id }}" name="jenis_semester" required>
                                                                    <option value="Ganjil" {{ $item->jenis_semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                                                    <option value="Genap" {{ $item->jenis_semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer py-2 px-3 bg-light">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted p-4">Belum ada data tahun ajaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                    <small class="text-muted">
                        Menampilkan {{ $tahunAjarans->firstItem() ?? 0 }} - {{ $tahunAjarans->lastItem() ?? 0 }} dari {{ $tahunAjarans->total() }} data
                    </small>
                    <div>
                        {{ $tahunAjarans->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Tahun Ajaran -->
<div class="modal fade" id="addTahunAjaranModal" tabindex="-1" aria-labelledby="addTahunAjaranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-start">
            <form method="POST" action="{{ route($currentPrefix . 'tahun-ajaran.store') }}">
                @csrf
                <div class="modal-header py-2 px-3 bg-light">
                    <h5 class="modal-title fs-6 fw-bold" id="addTahunAjaranModalLabel">Tambah Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="form-group mb-3">
                        <label for="tahun" class="form-label fw-semibold">Tahun (Awal) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tahun" name="tahun" placeholder="Contoh: 2024" min="2000" max="2100" value="{{ old('tahun', date('Y')) }}" required>
                        <small class="form-text text-muted">Akan dicatat sebagai tahun akademik (contoh: 2024 untuk 2024/2025)</small>
                        @error('tahun')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="jenis_semester" class="form-label fw-semibold">Jenis Semester <span class="text-danger">*</span></label>
                        <select class="form-control" id="jenis_semester" name="jenis_semester" required>
                            <option value="" disabled {{ old('jenis_semester') ? '' : 'selected' }}>Pilih Semester</option>
                            <option value="Ganjil" {{ old('jenis_semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('jenis_semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('jenis_semester')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->has('tahun') || $errors->has('jenis_semester'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalElement = document.getElementById('addTahunAjaranModal');
        if (modalElement) {
            var addModal = new bootstrap.Modal(modalElement);
            addModal.show();
        }
    });
</script>
@endif
@endsection
