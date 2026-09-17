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
                        <div class="p-2 border rounded bg-light h-100">
                            <small class="text-muted d-block fw-semibold">Jumlah Mahasiswa Terdaftar:</small>
                            <span class="fs-6 fw-bold text-dark">{{ $mutuData->pluck('NPM')->unique()->count() }} Mahasiswa</span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="p-2 border rounded bg-light h-100">
                            <small class="text-muted d-block mb-1 fw-semibold">Metode Penilaian & Bobot:</small>
                            @foreach ($konversi->konversiMetode as $km)
                                <span class="badge bg-primary me-1 mb-1">
                                    {{ $km->metodePenilaian->nama ?? 'Metode' }}: {{ number_format($km->bobot, 2) }}%
                                </span>
                            @endforeach
                            @if (!empty($cpmkList))
                                <div class="mt-1 pt-1 border-top">
                                    <small class="text-muted d-inline-block me-1 fw-semibold">CPMK Terpetakan:</small>
                                    @foreach ($cpmkList as $cpmkId => $cpmkKode)
                                        <span class="badge bg-info text-white me-1">
                                            <i class="mdi mdi-target me-1"></i>{{ $cpmkKode }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tabel Daftar Nilai Konversi --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-primary text-center align-middle">
                            <tr>
                                <th style="width: 4%" class="fw-bold">No</th>
                                <th style="width: 8%" class="fw-bold">Angkatan</th>
                                <th style="width: 12%" class="fw-bold">NPM</th>
                                <th class="fw-bold">Nama Mahasiswa</th>
                                <th class="fw-bold">Metode Penilaian</th>
                                <th class="fw-bold">CPMK / Soal</th>
                                <th class="fw-bold">Nilai CPMK (Nilai Soal)</th>
                                <th class="fw-bold">Nilai Akhir Metode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $mhsCounter = 0;
                            @endphp
                            @forelse ($mhsGrouped as $npm => $items)
                                @php
                                    $mhsCounter++;
                                    $firstItem = $items->first();
                                    $mhsModel = $firstItem->mahasiswa ?? null;
                                    $totalMhsRows = $items->count();

                                    // Kelompokkan item per metode penilaian
                                    $metodeGrouped = $items->groupBy(function($it) {
                                        return $it->konversi_metode_id ?: ($it->Jenis ?: 'default');
                                    });

                                    $isFirstMhsRow = true;
                                @endphp

                                @foreach ($metodeGrouped as $kmKey => $kmItems)
                                    @php
                                        $totalKmRows = $kmItems->count();
                                        $firstKmItem = $kmItems->first();
                                        $metodeName = $firstKmItem->Jenis ?? $firstKmItem->konversiMetode?->metodePenilaian?->nama ?? 'Metode';
                                        $nilaiAkhirMetode = $firstKmItem->Nilai;
                                    @endphp

                                    @foreach ($kmItems as $cIndex => $item)
                                        <tr>
                                            {{-- Informasi Mahasiswa (Span Seluruh Metode) --}}
                                            @if ($isFirstMhsRow)
                                                <td class="text-center bg-white align-middle" rowspan="{{ $totalMhsRows }}">{{ $mhsCounter }}</td>
                                                <td class="text-center bg-white align-middle" rowspan="{{ $totalMhsRows }}">{{ $mhsModel->angkatan ?? $item->angkatan ?? '-' }}</td>
                                                <td class="text-center fw-semibold text-dark bg-white align-middle" rowspan="{{ $totalMhsRows }}">{{ $npm }}</td>
                                                <td class="fw-semibold text-dark bg-white align-middle" rowspan="{{ $totalMhsRows }}">{{ $item->Nama_mhs ?? $item->nama_mhs ?? $mhsModel->Nama ?? '-' }}</td>
                                                @php $isFirstMhsRow = false; @endphp
                                            @endif

                                            {{-- Kolom Metode Penilaian (Span Per Metode) --}}
                                            @if ($cIndex === 0)
                                                <td class="align-middle bg-white fw-semibold text-dark" rowspan="{{ $totalKmRows }}">
                                                    {{ $metodeName }}
                                                </td>
                                            @endif

                                            {{-- Kolom CPMK / Soal (Tulisan Biasa) --}}
                                            <td class="align-middle text-dark">
                                                @php
                                                    $cpmkLabel = $item->cpmk?->kode ?? ($item->Cpmk ? 'CPMK-' . $item->Cpmk : null);
                                                    $soalLabel = $item->soal;
                                                @endphp
                                                @if ($soalLabel && $cpmkLabel)
                                                    <span>{{ $soalLabel }} ({{ $cpmkLabel }})</span>
                                                @elseif ($soalLabel)
                                                    <span>{{ $soalLabel }}</span>
                                                @elseif ($cpmkLabel)
                                                    <span>{{ $cpmkLabel }}</span>
                                                @else
                                                    <span class="text-muted small">&mdash;</span>
                                                @endif
                                            </td>

                                            {{-- Kolom Nilai CPMK / Nilai Soal (Biasa/Tidak Berwarna) --}}
                                            <td class="text-center align-middle bg-white text-dark">
                                                {{ $item->nilaiSoal !== null ? number_format($item->nilaiSoal, 2) : ($item->Nilai !== null ? number_format($item->Nilai, 2) : '-') }}
                                            </td>

                                            {{-- Kolom Nilai Akhir Metode (Span Per Metode: 1 baris per metode, Biasa/Tidak Berwarna) --}}
                                            @if ($cIndex === 0)
                                                <td class="text-center align-middle bg-white text-dark" rowspan="{{ $totalKmRows }}">
                                                    {{ $nilaiAkhirMetode !== null ? number_format($nilaiAkhirMetode, 2) : '-' }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted p-4">
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
