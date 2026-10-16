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
                            <th>Jenis</th>
                            <th class="text-center" style="width: 120px;">Nilai</th>
                            <th class="text-center" style="width: 150px;">Rincian Soal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($mutus->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="ti ti-folder me-2"></i>Belum ada data nilai yang diimport.
                                </td>
                            </tr>
                        @else
                            @foreach ($mutus as $item)
                                @php
                                    $detailId = 'detailSoal-' . $loop->index;
                                    $detailSoals = $item->detail_soals ?? collect();
                                    $taLabel = $item->ta_tahun && $item->ta_tahun != '-'
                                        ? ($item->ta_tahun . ($item->ta_semester ? ' (' . $item->ta_semester . ')' : ''))
                                        : ($item->tahun ?: '-');
                                @endphp
                                <tr>
                                    <!-- <td class="text-center text-muted small">
                                        {{ method_exists($mutus, 'firstItem') ? $mutus->firstItem() + $loop->index : $loop->iteration }}
                                    </td> -->
                                    <td class="small fw-semibold text-secondary">
                                        {{ $taLabel }}
                                    </td>
                                    <td class="text-center text-muted">
                                        {{ $item->angkatan ?: '-' }}
                                    </td>
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
                                    <td>
                                        {{ strtoupper($item->Jenis) }}{{ !empty($item->exam_weight) && $item->exam_weight > 0 ? ' (' . $item->exam_weight . '%)' : '' }}
                                    </td>
                                    <td class="text-center">
                                        {{ is_numeric($item->nilai_akhir) ? number_format((float)$item->nilai_akhir, 2) : ($item->nilai_akhir ?: '0') }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 toggle-soal-btn d-inline-flex align-items-center gap-1 shadow-none"
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#{{ $detailId }}" 
                                            aria-expanded="false" 
                                            aria-controls="{{ $detailId }}">
                                            <i class="ti ti-list"></i>
                                            <span>{{ $detailSoals->count() }} Soal</span>
                                            <i class="ti ti-chevron-down ms-1"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Baris Dropdown / Collapse: Rincian Soal & Nilai Per Item --}}
                                <tr class="collapse bg-light bg-opacity-50" id="{{ $detailId }}">
                                    <td colspan="8" class="p-3 border-top-0">
                                        <div class="card border-0 shadow-sm rounded-3">
                                            <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                                <span class="fw-bold small text-primary">
                                                    <i class="ti ti-file-text me-1"></i> Rincian Nilai per Soal / Instrumen &mdash; {{ strtoupper($item->Jenis) }}
                                                </span>
                                                <span class="badge bg-light text-secondary border small">
                                                    Total Item: {{ $detailSoals->count() }}
                                                </span>
                                            </div>
                                            <div class="table-responsive p-0">
                                                <table class="table table-sm table-bordered align-middle mb-0 small">
                                                    <thead class="table-light">
                                                        <tr class="text-muted">
                                                            <th class="text-center" style="width: 45px;">No</th>
                                                            <th>Nama Soal / Instrumen</th>
                                                            <th class="text-center" style="width: 130px;">CPMK</th>
                                                            <th class="text-center" style="width: 130px;">CPL</th>
                                                            <th class="text-center" style="width: 120px;">Bobot (%)</th>
                                                            <th class="text-center" style="width: 120px;">Nilai Soal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($detailSoals as $idx => $soal)
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
                                                                    <span class="fw-bold text-dark">{{ $soal->nilaiSoal }}</span>
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
        </div>
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
