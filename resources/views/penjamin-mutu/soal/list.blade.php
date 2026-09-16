@extends('penjamin-mutu.template')
@section('content')
    @php
        $rolePrefix = match (auth()->user()->otoritas->otoritas) {
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.',
            'Kepala Program Studi' => 'kepala-program-studi.',
            default => 'penjamin-mutu.program-studi.',
        };

        $countValid = $mkList->where('status_label', 'valid')->count();
        $countSiap = $mkList->where('status_label', 'siap')->count();
        $countDitolak = $mkList->where('status_label', 'ditolak')->count();
        $countBelum = $mkList->where('status_label', 'belum')->count();
    @endphp

    <div class="col-lg-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">

                {{-- Judul --}}
                <div class="mb-4">
                    <h4 class="fw-bold mb-1">
                        <i class="ti-book me-2 text-primary"></i>Validasi Soal per Mata Kuliah
                    </h4>
                    <p class="text-muted small mb-0">
                        Mata kuliah yang sudah ada soal atau instrumen diajukan oleh dosen.
                    </p>
                </div>

                <x-filter-form :universities="$universities" :faculties="$faculties" :programs="$programs" />

                {{-- Stats Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 rounded-3 h-100" style="background:#d1fae5;">
                            <div class="card-body py-3 px-3 d-flex align-items-center gap-3">
                                <div>
                                    <div class="fs-3 fw-bold text-success">{{ $countValid }}</div>
                                    <div class="small text-success fw-semibold">Tervalidasi</div>
                                </div>
                                <i class="ti-check-box text-success fs-3 ms-auto opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 rounded-3 h-100" style="background:#dbeafe;">
                            <div class="card-body py-3 px-3 d-flex align-items-center gap-3">
                                <div>
                                    <div class="fs-3 fw-bold text-primary">{{ $countSiap }}</div>
                                    <div class="small text-primary fw-semibold">Siap Divalidasi</div>
                                </div>
                                <i class="ti-thumb-up text-primary fs-3 ms-auto opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 rounded-3 h-100" style="background:#fee2e2;">
                            <div class="card-body py-3 px-3 d-flex align-items-center gap-3">
                                <div>
                                    <div class="fs-3 fw-bold text-danger">{{ $countDitolak }}</div>
                                    <div class="small text-danger fw-semibold">Ditolak</div>
                                </div>
                                <i class="ti-close text-danger fs-3 ms-auto opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 rounded-3 h-100" style="background:#fef9c3;">
                            <div class="card-body py-3 px-3 d-flex align-items-center gap-3">
                                <div>
                                    <div class="fs-3 fw-bold text-warning">{{ $countBelum }}</div>
                                    <div class="small text-warning fw-semibold">Belum Lengkap</div>
                                </div>
                                <i class="ti-time text-warning fs-3 ms-auto opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Search --}}
                <form method="GET" action="{{ route($rolePrefix . 'list-soal') }}" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-sm-6 col-md-4">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Cari kode MK atau nama MK..." value="{{ request('search') }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <i class="ti-search me-1"></i> Cari
                            </button>
                            @if (request('search'))
                                <a href="{{ route($rolePrefix . 'list-soal') }}" class="btn btn-secondary btn-sm px-3">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Tabel --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">No</th>
                                <th width="12%">Kode MK</th>
                                <th>Nama MK</th>
                                <th width="18%">Prodi</th>
                                <th width="10%" class="text-center">Diajukan</th>
                                <th width="11%" class="text-center">Status</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mkList as $i => $mk)
                                <tr>
                                    <td class="text-muted small">{{ $i + 1 }}</td>

                                    {{-- Kode MK --}}
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                            {{ $mk->kode }}
                                        </span>
                                    </td>

                                    {{-- Nama MK --}}
                                    <td>
                                        <strong class="small">{{ $mk->nama_mk }}</strong>
                                        @if ($mk->nama_fakultas)
                                            <div class="text-muted" style="font-size:0.78rem;">{{ $mk->nama_fakultas }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Prodi --}}
                                    <td class="small text-muted">{{ $mk->nama_prodi }}</td>

                                    {{-- Diajukan --}}
                                    <td class="text-center">
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            {{ $mk->jumlah_diajukan }} item
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="text-center">
                                        @if ($mk->status_label === 'valid')
                                            <span class="badge bg-success">Tervalidasi</span>
                                        @elseif ($mk->status_label === 'siap')
                                            <span class="badge bg-primary">Siap Validasi</span>
                                        @elseif ($mk->status_label === 'ditolak')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum Lengkap</span>
                                        @endif
                                    </td>

                                    {{-- Aksi: tiga tombol icon --}}
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1">

                                            {{-- Periksa --}}
                                            <a href="{{ route($rolePrefix . 'soal-detail', $mk->kode) }}"
                                                class="btn btn-info btn-icons" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Periksa detail soal">
                                                <i class="ti-eye"></i>
                                            </a>

                                            {{-- Setujui --}}
                                            @php
                                                $canAction = ($mk->status_label === 'siap');
                                            @endphp
                                            <form action="{{ route($rolePrefix . 'soal-validasi-mk', $mk->kode) }}"
                                                method="POST" class="d-inline m-0 p-0">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-success btn-icons {{ !$canAction ? 'disabled' : '' }}" 
                                                    {{ !$canAction ? 'disabled' : '' }}
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ $mk->status_label === 'valid' ? 'Semua soal MK ini sudah divalidasi' : ($mk->status_label === 'ditolak' ? 'Soal MK ini ditolak, menunggu dosen mengajukan soal baru' : ($canAction ? 'Setujui semua soal MK ini' : 'Belum ada soal diajukan')) }}"
                                                    onclick="return confirm('Validasi semua soal MK {{ $mk->kode }}?')">
                                                    <i class="ti-check"></i>
                                                </button>
                                            </form>

                                            {{-- Tolak — buka modal --}}
                                            <button type="button"
                                                class="btn btn-danger btn-icons {{ !$canAction ? 'disabled' : '' }}" 
                                                {{ !$canAction ? 'disabled' : '' }}
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ $mk->status_label === 'valid' ? 'Semua soal MK ini sudah divalidasi' : ($mk->status_label === 'ditolak' ? 'Soal MK ini ditolak, menunggu dosen mengajukan soal baru' : ($canAction ? 'Tolak soal MK ini' : 'Belum ada soal diajukan')) }}"
                                                onclick="openTolakModal('{{ $mk->kode }}')">
                                                <i class="ti-close"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="ti-folder me-2"></i>
                                        Belum ada MK yang memiliki soal diajukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== MODAL TOLAK ===== --}}
    <div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="modalTolakLabel">
                        <i class="ti-close me-2 text-danger"></i>Tolak Soal MK
                        <span id="modalTolakKode" class="text-danger"></span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTolak" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label small fw-semibold">
                            Alasan penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="komentar" class="form-control" rows="4"
                            placeholder="Jelaskan alasan penolakan (minimal 10 karakter)..." required minlength="10"></textarea>
                        <div class="text-muted small mt-1">
                            <i class="ti-info-alt me-1"></i>
                            Komentar ini akan dikirimkan ke dosen. Status soal akan kembali ke <strong>Belum</strong>.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="ti-close me-1"></i>Tolak & Kirim ke Dosen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Peta route tolak per MK (digenerate Blade agar aman multi-role)
        const tolakRoutes = {
            @foreach ($mkList as $mk)
                "{{ $mk->kode }}": "{{ route($rolePrefix . 'soal-tolak-mk', $mk->kode) }}",
            @endforeach
        };

        function openTolakModal(kodeMk) {
            document.getElementById('modalTolakKode').textContent = kodeMk;
            document.getElementById('formTolak').action = tolakRoutes[kodeMk];
            document.getElementById('formTolak').querySelector('textarea').value = '';
            new bootstrap.Modal(document.getElementById('modalTolak')).show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
        });
    </script>
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection
