@extends('penjamin-mutu.template')

@section('content')
@php
    $routePrefix = [
        'Penjamin Mutu Universitas' => ['prefix' => 'penjamin-mutu.universitas.'],
        'Penjamin Mutu Fakultas' => ['prefix' => 'penjamin-mutu.fakultas.'],
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
        'Dosen' => ['prefix' => 'dosen.'],
    ];
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp

<div class="row">
    <div class="col-md-12">

        @php
            $summaryDatasetHeader = isset($allPenilaiansForSummary) ? $allPenilaiansForSummary : $penilaians;
            $headerTotalBobot = 0;
            $hasIncompleteInstrumen = false;
            foreach ($summaryDatasetHeader as $p) {
                foreach ($p->penilaianMetode as $pm) {
                    $headerTotalBobot += (float) $pm->bobot;
                    if ($pm->instrumens->isEmpty()) {
                        $hasIncompleteInstrumen = true;
                    }
                }
            }
        @endphp

        {{-- Header MK Box --}}
        <div class="card mb-3 border-0 shadow-sm rounded-4">
            <div class="card-body p-3 p-md-4">
                <div class="mb-2">
                    <a href="javascript:history.back()" class="text-decoration-none text-secondary small d-inline-flex align-items-center">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar MK
                    </a>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">
                        {{ $mk->kode }} &mdash; {{ $mk->nama }}
                    </h4>
                    <div class="text-muted small">
                        <i class="mdi mdi-grid-large me-1"></i>{{ $mk->prodi->nama ?? '-' }}
                        @if ($mk->prodi->fakultas ?? null) &mdash; {{ $mk->prodi->fakultas->nama }} @endif
                        &nbsp;|&nbsp; SKS: {{ $mk->total_sks }}
                        @if ($mk->kurikulum) &nbsp;|&nbsp; Kurikulum {{ $mk->kurikulum->tahun }} @endif
                    </div>
                </div>
            </div>
        </div>



        @if ($penilaians->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-clipboard-outline" style="font-size:3rem; color:#ccc;"></i>
                    <p class="text-muted mt-3 mb-3">Belum ada asesmen yang dikonfigurasi untuk mata kuliah ini.</p>
                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                        <a href="{{ Route::has($currentPrefix . 'asesmen.asesmen-add') ? route($currentPrefix . 'asesmen.asesmen-add') . '?mk_kode=' . $mk->kode : '#' }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus-circle me-1"></i> Tambah Asesmen Pertama
                        </a>
                    @endif
                </div>
            </div>
        @else

        {{-- ====== CARD RINGKASAN CPMK TERPAKAI ====== --}}
        @if (isset($mappedCpmks))
            @php
                $totalCpmk = count($mappedCpmks);
                $assessedArray = $assessedCpmkIds ?? [];
                $usedCpmkCount = count(array_intersect($mappedCpmks->pluck('id')->toArray(), $assessedArray));
                $percentageUsed = $totalCpmk > 0 ? round(($usedCpmkCount / $totalCpmk) * 100) : 0;
                $unassessedCpmks = $mappedCpmks->reject(fn($cpmk) => in_array($cpmk->id, $assessedArray));
            @endphp
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-bookmark-check fs-5"></i>
                            </div>
                            <div>
                                <span class="fw-bold text-dark me-2 small">Penggunaan CPMK:</span>
                                <span class="text-muted small">
                                    <strong>{{ $usedCpmkCount }}</strong> dari <strong>{{ $totalCpmk }}</strong> CPMK terpetakan telah memiliki asesmen
                                </span>
                            </div>
                        </div>

                        {{-- Dropdown CPMK Belum Terpetakan/Belum Punya Asesmen --}}
                        <div class="d-flex align-items-center ms-md-2">
                            <select class="form-select form-select-sm border-warning text-dark" style="max-width: 280px; font-size: 0.8rem;">
                                @if($unassessedCpmks->isNotEmpty())
                                    <option value="" disabled selected>⚠️ {{ $unassessedCpmks->count() }} CPMK Belum Punya Asesmen...</option>
                                    @foreach($unassessedCpmks as $uCpmk)
                                        <option value="{{ $uCpmk->id }}">
                                            {{ $uCpmk->kode }} - {{ Str::limit($uCpmk->judul ?? $uCpmk->deskripsi ?? '', 45) }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled selected>✓ Semua CPMK Sudah Terpetakan</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div>
                        <span class="badge {{ $usedCpmkCount == $totalCpmk && $totalCpmk > 0 ? 'bg-success' : ($usedCpmkCount > 0 ? 'bg-info text-dark' : 'bg-warning text-dark') }} px-2 py-1" style="font-size: 0.78rem; font-weight: 500;">
                            <i class="mdi mdi-check-decagram me-1"></i> {{ $usedCpmkCount }} / {{ $totalCpmk }} CPMK Terpakai ({{ $percentageUsed }}%)
                        </span>
                    </div>
                </div>
            </div>
        @endif

        {{-- ====== CARD RINGKASAN BOBOT METODE TERKAIT (KOMPAK) ====== --}}
        @php
            $summaryDataset = isset($allPenilaiansForSummary) ? $allPenilaiansForSummary : $penilaians;
            $metodeMap = [];
            foreach ($summaryDataset as $p) {
                foreach ($p->penilaianMetode as $pm) {
                    $namaMetode = $pm->metode->nama ?? 'Metode';
                    if (!isset($metodeMap[$namaMetode])) {
                        $metodeMap[$namaMetode] = ['bobot' => 0, 'count' => 0];
                    }
                    $metodeMap[$namaMetode]['bobot'] += (float) $pm->bobot;
                    $metodeMap[$namaMetode]['count']++;
                }
            }
            $totalBobot = collect($metodeMap)->sum('bobot');
        @endphp

        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-white fw-bold py-2 px-3 d-flex align-items-center justify-content-between border-bottom">
                <div class="small fw-bold">
                    <i class="mdi mdi-chart-donut text-primary me-1"></i> Ringkasan Bobot Metode Penilaian
                </div>
                <div>
                    <span class="badge {{ round($totalBobot, 2) == 100 ? 'bg-success' : 'bg-warning text-dark' }} px-2 py-1" style="font-size:0.75rem;">
                        Total Bobot: {{ number_format($totalBobot, 2) }}%
                    </span>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    @foreach ($metodeMap as $nama => $data)
                        @php
                            $pct = $totalBobot > 0 ? ($data['bobot'] / $totalBobot) * 100 : 0;
                        @endphp
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <div class="border rounded p-2 text-center shadow-sm bg-body h-100">
                                <div class="fw-semibold text-dark small text-truncate" title="{{ $nama }}">{{ $nama }}</div>
                                <div style="font-size:1.25rem; font-weight:700; color:#0d6efd;" class="my-1">
                                    {{ number_format($data['bobot'], 2) }}%
                                </div>
                                <div class="progress my-1" style="height:4px;">
                                    <div class="progress-bar bg-primary" style="width:{{ min($pct,100) }}%"></div>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.7rem;">{{ $data['count'] }} CPL-CPMK</small>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="border rounded p-2 text-center shadow-sm bg-light h-100">
                            <div class="fw-semibold text-muted small">Status Total</div>
                            <div style="font-size:1.25rem; font-weight:700; color:{{ round($totalBobot, 2) == 100 ? '#198754' : '#dc3545' }};" class="my-1">
                                {{ number_format($totalBobot, 2) }}%
                            </div>
                            <small class="{{ round($totalBobot, 2) == 100 ? 'text-success fw-semibold' : 'text-danger fw-semibold' }} d-block" style="font-size:0.7rem;">
                                {{ round($totalBobot, 2) == 100 ? '✓ 100%' : '⚠ Belum 100%' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== TABEL DETAIL ASESMEN ====== --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="small fw-bold"><i class="mdi mdi-table me-1 text-primary"></i> Detail Asesmen</span>
                    <small class="text-muted" style="font-size:0.75rem;">({{ $penilaians->total() }} baris CPL-CPMK)</small>
                </div>
                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                    <div>
                        <a href="{{ Route::has($currentPrefix . 'asesmen.asesmen-add') ? route($currentPrefix . 'asesmen.asesmen-add') . '?mk_kode=' . $mk->kode : '#' }}" class="btn btn-primary btn-sm px-3 py-1 shadow-sm">
                            <i class="mdi mdi-plus-circle me-1"></i> Tambah Asesmen
                        </a>
                    </div>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 align-middle">
                        <thead class="table-light text-center align-middle">
                            <tr>
                                <th style="width: 4%">No</th>
                                <th style="width: 14%">CPL</th>
                                <th style="width: 18%">CPMK</th>
                                <th style="width: 16%">Metode</th>
                                <th style="width: 10%" class="text-center">Bobot Metode</th>
                                <th style="width: 25%">Kriteria Penilaian</th>
                                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                    <th style="width: 8%" class="text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penilaians as $i => $p)
                                @php
                                    $metodes = $p->penilaianMetode;
                                    $totalRows = max($metodes->sum(fn($pm) => max($pm->instrumens->count(), 1)), 1);
                                    $rowNum = ($penilaians->currentPage() - 1) * $penilaians->perPage() + $i + 1;
                                @endphp

                                @forelse ($metodes as $jm => $pm)
                                    @php
                                        $instrumens = $pm->instrumens;
                                        $instrumenRows = max($instrumens->count(), 1);
                                    @endphp

                                    @for ($ji = 0; $ji < $instrumenRows; $ji++)
                                        @php $pi = $instrumens->get($ji); @endphp
                                        <tr>
                                            {{-- CPL + CPMK (rowspan = totalRows) --}}
                                            @if ($jm === 0 && $ji === 0)
                                                <td class="text-center fw-bold align-top" rowspan="{{ $totalRows }}">{{ $rowNum }}</td>
                                                <td rowspan="{{ $totalRows }}" class="align-top">
                                                    <span class="badge bg-info text-dark d-block mb-1 text-wrap">{{ $p->cpl->kode ?? '-' }}</span>
                                                    <small class="text-muted d-block">{{ Str::limit($p->cpl->deskripsi ?? '', 70) }}</small>
                                                </td>
                                                <td rowspan="{{ $totalRows }}" class="align-top">
                                                    <span class="badge bg-success d-block mb-1 text-wrap">{{ $p->cpmk->kode ?? '-' }}</span>
                                                    <small class="text-muted d-block">{{ Str::limit($p->cpmk->deskripsi ?? '', 70) }}</small>
                                                </td>
                                            @endif

                                            {{-- Metode (rowspan = instrumenRows) --}}
                                            @if ($ji === 0)
                                                <td rowspan="{{ $instrumenRows }}" class="align-top">
                                                    <div class="fw-semibold text-dark">
                                                        <i class="mdi mdi-check-circle text-success me-1"></i>{{ $pm->metode->nama ?? 'Metode' }}
                                                    </div>
                                                </td>
                                                <td rowspan="{{ $instrumenRows }}" class="text-center align-top">
                                                    <!-- <span class="badge bg-primary px-2 py-1 fs-6"> -->
                                                        {{ number_format($pm->bobot, 2) }}
                                                    <!-- </span> -->
                                                </td>
                                            @endif

                                            {{-- Kriteria --}}
                                            @if ($pi)
                                                <td>{{ $pi->instrumenPenilaian->nama_kriteria ?? '-' }}</td>
                                            @else
                                                <td class="text-muted small text-center">—</td>
                                            @endif

                                            {{-- Aksi Per Paket Metode --}}
                                            @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                                @if ($ji === 0)
                                                    <td class="text-center align-middle" rowspan="{{ $instrumenRows }}">
                                                        <div class="d-inline-flex gap-1 justify-content-center">
                                                            <button type="button" class="btn btn-warning btn-sm p-1" style="font-size: 0.75rem; line-height: 1;" data-bs-toggle="modal" data-bs-target="#editMetodeModal{{ $pm->id }}" title="Edit Metode & Kriteria">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </button>

                                                            <form action="{{ url()->current() . '/metode/' . $pm->id }}" method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Hapus paket metode ini ({{ $pm->metode->nama ?? 'Metode' }}) beserta seluruh kriteria di dalamnya?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm p-1" style="font-size: 0.75rem; line-height: 1;" title="Hapus Paket Metode">
                                                                    <i class="mdi mdi-delete"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                @endif
                                            @endif
                                        </tr>
                                    @endfor

                                    {{-- ====== MODAL EDIT PAKET METODE (VERTIKAL CENTER TERKONTROL) ====== --}}
                                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                        <div class="modal fade" id="editMetodeModal{{ $pm->id }}" tabindex="-1" aria-labelledby="editMetodeModalLabel{{ $pm->id }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                            <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
                                                <div class="modal-content text-start border-0 shadow">
                                                    <form action="{{ url()->current() . '/metode/' . $pm->id }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header py-2 px-3 bg-light">
                                                            <h5 class="modal-title fs-6 fw-bold" id="editMetodeModalLabel{{ $pm->id }}">
                                                                <i class="mdi mdi-pencil-box text-warning me-1"></i> Edit Metode: {{ $pm->metode->nama ?? 'Metode' }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-3" style="max-height: 65vh; overflow-y: auto;">
                                                            {{-- Context CPL & CPMK --}}
                                                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-1 mb-md-0">
                                                                        <label class="fw-bold d-block small mb-0">CPL</label>
                                                                        <span class="badge bg-info text-dark me-1">{{ $p->cpl->kode ?? '-' }}</span>
                                                                        <span class="small text-muted">{{ $p->cpl->deskripsi ?? '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="fw-bold d-block small mb-0">CPMK</label>
                                                                        <span class="badge bg-success me-1">{{ $p->cpmk->kode ?? '-' }}</span>
                                                                        <span class="small text-muted">{{ $p->cpmk->deskripsi ?? '' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Bobot Metode Manual / Auto --}}
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold small mb-1">Bobot Metode dalam CPMK <span class="text-danger">*</span></label>
                                                                <div style="max-width: 170px;">
                                                                    <input type="number" step="0.01" min="0" max="100" 
                                                                           class="form-control form-control-sm pm-bobot-metode-input-{{ $pm->id }}" 
                                                                           name="bobot_metode" 
                                                                           value="{{ number_format((float)$pm->bobot, 2) }}" 
                                                                           placeholder="Bobot"
                                                                           required>
                                                                </div>
                                                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                                                    *Kosongkan bobot kriteria di bawah jika ingin membagi rata bobot metode ini secara otomatis ke seluruh kriteria.
                                                                </small>
                                                             </div>

                                                             {{-- Total Bobot Metode Live Indicator --}}
                                                             <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                                                 <label class="fw-bold mb-0 small">Pilih Kriteria Penilaian & Bobot Kriteria</label>
                                                             </div>

                                                             <input type="text" class="form-control form-control-sm mb-2 search-kriteria-modal" placeholder="Cari Kriteria...">
                                                             
                                                             <div class="border rounded p-2 bg-body" style="max-height: 240px; overflow-y: auto;">
                                                                 @foreach ($allKriterias as $k)
                                                                     @php
                                                                         $existing = $pm->instrumens->firstWhere('kriteria_id', $k->id);
                                                                         $isChecked = $existing ? true : false;
                                                                         $bobotValue = ($existing && $existing->bobot_metode !== null && $existing->bobot_metode > 0) ? $existing->bobot_metode : '';
                                                                     @endphp
                                                                     <div class="row align-items-center mb-2 p-2 border-bottom kriteria-modal-item">
                                                                         <div class="col-md-8 col-sm-7 d-flex align-items-center">
                                                                             <input class="form-check-input kriteria-modal-check me-2" 
                                                                                    type="checkbox" 
                                                                                    name="kriteria_penilaian[]" 
                                                                                    value="{{ $k->id }}" 
                                                                                    id="kriteria_pm_{{ $pm->id }}_{{ $k->id }}"
                                                                                    data-pm-id="{{ $pm->id }}"
                                                                                    {{ $isChecked ? 'checked' : '' }}>
                                                                             <label class="form-check-label text-dark fw-semibold mb-0 small" for="kriteria_pm_{{ $pm->id }}_{{ $k->id }}">
                                                                                 {{ $k->nama_kriteria }}
                                                                             </label>
                                                                         </div>
                                                                         <div class="col-md-4 col-sm-5">
                                                                             <input type="number" step="0.01" min="0" max="100"
                                                                                    class="form-control form-control-sm kriteria-modal-bobot pm-bobot-input-{{ $pm->id }}"
                                                                                    name="bobot_kriteria[{{ $k->id }}]"
                                                                                    id="bobot_pm_{{ $pm->id }}_{{ $k->id }}"
                                                                                    data-pm-id="{{ $pm->id }}"
                                                                                    value="{{ $bobotValue }}"
                                                                                    placeholder="Bobot"
                                                                                    {{ $isChecked ? '' : 'disabled' }}>
                                                                         </div>
                                                                     </div>
                                                                 @endforeach
                                                             </div>
                                                         </div>
                                                         <div class="modal-footer py-2 px-3 bg-light border-top">
                                                             <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                             <button type="submit" class="btn btn-primary btn-sm px-3"><i class="mdi mdi-content-save me-1"></i>Simpan Perubahan</button>
                                                         </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td class="text-center" rowspan="1">{{ $rowNum }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $p->cpl->kode ?? '-' }}</span></td>
                                        <td><span class="badge bg-success">{{ $p->cpmk->kode ?? '-' }}</span></td>
                                        <td colspan="3" class="text-muted text-center">Belum ada metode & kriteria penilaian</td>
                                        @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                            <td class="text-center">
                                                <form action="{{ url()->current() . '/' . $p->id }}" method="POST"
                                                      onsubmit="return confirm('Hapus asesmen ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm px-2 py-1">
                                                        <i class="mdi mdi-delete me-1"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforelse
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- ====== FOOTER PAGINATION ====== --}}
            @if ($penilaians->hasPages())
                <div class="card-footer bg-white border-top py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Menampilkan {{ $penilaians->firstItem() ?? 0 }} - {{ $penilaians->lastItem() ?? 0 }} dari {{ $penilaians->total() }} asesmen
                    </div>
                    <div>
                        {{ $penilaians->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
        @endif

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Enable/disable input bobot saat checkbox kriteria di-check/uncheck di modal edit
    $(document).on('change', '.kriteria-modal-check', function() {
        let isChecked = $(this).is(':checked');
        let $row = $(this).closest('.kriteria-modal-item');
        let $bobotInput = $row.find('.kriteria-modal-bobot');
        
        $bobotInput.prop('disabled', !isChecked);
        if (!isChecked) {
            $bobotInput.val('');
        }
    });

    // Search Kriteria di dalam modal edit
    $(document).on('input', '.search-kriteria-modal', function() {
        let val = $(this).val().toLowerCase();
        let $modalBody = $(this).closest('.modal-body');
        $modalBody.find('.kriteria-modal-item').each(function() {
            let text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(val));
        });
    });
});
</script>
@endsection
