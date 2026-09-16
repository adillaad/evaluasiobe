@extends('penjamin-mutu.template')

@section('content')

    @php
        $rolePrefix = match (auth()->user()->otoritas->otoritas) {
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.',
            'Kepala Program Studi' => 'kepala-program-studi.',
            default => 'penjamin-mutu.program-studi.',
        };
    @endphp

    <div class="col-lg-12">

        {{-- Header --}}
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <a href="{{ route($rolePrefix . 'list-soal') }}" class="text-muted text-decoration-none small">
                            <i class="ti-arrow-left me-1"></i>Kembali ke Daftar MK
                        </a>
                        <h4 class="fw-bold mb-1 mt-1">
                            {{ $mk->kode }} &mdash; {{ $mk->nama }}
                        </h4>
                        <div class="text-muted small">
                            <i class="ti-layout-grid3-alt me-1"></i>{{ $mk->nama_prodi }}
                            &nbsp;&bull;&nbsp;
                            @if ($mkLengkap)
                                <span class="badge bg-success">
                                    <i class="ti-check me-1"></i>Semua instrumen lengkap
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="ti-alert me-1"></i>Ada instrumen belum lengkap
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach ($statusSoal as $status => $jumlah)
                            @php
                                $cls = match ($status) {
                                    'Valid' => 'bg-success',
                                    'Tolak' => 'bg-danger',
                                    'Menunggu' => 'bg-primary',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $cls }}">{{ $status }}: {{ $jumlah }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== SETIAP METODE ===== --}}
        @foreach ($detailMetodes as $metode)
            @php
                $pct = $metode['persen_kelengkapan'];
                $barClass = $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger');
                $collapseId = 'metode-' . $metode['metode_id'];
            @endphp

            <div class="card mb-3">
                <div class="card-header bg-light d-flex align-items-center justify-content-between flex-wrap gap-2"
                    style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti-layers-alt text-primary fs-5"></i>
                        <strong class="fs-6">{{ $metode['nama_metode'] }}</strong>
                    </div>
                    <i class="ti-angle-down text-muted"></i>
                </div>

                <div class="collapse show" id="{{ $collapseId }}">
                    <div class="card-body p-0">

                        {{-- Status CPMK --}}
                        <div class="px-3 py-2 border-bottom bg-white">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="small fw-bold text-muted text-uppercase">Status CPMK:</span>
                                @foreach ($metode['cpmk_kelengkapan'] as $ck)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                        <i class="ti-bookmark me-1"></i>{{ $ck['kode'] }}
                                    </span>
                                @endforeach
                                @if ($metode['cpmk_belum']->count() > 0)
                                    <span class="small text-danger fw-semibold ms-2">
                                        <i class="ti-close me-1"></i>
                                        {{ $metode['cpmk_belum']->count() }} CPMK belum terpetakan:
                                        {{ $metode['cpmk_belum']->pluck('kode')->implode(', ') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- ===== SOAL YANG DIAJUKAN ===== --}}
                        <div class="px-3 pt-3">
                            <p class="small fw-bold text-uppercase text-muted mb-2">
                                <i class="ti-file-alt me-1"></i>
                                Soal Diajukan ({{ $metode['soals']->count() }})
                            </p>
                            @if ($metode['soals']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Soal</th>
                                                <th>CPL</th>
                                                <th>CPMK</th>
                                                <th class="text-center">Bobot CPMK</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($metode['soals'] as $i => $soal)
                                                <tr>
                                                    <td class="text-muted small">{{ $i + 1 }}</td>

                                                    {{-- Pertanyaan: link ke modal, bukan teks mentah --}}
                                                    <td>
                                                        <a href="#"
                                                            class="text-primary text-decoration-none small fw-semibold"
                                                            style="border-bottom:1px dashed #93c5fd;" data-bs-toggle="modal"
                                                            data-bs-target="#modalSoal{{ $soal->id }}">
                                                            <i class="ti-file-alt me-1"></i>Soal #{{ $i + 1 }}
                                                        </a>
                                                        @if ($soal->komentar)
                                                            <span
                                                                class="ms-1 badge bg-warning bg-opacity-10 text-warning border border-warning"
                                                                style="font-size:0.7rem;" data-bs-toggle="tooltip"
                                                                title="{{ $soal->komentar }}">
                                                                <i class="ti-comment-alt"></i>
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <span class="badge" style="background:#f3f0ff;color:#7048e8">
                                                            {{ $soal->kode_cpl ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                                            {{ $soal->kode_cpmk ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center fw-semibold text-dark">
                                                        {{ $soal->bobot_cpmk ?? 0 }}%
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $bc = match ($soal->status) {
                                                                'Valid' => 'bg-success',
                                                                'Tolak' => 'bg-danger',
                                                                'Menunggu' => 'bg-primary',
                                                                default => 'bg-secondary',
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $bc }}">{{ $soal->status }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- ===== MODAL PER SOAL ===== --}}
                                @foreach ($metode['soals'] as $i => $soal)
                                    <div class="modal fade" id="modalSoal{{ $soal->id }}" tabindex="-1"
                                        aria-labelledby="labelSoal{{ $soal->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold" id="labelSoal{{ $soal->id }}">
                                                        <i class="ti-file-alt me-2 text-primary"></i>
                                                        Detail Soal #{{ $i + 1 }}
                                                        &mdash; {{ $metode['nama_metode'] }}
                                                    </h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{-- Meta badges --}}
                                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                                        <span class="badge" style="background:#f3f0ff;color:#7048e8;">
                                                            CPL: {{ $soal->kode_cpl ?? '-' }}
                                                        </span>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                                            CPMK: {{ $soal->kode_cpmk ?? '-' }}
                                                        </span>
                                                        @php
                                                            $bc = match ($soal->status) {
                                                                'Valid' => 'bg-success',
                                                                'Tolak' => 'bg-danger',
                                                                'Menunggu' => 'bg-primary',
                                                                default => 'bg-secondary',
                                                            };
                                                        @endphp
                                                        <span
                                                            class="badge {{ $bc }}">{{ $soal->status }}</span>
                                                    </div>

                                                    {{-- Isi pertanyaan --}}
                                                    <div class="p-3 rounded border bg-light"
                                                        style="white-space:pre-wrap;font-size:0.92rem;line-height:1.75;min-height:80px;">
                                                        {{ $soal->pertanyaan }}
                                                    </div>

                                                    {{-- Komentar PM (jika ada) --}}
                                                    @if ($soal->komentar)
                                                        <div class="alert alert-warning mt-3 py-2 small mb-0">
                                                            <i class="ti-comment-alt me-1"></i>
                                                            <strong>Catatan PM:</strong> {{ $soal->komentar }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                {{-- ===== END MODAL SOAL ===== --}}
                            @else
                                <div class="alert alert-warning py-2">
                                    <i class="ti-alert me-2"></i>
                                    Belum ada soal yang diajukan untuk metode
                                    <strong>{{ $metode['nama_metode'] }}</strong>.
                                </div>
                            @endif
                        </div>

                        {{-- ===== TANPA SOAL ===== --}}
                        @if ($metode['tanpa_soals']->count() > 0)
                            <div class="px-3 pt-3">
                                <p class="small fw-bold text-uppercase text-muted mb-2">
                                    <i class="ti-clipboard me-1"></i>
                                    Instrumen Tanpa Soal ({{ $metode['tanpa_soals']->count() }})
                                </p>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Instrumen</th>
                                                <th>CPL</th>
                                                <th>CPMK</th>
                                                <th class="text-center">Bobot CPMK</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($metode['tanpa_soals'] as $i => $ts)
                                                <tr>
                                                    <td class="text-muted small">{{ $i + 1 }}</td>

                                                    {{-- Nama instrumen: link ke modal --}}
                                                    <td>
                                                        <a href="#"
                                                            class="text-primary text-decoration-none small fw-semibold"
                                                            style="border-bottom:1px dashed #93c5fd;"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalTS{{ $ts->id }}">
                                                            <i class="ti-clipboard me-1"></i>{{ $ts->nama_instrumen }}
                                                        </a>
                                                    </td>

                                                    <td>
                                                        <span class="badge" style="background:#f3f0ff;color:#7048e8">
                                                            {{ $ts->kode_cpl ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                                            {{ $ts->kode_cpmk ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center fw-semibold text-dark">
                                                        {{ $ts->bobot_cpmk ?? 0 }}%
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $bc = match ($ts->status) {
                                                                'Valid' => 'bg-success',
                                                                'Ditolak' => 'bg-danger',
                                                                'Menunggu Validasi' => 'bg-primary',
                                                                default => 'bg-secondary',
                                                            };
                                                        @endphp
                                                        <span
                                                            class="badge {{ $bc }}">{{ $ts->status }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- ===== MODAL PER TANPA SOAL ===== --}}
                                @foreach ($metode['tanpa_soals'] as $i => $ts)
                                    <div class="modal fade" id="modalTS{{ $ts->id }}" tabindex="-1"
                                        aria-labelledby="labelTS{{ $ts->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold" id="labelTS{{ $ts->id }}">
                                                        <i class="ti-clipboard me-2 text-warning"></i>
                                                        Detail Instrumen — {{ $ts->nama_instrumen }}
                                                    </h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{-- Meta badges --}}
                                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                                        <span class="badge" style="background:#f3f0ff;color:#7048e8;">
                                                            CPL: {{ $ts->kode_cpl ?? '-' }}
                                                        </span>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                                            CPMK: {{ $ts->kode_cpmk ?? '-' }}
                                                        </span>
                                                        <span
                                                            class="badge bg-info bg-opacity-10 text-info border border-info">
                                                            Bobot: {{ $ts->bobotSoal }}
                                                        </span>
                                                        <span
                                                            class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                                            % CPMK: {{ $ts->persentase_cpmk }}%
                                                        </span>
                                                        @php
                                                            $bc = match ($ts->status) {
                                                                'Valid' => 'bg-success',
                                                                'Ditolak' => 'bg-danger',
                                                                'Menunggu Validasi' => 'bg-primary',
                                                                default => 'bg-secondary',
                                                            };
                                                        @endphp
                                                        <span
                                                            class="badge {{ $bc }}">{{ $ts->status }}</span>
                                                    </div>

                                                    {{-- Nama instrumen --}}
                                                    <div class="p-3 rounded border bg-light"
                                                        style="font-size:0.92rem;line-height:1.75;">
                                                        <strong>Nama Instrumen:</strong><br>
                                                        {{ $ts->nama_instrumen }}
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                {{-- ===== END MODAL TANPA SOAL ===== --}}

                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @endforeach

        {{-- ===== ACTION PANEL ===== --}}
        <div class="card mt-2">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">
                    <i class="ti-check-box me-2"></i>Keputusan untuk MK {{ $kode_mk }}
                </h5>
            </div>
            <div class="card-body">
                {{-- Info jika belum lengkap (informatif, bukan blokir) --}}
                @if (!$mkLengkap)
                    <div class="alert alert-warning py-2 mb-3 small">
                        <i class="ti-alert me-1"></i>
                        Perhatian: ada instrumen yang belum lengkap. Validasi tetap bisa dilakukan,
                        namun pastikan kamu sudah memeriksa seluruh instrumen terlebih dahulu.
                    </div>
                @endif

                <div class="row g-3">

                    {{-- VALIDASI --}}
                    <div class="col-md-3">
                        <p class="small fw-bold text-muted text-uppercase mb-2">
                            <i class="ti-check me-1"></i>Setujui Soal
                        </p>
                        <form action="{{ route($rolePrefix . 'soal-validasi-mk', $kode_mk) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" data-bs-toggle="tooltip"
                                title="Validasi semua soal MK ini"
                                onclick="return confirm('Validasi semua soal MK {{ $kode_mk }}?')">
                                <i class="ti-check me-1"></i>Validasi Semua
                            </button>
                        </form>
                    </div>

                    {{-- TOLAK --}}
                    <div class="col-md-4">
                        <p class="small fw-bold text-muted text-uppercase mb-2">
                            <i class="ti-close me-1"></i>Tolak & Kembalikan ke Dosen
                        </p>
                        <form action="{{ route($rolePrefix . 'soal-tolak-mk', $kode_mk) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <textarea name="komentar" class="form-control form-control-sm" rows="3"
                                    placeholder="Jelaskan alasan penolakan (wajib diisi)...">{{ old('komentar') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm w-100" data-bs-toggle="tooltip"
                                title="Tolak soal dan kirim komentar ke dosen. Status soal akan kembali ke Belum.">
                                <i class="ti-close me-1"></i>Tolak & Kirim ke Dosen
                            </button>
                            <div class="text-muted small mt-1">
                                <i class="ti-info-alt me-1"></i>Status soal akan kembali ke <strong>Belum</strong>.
                            </div>
                        </form>
                    </div>

                    {{-- KIRIM PESAN SAJA --}}
                    <div class="col-md-5">
                        <p class="small fw-bold text-muted text-uppercase mb-2">
                            <i class="ti-comment-alt me-1"></i>Kirim Pesan ke Dosen (tanpa tolak)
                        </p>
                        <form action="{{ route($rolePrefix . 'soal-pesan-mk', $kode_mk) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <textarea name="pesan" class="form-control form-control-sm" rows="3"
                                    placeholder="Tulis pesan/catatan untuk dosen tanpa mengubah status soal...">{{ old('pesan') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-secondary btn-sm w-100" data-bs-toggle="tooltip"
                                title="Kirim catatan ke dosen tanpa mengubah status soal.">
                                <i class="ti-comment me-1"></i>Kirim Pesan
                            </button>
                            <div class="text-muted small mt-1">
                                <i class="ti-info-alt me-1"></i>Status soal <strong>tidak berubah</strong>.
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(el => {
                const target = document.querySelector(el.dataset.bsTarget);
                if (!target) return;
                const icon = el.querySelector('.ti-angle-down');
                target.addEventListener('show.bs.collapse', () => {
                    if (icon) icon.style.transform = 'rotate(0deg)';
                });
                target.addEventListener('hide.bs.collapse', () => {
                    if (icon) icon.style.transform = 'rotate(-90deg)';
                });
            });
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
            });
        </script>
    @endpush

@endsection
