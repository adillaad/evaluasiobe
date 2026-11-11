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
                            Tahun Akademik: {{ $konversi->tahunAjaran->tahun ?? '-' }}@if(!empty($konversi->tahunAjaran->jenis_semester)) ({{ $konversi->tahunAjaran->jenis_semester }})@endif &nbsp;|&nbsp;
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
                @php
                    $prodiObj = $konversi->mk?->prodi ?? auth()->user()->prodiUserObj ?? new \App\Models\Prodi();
                @endphp
                <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="table-primary text-center align-middle">
            <tr>
                <th class="fw-bold">No</th>
                <th class="fw-bold">Angkatan</th>
                <th class="fw-bold">NPM</th>
                <th class="fw-bold">Nama Mahasiswa</th>
                <th class="fw-bold">Nilai Akhir MK</th>
                <th class="fw-bold">Huruf Mutu MK</th>
                <th class="fw-bold">Detail</th>
            </tr>
        </thead>
        <tbody>
            @php $mhsCounter = 0; @endphp
            @forelse ($mhsGrouped as $npm => $items)
                @php
                    $mhsCounter++;
                    $firstItem = $items->first();
                    $mhsModel = $firstItem->mahasiswa ?? null;

                    // Group by metode for detail view
                    $metodeGrouped = $items->groupBy(function($it) {
                        return $it->konversi_metode_id ?: ($it->Jenis ?: 'default');
                    });

                    // Calculate Nilai Akhir MK (weighted sum)
                    $totalNaMk = 0;
                    $hasValidScore = false;
                    foreach ($metodeGrouped as $kmItems) {
                        $kmFirst = $kmItems->first();
                        $nMetode = $kmFirst->Nilai !== null ? (float)$kmFirst->Nilai : null;
                        $bMetode = (float)($kmFirst->konversiMetode?->bobot ?? $kmFirst->BobotSoal ?? 0);
                        if ($nMetode !== null) {
                            $totalNaMk += ($nMetode * ($bMetode / 100.0));
                            $hasValidScore = true;
                        }
                    }
                    $valNaMk = $hasValidScore ? round($totalNaMk, 2) : null;
                    $hurufNaMk = $valNaMk !== null ? ($prodiObj->convertGrade($valNaMk)[0] ?? '-') : '-';

                    // Hitung Skor CPMK per mahasiswa
                    $mhsCpmkScores = [];
                    $itemsByCpmk = $items->filter(fn($r) => !empty($r->cpmk?->kode) || !empty($r->Cpmk))
                        ->groupBy(fn($r) => $r->cpmk?->kode ?: ('CPMK-' . $r->Cpmk));
                    foreach ($itemsByCpmk as $cKode => $cRows) {
                        $sumW = 0; $sumS = 0;
                        foreach ($cRows as $r) {
                            $bSoal = (float)($r->BobotSoal ?? 0);
                            $w = $bSoal > 0 ? $bSoal : 1;
                            $sc = $r->nilaiSoal !== null ? (float)$r->nilaiSoal : (float)($r->Nilai ?? 0);
                            $sumS += ($sc * $w);
                            $sumW += $w;
                        }
                        $mhsCpmkScores[$cKode] = $sumW > 0 ? round($sumS / $sumW, 2) : 0;
                    }
                    uksort($mhsCpmkScores, 'strnatcmp');
                @endphp
                <tr>
                    <td class="text-center bg-white align-middle">{{ $mhsCounter }}</td>
                    <td class="text-center bg-white align-middle">{{ $mhsModel->angkatan ?? $firstItem->angkatan ?? '-' }}</td>
                    <td class="text-center fw-semibold text-dark bg-white align-middle">{{ $npm }}</td>
                    <td class="fw-semibold text-dark bg-white align-middle">
                        <div>{{ $firstItem->Nama_mhs ?? $firstItem->nama_mhs ?? $mhsModel->Nama ?? '-' }}</div>
                        @if (!empty($mhsCpmkScores))
                            <div class="mt-2 pt-1 border-top">
                                <div class="text-muted" style="font-size: 0.72rem; font-weight: 600;">Skor CPMK:</div>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @foreach ($mhsCpmkScores as $cKode => $scVal)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.7rem; font-weight: 600;">
                                            {{ $cKode }}: {{ number_format($scVal, 2) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </td>
                    <td class="text-center align-middle bg-white">
                        {{ $valNaMk !== null ? number_format($valNaMk, 2) : '-' }}
                    </td>
                    <td class="text-center align-middle bg-white">
                        {{ $hurufNaMk }}
                    </td>
                    <td class="text-center align-middle bg-white">
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#detail-{{ $npm }}" aria-expanded="false" aria-controls="detail-{{ $npm }}">
                            Lihat Detail
                        </button>
                    </td>
                </tr>
                {{-- Hidden detail rows for each metode & CPMK --}}
                <tr class="collapse" id="detail-{{ $npm }}">
                    <td colspan="7" class="p-0 pt-2">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Metode Penilaian</th>
                                    <th>CPMK / Soal</th>
                                    <th>Nilai CPMK (Nilai Soal)</th>
                                    <th>Nilai Akhir Metode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($metodeGrouped as $kmKey => $kmItems)
                                    @php
                                        $firstKm = $kmItems->first();
                                        $metodeName = $firstKm->Jenis ?? $firstKm->konversiMetode?->metodePenilaian?->nama ?? 'Metode';
                                    @endphp
                                    @foreach ($kmItems as $cIndex => $item)
                                        <tr>
                                            @if ($cIndex === 0)
                                                <td rowspan="{{ $kmItems->count() }}" class="align-middle bg-white fw-semibold">{{ $metodeName }}</td>
                                            @endif
                                            @php
                                                $cpmkLabel = $item->cpmk?->kode ?? ($item->Cpmk ? 'CPMK-' . $item->Cpmk : null);
                                                $soalLabel = $item->soal;
                                                $isGenericSoal = empty($soalLabel) || preg_match('/^Soal\s*(#\s*\d+|\d+)?$/i', trim($soalLabel));
                                            @endphp
                                            <td>
                                                @if (!$isGenericSoal && $cpmkLabel)
                                                    <span>{{ $cpmkLabel }} ({{ $soalLabel }})</span>
                                                @elseif ($cpmkLabel)
                                                    <span>{{ $cpmkLabel }}</span>
                                                @elseif (!$isGenericSoal)
                                                    <span>{{ $soalLabel }}</span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $item->nilaiSoal !== null ? number_format($item->nilaiSoal, 2) : ($item->Nilai !== null ? number_format($item->Nilai, 2) : '-') }}
                                            </td>
                                            @if ($cIndex === 0)
                                                <td rowspan="{{ $kmItems->count() }}" class="text-center align-middle bg-white">
                                                    {{ $firstKm->Nilai !== null ? number_format($firstKm->Nilai, 2) : '-' }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Tidak ada data nilai.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
            </div>
        </div>
    </div>
</div>
@endsection
