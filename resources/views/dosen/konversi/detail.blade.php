@extends('dosen.template')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h4 class="card-title mb-1">
                            <i class="mdi mdi-table text-primary me-1"></i> Detail Nilai Konversi: {{ $konversi->mk->nama ?? $konversi->mk_kode }}
                        </h4>
                        <p class="text-muted small mb-0">
                            Mata Kuliah: <strong>{{ $konversi->mk_kode }} - {{ $konversi->mk->nama ?? '' }}</strong> &nbsp;|&nbsp;
                            Tahun Ajaran: {{ $konversi->tahunAjaran->tahun ?? '-' }}@if(!empty($konversi->tahunAjaran->jenis_semester)) ({{ $konversi->tahunAjaran->jenis_semester }})@endif &nbsp;|&nbsp;
                            Kurikulum: Tahun {{ $konversi->kurikulum->tahun ?? '-' }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dosen.konversi-nilai.step-metode', $konversi->id) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Kelola Metode
                        </a>
                        <a href="{{ route('dosen.konversi-nilai.index') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-view-list me-1"></i> Daftar Konversi
                        </a>
                    </div>
                </div>

                {{-- Summary Badges --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Jumlah Mahasiswa Terdaftar:</small>
                            <span class="fs-6 fw-bold text-dark">{{ $mutuData->pluck('NPM')->unique()->count() }} Mahasiswa</span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block mb-1">Metode Penilaian & Bobot:</small>
                            @foreach ($konversi->konversiMetode as $km)
                                <span class="badge bg-primary me-1">
                                    {{ $km->metodePenilaian->nama ?? 'Metode' }}: {{ number_format($km->bobot, 2) }}%
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Tabel Daftar Nilai Konversi --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 10%">Angkatan</th>
                                <th style="width: 15%">NPM</th>
                                <th>Nama Mahasiswa</th>
                                @foreach ($metodeList as $mId => $mNama)
                                    <th>{{ $mNama }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mhsGrouped as $npm => $items)
                                @php
                                    $firstItem = $items->first();
                                    $mhsModel = $firstItem->mahasiswa ?? null;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $mhsModel->angkatan ?? '-' }}</td>
                                    <td class="text-center">{{ $npm }}</td>
                                    <td>{{ $firstItem->Nama_mhs ?? $firstItem->nama_mhs ?? $mhsModel->Nama ?? '-' }}</td>
                                    @foreach ($metodeList as $mId => $mNama)
                                        @php
                                            $val = $items->where('konversi_metode_id', $mId)->first()?->Nilai;
                                        @endphp
                                        <td class="text-center fw-bold">
                                            {{ $val !== null ? number_format($val, 2) : '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 4 + count($metodeList) }}" class="text-center text-muted p-4">
                                        <i class="mdi mdi-information-outline me-1"></i> Belum ada data nilai yang diimpor.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
