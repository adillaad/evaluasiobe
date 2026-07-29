{{-- =========================================================================
     PERBAIKAN 4: View Import Nilai - GABUNGAN (soal + tanpa soal dalam satu view)
     File: resources/views/dosen/mutu/importMutu.blade.php
     Masalah: import terpisah jadi dua form, seharusnya satu tampilan
     ========================================================================= --}}
@extends('dosen.template')
@section('content')

    @if (session()->has('failed') || session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('failed') ?? session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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

            <form action="{{ route($currentPrefix . 'filter') }}" method="GET" class="mb-3">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-sm-4 col-md-3">
                        <label class="form-label fw-semibold small mb-1">Cari Nama</label>
                        <input name="course" type="text" class="form-control form-control-sm"
                            value="{{ request('course') }}" placeholder="Nama mahasiswa...">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="ti ti-search me-1"></i> Cari
                        </button>
                        @if (request('course'))
                            <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm px-3">Reset</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Prodi</th>
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
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="ti ti-folder me-2"></i>Belum ada data nilai yang diimport.
                                </td>
                            </tr>
                        @else
                            @foreach ($mutus as $item)
                                <tr>
                                    <td class="small">{{ $item->nama_prodi }}</td>
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

@endsection
