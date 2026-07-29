@extends(auth()->user()->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
@if (session()->has('failed'))
    <div class="alert alert-danger" role="alert" id="box">
        <div>{{session('failed')}}</div>
    </div>
@elseif (session()->has('success'))
    <div class="alert greenAdd" role="alert" id="box">
        <div>{{session('success')}}</div>
    </div>
@endif

<h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL-MK-CPMK</h3>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div id="CPL-MK-CPMK"></div>
        </div>
    </div>
</div>

<script>
    const data = [
      {id: 'CPL', parent: '', name: 'CPL-MK-CPMK'},

      // CPL01
      {id: 'CPL01',parent: 'CPL',name: 'CPL01'},

      { id: 'MK01-CPL01', parent: 'CPL01', name: 'MK01' },
      { id: 'MK02-CPL01', parent: 'CPL01', name: 'MK02' },
      { id: 'MK15-CPL01', parent: 'CPL01', name: 'MK15' },
      { id: 'MK16-CPL01', parent: 'CPL01', name: 'MK16' },
      { id: 'MK17-CPL01', parent: 'CPL01', name: 'MK17' },
      { id: 'MK18-CPL01', parent: 'CPL01', name: 'MK18' },

      { id: 'CPMK011-MK01-CPL01', parent: 'MK01-CPL01', name: 'CPMK011', value:1 },
      { id: 'CPMK012-MK01-CPL01', parent: 'MK01-CPL01', name: 'CPMK012', value:1 },
      { id: 'CPMK013-MK02-CPL01', parent: 'MK02-CPL01', name: 'CPMK013', value:1 },
      { id: 'CPMK011-MK15-CPL01', parent: 'MK15-CPL01', name: 'CPMK011', value:1 },
      { id: 'CPMK012-MK16-CPL01', parent: 'MK16-CPL01', name: 'CPMK012', value:1 },
      { id: 'CPMK013-MK16-CPL01', parent: 'MK16-CPL01', name: 'CPMK013', value:1 },
      { id: 'CPMK013-MK17-CPL01', parent: 'MK17-CPL01', name: 'CPMK013', value:1 },
      { id: 'CPMK013-MK18-CPL01', parent: 'MK18-CPL01', name: 'CPMK013', value:1 },
      
      // CPL06
      { id: 'CPL06',parent: 'CPL',name: 'CPL06'},

      { id: 'MK01-CPL06', parent: 'CPL06', name: 'MK01' },
      { id: 'MK02-CPL06', parent: 'CPL06', name: 'MK02' },
      { id: 'MK03-CPL06', parent: 'CPL06', name: 'MK03' },
      { id: 'MK04-CPL06', parent: 'CPL06', name: 'MK04' },
      { id: 'MK18-CPL06', parent: 'CPL06', name: 'MK18' },
      { id: 'MK32-CPL06', parent: 'CPL06', name: 'MK32' },
      { id: 'MK34-CPL06', parent: 'CPL06', name: 'MK34' },

      { id: 'CPMK061-MK01-CPL06', parent: 'MK01-CPL06', name: 'CPMK061', value:1 },
      { id: 'CPMK061-MK02-CPL06', parent: 'MK02-CPL06', name: 'CPMK061', value:1 },
      { id: 'CPMK061-MK03-CPL06', parent: 'MK03-CPL06', name: 'CPMK061', value:1 },
      { id: 'CPMK061-MK04-CPL06', parent: 'MK04-CPL06', name: 'CPMK061', value:1 },
      { id: 'CPMK062-MK04-CPL06', parent: 'MK04-CPL06', name: 'CPMK062', value:1 },
      { id: 'CPMK063-MK04-CPL06', parent: 'MK04-CPL06', name: 'CPMK063', value:1 },
      { id: 'CPMK063-MK18-CPL06', parent: 'MK18-CPL06', name: 'CPMK063', value:1 },
      { id: 'CPMK061-MK32-CPL06', parent: 'MK32-CPL06', name: 'CPMK061', value:1 },
      { id: 'CPMK062-MK32-CPL06', parent: 'MK32-CPL06', name: 'CPMK062', value:1 },
      { id: 'CPMK063-MK32-CPL06', parent: 'MK32-CPL06', name: 'CPMK063', value:1 },
      { id: 'CPMK063-MK34-CPL06', parent: 'MK34-CPL06', name: 'CPMK063', value:1 },
      
    ]

    Highcharts.chart('CPL-MK-CPMK', {

      chart: {
        height: '50%'
      },

      // Let the center circle be transparent
      colors: ['transparent'].concat(Highcharts.getOptions().colors),

      title: {
        text: 'CPL-MK-CPMK'
      },

      series: [{
        type: 'sunburst',
        data: data,
        name: 'Root',
        allowTraversingTree: true,
        borderRadius: 3,
        cursor: 'pointer',
        dataLabels: {
          format: '{point.name}',
          filter: {
            property: 'innerArcLength',
            operator: '>',
            value: 16
          }
        },
        levels: [{
          level: 1,
          levelIsConstant: false,
          dataLabels: {
            filter: {
              property: 'outerArcLength',
              operator: '>',
              value: 64
            }
          }
        }, {
          level: 2,
          colorByPoint: true
        },
        {
          level: 3,
          colorVariation: {
            key: 'brightness',
            to: -0.5
          }
        }, {
          level: 4,
          colorVariation: {
            key: 'brightness',
            to: 0.5
          }
        }]

      }],

      tooltip: {
        headerFormat: '',
        pointFormat: '<b>{point.id}</b>'
      }
    });
  </script>

@endsection