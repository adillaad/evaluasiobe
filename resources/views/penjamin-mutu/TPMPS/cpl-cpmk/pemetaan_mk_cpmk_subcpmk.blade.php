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

<h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL-MK-CPMK-Sub CPMK</h3>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div id="MK-CPMK-SubCPMK"></div>
        </div>
    </div>
</div>

<script>
    const data = [
      {id: 'MK', parent: '', name: 'MK-CPMK-Sub CPMK'},

      // MK01
      {id: 'MK01',parent: 'MK',name: 'MK01'},

      { id: 'CPMK011-MK01', parent: 'MK01', name: 'CPMK011' },
      { id: 'CPMK012-MK01', parent: 'MK01', name: 'CPMK012' },

      { id: 'Sub-CPMK0111-CPMK011-MK01', parent: 'CPMK011-MK01', name: 'Sub-CPMK0111', value:1 },
      { id: 'Sub-CPMK0121-CPMK012-MK01', parent: 'CPMK012-MK01', name: 'Sub-CPMK0121', value:1 },
      { id: 'Sub-CPMK0122-CPMK012-MK01', parent: 'CPMK012-MK01', name: 'Sub-CPMK0122', value:1 },
      { id: 'Sub-CPMK0123-CPMK012-MK01', parent: 'CPMK012-MK01', name: 'Sub-CPMK0123', value:1 },
      
      // MK02
      {id: 'MK02',parent: 'MK',name: 'MK02'},

      { id: 'CPMK013-MK02', parent: 'MK02', name: 'CPMK013' },

      { id: 'Sub-CPMK0131-CPMK013-MK02', parent: 'CPMK013-MK02', name: 'Sub-CPMK0131', value:1 },
      { id: 'Sub-CPMK0132-CPMK013-MK02', parent: 'CPMK013-MK02', name: 'Sub-CPMK0132', value:1 },

      // MK04
      {id: 'MK04',parent: 'MK',name: 'MK04'},

      { id: 'CPMK061-MK04', parent: 'MK04', name: 'CPMK061' },
      { id: 'CPMK062-MK04', parent: 'MK04', name: 'CPMK062' },
      { id: 'CPMK063-MK04', parent: 'MK04', name: 'CPMK063' },
      { id: 'CPMK081-MK04', parent: 'MK04', name: 'CPMK081' },
      { id: 'CPMK082-MK04', parent: 'MK04', name: 'CPMK082' },
      { id: 'CPMK083-MK04', parent: 'MK04', name: 'CPMK083' },

      { id: 'Sub-CPMK0611-CPMK061-MK04', parent: 'CPMK061-MK04', name: 'Sub-CPMK0611', value:1 },
      { id: 'Sub-CPMK0621-CPMK062-MK04', parent: 'CPMK062-MK04', name: 'Sub-CPMK0621', value:1 },
      { id: 'Sub-CPMK0631-CPMK063-MK04', parent: 'CPMK063-MK04', name: 'Sub-CPMK0631', value:1 },
      { id: 'Sub-CPMK0811-CPMK081-MK04', parent: 'CPMK081-MK04', name: 'Sub-CPMK0811', value:1 },
      { id: 'Sub-CPMK0821-CPMK082-MK04', parent: 'CPMK082-MK04', name: 'Sub-CPMK0821', value:1 },
      { id: 'Sub-CPMK0831-CPMK081-MK04', parent: 'CPMK081-MK04', name: 'Sub-CPMK0831', value:1 },

      // MK05
      {id: 'MK05',parent: 'MK',name: 'MK05'},

      { id: 'CPMK081-MK05', parent: 'MK05', name: 'CPMK081' },
      { id: 'CPMK082-MK05', parent: 'MK05', name: 'CPMK082' },
      { id: 'CPMK083-MK05', parent: 'MK05', name: 'CPMK083' },
      { id: 'CPMK084-MK05', parent: 'MK05', name: 'CPMK084' },

      { id: 'Sub-CPMK0811-CPMK081-MK05', parent: 'CPMK081-MK05', name: 'Sub-CPMK0811', value:1 },
      { id: 'Sub-CPMK0821-CPMK082-MK05', parent: 'CPMK082-MK05', name: 'Sub-CPMK0821', value:1 },
      { id: 'Sub-CPMK0831-CPMK083-MK05', parent: 'CPMK083-MK05', name: 'Sub-CPMK0831', value:1 },
      { id: 'Sub-CPMK0841-CPMK084-MK05', parent: 'CPMK084-MK05', name: 'Sub-CPMK0841', value:1 },

      // MK08
      {id: 'MK08',parent: 'MK',name: 'MK08'},

      { id: 'CPMK051-MK08', parent: 'MK08', name: 'CPMK051' },
      { id: 'CPMK052-MK08', parent: 'MK08', name: 'CPMK052' },
      { id: 'CPMK081-MK08', parent: 'MK08', name: 'CPMK081' },
      { id: 'CPMK082-MK08', parent: 'MK08', name: 'CPMK082' },
      { id: 'CPMK083-MK08', parent: 'MK08', name: 'CPMK083' },
      { id: 'CPMK091-MK08', parent: 'MK08', name: 'CPMK091' },
      { id: 'CPMK092-MK08', parent: 'MK08', name: 'CPMK092' },
      { id: 'CPMK093-MK08', parent: 'MK08', name: 'CPMK093' },
      { id: 'CPMK101-MK08', parent: 'MK08', name: 'CPMK101' },
      { id: 'CPMK102-MK08', parent: 'MK08', name: 'CPMK102' },
      { id: 'CPMK103-MK08', parent: 'MK08', name: 'CPMK103' },

      { id: 'Sub-CPMK0511-CPMK051-MK08', parent: 'CPMK051-MK08', name: 'Sub-CPMK0511', value:1 },
      { id: 'Sub-CPMK0521-CPMK052-MK08', parent: 'CPMK052-MK08', name: 'Sub-CPMK0521', value:1 },
      { id: 'Sub-CPMK0811-CPMK081-MK08', parent: 'CPMK081-MK08', name: 'Sub-CPMK0811', value:1 },
      { id: 'Sub-CPMK0821-CPMK082-MK08', parent: 'CPMK082-MK08', name: 'Sub-CPMK0821', value:1 },
      { id: 'Sub-CPMK0831-CPMK083-MK08', parent: 'CPMK083-MK08', name: 'Sub-CPMK0831', value:1 },
      { id: 'Sub-CPMK0911-CPMK091-MK08', parent: 'CPMK091-MK08', name: 'Sub-CPMK0911', value:1 },
      { id: 'Sub-CPMK0921-CPMK092-MK08', parent: 'CPMK092-MK08', name: 'Sub-CPMK0921', value:1 },
      { id: 'Sub-CPMK0931-CPMK093-MK08', parent: 'CPMK093-MK08', name: 'Sub-CPMK0931', value:1 },
      { id: 'Sub-CPMK1011-CPMK101-MK08', parent: 'CPMK101-MK08', name: 'Sub-CPMK1011', value:1 },
      { id: 'Sub-CPMK1021-CPMK102-MK08', parent: 'CPMK102-MK08', name: 'Sub-CPMK1021', value:1 },
      { id: 'Sub-CPMK1031-CPMK103-MK08', parent: 'CPMK103-MK08', name: 'Sub-CPMK1031', value:1 },

      // MK09
      {id: 'MK09',parent: 'MK',name: 'MK09'},

      { id: 'CPMK051-MK09', parent: 'MK09', name: 'CPMK051' },
      { id: 'CPMK081-MK09', parent: 'MK09', name: 'CPMK081' },
      { id: 'CPMK083-MK09', parent: 'MK09', name: 'CPMK083' },
      { id: 'CPMK091-MK09', parent: 'MK09', name: 'CPMK091' },
      { id: 'CPMK092-MK09', parent: 'MK09', name: 'CPMK092' },
      { id: 'CPMK093-MK09', parent: 'MK09', name: 'CPMK093' },
      { id: 'CPMK101-MK09', parent: 'MK09', name: 'CPMK101' },
      { id: 'CPMK103-MK09', parent: 'MK09', name: 'CPMK103' },

      { id: 'Sub-CPMK0511-CPMK051-MK09', parent: 'CPMK051-MK09', name: 'Sub-CPMK0511', value:1 },
      { id: 'Sub-CPMK0811-CPMK081-MK09', parent: 'CPMK081-MK09', name: 'Sub-CPMK0811', value:1 },
      { id: 'Sub-CPMK0831-CPMK083-MK09', parent: 'CPMK083-MK09', name: 'Sub-CPMK0831', value:1 },
      { id: 'Sub-CPMK0911-CPMK091-MK09', parent: 'CPMK091-MK09', name: 'Sub-CPMK0911', value:1 },
      { id: 'Sub-CPMK0921-CPMK092-MK09', parent: 'CPMK092-MK09', name: 'Sub-CPMK0921', value:1 },
      { id: 'Sub-CPMK0931-CPMK093-MK09', parent: 'CPMK093-MK09', name: 'Sub-CPMK0931', value:1 },
      { id: 'Sub-CPMK1011-CPMK101-MK09', parent: 'CPMK101-MK09', name: 'Sub-CPMK1011', value:1 },
      { id: 'Sub-CPMK1031-CPMK103-MK09', parent: 'CPMK103-MK09', name: 'Sub-CPMK1031', value:1 },

      // MK24
      {id: 'MK24',parent: 'MK',name: 'MK24'},

      { id: 'CPMK051-MK24', parent: 'MK24', name: 'CPMK051' },
      { id: 'CPMK052-MK24', parent: 'MK24', name: 'CPMK052' },
      { id: 'CPMK091-MK24', parent: 'MK24', name: 'CPMK091' },
      { id: 'CPMK092-MK24', parent: 'MK24', name: 'CPMK092' },
      { id: 'CPMK093-MK24', parent: 'MK24', name: 'CPMK093' },

      { id: 'Sub-CPMK0511-CPMK051-MK24', parent: 'CPMK051-MK24', name: 'Sub-CPMK0511', value:1 },
      { id: 'Sub-CPMK0521-CPMK052-MK24', parent: 'CPMK052-MK24', name: 'Sub-CPMK0521', value:1 },
      { id: 'Sub-CPMK0911-CPMK091-MK24', parent: 'CPMK091-MK24', name: 'Sub-CPMK0911', value:1 },
      { id: 'Sub-CPMK0921-CPMK092-MK24', parent: 'CPMK092-MK24', name: 'Sub-CPMK0921', value:1 },
      { id: 'Sub-CPMK0931-CPMK093-MK24', parent: 'CPMK093-MK24', name: 'Sub-CPMK0931', value:1 },
    ]

    Highcharts.chart('MK-CPMK-SubCPMK', {
      chart: {
        height: '50%'
      },

      // Let the center circle be transparent
      colors: ['transparent'].concat(Highcharts.getOptions().colors),

      title: {
        text: 'MK-CPMK-Sub CPMK'
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