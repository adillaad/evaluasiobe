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
            <div id="CPL-BK"></div>
        </div>
    </div>
</div>

<script>
    const data = [
        {id: 'CPL', parent: '', name: 'CPL-BK'},

        // CPL01
        {id: 'CPL01',parent: 'CPL',name: 'CPL01'},

        {id: 'BK01-CPL01', parent: 'CPL01', name: 'BK01', value: 1 },
        {id: 'BK21-CPL01', parent: 'CPL01', name: 'BK21', value: 1 },

        // CPL02
        {id: 'CPL02',parent: 'CPL',name: 'CPL02'},

        {id: 'BK01-CPL02', parent: 'CPL02', name: 'BK01', value: 1 },
        {id: 'BK03-CPL02', parent: 'CPL02', name: 'BK03', value: 1 },
        {id: 'BK21-CPL02', parent: 'CPL02', name: 'BK21', value: 1 },
        
        // CPL03
        {id: 'CPL03',parent: 'CPL',name: 'CPL03'},

        {id: 'BK07-CPL03', parent: 'CPL03', name: 'BK07', value: 1 },
        {id: 'BK08-CPL03', parent: 'CPL03', name: 'BK08', value: 1 },
        {id: 'BK11-CPL03', parent: 'CPL03', name: 'BK11', value: 1 },
        {id: 'BK12-CPL03', parent: 'CPL03', name: 'BK12', value: 1 },
        {id: 'BK14-CPL03', parent: 'CPL03', name: 'BK14', value: 1 },
        {id: 'BK16-CPL03', parent: 'CPL03', name: 'BK16', value: 1 },
        {id: 'BK18-CPL03', parent: 'CPL03', name: 'BK18', value: 1 },
        {id: 'BK20-CPL03', parent: 'CPL03', name: 'BK20', value: 1 },

        // CPL04
        {id: 'CPL04',parent: 'CPL',name: 'CPL04'},

        {id: 'BK02-CPL04', parent: 'CPL04', name: 'BK02', value: 1 },
        {id: 'BK03-CPL04', parent: 'CPL04', name: 'BK03', value: 1 },
        {id: 'BK03-CPL04', parent: 'CPL04', name: 'BK03', value: 1 },
        {id: 'BK05-CPL04', parent: 'CPL04', name: 'BK05', value: 1 },
        {id: 'BK07-CPL04', parent: 'CPL04', name: 'BK07', value: 1 },
        {id: 'BK08-CPL04', parent: 'CPL04', name: 'BK08', value: 1 },
        {id: 'BK10-CPL04', parent: 'CPL04', name: 'BK10', value: 1 },
        {id: 'BK16-CPL04', parent: 'CPL04', name: 'BK16', value: 1 },
        {id: 'BK17-CPL04', parent: 'CPL04', name: 'BK17', value: 1 },
        {id: 'BK18-CPL04', parent: 'CPL04', name: 'BK18', value: 1 },
        {id: 'BK19-CPL04', parent: 'CPL04', name: 'BK19', value: 1 },
        {id: 'BK20-CPL04', parent: 'CPL04', name: 'BK20', value: 1 },

        // CPL05
        {id: 'CPL05',parent: 'CPL',name: 'CPL05'},

        {id: 'BK03-CPL05', parent: 'CPL05', name: 'BK03', value: 1 },
        {id: 'BK04-CPL05', parent: 'CPL05', name: 'BK04', value: 1 },
        {id: 'BK05-CPL05', parent: 'CPL05', name: 'BK05', value: 1 },
        {id: 'BK06-CPL05', parent: 'CPL05', name: 'BK06', value: 1 },
        {id: 'BK07-CPL05', parent: 'CPL05', name: 'BK07', value: 1 },
        {id: 'BK09-CPL05', parent: 'CPL05', name: 'BK09', value: 1 },
        {id: 'BK10-CPL05', parent: 'CPL05', name: 'BK10', value: 1 },
        {id: 'BK11-CPL05', parent: 'CPL05', name: 'BK11', value: 1 },
        {id: 'BK12-CPL05', parent: 'CPL05', name: 'BK12', value: 1 },
        {id: 'BK15-CPL05', parent: 'CPL05', name: 'BK15', value: 1 },
        {id: 'BK16-CPL05', parent: 'CPL05', name: 'BK16', value: 1 },
        {id: 'BK19-CPL05', parent: 'CPL05', name: 'BK19', value: 1 },
        {id: 'BK20-CPL05', parent: 'CPL05', name: 'BK20', value: 1 },

        // CPL06
        {id: 'CPL06',parent: 'CPL',name: 'CPL06'},

        {id: 'BK01-CPL06', parent: 'CPL06', name: 'BK01', value: 1 },
        {id: 'BK03-CPL06', parent: 'CPL06', name: 'BK03', value: 1 },
        {id: 'BK20-CPL06', parent: 'CPL06', name: 'BK20', value: 1 },
        {id: 'BK21-CPL06', parent: 'CPL06', name: 'BK21', value: 1 },

        // CPL07
        {id: 'CPL07',parent: 'CPL',name: 'CPL07'},

        {id: 'BK01-CPL07', parent: 'CPL07', name: 'BK01', value: 1 },
        {id: 'BK03-CPL07', parent: 'CPL07', name: 'BK03', value: 1 },
        {id: 'BK20-CPL07', parent: 'CPL07', name: 'BK20', value: 1 },
        {id: 'BK22-CPL07', parent: 'CPL07', name: 'BK22', value: 1 },

        // CPL08
        {id: 'CPL08',parent: 'CPL',name: 'CPL08'},

        {id: 'BK05-CPL08', parent: 'CPL08', name: 'BK05', value: 1 },
        {id: 'BK07-CPL08', parent: 'CPL08', name: 'BK07', value: 1 },
        {id: 'BK08-CPL08', parent: 'CPL08', name: 'BK08', value: 1 },
        {id: 'BK10-CPL08', parent: 'CPL08', name: 'BK10', value: 1 },
        {id: 'BK12-CPL08', parent: 'CPL08', name: 'BK12', value: 1 },
        {id: 'BK13-CPL08', parent: 'CPL08', name: 'BK13', value: 1 },
        {id: 'BK14-CPL08', parent: 'CPL08', name: 'BK14', value: 1 },
        {id: 'BK16-CPL08', parent: 'CPL08', name: 'BK16', value: 1 },
        {id: 'BK17-CPL08', parent: 'CPL08', name: 'BK17', value: 1 },
        {id: 'BK18-CPL08', parent: 'CPL08', name: 'BK18', value: 1 },
        {id: 'BK20-CPL08', parent: 'CPL08', name: 'BK20', value: 1 },

        // CPL09
        {id: 'CPL09',parent: 'CPL',name: 'CPL09'},

        {id: 'BK04-CPL09', parent: 'CPL09', name: 'BK04', value: 1 },
        {id: 'BK05-CPL09', parent: 'CPL09', name: 'BK05', value: 1 },
        {id: 'BK09-CPL09', parent: 'CPL09', name: 'BK09', value: 1 },
        {id: 'BK10-CPL09', parent: 'CPL09', name: 'BK10', value: 1 },
        {id: 'BK16-CPL09', parent: 'CPL09', name: 'BK16', value: 1 },

        // CPL10
        {id: 'CPL10',parent: 'CPL',name: 'CPL10'},

        {id: 'BK10-CPL10', parent: 'CPL10', name: 'BK10', value: 1 },
        {id: 'BK15-CPL10', parent: 'CPL10', name: 'BK15', value: 1 },
        {id: 'BK16-CPL10', parent: 'CPL10', name: 'BK16', value: 1 },
    ]

    Highcharts.chart('CPL-BK', {
        chart: {
            height: '50%'
        },

        // Let the center circle be transparent
        colors: ['transparent'].concat(Highcharts.getOptions().colors),

        title: {
            text: 'CPL-BK'
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