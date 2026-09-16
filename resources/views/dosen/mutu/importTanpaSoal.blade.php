@php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';
@endphp
@extends('dosen.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif
    <div>
        @if ($userOtoritas == 'Dosen')
            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
                title="Form ini digunakan untuk import template yang telah diisi" style="float:right;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                    <path
                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                </svg>
            </button>
        @endif
        <h3 class="px-4 pb-4 fw-bold text-center">Import Template Tanpa Soal</h3>
    </div>
    @if ($userOtoritas == 'Dosen')
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style="font-weight:bold">Import</div>
                        <div class="card-body">
                            <b>Upload Template Yang Telah di Unduh Dari Menu 'Download Template'</b>
                            <div class="form-text mb-3"></div>
                            <form action="{{ route($currentPrefix . 'importTanpaSoal') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="file">
                                <br>
                                <br>
                                <button class="btn btn-sm btn-primary" type="submit">Import</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="form-text mb-3"></div>
            </div>
        </div>
    @endif
    </div>

    <div class="content">
        <div class="card card-info card-outline">
            <div class="card-body">
                <form action="{{ route($currentPrefix . 'filter') }}" method="get">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <label for="" class="form-table">Nama</label>
                            <input name="course" type="text" class="form-control"
                                value="{{ isset($_GET['course']) ? $_GET['course'] : '' }}">
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary mt-4">Search</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip"
                                data-bs-placement="left" title="Tabel ini berisi nilai mahasiswa yang telah di import"
                                style="float:right;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Angkatan</th>
                                <th>Nama</th>
                                <th>NPM</th>
                                <th>Mata Kuliah</th>
                                <th>Jenis</th>
                                <th>Instrumen Penilaian</th> <!-- Judul lebih jelas -->
                                <th>Nilai</th>
                                <th>CPL</th>
                                <th>CPMK</th>
                                <th>Aksi</th> <!-- Opsional: untuk edit/hapus -->
                            </tr>
                        </thead>
                        <tbody>
                            @if ($mutus->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center">Belum ada data nilai yang diimport.</td>
                                </tr>
                            @else
                                @foreach ($mutus as $item)
                                    <tr>
                                        <td>{{ $item->angkatan }}</td>
                                        <td>{{ $item->nama_mhs }}</td>
                                        <td>{{ $item->npm }}</td>
                                        <td>{{ $item->nama_mk ?? $item->Course }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $item->Jenis }}</span>
                                        </td>
                                        <td>
                                            <!-- Tampilkan nama instrumen dari kolom 'soal' -->
                                            {{ $item->soal ?? 'Instrumen Tidak Dikenal' }}
                                        </td>
                                        <td class="fw-bold">{{ number_format($item->nilaiSoal, 2) }}</td>

                                        <!-- Tampilkan CPL & CPMK (Disarankan join ke tabel cpls/cpmks di Controller agar tampil judul/kodenya) -->
                                        <td>
                                            @if ($item->Cpl)
                                                <!-- Jika di controller sudah di-join ke tabel cpls, pakai ini: -->
                                                {{ $item->cpl_kode ?? 'CPL-' . $item->Cpl }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->Cpmk)
                                                {{ $item->cpmk_kode ?? 'CPMK-' . $item->Cpmk }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- Tombol aksi kecil -->
                                            <button class="btn btn-xs btn-warning" title="Edit">✎</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $mutus->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

    {{-- Modal Konfirmasi NPM Unregistered --}}
    @if (session('warning_unregistered'))
        @php
            $unregisteredList = session('unregistered_mhs', []);
            $filePathTemp = session('file_path_temp', '');
        @endphp
        <div class="modal fade show" id="unregisteredModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route($currentPrefix . 'importTanpaSoal') }}" method="POST">
                        @csrf
                        <input type="hidden" name="file_path_temp" value="{{ $filePathTemp }}">
                        <div class="modal-header py-2 px-3 bg-warning text-dark">
                            <h5 class="modal-title fs-6 fw-bold">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Perhatian: {{ count($unregisteredList) }} NPM Belum Terdaftar
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
                                Lanjutkan & Skip Baris Ini
                            </button>
                            <button type="submit" name="action_option" value="register" class="btn btn-primary btn-sm">
                                Tambahkan Data Mahasiswa Otomatis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
