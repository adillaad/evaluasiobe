{{-- =========================================================================
     PERBAIKAN 4: View Import Nilai - GABUNGAN (soal + tanpa soal dalam satu view)
     File: resources/views/dosen/mutu/importMutu.blade.php
     Masalah: import terpisah jadi dua form, seharusnya satu tampilan
     ========================================================================= --}}
@php
    $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
    $currentPrefix = $userOtoritas
        ? str_replace(' ', '-', strtolower($userOtoritas)) . '.'
        : 'dosen.';
@endphp
@extends('dosen.template')
@section('content')

    <h3 class="fw-bold text-center mb-4">Import Nilai Penilaian</h3>

    {{-- ── SATU CARD IMPORT GABUNGAN ─────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <h6 class="fw-bold mb-0">
                <i class="ti ti-upload me-2 text-primary"></i>
                Upload Template Penilaian Gabungan
            </h6>
        </div>
        <div class="card-body p-4">
            <p class="text-muted small mb-3">
                Upload <strong>satu file template</strong> yang telah diunduh dari menu
                <strong>Download Template Penilaian</strong>. Template ini mencakup baik soal ujian
                maupun instrumen penilaian tanpa soal sekaligus.
            </p>

            {{-- Info box --}}
            <div class="alert alert-info py-2 mb-3 small">
                <i class="ti ti-info-circle me-1"></i>
                <strong>Catatan:</strong> Template penilaian gabungan (kolom <code>Soal - CPMK</code> dan instrumen)
                diproses dalam satu import. Pastikan file yang diunggah adalah template yang diunduh dari
                menu <em>Download Template Penilaian</em> dan sudah diisi lengkap.
            </div>

            <form action="{{ route($currentPrefix . 'importmutu') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">File Template (.xlsx)</label>
                        <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls"
                            required>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary btn-sm fw-semibold px-4">
                            <i class="ti ti-upload me-1"></i> Import
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Filter + Tabel Data ──────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <h6 class="fw-bold mb-0">
                <i class="ti ti-list me-2"></i>Data Nilai yang Sudah Diimport
            </h6>
        </div>
        <div class="card-body p-4">

            <form id="dosenFilterForm" action="{{ route($currentPrefix . 'filter') }}" method="GET" class="mb-3">
                @csrf
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-end flex-wrap gap-2">
                        @if (isset($tahunAjarans) && $tahunAjarans->isNotEmpty())
                            <div style="max-width: 220px; width: 100%;">
                                <label class="form-label fw-semibold small mb-1">Tahun Akademik</label>
                                <select name="tahun_ajaran_id" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                    <option value="">-- Semua Tahun Akademik --</option>
                                    @foreach ($tahunAjarans as $ta)
                                        <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                            {{ $ta->label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if (isset($angkatans) && $angkatans->isNotEmpty())
                            <div style="max-width: 170px; width: 100%;">
                                <label class="form-label fw-semibold small mb-1">Angkatan</label>
                                <select name="angkatan" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                    <option value="">-- Semua Angkatan --</option>
                                    @foreach ($angkatans as $angk)
                                        <option value="{{ $angk }}" {{ request('angkatan') == (string)$angk ? 'selected' : '' }}>
                                            {{ $angk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if (isset($mks) && $mks->isNotEmpty())
                            <div style="max-width: 320px; width: 100%;">
                                <label class="form-label fw-semibold small mb-1">Mata Kuliah</label>
                                <select name="mk_kode" class="form-select form-select-sm filter-auto-submit" style="height: 38px;">
                                    <option value="">-- Semua Mata Kuliah --</option>
                                    @foreach ($mks as $mk)
                                        <option value="{{ $mk->kode }}" {{ request('mk_kode') == $mk->kode ? 'selected' : '' }}>
                                            {{ $mk->nama }} ({{ $mk->kode }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if (request('course') || request('mk_kode') || request('tahun_ajaran_id') || request('angkatan'))
                            <div>
                                <a href="{{ route($currentPrefix . 'import-mutu') }}" class="btn btn-secondary btn-sm px-4 fw-semibold d-flex align-items-center justify-content-center" style="height: 38px;">
                                    <i class="ti ti-refresh me-1"></i> Reset
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <div style="max-width: 350px; width: 100%;">
                            <label class="form-label fw-semibold small mb-1">Cari Mahasiswa</label>
                            <input name="course" type="text" class="form-control form-control-sm instant-search" style="height: 38px;"
                                value="{{ request('course') }}" placeholder="Ketik nama atau NPM..." autocomplete="off">
                        </div>
                    </div>
                </div>
            </form>

            <style>
                .toggle-soal-btn[aria-expanded="true"] .ti-chevron-down {
                    transform: rotate(180deg);
                }
                .toggle-soal-btn .ti-chevron-down {
                    transition: transform 0.2s ease-in-out;
                }
            </style>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <!-- <th style="width: 45px;" class="text-center">#</th> -->
                            <th style="width: 140px;">Tahun</th>
                            <th style="width: 70px;" class="text-center">Angkatan</th>
                            <th style="width: 120px;">NPM</th>
                            <th>Nama</th>
                            <th>Mata Kuliah</th>
                            <th class="text-center" style="width: 120px;">Nilai Akhir MK</th>
                            <th class="text-center" style="width: 130px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($mutus->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ti ti-folder me-2"></i>Belum ada data nilai yang diimport.
                                </td>
                            </tr>
                        @else
                            @foreach ($mutus as $item)
                                @php
                                    $detailId  = 'detailSoal-' . $loop->index;
                                    $metodes   = $item->metodes ?? collect();
                                    $skorCpmk  = $item->skor_cpmk ?? [];
                                    $taLabel   = $item->ta_label ?? ($item->tahun ?: '-');
                                @endphp
                                <tr>
                                    <td class="small fw-semibold text-secondary">{{ $taLabel }}</td>
                                    <td class="text-center text-muted">{{ $item->angkatan ?: '-' }}</td>
                                    <td>
                                        <span class="font-monospace fw-semibold">{{ $item->npm }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $item->nama_mhs ?: 'Tanpa Nama' }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $item->nama_mk ?: $item->Course }}</div>
                                        <div class="text-muted small">{{ $item->Course }}</div>
                                    </td>
                                    <td class="text-center fw-bold text-dark">
                                        {{ number_format((float)($item->nilai_akhir_mk ?? 0), 2) }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 toggle-soal-btn d-inline-flex align-items-center gap-1 shadow-none"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#{{ $detailId }}"
                                            aria-expanded="false"
                                            aria-controls="{{ $detailId }}">
                                            <i class="ti ti-list"></i>
                                            <span>{{ $metodes->count() }} Metode</span>
                                            <i class="ti ti-chevron-down ms-1"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Baris Detail: Rincian per Metode --}}
                                <tr class="collapse bg-light bg-opacity-50" id="{{ $detailId }}">
                                    <td colspan="7" class="p-3 border-top-0">
                                        <div class="card border-0 shadow-sm rounded-3">
                                            <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                                <span class="fw-bold small text-primary">
                                                    <i class="ti ti-school me-1"></i>
                                                    Rincian Nilai per Metode &mdash; {{ $item->nama_mk ?: $item->Course }}
                                                </span>
                                                <span class="badge bg-light text-secondary border small">
                                                    {{ $metodes->count() }} Metode
                                                </span>
                                            </div>
                                            <div class="p-3">
                                                @foreach ($metodes as $mIdx => $metode)
                                                    @php $metodeDetailId = $detailId . '-m-' . $mIdx; @endphp
                                                    <div class="mb-3">
                                                        <div class="d-flex align-items-center justify-content-between bg-white border rounded px-3 py-2 mb-0">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="fw-bold text-dark">{{ strtoupper($metode->jenis_nama) }}</span>
                                                                @if ($metode->bobot > 0)
                                                                    <span class="badge bg-warning bg-opacity-20 text-warning-emphasis border border-warning border-opacity-25 small">Bobot: {{ $metode->bobot }}%</span>
                                                                @endif
                                                                <span class="fw-bold text-primary ms-2">Nilai: {{ number_format((float)$metode->nilai, 2) }}</span>
                                                            </div>
                                                            @if ($metode->soal_count > 0)
                                                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0 toggle-soal-btn d-inline-flex align-items-center gap-1 shadow-none"
                                                                    type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#{{ $metodeDetailId }}"
                                                                    aria-expanded="false">
                                                                    <i class="ti ti-list small"></i>
                                                                    <span class="small">{{ $metode->soal_count }} Soal</span>
                                                                    <i class="ti ti-chevron-down ms-1 small"></i>
                                                                </button>
                                                            @endif
                                                        </div>

                                                        @if ($metode->soal_count > 0)
                                                            <div class="collapse" id="{{ $metodeDetailId }}">
                                                                <div class="table-responsive border rounded-bottom border-top-0">
                                                                    <table class="table table-sm table-bordered align-middle mb-0 small">
                                                                        <thead class="table-light">
                                                                            <tr class="text-muted">
                                                                                <th class="text-center" style="width: 45px;">No</th>
                                                                                <th>Nama Soal / Instrumen</th>
                                                                                <th class="text-center" style="width: 110px;">CPMK</th>
                                                                                <th class="text-center" style="width: 110px;">CPL</th>
                                                                                <th class="text-center" style="width: 100px;">Bobot (%)</th>
                                                                                <th class="text-center" style="width: 100px;">Nilai Soal</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse ($metode->soals as $idx => $soal)
                                                                                <tr>
                                                                                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                                                                    <td>
                                                                                        @if (!empty($soal->idSoal))
                                                                                            <a href="{{ url('dosen/soal/cetakSoal/' . $soal->idSoal) }}" target="_blank" class="fw-semibold text-primary text-decoration-none d-inline-flex align-items-center gap-1">
                                                                                                <i class="ti ti-external-link small"></i> {{ $soal->soal }}
                                                                                            </a>
                                                                                        @else
                                                                                            <span class="fw-semibold text-dark">{{ $soal->soal }}</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        @if (!empty($soal->kode_cpmk))
                                                                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ $soal->kode_cpmk }}</span>
                                                                                        @else
                                                                                            <span class="text-muted">-</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        @if (!empty($soal->kode_cpl))
                                                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $soal->kode_cpl }}</span>
                                                                                        @else
                                                                                            <span class="text-muted">-</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-center fw-semibold text-muted">
                                                                                        {{ $soal->BobotSoal !== null ? $soal->BobotSoal . '%' : '-' }}
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <span class="fw-bold text-dark">{{ is_numeric($soal->nilaiSoal ?? $soal->Nilai) ? number_format((float)($soal->nilaiSoal ?? $soal->Nilai), 2) : ($soal->nilaiSoal ?? $soal->Nilai ?? '0') }}</span>
                                                                                    </td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="6" class="text-center text-muted py-2">Tidak ada rincian soal.</td>
                                                                                </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $mutus->links() }}
            </div>


    {{-- Modal Konfirmasi NPM Unregistered --}}
    @if (session('warning_unregistered'))
        @php
            $unregisteredList = session('unregistered_mhs', []);
            $tempParsedRows = session('temp_parsed_rows', []);
            $tempHeaders = session('temp_headers', []);
        @endphp
        <div class="modal fade show" id="unregisteredModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route($currentPrefix . 'importmutu') }}" method="POST">
                        @csrf
                        <input type="hidden" name="parsed_rows_data" value="{{ json_encode($tempParsedRows) }}">
                        <input type="hidden" name="headers_data" value="{{ json_encode($tempHeaders) }}">
                        <input type="hidden" name="display_headers_data" value="{{ json_encode(session('temp_display_headers', [])) }}">
                        <div class="modal-header py-2 px-3 bg-warning text-dark">
                            <h5 class="modal-title fs-6 fw-bold">
                                <i class="ti ti-alert-circle me-1"></i> Perhatian: {{ count($unregisteredList) }} NPM Belum Terdaftar
                            </h5>
                        </div>
                        <div class="modal-body p-3" style="max-height: 50vh; overflow-y: auto;">
                            <p class="small mb-2">Terdapat beberapa NPM dalam file Excel yang belum terdaftar di basis data mahasiswa:</p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Baris</th>
                                            <th>Angkatan</th>
                                            <th>NPM</th>
                                            <th>Nama</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($unregisteredList as $unreg)
                                            <tr>
                                                <td>Baris {{ $unreg['line'] }}</td>
                                                <td>{{ $unreg['angkatan'] ?: '-' }}</td>
                                                <td><code>{{ $unreg['npm'] }}</code></td>
                                                <td>{{ $unreg['nama'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-info py-2 mt-2 mb-0 small">
                                Silakan pilih tindakan yang akan diambil untuk data mahasiswa di atas:
                            </div>
                        </div>
                        <div class="modal-footer py-2 px-3 bg-light justify-content-between">
                            <button type="submit" name="action_option" value="skip" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-player-skip-forward me-1"></i> Lanjutkan & Skip Baris Ini
                            </button>
                            <button type="submit" name="action_option" value="register" class="btn btn-primary btn-sm">
                                <i class="ti ti-user-plus me-1"></i> Tambahkan Data Mahasiswa Otomatis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('dosenFilterForm');
                if (!form) return;

                // Auto submit on dropdown select change
                const dropdowns = form.querySelectorAll('.filter-auto-submit');
                dropdowns.forEach(select => {
                    select.addEventListener('change', function() {
                        form.submit();
                    });
                });

                // Instant typing search (debounce 400ms)
                const searchInput = form.querySelector('.instant-search');
                if (searchInput) {
                    let timer = null;
                    searchInput.addEventListener('input', function() {
                        clearTimeout(timer);
                        timer = setTimeout(function() {
                            form.submit();
                        }, 400);
                    });

                    // Keep cursor at end of input
                    const val = searchInput.value;
                    if (val) {
                        searchInput.focus();
                        searchInput.setSelectionRange(val.length, val.length);
                    }
                }
            });
        </script>
    @endpush
@endsection
