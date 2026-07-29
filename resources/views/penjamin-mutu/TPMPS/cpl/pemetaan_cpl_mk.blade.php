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

<h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL-MK</h3>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div id="CPL-MK"></div>
        </div>
    </div>
</div>

{{-- CPL-MK --}}
<script>
    const data = [
        {id: 'CPL', parent: '', name: 'CPL-MK'},

        // CPL01
        {id: 'CPL01',parent: 'CPL',name: 'CPL01'},

        {id: 'MK01-CPL01', parent: 'CPL01', name: 'MK01', value: 1 },
        {id: 'MK02-CPL01', parent: 'CPL01', name: 'MK02', value: 1 },

        // CPL02
        {id: 'CPL02',parent: 'CPL',name: 'CPL02'},

        {id: 'MK01-CPL02', parent: 'CPL02', name: 'MK01', value: 1 },
        {id: 'MK02-CPL02', parent: 'CPL02', name: 'MK02', value: 1 },
        {id: 'MK03-CPL02', parent: 'CPL02', name: 'MK03', value: 1 },
        {id: 'MK04-CPL02', parent: 'CPL02', name: 'MK04', value: 1 },
        
        // CPL03
        {id: 'CPL03',parent: 'CPL',name: 'CPL03'},

        {id: 'MK05-CPL03', parent: 'CPL03', name: 'MK05', value: 1 },
        {id: 'MK06-CPL03', parent: 'CPL03', name: 'MK06', value: 1 },

        // CPL04
        {id: 'CPL04',parent: 'CPL',name: 'CPL04'},

        {id: 'MK02-CPL04', parent: 'CPL04', name: 'MK02', value: 1 },
        {id: 'MK03-CPL04', parent: 'CPL04', name: 'MK03', value: 1 },
        {id: 'MK04-CPL04', parent: 'CPL04', name: 'MK04', value: 1 },
        {id: 'MK07-CPL04', parent: 'CPL04', name: 'MK07', value: 1 },
        {id: 'MK08-CPL04', parent: 'CPL04', name: 'MK08', value: 1 },
        {id: 'MK09-CPL04', parent: 'CPL04', name: 'MK09', value: 1 },
        {id: 'MK10-CPL04', parent: 'CPL04', name: 'MK10', value: 1 },

        // CPL05
        {id: 'CPL05',parent: 'CPL',name: 'CPL05'},

        {id: 'MK09-CPL05', parent: 'CPL05', name: 'MK09', value: 1 },

        // CPL06
        {id: 'CPL06',parent: 'CPL',name: 'CPL06'},

        {id: 'MK01-CPL06', parent: 'CPL06', name: 'MK01', value: 1 },
        {id: 'MK02-CPL06', parent: 'CPL06', name: 'MK02', value: 1 },
        {id: 'MK03-CPL06', parent: 'CPL06', name: 'MK03', value: 1 },
        {id: 'MK04-CPL06', parent: 'CPL06', name: 'MK04', value: 1 },

        // CPL08
        {id: 'CPL08',parent: 'CPL',name: 'CPL08'},

        {id: 'MK05-CPL08', parent: 'CPL08', name: 'MK05', value: 1 },
        {id: 'MK06-CPL08', parent: 'CPL08', name: 'MK06', value: 1 },
        {id: 'MK08-CPL08', parent: 'CPL08', name: 'MK08', value: 1 },
        {id: 'MK09-CPL08', parent: 'CPL08', name: 'MK09', value: 1 },

        // CPL09
        {id: 'CPL09',parent: 'CPL',name: 'CPL09'},

        {id: 'MK04-CPL09', parent: 'CPL09', name: 'MK04', value: 1 },
        {id: 'MK08-CPL09', parent: 'CPL09', name: 'MK08', value: 1 },
        {id: 'MK09-CPL09', parent: 'CPL09', name: 'MK09', value: 1 },

        // CPL10
        {id: 'CPL10',parent: 'CPL',name: 'CPL10'},

        {id: 'MK09-CPL10', parent: 'CPL10', name: 'MK09', value: 1 },
    ]

    Highcharts.chart('CPL-MK', {
        chart: {
            height: '50%'
        },

        // Let the center circle be transparent
        colors: ['transparent'].concat(Highcharts.getOptions().colors),

        title: {
            text: 'CPL-MK'
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