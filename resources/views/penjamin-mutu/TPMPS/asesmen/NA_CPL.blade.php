@php
    $selectedProdiId = request('prodi_id');
    if (!$selectedProdiId && request('kurikulum_id')) {
        $selectedProdiId = \Illuminate\Support\Facades\DB::table('kurikulums')->where('id', request('kurikulum_id'))->value('id_prodi');
    }
    if (!$selectedProdiId && auth()->check()) {
        $selectedProdiId = auth()->user()->id_prodiUser ?? (auth()->user()->prodi ? auth()->user()->prodi->id : null);
    }
    if ($selectedProdiId) {
        $isAptikom = (bool) \Illuminate\Support\Facades\DB::table('prodi')->where('id', $selectedProdiId)->value('is_aptikom');
    } else {
        $isAptikom = auth()->check() && auth()->user()->prodi ? (bool) auth()->user()->prodi->is_aptikom : true;
    }
@endphp

@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

<style>
    @if ($isAptikom)
        /* ── APTIKOM Theme (Ocean Blue #006199 Accent) ── */
        .cpl-nav-pills .nav-link.active {
            background: linear-gradient(135deg, #006199 0%, #004c78 100%) !important;
            color: #ffffff !important;
            border-color: #006199 !important;
            box-shadow: 0 4px 12px rgba(0, 97, 153, 0.28) !important;
        }
        .text-primary-accent {
            color: #006199 !important;
        }
    @else
        /* ── NON-APTIKOM Theme (Sky Blue #76C0EC Accent) ── */
        .cpl-nav-pills .nav-link.active {
            background: linear-gradient(135deg, #76C0EC 0%, #5bb0e5 100%) !important;
            color: #ffffff !important;
            border-color: #76C0EC !important;
            box-shadow: 0 4px 12px rgba(118, 192, 236, 0.35) !important;
        }
        .text-primary-accent {
            color: #76C0EC !important;
        }
    @endif
    .cpl-tabs-wrapper {
        background: #ffffff;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
    }
    .cpl-nav-pills {
        gap: 8px;
    }
    .cpl-nav-pills .nav-link {
        color: #475569 !important;
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 8px 14px !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        white-space: nowrap !important;
    }
    .cpl-nav-pills .nav-link:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
    .cpl-nav-pills .nav-link.active .badge-cpmk-count {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }
    .badge-cpmk-count {
        background-color: #e2e8f0;
        color: #475569;
        font-size: 11px;
        border-radius: 6px;
        padding: 2px 6px;
        margin-left: 6px;
        font-weight: 700;
    }
    .table-pemetaan {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    .table-pemetaan th {
        background-color: #f8fafc !important;
        color: #334155 !important;
        font-weight: 600 !important;
        font-size: 13.5px !important;
        padding: 12px 14px !important;
        vertical-align: middle !important;
        border-bottom: 2px solid #cbd5e1 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table-pemetaan td {
        vertical-align: middle !important;
        line-height: 1.6 !important;
        padding: 12px 14px !important;
        font-size: 13.5px !important;
        color: #1e293b !important;
        white-space: normal !important;
        word-break: break-word !important;
    }
    .table-pemetaan tbody tr:hover {
        background-color: #f8fafc !important;
    }
</style>

<div class="container-fluid mb-4">
  <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <h5 class="card-title fw-bold mb-0 text-dark">Nilai Akhir CPL</h5>
      </div>
      <div class="card-body">
          <x-filter-form
              :universities="$universities ?? collect()"
              :faculties="$faculties ?? collect()"
              :programs="$programs ?? collect()"
              :kurikulums="$kurikulums ?? collect()"
              :showKurikulum="true"
          />

          @php
              $groupedCPLs = $penilaian->groupBy('cpl_kode')->sortKeys(SORT_NATURAL | SORT_FLAG_CASE);
              $totalValidPenilaianCount = 0;
              foreach ($groupedCPLs as $cpl_kode => $rows) {
                  if ($instrumens->whereIn('id', $rows->pluck('id'))->isNotEmpty()) {
                      $totalValidPenilaianCount += $rows->count();
                  }
              }
          @endphp

          @if($groupedCPLs->isNotEmpty() && $totalValidPenilaianCount > 0)
              {{-- Tab Filter Per CPL --}}
              <div class="cpl-tabs-wrapper mb-3 mt-3">
                  <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                      <span class="fw-bold text-secondary text-uppercase small" style="letter-spacing: 0.5px;">
                          <i class="mdi mdi-filter-variant me-1"></i> Pilih CPL:
                      </span>
                      <span class="text-muted small">Klik tab CPL untuk melihat nilai akhir</span>
                  </div>
                  <ul class="nav nav-pills cpl-nav-pills overflow-auto flex-nowrap pb-1" id="cplTab" role="tablist">
                      <li class="nav-item" role="presentation">
                          <button
                              class="nav-link active"
                              id="tab-semua-cpl"
                              data-bs-toggle="tab"
                              data-bs-target="#content-semua-cpl"
                              type="button"
                              role="tab"
                              aria-controls="content-semua-cpl"
                              aria-selected="true">
                              Semua CPL
                              <span class="badge-cpmk-count">{{ $totalValidPenilaianCount }}</span>
                          </button>
                      </li>
                      @foreach ($groupedCPLs as $cpl_kode => $rows)
                          @if ($instrumens->whereIn('id', $rows->pluck('id'))->isNotEmpty())
                              <li class="nav-item" role="presentation">
                                  <button
                                      class="nav-link"
                                      id="tab-{{ Str::slug($cpl_kode) }}"
                                      data-bs-toggle="tab"
                                      data-bs-target="#content-{{ Str::slug($cpl_kode) }}"
                                      type="button"
                                      role="tab"
                                      aria-controls="content-{{ Str::slug($cpl_kode) }}"
                                      aria-selected="false">
                                      {{ $cpl_kode }}
                                      <span class="badge-cpmk-count">{{ $rows->count() }}</span>
                                  </button>
                              </li>
                          @endif
                      @endforeach
                  </ul>
              </div>

              {{-- Tab Content Per CPL --}}
              <div class="tab-content" id="cplTabContent">
                  {{-- Tab Pane: Semua CPL --}}
                  <div
                      class="tab-pane fade show active"
                      id="content-semua-cpl"
                      role="tabpanel"
                      aria-labelledby="tab-semua-cpl">

                      <div class="table-responsive">
                          <table class="table table-bordered table-hover align-middle table-pemetaan mb-0">
                              <thead>
                                  <tr>
                                      <th style="width: 15%;">CPL</th>
                                      <th>CPMK</th>
                                      <th>MK</th>
                                      <th style="width: 15%;">Skor Max</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach ($groupedCPLs as $cpl_kode => $rows)
                                      @php
                                          $validRows = $rows->filter(function($row) use ($instrumens) {
                                              return $instrumens->where('id', $row->id)->isNotEmpty();
                                          });
                                      @endphp
                                      @if ($validRows->isNotEmpty())
                                          @php
                                              $cplRowspan = $validRows->count();
                                              $cplTotalBobot = $instrumens->whereIn('id', $validRows->pluck('id'))->sum('bobot_metode');
                                              $isFirstRow = true;
                                          @endphp
                                          @foreach ($validRows as $row)
                                              @php
                                                  $bobotPerRow = $instrumens->where('id', $row->id)->sum('bobot_metode');
                                              @endphp
                                              <tr>
                                                  @if ($isFirstRow)
                                                      <td rowspan="{{ $cplRowspan }}" class="fw-bold align-middle bg-light text-center">
                                                          {{ $cpl_kode }}
                                                      </td>
                                                      @php $isFirstRow = false; @endphp
                                                  @endif
                                                  <td class="fw-semibold">{{ $row->cpmk_kode }}</td>
                                                  <td>{{ $row->mk_kode }}</td>
                                                  <td>{{ $bobotPerRow }}</td>
                                              </tr>
                                          @endforeach
                                          <tr class="table-light">
                                              <td colspan="3" class="text-end fw-bold">Nilai {{ $cpl_kode }} :</td>
                                              <td class="fw-bold text-primary-accent">{{ $cplTotalBobot }}</td>
                                          </tr>
                                      @endif
                                  @endforeach
                              </tbody>
                          </table>
                      </div>
                  </div>

                  {{-- Tab Pane: Per CPL --}}
                  @foreach ($groupedCPLs as $cpl_kode => $rows)
                      @if ($instrumens->whereIn('id', $rows->pluck('id'))->isNotEmpty())
                          @php
                              $totalBobot = $instrumens->whereIn('id', $rows->pluck('id'))->sum('bobot_metode');
                          @endphp
                          <div
                              class="tab-pane fade"
                              id="content-{{ Str::slug($cpl_kode) }}"
                              role="tabpanel"
                              aria-labelledby="tab-{{ Str::slug($cpl_kode) }}">

                              <div class="table-responsive">
                                  <table class="table table-bordered table-hover align-middle table-pemetaan mb-0">
                                      <thead>
                                          <tr>
                                              <th>CPMK</th>
                                              <th>MK</th>
                                              <th style="width: 15%;">Skor Max</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          @foreach ($rows as $row)
                                              @php
                                                  $bobotPerRow = $instrumens->where('id', $row->id)->sum('bobot_metode');
                                              @endphp
                                              <tr>
                                                  <td class="fw-semibold">{{ $row->cpmk_kode }}</td>
                                                  <td>{{ $row->mk_kode }}</td>
                                                  <td>{{ $bobotPerRow }}</td>
                                              </tr>
                                          @endforeach
                                      </tbody>
                                      <tfoot class="bg-light">
                                          <tr>
                                              <td colspan="2" class="text-end fw-bold">Nilai {{ $cpl_kode }} :</td>
                                              <td class="fw-bold text-primary-accent">{{ $totalBobot }}</td>
                                          </tr>
                                      </tfoot>
                                  </table>
                              </div>
                          </div>
                      @endif
                  @endforeach
              </div>
          @else
              <div class="alert alert-info text-center mt-3" role="alert">
                  Data Nilai Akhir CPL tidak ditemukan. Silakan sesuaikan filter yang dipilih.
              </div>
          @endif
      </div>
  </div>
</div>
@endsection