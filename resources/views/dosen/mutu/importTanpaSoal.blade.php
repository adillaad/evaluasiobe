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
                                <th>Prodi</th>
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
                                    <td colspan="11" class="text-center">Belum ada data nilai yang diimport.</td>
                                </tr>
                            @else
                                @foreach ($mutus as $item)
                                    <tr>
                                        <td>{{ $item->nama_prodi ?? '-' }}</td>
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
@endsection
