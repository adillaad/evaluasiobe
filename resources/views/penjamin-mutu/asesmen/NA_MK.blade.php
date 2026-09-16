@extends(auth()->user()->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
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

<div class="container-fluid">
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
  </script>

@endsection