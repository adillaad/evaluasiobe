@extends('dosen.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">Daftar Soal</h4>
                        <p class="text-muted small mb-0">
                            Buat soal, lalu <strong>ajukan</strong> agar dapat ditinjau oleh Penjamin Mutu.
                        </p>
                    </div>
                    <a href="{{ route('dosen.soal-addRaw') }}" class="btn btn-primary btn-sm fw-semibold px-3">
                        <i class="ti-plus me-1"></i> Tambah Soal
                    </a>
                </div>

                {{-- Info status --}}
                <div class="alert alert-info py-2 mb-3 small">
                    <i class="ti-info-alt me-1"></i>
                    <span class="badge bg-secondary">Belum</span> = baru dibuat &nbsp;|&nbsp;
                    <span class="badge bg-primary">Menunggu</span> = sudah diajukan &nbsp;|&nbsp;
                    <span class="badge bg-success">Valid</span> = disetujui &nbsp;|&nbsp;
                    <span class="badge bg-danger">Tolak</span> = ditolak, perlu diperbaiki
                </div>

                {{-- Filter Bar (Mata Kuliah & Metode Penilaian) --}}
                <form method="GET" action="{{ route('dosen.soal-list') }}" class="card bg-light border-0 rounded-3 p-3 mb-4">
                    <div class="row g-2 align-items-end">
                        {{-- Filter Mata Kuliah --}}
                        <div class="col-md-5">
                            <label for="kode_mk" class="form-label font-13 fw-semibold text-dark mb-1">Mata Kuliah</label>
                            <select name="kode_mk" id="kode_mk" class="form-select form-select-sm border-slate rounded-2" style="height: 38px; font-size: 13.5px;">
                                <option value="">-- Semua Mata Kuliah --</option>
                                @foreach ($mksFilter ?? [] as $mk)
                                    <option value="{{ $mk->kode }}" {{ request('kode_mk') == $mk->kode ? 'selected' : '' }}>
                                        {{ $mk->kode }} - {{ $mk->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Metode Penilaian --}}
                        <div class="col-md-4">
                            <label for="jenis" class="form-label font-13 fw-semibold text-dark mb-1">Metode Penilaian</label>
                            <select name="jenis" id="jenis" class="form-select form-select-sm border-slate rounded-2" style="height: 38px; font-size: 13.5px;">
                                <option value="">-- Semua Metode Penilaian --</option>
                                @foreach ($metodeFilter ?? [] as $metode)
                                    <option value="{{ $metode->id }}" {{ request('jenis') == $metode->id ? 'selected' : '' }}>
                                        {{ $metode->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" style="height: 38px;">
                                    <i class="ti-filter"></i> Filter
                                </button>
                                <a href="{{ route('dosen.soal-list') }}" class="btn btn-outline-secondary btn-sm w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" style="height: 38px;">
                                    <i class="ti-reload"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Baris kontrol: per-page (kiri) + info total (kanan) --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">

                    {{-- Dropdown jumlah per halaman --}}
                    <form method="GET" action="{{ request()->url() }}" class="d-flex align-items-center gap-2">
                        {{-- Pertahankan semua parameter filter yang aktif --}}
                        @foreach (request()->except('per_page', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <label class="text-muted small mb-0 text-nowrap">Tampilkan</label>
                        <select name="per_page" class="form-select form-select-sm" style="width: auto; padding-right: 1.8rem; height: 31px;"
                            onchange="this.form.submit()">
                            @foreach ([10, 25, 50, 100] as $option)
                                <option value="{{ $option }}"
                                    {{ request('per_page', 10) == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-muted small mb-0 text-nowrap">soal per halaman</span>
                    </form>

                    {{-- Info total record --}}
                    <span class="text-muted small">
                        Menampilkan
                        <strong>{{ $soals->firstItem() ?? 0 }}–{{ $soals->lastItem() ?? 0 }}</strong>
                        dari <strong>{{ $soals->total() }}</strong> soal
                    </span>

                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">No</th>
                                <th width="22%">Mata Kuliah</th>
                                <th width="16%">Metode Penilaian</th>
                                <th width="14%">CPL → CPMK</th>
                                <th width="11%" class="text-center">Status</th>
                                <th width="15%">Komentar</th>
                                <th width="18%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($soals as $soal)
                                <tr>
                                    <td class="text-muted small">{{ $soals->firstItem() + $loop->index }}</td>

                                    {{-- Mata Kuliah --}}
                                    <td>
                                        @if ($soal->nama_mk)
                                            <code class="small">{{ $soal->kode_mk }}</code><br>
                                            <strong class="small">{{ $soal->nama_mk }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Metode --}}
                                    <td class="small">{{ $soal->nama_kriteria ?? '-' }}</td>

                                    {{-- CPL → CPMK --}}
                                    <td>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary">{{ $soal->cpl_kode ?? '-' }}</span>
                                        <i class="ti-arrow-right mx-1 small text-muted"></i>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success">{{ $soal->cpmk_kode ?? '-' }}</span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="text-center">
                                        @php
                                            $statusMap = [
                                                'Valid' => ['bg-success', 'Disetujui'],
                                                'Menunggu' => ['bg-primary', 'Menunggu'],
                                                'Tolak' => ['bg-danger', 'Ditolak'],
                                                'Belum' => ['bg-secondary', 'Belum'],
                                            ];
                                            [$badgeClass, $badgeLabel] = $statusMap[$soal->status] ?? [
                                                'bg-secondary',
                                                $soal->status,
                                            ];
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    </td>

                                    {{-- Komentar --}}
                                    <td>
                                        @if ($soal->komentar)
                                            <small class="text-danger" data-bs-toggle="tooltip"
                                                title="{{ $soal->komentar }}">
                                                {{ Str::limit($soal->komentar, 35) }}
                                            </small>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">

                                            @if ($soal->status === 'Valid')
                                                <span class="badge bg-success px-2 py-1" data-bs-toggle="tooltip"
                                                    title="Soal telah disetujui dan tidak dapat diubah">
                                                    <i class="ti-lock me-1"></i> Terkunci
                                                </span>
                                            @elseif ($soal->status === 'Menunggu')
                                                <button class="btn btn-warning btn-sm p-1" disabled data-bs-toggle="tooltip"
                                                    title="Sedang ditinjau, tidak dapat diedit">
                                                    <i class="ti-pencil"></i>
                                                </button>
                                                <button class="btn btn-primary btn-sm p-1" disabled data-bs-toggle="tooltip"
                                                    title="Sudah diajukan">
                                                    <i class="ti-share"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm p-1" disabled data-bs-toggle="tooltip"
                                                    title="Sedang ditinjau, tidak dapat dihapus">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            @elseif (in_array($soal->status, ['Belum', 'Tolak']))
                                                <a href="/dosen/soal/edit-soal/{{ $soal->id }}"
                                                    class="btn btn-warning btn-sm p-1" data-bs-toggle="tooltip"
                                                    title="Edit Soal">
                                                    <i class="ti-pencil"></i>
                                                </a>

                                                <form action="{{ route('dosen.soal-ajukan', $soal->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Ajukan soal ini ke Penjamin Mutu?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm p-1"
                                                        data-bs-toggle="tooltip" title="Ajukan ke Penjamin Mutu">
                                                        <i class="ti-share"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ url('dosen/soal/delete-soal/' . $soal->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Hapus soal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm p-1"
                                                        data-bs-toggle="tooltip" title="Hapus Soal">
                                                        <i class="ti-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="ti-folder me-2"></i>Belum ada soal. Silakan buat soal terlebih dahulu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination — tetap membawa per_page dan filter lain --}}
                <div class="mt-2">
                    {{ $soals->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
        });
    </script>
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection