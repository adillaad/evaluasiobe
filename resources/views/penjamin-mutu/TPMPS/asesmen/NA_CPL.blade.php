@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
<div class="container-fluid">
  <div class="card">
      <div class="card-body">
          <h4 class="card-title mb-3">Nilai Akhir CPL</h4>

          <x-filter-form
              :universities="$universities ?? collect()"
              :faculties="$faculties ?? collect()"
              :programs="$programs ?? collect()"
              :kurikulums="$kurikulums ?? collect()"
              :showKurikulum="true"
          />

          @php
              $groupedCPLs = $penilaian->groupBy('cpl_kode');
          @endphp

          @if($groupedCPLs->isNotEmpty())
              <style>
                  #cplTab .nav-link {
                      border: 1px solid transparent;
                      border-bottom: 1px solid #dee2e6;
                      color: #0d6efd;
                      background-color: transparent;
                      border-top-left-radius: 0.375rem;
                      border-top-right-radius: 0.375rem;
                      font-weight: 500;
                      padding: 0.5rem 1rem;
                  }
                  #cplTab .nav-link:hover {
                      border-color: #e9ecef #e9ecef #dee2e6;
                      color: #0b5ed7;
                  }
                  #cplTab .nav-link.active {
                      color: #212529;
                      background-color: #fff;
                      border-color: #dee2e6 #dee2e6 #fff;
                      font-weight: 600;
                  }
              </style>
              {{-- Navigation Tabs Per CPL --}}
              <ul class="nav nav-tabs mb-0 border-bottom-0" id="cplTab" role="tablist">
                  @foreach ($groupedCPLs as $cpl_kode => $rows)
                      @if ($instrumens->whereIn('id', $rows->pluck('id'))->isNotEmpty())
                          <li class="nav-item" role="presentation">
                              <button 
                                  class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                  id="tab-{{ Str::slug($cpl_kode) }}" 
                                  data-bs-toggle="tab" 
                                  data-bs-target="#content-{{ Str::slug($cpl_kode) }}" 
                                  type="button" 
                                  role="tab" 
                                  aria-controls="content-{{ Str::slug($cpl_kode) }}" 
                                  aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                  {{ $cpl_kode }}
                              </button>
                          </li>
                      @endif
                  @endforeach
              </ul>

              {{-- Tab Content Per CPL --}}
              <div class="tab-content" id="cplTabContent">
                  @foreach ($groupedCPLs as $cpl_kode => $rows)
                      @if ($instrumens->whereIn('id', $rows->pluck('id'))->isNotEmpty())
                          @php
                              $totalBobot = $instrumens->whereIn('id', $rows->pluck('id'))->sum('bobot_metode');
                          @endphp
                          <div 
                              class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                              id="content-{{ Str::slug($cpl_kode) }}" 
                              role="tabpanel" 
                              aria-labelledby="tab-{{ Str::slug($cpl_kode) }}">
                              
                              <div class="table-responsive">
                                  <table class="table table-hover table-bordered">
                                      <thead class="bg-light">
                                          <tr>
                                              <th>CPMK</th>
                                              <th>MK</th>
                                              <th>Skor Max</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          @foreach ($rows as $row)
                                              @php
                                                  $bobotPerRow = $instrumens->where('id', $row->id)->sum('bobot_metode');        
                                              @endphp
                                              <tr>
                                                  <td>{{ $row->cpmk_kode }}</td>
                                                  <td>{{ $row->mk_kode }}</td>
                                                  <td>{{ $bobotPerRow }}</td>
                                              </tr>
                                          @endforeach
                                      </tbody>
                                      <tfoot class="bg-light font-weight-bold">
                                          <tr>
                                              <td colspan="2" class="text-end fw-bold">Nilai {{ $cpl_kode }} :</td>
                                              <td class="fw-bold">{{ $totalBobot }}</td>
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



{{-- <div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <div id="NA_CPL"></div>
        </div>
    </div>
</div> --}}

<!-- NA_CPL -->
{{-- <script>
    Highcharts.chart('NA_CPL', {
      chart: {
        type: 'column'
      },

      title: {
        text: 'Nilai Akhir CPL',
        align: 'center'
      },

      xAxis: {
        categories: ['CPL05', 'CPL06', 'CPL07']
      },

      yAxis: {
        allowDecimals: false,
        min: 0,
        title: {
          text: 'Nilai Akhir CPL'
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
          id: 'MK08-CPMK051-CPL05',
          name: 'CPMK051',
          data: [11, 0, 0],
          color: '#FF9999'
        },
        {
          id: 'MK08-CPMK052-CPL05',
          name: 'CPMK052',
          data: [11, 0, 0],
          color: '#FF7777'
        },
        {
          id: 'MK09-CPMK051-CPL05',
          name: 'CPMK051',
          data: [10, 0, 0],
          color: '#FF5555'
        },
        {
          id: 'MK24-CPMK051-CPL05',
          name: 'CPMK051',
          data: [15, 0, 0],
          color: '#FFDD77'
        },
        {
          id: 'MK24-CPMK052-CPL05',
          name: 'CPMK052',
          data: [15, 0, 0],
          color: '#FFBB44'
        },
        {
          id: 'MK04-CPMK061-CPL06',
          name: 'CPMK061',
          data: [0, 15, 0],
          color: '#77DD77'
        },
        {
          id: 'MK04-CPMK062-CPL06',
          name: 'CPMK062',
          data: [0, 15, 0],
          color: '#55AA55'
        },
        {
          id: 'MK04-CPMK063-CPL06',
          name: 'CPMK063',
          data: [0, 20, 0],
          color: '#339933'
        },
        {
          id: 'MK34-CPMK063-CPL06',
          name: 'CPMK063',
          data: [0, 30, 0],
          color: '#227722'
        },
        {
          id: 'MK34-CPMK071-CPL07',
          name: 'CPMK071',
          data: [0, 0, 40],
          color: '#99DD99'
        },
        {
          id: 'MK34-CPMK071-CPL07',
          name: 'CPMK072',
          data: [0, 0, 30],
          color: '#77BB77'
        }
      ]
    });
  </script> --}}

@endsection