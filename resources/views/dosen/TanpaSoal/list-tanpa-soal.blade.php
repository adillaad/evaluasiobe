@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $userOtoritas
        ? str_replace(' ', '-', strtolower($userOtoritas)) . '.'
        : 'dosen.';
@endphp
@extends('dosen.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">Daftar Instrumen Tanpa Soal</h4>
                        <p class="text-muted small mb-0">
                            Buat instrumen, lalu <strong>ajukan</strong> agar dapat ditinjau oleh Penjamin Mutu.
                        </p>
                    </div>
                    <a href="{{ route($currentPrefix . 'addRawTS') }}" class="btn btn-primary btn-sm fw-semibold px-3">
                        <i class="ti-plus me-1"></i> Tambah Instrumen
                    </a>
                </div>

                {{-- Info status --}}
                <div class="alert alert-info py-2 mb-3 small">
                    <i class="ti-info-alt me-1"></i>
                    <span class="badge bg-secondary">Draft</span> = baru dibuat &nbsp;|&nbsp;
                    <span class="badge bg-primary">Menunggu Validasi</span> = sudah diajukan &nbsp;|&nbsp;
                    <span class="badge bg-success">Valid</span> = disetujui &nbsp;|&nbsp;
                    <span class="badge bg-danger">Ditolak</span> = ditolak, perlu diperbaiki
                </div>

                {{-- Filter Bar (Mata Kuliah & Metode Penilaian) --}}
                <form method="GET" action="{{ route($currentPrefix . 'tanpa-soal-list') }}" class="card bg-light border-0 rounded-3 p-3 mb-4">
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
                                    <option value="{{ $metode->id }}" {{ (request('jenis') == $metode->id || request('metode_id') == $metode->id) ? 'selected' : '' }}>
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
                                <a href="{{ route($currentPrefix . 'tanpa-soal-list') }}" class="btn btn-outline-secondary btn-sm w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" style="height: 38px;">
                                    <i class="ti-reload"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">No</th>
                                <th width="22%">Mata Kuliah</th>
                                <th width="18%">Metode / Instrumen</th>
                                <th width="14%">CPL → CPMK</th>
                                <th width="11%" class="text-center">Status</th>
                                <th width="13%">Komentar</th>
                                <th width="18%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tanpaSoalList as $item)
                                <tr>
                                    <td class="text-muted small">{{ $tanpaSoalList->firstItem() + $loop->index }}</td>

                                    {{-- Mata Kuliah --}}
                                    <td>
                                        <code class="small">{{ $item->kode_mk }}</code><br>
                                        <strong class="small">{{ optional($item->mk)->nama ?? $item->kode_mk }}</strong>
                                    </td>

                                    {{-- Metode --}}
                                    <td class="small">
                                        {{ optional($item->metode)->nama ?? $item->nama_instrumen }}
                                    </td>

                                    {{-- CPL → CPMK --}}
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary" data-bs-toggle="tooltip"
                                            title="{{ optional($item->cpl)->judul ?? '' }}">
                                            {{ optional($item->cpl)->kode ?? '-' }}
                                        </span>
                                        <i class="ti-arrow-right mx-1 small text-muted"></i>
                                        <span class="badge bg-success bg-opacity-10 text-success" data-bs-toggle="tooltip"
                                            title="{{ optional($item->cpmk)->judul ?? '' }}">
                                            {{ optional($item->cpmk)->kode ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="text-center">
                                        @php
                                            $statusMap = [
                                                'Valid' => ['bg-success', 'Disetujui'],
                                                'Menunggu Validasi' => ['bg-primary', 'Menunggu'],
                                                'Ditolak' => ['bg-danger', 'Ditolak'],
                                                'Draft' => ['bg-secondary', 'Draft'],
                                            ];
                                            [$badgeClass, $badgeLabel] = $statusMap[$item->status] ?? [
                                                'bg-secondary',
                                                $item->status,
                                            ];
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    </td>

                                    {{-- Komentar --}}
                                    <td>
                                        @if ($item->status === 'Ditolak' && isset($item->komentar))
                                            <small class="text-danger" data-bs-toggle="tooltip"
                                                title="{{ $item->komentar }}">
                                                {{ Str::limit($item->komentar, 30) }}
                                            </small>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">

                                            @if ($item->status === 'Valid')
                                                <span class="badge bg-success px-2 py-1 ms-1" data-bs-toggle="tooltip"
                                                    title="Instrumen telah disetujui dan tidak dapat diubah">
                                                    <i class="ti-lock me-1"></i> Terkunci
                                                </span>
                                            @elseif ($item->status === 'Menunggu Validasi')
                                                <button class="btn btn-action-table btn-action-outline-blue" disabled data-bs-toggle="tooltip"
                                                    title="Sedang ditinjau">
                                                    <i class="ti-pencil"></i>
                                                </button>
                                                <button class="btn btn-action-table btn-action-solid-blue" disabled data-bs-toggle="tooltip"
                                                    title="Sudah diajukan">
                                                    <i class="ti-share"></i>
                                                </button>
                                                <button class="btn btn-action-table btn-action-outline-red" disabled data-bs-toggle="tooltip"
                                                    title="Sedang ditinjau">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            @elseif (in_array($item->status, ['Draft', 'Ditolak']))
                                                <a href="{{ route($currentPrefix . 'tanpa-soal.edit', $item->id) }}"
                                                    class="btn btn-action-table btn-action-outline-blue" data-bs-toggle="tooltip"
                                                    title="Edit Instrumen">
                                                    <i class="ti-pencil"></i>
                                                </a>

                                                <form action="{{ route($currentPrefix . 'tanpa-soal-ajukan', $item->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Ajukan instrumen ini ke Penjamin Mutu?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-action-table btn-action-solid-blue"
                                                        data-bs-toggle="tooltip" title="Ajukan ke Penjamin Mutu">
                                                        <i class="ti-share"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route($currentPrefix . 'tanpa-soal.delete', $item->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Hapus instrumen ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-action-table btn-action-outline-red"
                                                        data-bs-toggle="tooltip" title="Hapus Instrumen">
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
                                        <i class="ti-folder me-2"></i>Belum ada instrumen tanpa soal.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-2">
                    {{ $tanpaSoalList->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </div>

    <style>
        /* Action Buttons Sesuai Model Gambar (#006199 & #dc3545) */
        .btn-action-table {
            width: 32px;
            height: 32px;
            min-width: 32px;
            padding: 0 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 13.5px;
            line-height: 1;
            border: 1.5px solid transparent;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .btn-action-table:hover:not(:disabled) {
            transform: translateY(-2px);
        }

        .btn-action-table:active:not(:disabled) {
            transform: translateY(0);
        }

        /* 1. Outline Biru (Edit) - Background putih, border biru #006199 */
        .btn-action-outline-blue {
            background-color: #ffffff !important;
            border-color: #006199 !important;
            color: #006199 !important;
        }
        .btn-action-outline-blue:hover:not(:disabled),
        .btn-action-outline-blue:focus:not(:disabled) {
            background-color: #006199 !important;
            border-color: #006199 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(0, 97, 153, 0.28) !important;
        }

        /* 2. Solid Biru (Ajukan) - Background biru #006199, icon putih */
        .btn-action-solid-blue {
            background-color: #006199 !important;
            border-color: #006199 !important;
            color: #ffffff !important;
        }
        .btn-action-solid-blue:hover:not(:disabled),
        .btn-action-solid-blue:focus:not(:disabled) {
            background-color: #004c78 !important;
            border-color: #004c78 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(0, 76, 120, 0.32) !important;
        }

        /* 3. Outline Merah (Hapus) - Background putih, border merah #dc3545 */
        .btn-action-outline-red {
            background-color: #ffffff !important;
            border-color: #dc3545 !important;
            color: #dc3545 !important;
        }
        .btn-action-outline-red:hover:not(:disabled),
        .btn-action-outline-red:focus:not(:disabled) {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.28) !important;
        }

        .btn-action-table:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
        });
    </script>
@endsection
