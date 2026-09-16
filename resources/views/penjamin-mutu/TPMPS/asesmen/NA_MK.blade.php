@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

<div class="container-fluid">
  <div class="card">
      <div class="card-body">
          <h4 class="card-title mb-3">Nilai Akhir MK</h4>

          <x-filter-form
              :universities="$universities ?? collect()"
              :faculties="$faculties ?? collect()"
              :programs="$programs ?? collect()"
              :kurikulums="$kurikulums ?? collect()"
              :showKurikulum="true"
          />

          {{-- Control bar: Search & Entries per Page --}}
          <form method="GET" action="{{ route(Request::route()->getName()) }}" class="row g-2 mb-3 align-items-center">
              {{-- Preserve existing filter values --}}
              @if(request('universitas_id')) <input type="hidden" name="universitas_id" value="{{ request('universitas_id') }}"> @endif
              @if(request('fakultas_id')) <input type="hidden" name="fakultas_id" value="{{ request('fakultas_id') }}"> @endif
              @if(request('prodi_id')) <input type="hidden" name="prodi_id" value="{{ request('prodi_id') }}"> @endif
              @if(request('kurikulum_id')) <input type="hidden" name="kurikulum_id" value="{{ request('kurikulum_id') }}"> @endif

              <div class="col-auto d-flex align-items-center gap-2">
                  <label for="per_page" class="col-form-label text-muted small mb-0 text-nowrap">Tampilkan:</label>
                  <select name="per_page" id="per_page" class="form-select form-select-sm" style="width: auto; padding-right: 1.8rem; height: 31px;" onchange="this.form.submit()">
                      <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                      <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                      <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                      <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                  </select>
                  <span class="text-muted small text-nowrap">data/halaman</span>
              </div>

              <div class="col-md-4 ms-auto">
                  <div class="input-group input-group-sm">
                      <input type="text" name="search" class="form-control" placeholder="Cari Kode atau Nama MK..." value="{{ $search }}">
                      <button class="btn btn-outline-secondary" type="submit">
                          <i class="mdi mdi-magnify"></i> Cari
                      </button>
                      @if(!empty($search))
                          <a href="{{ route(Request::route()->getName(), request()->except('search', 'page')) }}" class="btn btn-outline-danger" title="Reset Search">
                              <i class="mdi mdi-close"></i>
                          </a>
                      @endif
                  </div>
              </div>
          </form>

          @php
              $groupedMKs = $penilaian->groupBy('mk_kode');
          @endphp

          @if($paginatedMKs->isNotEmpty())
              <div class="table-responsive">
                  <table class="table table-hover table-bordered align-middle">
                      <thead class="bg-light">
                          <tr>
                              <th style="width: 25%;">MK (Mata Kuliah)</th>
                              <th>CPL</th>
                              <th>CPMK</th>
                              <th style="width: 15%;">Skor Max</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($paginatedMKs as $mkItem)
                              @php
                                  $mk_kode = $mkItem->mk_kode;
                                  $rows = $groupedMKs->get($mk_kode, collect());
                                  $validInstrumens = $rows->isNotEmpty() ? $instrumens->whereIn('id', $rows->pluck('id')) : collect();
                              @endphp

                              @if ($rows->isNotEmpty() && $validInstrumens->isNotEmpty())
                                  @php
                                      $rowspan = $rows->count();
                                      $totalBobot = $instrumens->whereIn('id', $rows->pluck('id'))->sum('bobot_metode');
                                  @endphp
                                  <tr>
                                      <td rowspan="{{ $rowspan }}">
                                          <strong>{{ $mk_kode }}</strong>
                                          @if(!empty($mkItem->mk_nama))
                                              <br><small class="text-muted">{{ $mkItem->mk_nama }}</small>
                                          @endif
                                      </td>
                                      @foreach ($rows as $index => $row)
                                          @php
                                              $bobotPerRow = $instrumens->where('id', $row->id)->sum('bobot_metode');        
                                          @endphp
                                          @if ($index > 0)
                                              <tr>
                                          @endif
                                          <td>{{ $row->cpl_kode }}</td>
                                          <td>{{ $row->cpmk_kode }}</td>
                                          <td>{{ $bobotPerRow }}</td>
                                          @if ($index < $rows->count() - 1)
                                              </tr>
                                          @endif
                                      @endforeach
                                  </tr>
                                  <tr class="table-light">
                                      <td colspan="3" class="text-end fw-bold">Nilai {{ $mk_kode }} :</td>
                                      <td class="fw-bold text-primary">{{ $totalBobot }}</td>
                                  </tr>
                              @else
                                  <tr>
                                      <td>
                                          <strong>{{ $mk_kode }}</strong>
                                          @if(!empty($mkItem->mk_nama))
                                              <br><small class="text-muted">{{ $mkItem->mk_nama }}</small>
                                          @endif
                                      </td>
                                      <td colspan="3" class="text-muted italic">Belum ada data asesmen/instrumen</td>
                                  </tr>
                              @endif
                          @endforeach
                      </tbody>
                  </table>
              </div>

              {{-- Pagination Footer & Info --}}
              <div class="d-flex flex-wrap justify-content-between align-items-center mt-3">
                  <div class="text-muted small mb-2 mb-md-0">
                      Menampilkan {{ $paginatedMKs->firstItem() ?? 0 }} sampai {{ $paginatedMKs->lastItem() ?? 0 }} dari total {{ $paginatedMKs->total() }} data
                  </div>
                  <div>
                      {{ $paginatedMKs->links() }}
                  </div>
              </div>
          @else
              <div class="alert alert-info text-center mt-3" role="alert">
                  Data Nilai Akhir MK tidak ditemukan. Silakan sesuaikan pencarian atau filter yang dipilih.
              </div>
          @endif
      </div>
  </div>
</div>



{{-- <div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <div id="NA_MK"></div>
        </div>
    </div>
</div>

<!-- NA_MK -->
<script>
    Highcharts.chart('NA_MK', {
      chart: {
        type: 'column'
      },

      title: {
        text: 'Nilai Akhir MK',
        align: 'center'
      },

      xAxis: {
        categories: ['MK04', 'MK05', 'MK06']
      },

      yAxis: {
        allowDecimals: false,
        min: 0,
        title: {
          text: 'Nilai Akhir MK'
        }
      },

      tooltip: {
        format: '<b>{key}</b><br/>{series.name}: {y}<br/>' +
          'Total: {point.stackTotal}'
      },

      plotOptions: {
        column: {
          stacking: 'normal'
        }
      },

      series: [
        {
          name: 'CPMK061',
          data: [15, 0, 0],
          color: '#FF9999'
        },
        {
          name: 'CPMK062',
          data: [15, 0, 0],
          color: '#FF7777'
        },
        {
          name: 'CPMK063',
          data: [20, 30, 0],
          color: '#FF5555'
        },
        {
          name: 'CPMK081',
          data: [15, 0, 20],
          color: '#FFDD77'
        },
        {
          name: 'CPMK082',
          data: [15, 0, 30],
          color: '#FFBB44'
        },
        {
          name: 'CPMK083',
          data: [20, 0, 20],
          color: '#FFAA33'
        },
        {
          name: 'CPMK084',
          data: [0, 0, 30],
          color: '#FFAA33'
        },
        {
          name: 'CPMK071',
          data: [0, 40, 0],
          color: '#77DD77'
        },
        {
          name: 'CPMK072',
          data: [0, 30, 0],
          color: '#55AA55'
        }
      ]
    });
  </script> --}}

@endsection