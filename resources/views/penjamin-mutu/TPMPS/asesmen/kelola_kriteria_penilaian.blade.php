@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')

@section('content')
@php
    $routePrefix = [
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
        'Dosen' => ['prefix' => 'dosen.'],
    ];
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <h4 class="card-title fw-bold mb-1">Pengelolaan Kriteria Penilaian</h4>
                    <p class="text-muted small mb-0">Kelola daftar kriteria/instrumen penilaian yang digunakan dalam asesmen mata kuliah.</p>
                </div>

                <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#addKriteriaModal">
                                <i class="mdi mdi-plus-circle font-16"></i>
                                <span>Tambah Kriteria Penilaian</span>
                            </button>
                        @endif
                    </div>

                    <div>
                        <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-2 m-0">
                            <label for="search_input_kriteria" class="fw-semibold text-dark mb-0 me-1" style="font-size: 15px;">Search:</label>
                            <div class="position-relative d-inline-block">
                                <input type="text" id="search_input_kriteria" name="search" class="form-control bg-white"
                                       value="{{ request('search') }}" autocomplete="off"
                                       style="width: 220px; border-radius: 8px; border: 1px solid #cbd5e1; padding-right: {{ request('search') ? '30px' : '12px' }};">
                                @if(request('search'))
                                    <a href="{{ url()->current() }}" class="position-absolute text-secondary" style="right: 8px; top: 50%; transform: translateY(-50%); text-decoration: none;" title="Reset Search">
                                        <i class="mdi mdi-close-circle font-16"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th style="width: 8%" class="text-center">No</th>
                                <th>Nama Kriteria Penilaian</th>
                                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                    <th style="width: 20%" class="text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kriterias as $i => $kriteria)
                                <tr>
                                    <td class="text-center">{{ ($kriterias->currentPage() - 1) * $kriterias->perPage() + $i + 1 }}</td>
                                    <td>{{ $kriteria->nama_kriteria }}</td>
                                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" class="btn btn-warning btn-sm py-1 px-2" data-bs-toggle="modal" data-bs-target="#editKriteriaModal{{ $kriteria->id }}">
                                                    <i class="mdi mdi-pencil me-1"></i> Edit
                                                </button>
                                                <form action="{{ route($currentPrefix . 'asesmen.kelola-kriteria.destroy', $kriteria->id) }}" method="POST"
                                                      onsubmit="return confirm('Yakin ingin menghapus kriteria {{ $kriteria->nama_kriteria }}?')" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm py-1 px-2">
                                                        <i class="mdi mdi-delete me-1"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- Modal Edit --}}
                                            <div class="modal fade" id="editKriteriaModal{{ $kriteria->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content text-start">
                                                        <form action="{{ route($currentPrefix . 'asesmen.kelola-kriteria.update', $kriteria->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header py-2 px-3 bg-light">
                                                                <h5 class="modal-title fs-6 fw-bold">Edit Kriteria Penilaian</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-3">
                                                                <div class="mb-3">
                                                                    <label for="nama_kriteria_{{ $kriteria->id }}" class="form-label fw-semibold">Nama Kriteria Penilaian</label>
                                                                    <input type="text" class="form-control" id="nama_kriteria_{{ $kriteria->id }}" name="nama_kriteria" value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}" required>
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
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']) ? 3 : 2 }}" class="text-center text-muted p-4">
                                        Belum ada data Kriteria Penilaian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                    <small class="text-muted">
                        Menampilkan {{ $kriterias->firstItem() ?? 0 }} - {{ $kriterias->lastItem() ?? 0 }} dari {{ $kriterias->total() }} kriteria
                    </small>
                    <div>
                        {{ $kriterias->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
@if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
    <div class="modal fade" id="addKriteriaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route($currentPrefix . 'asesmen.instrumen-penilaian-store') }}" method="POST">
                    @csrf
                    <div class="modal-header py-2 px-3 bg-light">
                        <h5 class="modal-title fs-6 fw-bold">Tambah Kriteria Penilaian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label for="nama_kriteria_new" class="form-label fw-semibold">Nama Kriteria Penilaian</label>
                            <input type="text" class="form-control" id="nama_kriteria_new" name="nama_kriteria" placeholder="Contoh: Ketepatan Penjelasan" required>
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
@endif
@endsection
