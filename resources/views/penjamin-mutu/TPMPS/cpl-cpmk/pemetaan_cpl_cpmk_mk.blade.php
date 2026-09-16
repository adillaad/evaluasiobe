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
            <div id="CPL-CPMK-MK"></div>
        </div>
    </div>
</div>

<script>
    const data = [
      {id: 'CPL', parent: '', name: 'CPL-CPMK-MK'},

      // CPL01
      {id: 'CPL01',parent: 'CPL',name: 'CPL01'},

      { id: 'CPMK011-CPL01', parent: 'CPL01', name: 'CPMK011' },
      { id: 'CPMK012-CPL01', parent: 'CPL01', name: 'CPMK012' },
      { id: 'CPMK013-CPL01', parent: 'CPL01', name: 'CPMK013' },

      { id: 'MK01-CPMK011-CPL01', parent: 'CPMK011-CPL01', name: 'MK01', value:1 },
      { id: 'MK15-CPMK011-CPL01', parent: 'CPMK011-CPL01', name: 'MK15', value:1 },
      { id: 'MK01-CPMK012-CPL01', parent: 'CPMK012-CPL01', name: 'MK01', value:1 },
      { id: 'MK16-CPMK012-CPL01', parent: 'CPMK012-CPL01', name: 'MK16', value:1 },
      { id: 'MK17-CPMK012-CPL01', parent: 'CPMK012-CPL01', name: 'MK17', value:1 },
      { id: 'MK02-CPMK013-CPL01', parent: 'CPMK013-CPL01', name: 'MK02', value:1 },
      { id: 'MK16-CPMK013-CPL01', parent: 'CPMK013-CPL01', name: 'MK16', value:1 },
      { id: 'MK17-CPMK013-CPL01', parent: 'CPMK013-CPL01', name: 'MK17', value:1 },
      { id: 'MK18-CPMK013-CPL01', parent: 'CPMK013-CPL01', name: 'MK18', value:1 },

      // CPL03
      {id: 'CPL03',parent: 'CPL',name: 'CPL03'},

      { id: 'CPMK031-CPL03', parent: 'CPL03', name: 'CPMK031' },
      { id: 'CPMK032-CPL03', parent: 'CPL03', name: 'CPMK032' },

      { id: 'MK13-CPMK031-CPL03', parent: 'CPMK031-CPL03', name: 'MK13', value:1 },
      { id: 'MK19-CPMK031-CPL03', parent: 'CPMK031-CPL03', name: 'MK19', value:1 },
      { id: 'MK25-CPMK031-CPL03', parent: 'CPMK031-CPL03', name: 'MK25', value:1 },
      { id: 'MK06-CPMK032-CPL03', parent: 'CPMK032-CPL03', name: 'MK06', value:1 },
      { id: 'MK35-CPMK032-CPL03', parent: 'CPMK032-CPL03', name: 'MK35', value:1 },

      // CPL05
      {id: 'CPL05',parent: 'CPL',name: 'CPL05'},

      { id: 'CPMK051-CPL05', parent: 'CPL05', name: 'CPMK051' },
      { id: 'CPMK052-CPL05', parent: 'CPL05', name: 'CPMK052' },

      { id: 'MK08-CPMK051-CPL05', parent: 'CPMK051-CPL05', name: 'MK08', value:1 },
      { id: 'MK09-CPMK051-CPL05', parent: 'CPMK051-CPL05', name: 'MK09', value:1 },
      { id: 'MK24-CPMK051-CPL05', parent: 'CPMK051-CPL05', name: 'MK24', value:1 },
      { id: 'MK08-CPMK052-CPL05', parent: 'CPMK052-CPL05', name: 'MK08', value:1 },
      { id: 'MK24-CPMK052-CPL05', parent: 'CPMK052-CPL05', name: 'MK24', value:1 },

      // CPL06
      {id: 'CPL06',parent: 'CPL',name: 'CPL06'},

      { id: 'CPMK061-CPL06', parent: 'CPL06', name: 'CPMK061' },
      { id: 'CPMK062-CPL06', parent: 'CPL06', name: 'CPMK062' },
      { id: 'CPMK063-CPL06', parent: 'CPL06', name: 'CPMK063' },
      
      { id: 'MK01-CPMK061-CPL06', parent: 'CPMK061-CPL06', name: 'MK01', value:1 },
      { id: 'MK02-CPMK061-CPL06', parent: 'CPMK061-CPL06', name: 'MK02', value:1 },
      { id: 'MK03-CPMK061-CPL06', parent: 'CPMK061-CPL06', name: 'MK03', value:1 },
      { id: 'MK04-CPMK061-CPL06', parent: 'CPMK061-CPL06', name: 'MK04', value:1 },
      { id: 'MK32-CPMK061-CPL06', parent: 'CPMK061-CPL06', name: 'MK32', value:1 },
      { id: 'MK04-CPMK062-CPL06', parent: 'CPMK062-CPL06', name: 'MK04', value:1 },
      { id: 'MK32-CPMK062-CPL06', parent: 'CPMK062-CPL06', name: 'MK32', value:1 },
      { id: 'MK04-CPMK063-CPL06', parent: 'CPMK063-CPL06', name: 'MK04', value:1 },
      { id: 'MK18-CPMK063-CPL06', parent: 'CPMK063-CPL06', name: 'MK18', value:1 },
      { id: 'MK32-CPMK063-CPL06', parent: 'CPMK063-CPL06', name: 'MK32', value:1 },
      { id: 'MK34-CPMK063-CPL06', parent: 'CPMK063-CPL06', name: 'MK34', value:1 },

      // CPL07
      {id: 'CPL07',parent: 'CPL',name: 'CPL07'},

      { id: 'CPMK071-CPL07', parent: 'CPL07', name: 'CPMK071' },
      { id: 'CPMK072-CPL07', parent: 'CPL07', name: 'CPMK072' },
      
      { id: 'MK34-CPMK071-CPL07', parent: 'CPMK071-CPL07', name: 'MK34', value:1 },
      { id: 'MK34-CPMK072-CPL07', parent: 'CPMK072-CPL07', name: 'MK34', value:1 },
      { id: 'MK37-CPMK072-CPL07', parent: 'CPMK072-CPL07', name: 'MK37', value:1 },

      // CPL08
      {id: 'CPL08',parent: 'CPL',name: 'CPL08'},

      { id: 'CPMK081-CPL08', parent: 'CPL08', name: 'CPMK081' },
      { id: 'CPMK082-CPL08', parent: 'CPL08', name: 'CPMK082' },
      { id: 'CPMK083-CPL08', parent: 'CPL08', name: 'CPMK083' },
      { id: 'CPMK084-CPL08', parent: 'CPL08', name: 'CPMK084' },
      
      { id: 'MK08-CPMK081-CPL08', parent: 'CPMK081-CPL08', name: 'MK08', value:1 },
      { id: 'MK09-CPMK081-CPL08', parent: 'CPMK081-CPL08', name: 'MK09', value:1 },
      { id: 'MK04-CPMK081-CPL08', parent: 'CPMK081-CPL08', name: 'MK04', value:1 },
      { id: 'MK20-CPMK081-CPL08', parent: 'CPMK081-CPL08', name: 'MK20', value:1 },
      { id: 'MK35-CPMK081-CPL08', parent: 'CPMK081-CPL08', name: 'MK35', value:1 },
      { id: 'MK36-CPMK081-CPL08', parent: 'CPMK081-CPL08', name: 'MK36', value:1 },
      { id: 'MKP02-CPMK081-CPL08', parent: 'CPMK081-CPL08', name: 'MKP02', value:1 },
      { id: 'MK08-CPMK082-CPL08', parent: 'CPMK082-CPL08', name: 'MK08', value:1 },
      { id: 'MK09-CPMK082-CPL08', parent: 'CPMK082-CPL08', name: 'MK09', value:1 },
      { id: 'MK04-CPMK082-CPL08', parent: 'CPMK082-CPL08', name: 'MK04', value:1 },
      { id: 'MK20-CPMK082-CPL08', parent: 'CPMK082-CPL08', name: 'MK20', value:1 },
      { id: 'MK35-CPMK082-CPL08', parent: 'CPMK082-CPL08', name: 'MK35', value:1 },
      { id: 'MK36-CPMK082-CPL08', parent: 'CPMK082-CPL08', name: 'MK36', value:1 },
      { id: 'MKP02-CPMK082-CPL08', parent: 'CPMK082-CPL08', name: 'MKP02', value:1 },
      { id: 'MK08-CPMK083-CPL08', parent: 'CPMK083-CPL08', name: 'MK08', value:1 },
      { id: 'MK09-CPMK083-CPL08', parent: 'CPMK083-CPL08', name: 'MK09', value:1 },
      { id: 'MK04-CPMK083-CPL08', parent: 'CPMK083-CPL08', name: 'MK04', value:1 },
      { id: 'MK20-CPMK083-CPL08', parent: 'CPMK083-CPL08', name: 'MK20', value:1 },
      { id: 'MK35-CPMK083-CPL08', parent: 'CPMK083-CPL08', name: 'MK35', value:1 },
      { id: 'MK36-CPMK083-CPL08', parent: 'CPMK083-CPL08', name: 'MK36', value:1 },
      { id: 'MKP02-CPMK083-CPL08', parent: 'CPMK083-CPL08', name: 'MKP02', value:1 },
      { id: 'MK20-CPMK084-CPL08', parent: 'CPMK084-CPL08', name: 'MK20', value:1 },
      { id: 'MK35-CPMK084-CPL08', parent: 'CPMK084-CPL08', name: 'MK35', value:1 },
      { id: 'MK36-CPMK084-CPL08', parent: 'CPMK084-CPL08', name: 'MK36', value:1 },
      { id: 'MKP02-CPMK084-CPL08', parent: 'CPMK084-CPL08', name: 'MKP02', value:1 },

      // CPL09
      {id: 'CPL09',parent: 'CPL',name: 'CPL09'},

      { id: 'CPMK091-CPL09', parent: 'CPL09', name: 'CPMK091' },
      { id: 'CPMK092-CPL09', parent: 'CPL09', name: 'CPMK092' },
      { id: 'CPMK093-CPL09', parent: 'CPL09', name: 'CPMK093' },

      { id: 'MK08-CPMK091-CPL09', parent: 'CPMK091-CPL09', name: 'MK08', value:1 },
      { id: 'MK09-CPMK091-CPL09', parent: 'CPMK091-CPL09', name: 'MK09', value:1 },
      { id: 'MK24-CPMK091-CPL09', parent: 'CPMK091-CPL09', name: 'MK24', value:1 },
      { id: 'MK08-CPMK092-CPL09', parent: 'CPMK092-CPL09', name: 'MK08', value:1 },
      { id: 'MK09-CPMK092-CPL09', parent: 'CPMK092-CPL09', name: 'MK09', value:1 },
      { id: 'MK24-CPMK092-CPL09', parent: 'CPMK092-CPL09', name: 'MK24', value:1 },
      { id: 'MK08-CPMK093-CPL09', parent: 'CPMK093-CPL09', name: 'MK08', value:1 },
      { id: 'MK09-CPMK093-CPL09', parent: 'CPMK093-CPL09', name: 'MK09', value:1 },
      { id: 'MK24-CPMK093-CPL09', parent: 'CPMK093-CPL09', name: 'MK24', value:1 },

       // CPL10
      {id: 'CPL10',parent: 'CPL',name: 'CPL10'},

      { id: 'CPMK101-CPL10', parent: 'CPL10', name: 'CPMK101' },
      { id: 'CPMK102-CPL10', parent: 'CPL10', name: 'CPMK102' },
      { id: 'CPMK103-CPL10', parent: 'CPL10', name: 'CPMK103' },

      { id: 'MK04-CPMK101-CPL10', parent: 'CPMK101-CPL10', name: 'MK04', value:1 },
      { id: 'MK08-CPMK101-CPL10', parent: 'CPMK101-CPL10', name: 'MK08', value:1 },
      { id: 'MK09-CPMK101-CPL10', parent: 'CPMK101-CPL10', name: 'MK09', value:1 },
      { id: 'MK30-CPMK101-CPL10', parent: 'CPMK101-CPL10', name: 'MK30', value:1 },
      { id: 'MK04-CPMK102-CPL10', parent: 'CPMK102-CPL10', name: 'MK04', value:1 },
      { id: 'MK08-CPMK102-CPL10', parent: 'CPMK102-CPL10', name: 'MK08', value:1 },
      { id: 'MK09-CPMK102-CPL10', parent: 'CPMK102-CPL10', name: 'MK09', value:1 },
      { id: 'MK30-CPMK102-CPL10', parent: 'CPMK102-CPL10', name: 'MK30', value:1 },
      { id: 'MK04-CPMK103-CPL10', parent: 'CPMK103-CPL10', name: 'MK04', value:1 },
      { id: 'MK08-CPMK103-CPL10', parent: 'CPMK103-CPL10', name: 'MK08', value:1 },
      { id: 'MK09-CPMK103-CPL10', parent: 'CPMK103-CPL10', name: 'MK09', value:1 },
      { id: 'MK30-CPMK103-CPL10', parent: 'CPMK103-CPL10', name: 'MK30', value:1 },
    ]

    Highcharts.chart('CPL-CPMK-MK', {

      chart: {
        height: '50%'
      },

      // Let the center circle be transparent
      colors: ['transparent'].concat(Highcharts.getOptions().colors),

      title: {
        text: 'CPL-CPMK-MK'
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