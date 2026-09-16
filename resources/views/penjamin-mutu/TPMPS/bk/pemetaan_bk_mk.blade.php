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
            <div id="BK-MK"></div>
        </div>
    </div>
</div>

{{-- BK-MK --}}
<script>
const data = [
    {id: 'BK', parent: '', name: 'BK_MK'},

    // BK01
    {id: 'BK01',parent: 'BK',name: 'BK01'},

    {id: 'MK01-BK01', parent: 'BK01', name: 'MK01', value: 1 },
    {id: 'MK02-BK01', parent: 'BK01', name: 'MK02', value: 1 },

    // BK02
    {id: 'BK02',parent: 'BK',name: 'BK02'},

    {id: 'MK07-BK02', parent: 'BK02', name: 'MK07', value: 1 },

    // BK03
    {id: 'BK03',parent: 'BK',name: 'BK03'},

    {id: 'MK03-BK03', parent: 'BK03', name: 'MK03', value: 1 },
    {id: 'MK04-BK03', parent: 'BK03', name: 'MK04', value: 1 },

    // BK05
    {id: 'BK05',parent: 'BK',name: 'BK05'},

    {id: 'MK09-BK05', parent: 'BK05', name: 'MK09', value: 1 },
    {id: 'MK10-BK05', parent: 'BK05', name: 'MK10', value: 1 },

    // BK10
    {id: 'BK10',parent: 'BK',name: 'BK10'},

    {id: 'MK08-BK10', parent: 'BK10', name: 'MK08', value: 1 },
    {id: 'MK09-BK10', parent: 'BK10', name: 'MK09', value: 1 },
]

Highcharts.chart('BK-MK', {
    chart: {
        height: '50%'
    },

    // Let the center circle be transparent
    colors: ['transparent'].concat(Highcharts.getOptions().colors),

    title: {
        text: 'BK_MK'
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
            }
        ]

    }],

    tooltip: {
        headerFormat: '',
        pointFormat: '<b>{point.id}</b>'
    }
});
</script>
@endsection
