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
                    <a href="{{ route('dosen.addRawTS') }}" class="btn btn-primary btn-sm fw-semibold px-3">
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
                                                <span class="badge bg-success px-2 py-1" data-bs-toggle="tooltip"
                                                    title="Instrumen telah disetujui dan tidak dapat diubah">
                                                    <i class="ti-lock me-1"></i> Terkunci
                                                </span>
                                            @elseif ($item->status === 'Menunggu Validasi')
                                                <button class="btn btn-warning btn-sm p-1" disabled data-bs-toggle="tooltip"
                                                    title="Sedang ditinjau">
                                                    <i class="ti-pencil"></i>
                                                </button>
                                                <button class="btn btn-primary btn-sm p-1" disabled data-bs-toggle="tooltip"
                                                    title="Sudah diajukan">
                                                    <i class="ti-share"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm p-1" disabled data-bs-toggle="tooltip"
                                                    title="Sedang ditinjau">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            @elseif (in_array($item->status, ['Draft', 'Ditolak']))
                                                <a href="{{ route('dosen.tanpa-soal.edit', $item->id) }}"
                                                    class="btn btn-warning btn-sm p-1" data-bs-toggle="tooltip"
                                                    title="Edit Instrumen">
                                                    <i class="ti-pencil"></i>
                                                </a>

                                                <form action="{{ route('dosen.tanpa-soal-ajukan', $item->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Ajukan instrumen ini ke Penjamin Mutu?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm p-1"
                                                        data-bs-toggle="tooltip" title="Ajukan ke Penjamin Mutu">
                                                        <i class="ti-share"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('dosen.tanpa-soal.delete', $item->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Hapus instrumen ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm p-1"
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
                    {{ $tanpaSoalList->appends(request()->except('page'))->links() }}
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
        });
    </script>
@endsection
