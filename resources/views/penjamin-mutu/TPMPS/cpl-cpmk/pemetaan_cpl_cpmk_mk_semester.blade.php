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

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div id="CPL-CPMK-MK-Semester"></div>
        </div>
    </div>
</div>

<script>
    const data = [
      {id: 'CPL', parent: '', name: 'CPL-CPMK-MK-Semester'},

      // CPL01
      {id: 'CPL01',parent: 'CPL',name: 'CPL01'},

      { id: 'CPMK011-CPL01', parent: 'CPL01', name: 'CPMK011' },
      { id: 'CPMK012-CPL01', parent: 'CPL01', name: 'CPMK012' },
      { id: 'CPMK013-CPL01', parent: 'CPL01', name: 'CPMK013' },

      { id: 'MK15-CPMK011-CPL01', parent: 'CPMK011-CPL01', name: 'MK15', value:1 },
      { id: 'MK16-CPMK012-CPL01', parent: 'CPMK012-CPL01', name: 'MK01', value:1 },
      { id: 'MK17-CPMK012-CPL01', parent: 'CPMK012-CPL01', name: 'MK17', value:1 },
      { id: 'MK16-CPMK013-CPL01', parent: 'CPMK013-CPL01', name: 'MK16', value:1 },
      { id: 'MK17-CPMK013-CPL01', parent: 'CPMK013-CPL01', name: 'MK17', value:1 },

      // CPL06
      {id: 'CPL06',parent: 'CPL',name: 'CPL06'},

      { id: 'CPMK061-CPL06', parent: 'CPL06', name: 'CPMK061' },
      { id: 'CPMK062-CPL06', parent: 'CPL06', name: 'CPMK062' },
      { id: 'CPMK063-CPL06', parent: 'CPL06', name: 'CPMK063' },
      
      { id: 'MK03-CPMK061-CPL06', parent: 'CPMK061-CPL06', name: 'MK03', value:1 },
      { id: 'MK01-CPMK061-CPL06', parent: 'CPMK061-CPL06', name: 'MK01', value:1 },
      { id: 'MK04-CPMK061-CPL06', parent: 'CPMK061-CPL06', name: 'MK04', value:1 },
      { id: 'MK02-CPMK061-CPL06', parent: 'CPMK061-CPL06', name: 'MK02', value:1 },
      { id: 'MK32-CPMK061-CPL06', parent: 'CPMK061-CPL06', name: 'MK32', value:1 },
      { id: 'MK04-CPMK062-CPL06', parent: 'CPMK062-CPL06', name: 'MK04', value:1 },
      { id: 'MK32-CPMK062-CPL06', parent: 'CPMK062-CPL06', name: 'MK32', value:1 },
      { id: 'MK18-CPMK063-CPL06', parent: 'CPMK063-CPL06', name: 'MK18', value:1 },
      { id: 'MK04-CPMK063-CPL06', parent: 'CPMK063-CPL06', name: 'MK04', value:1 },
      { id: 'MK32-CPMK063-CPL06', parent: 'CPMK063-CPL06', name: 'MK32', value:1 },
      { id: 'MK34-CPMK063-CPL06', parent: 'CPMK063-CPL06', name: 'MK34', value:1 },

    ]

    Highcharts.chart('CPL-CPMK-MK-Semester', {
      chart: {
        height: '50%'
      },

      // Let the center circle be transparent
      colors: ['transparent'].concat(Highcharts.getOptions().colors),

      title: {
        text: 'CPL-CPMK-MK-Semester'
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