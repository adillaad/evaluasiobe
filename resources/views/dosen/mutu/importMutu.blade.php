{{-- =========================================================================
     PERBAIKAN 4: View Import Nilai - GABUNGAN (soal + tanpa soal dalam satu view)
     File: resources/views/dosen/mutu/importMutu.blade.php
     Masalah: import terpisah jadi dua form, seharusnya satu tampilan
     ========================================================================= --}}
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
                <strong>Catatan:</strong> Template gabungan (kolom <code>SOAL|…</code> dan <code>TS|…</code>)
                diproses dalam satu import. Pastikan file yang diunggah adalah template yang diunduh dari
                menu <em>Download Template Penilaian</em> dan sudah diisi lengkap.
            </div>

            <form action="{{ route('dosen.importmutu') }}" method="POST" enctype="multipart/form-data">
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

            <form id="dosenFilterForm" action="{{ route('dosen.filter') }}" method="GET" class="mb-3">
                @csrf
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-end flex-wrap gap-2">
                        @if (isset($mks) && $mks->isNotEmpty())
                            <div style="max-width: 380px; width: 100%;">
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
                        @if (request('course') || request('mk_kode'))
                            <div>
                                <a href="{{ route('dosen.import-mutu') }}" class="btn btn-secondary btn-sm px-4 fw-semibold d-flex align-items-center justify-content-center" style="height: 38px;">
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

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Angkatan</th>
                            <th>Nama</th>
                            <th>NPM</th>
                            <th>Nama Mata Kuliah</th>
                            <th>Jenis</th>
                            <th>Soal / Instrumen</th>
                            <th>Nilai</th>
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
                                <tr>
                                    <td class="small">{{ $item->angkatan }}</td>
                                    <td class="small fw-semibold">{{ $item->nama_mhs }}</td>
                                    <td class="small">{{ $item->npm }}</td>
                                    <td class="small">{{ $item->nama_mk }}</td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ $item->Jenis }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        @if (isset($item->idSoal))
                                            <a href="soal/cetakSoal/{{ $item->idSoal }}" target="_blank"
                                                class="text-primary">
                                                {{ $item->soal }}
                                            </a>
                                        @else
                                            {{ $item->soal }}
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ $item->nilaiSoal }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            {{ $mutus->links() }}
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
                    <form action="{{ route('dosen.importmutu') }}" method="POST">
                        @csrf
                        <input type="hidden" name="parsed_rows_data" value="{{ json_encode($tempParsedRows) }}">
                        <input type="hidden" name="headers_data" value="{{ json_encode($tempHeaders) }}">
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
