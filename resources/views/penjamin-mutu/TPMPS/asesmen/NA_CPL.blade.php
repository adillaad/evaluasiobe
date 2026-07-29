@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
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

<h3 class="px-4 pb-4 fw-bold text-center">Halaman Nilai Akhir CPL</h3>
<div class="container-fluid">
  <div class="card">
      <div class="card-body">
          <h4 class="card-title">Nilai Akhir CPL</h4>
          <div class="table-responsive">
              <table class="table table-hover">
                  <thead class="bg-light">
                      <tr>
                          <th>CPL</th>
                          <th>CPMK</th>
                          <th>MK</th>
                          <th>Skor Max</th>
                      </tr>
                  </thead>
                  <tbody>
                      @php
                          $groupedCPLs = $penilaian->groupBy('cpl_kode');
                      @endphp
                      @foreach ($groupedCPLs as $cpl_kode => $rows)
                        @if ($instrumens->whereIn('id', $rows->pluck('id'))->isNotEmpty())
                          @php
                            $rowspan = $rows->count();
                            $totalBobot = $instrumens->whereIn('id', $rows->pluck('id'))->sum('bobot_metode');
                          @endphp
                          <tr>
                              <td rowspan="{{ $rowspan }}">{{ $cpl_kode }}</td>
                              @foreach ($rows as $index => $row)
                                  @php
                                    $bobotPerRow = $instrumens->where('id',$row->id)->sum('bobot_metode');        
                                  @endphp
                                  @if ($index > 0)
                                      <tr>
                                  @endif
                                  <td >{{ $row->cpmk_kode }}</td>
                                  <td>{{ $row->mk_kode }}</td>
                                  <td>{{ $bobotPerRow }}</td>
                                  @if ($index < $rows->count() - 1)
                                      </tr>
                                  @endif
                              @endforeach
                              <tr>
                                <td colspan="3">Nilai {{ $cpl_kode }} : </td>
                                <td>{{ $totalBobot }}</td>
                              </tr>
                          </tr>
                        @endif
                      @endforeach
                  </tbody>
              </table>
          </div>
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