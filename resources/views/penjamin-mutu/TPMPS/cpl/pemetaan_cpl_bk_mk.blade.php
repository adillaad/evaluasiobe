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
                <div id="CPL-BK-MK"></div>
            </div>
        </div>
    </div>

    <!-- CPL-BK-MK -->
    <script>
        const data = [
        {id: 'CPL', parent: '', name: 'CPL-BK-MK'},
    
        // CPL01
        { id: 'CPL01',parent: 'CPL',name: 'CPL01'},
        { id: 'BK01-CPL01', parent: 'CPL01', name: 'BK01' },
        { id: 'MK01-BK01-CPL01', parent: 'BK01-CPL01', name: 'MK01', value:1 },
        { id: 'MK02-BK01-CPL01', parent: 'BK01-CPL01', name: 'MK02', value:1 },
    
        // CPL02
        { id: 'CPL02', parent: 'CPL', name: 'CPL02' },
        { id: 'BK01-CPL02', parent: 'CPL02', name: 'BK01' },
        { id: 'BK03-CPL02', parent: 'CPL02', name: 'BK03' },

        { id: 'MK01-BK01-CPL02', parent: 'BK01-CPL02', name: 'MK01', value:1 },
        { id: 'MK02-BK01-CPL02', parent: 'BK01-CPL02', name: 'MK02', value:1 },
        
        { id: 'MK03-BK03-CPL02', parent: 'BK03-CPL02', name: 'MK03', value:1 },
        { id: 'MK04-BK03-CPL02', parent: 'BK03-CPL02', name: 'MK04', value:1 },

        // CPL03
        { id: 'CPL03', parent: 'CPL', name: 'CPL03' },

        { id: 'BK05-CPL03', parent: 'CPL03', name: 'BK05' },
        { id: 'BK07-CPL03', parent: 'CPL03', name: 'BK07' },
        { id: 'BK08-CPL03', parent: 'CPL03', name: 'BK08' },
        { id: 'BK11-CPL03', parent: 'CPL03', name: 'BK11' },
        { id: 'BK12-CPL03', parent: 'CPL03', name: 'BK12' },
        { id: 'BK14-CPL03', parent: 'CPL03', name: 'BK14' },
        { id: 'BK16-CPL03', parent: 'CPL03', name: 'BK16' },

        { id: 'MK21-BK05-CPL03', parent: 'BK05-CPL03', name: 'MK21', value:1 },
        { id: 'MK22-BK05-CPL03', parent: 'BK05-CPL03', name: 'MK22', value:1 },
        { id: 'MK23-BK05-CPL03', parent: 'BK05-CPL03', name: 'MK23', value:1 },
        { id: 'MK28-BK05-CPL03', parent: 'BK05-CPL03', name: 'MK28', value:1 },

        { id: 'MK13-BK07-CPL03', parent: 'BK07-CPL03', name: 'MK13', value:1 },
        { id: 'MK25-BK07-CPL03', parent: 'BK07-CPL03', name: 'MK25', value:1 },
        { id: 'MK35-BK07-CPL03', parent: 'BK07-CPL03', name: 'MK35', value:1 },
        { id: 'MK20-BK07-CPL03', parent: 'BK07-CPL03', name: 'MK20', value:1 },

        { id: 'MK13-BK08-CPL03', parent: 'BK08-CPL03', name: 'MK13', value:1 },
        { id: 'MK35-BK08-CPL03', parent: 'BK08-CPL03', name: 'MK35', value:1 },

        { id: 'MK25-BK11-CPL03', parent: 'BK11-CPL03', name: 'MK25', value:1 },

        { id: 'MK05-BK12-CPL03', parent: 'BK12-CPL03', name: 'MK05', value:1 },
        { id: 'MK06-BK12-CPL03', parent: 'BK12-CPL03', name: 'MK06', value:1 },
        { id: 'MK11-BK12-CPL03', parent: 'BK12-CPL03', name: 'MK11', value:1 },

        { id: 'MK06-BK14-CPL03', parent: 'BK14-CPL03', name: 'MK06', value:1 },
        { id: 'MK10-BK14-CPL03', parent: 'BK14-CPL03', name: 'MK10', value:1 },

        { id: 'MK25-BK16-CPL03', parent: 'BK16-CPL03', name: 'MK25', value:1 },

        // CPL04
        { id: 'CPL04', parent: 'CPL', name: 'CPL04' },

        { id: 'BK02-CPL04', parent: 'CPL04', name: 'BK02' },
        { id: 'BK03-CPL04', parent: 'CPL04', name: 'BK03' },
        { id: 'BK05-CPL04', parent: 'CPL04', name: 'BK05' },
        { id: 'BK07-CPL04', parent: 'CPL04', name: 'BK07' },
        { id: 'BK08-CPL04', parent: 'CPL04', name: 'BK08' },
        { id: 'BK10-CPL04', parent: 'CPL04', name: 'BK10' },
        { id: 'BK16-CPL04', parent: 'CPL04', name: 'BK16' },
        { id: 'BK17-CPL04', parent: 'CPL04', name: 'BK17' },

        { id: 'MK07-BK02-CPL04', parent: 'BK02-CPL04', name: 'MK07', value:1 },
        { id: 'MK03-BK03-CPL04', parent: 'BK03-CPL04', name: 'MK03', value:1 },
        { id: 'MK04-BK03-CPL04', parent: 'BK03-CPL04', name: 'MK04', value:1 },
        { id: 'MK08-BK05-CPL04', parent: 'BK05-CPL04', name: 'MK08', value:1 },
        { id: 'MK09-BK05-CPL04', parent: 'BK05-CPL04', name: 'MK09', value:1 },
        { id: 'MK10-BK05-CPL04', parent: 'BK05-CPL04', name: 'MK10', value:1 },
        { id: 'MK31-BK05-CPL04', parent: 'BK05-CPL04', name: 'MK31', value:1 },
        { id: 'MK33-BK05-CPL04', parent: 'BK05-CPL04', name: 'MK33', value:1 },
        { id: 'MK29-BK07-CPL04', parent: 'BK07-CPL04', name: 'MK29', value:1 },
        { id: 'MK20-BK07-CPL04', parent: 'BK07-CPL04', name: 'MK20', value:1 },
        { id: 'MK13-BK08-CPL04', parent: 'BK08-CPL04', name: 'MK13', value:1 },
        { id: 'MK08-BK10-CPL04', parent: 'BK10-CPL04', name: 'MK08', value:1 },
        { id: 'MK29-BK16-CPL04', parent: 'BK16-CPL04', name: 'MK29', value:1 },
        { id: 'MK36-BK17-CPL04', parent: 'BK17-CPL04', name: 'MK36', value:1 },

        // CPL05
        { id: 'CPL05', parent: 'CPL', name: 'CPL05' },

        { id: 'BK03-CPL05', parent: 'CPL05', name: 'BK03' },
        { id: 'BK04-CPL05', parent: 'CPL05', name: 'BK04' },
        { id: 'BK05-CPL05', parent: 'CPL05', name: 'BK05' },
        { id: 'BK06-CPL05', parent: 'CPL05', name: 'BK06' },
        { id: 'BK07-CPL05', parent: 'CPL05', name: 'BK07' },
        { id: 'BK09-CPL05', parent: 'CPL05', name: 'BK09' },
        { id: 'BK10-CPL05', parent: 'CPL05', name: 'BK10' },
        { id: 'BK11-CPL05', parent: 'CPL05', name: 'BK11' },
        { id: 'BK12-CPL05', parent: 'CPL05', name: 'BK12' },
        { id: 'BK15-CPL05', parent: 'CPL05', name: 'BK15' },
        { id: 'BK16-CPL05', parent: 'CPL05', name: 'BK16' },

        { id: 'MK32-BK03-CPL05', parent: 'BK03-CPL05', name: 'MK32', value:1 },
        { id: 'MK24-BK04-CPL05', parent: 'BK04-CPL05', name: 'MK24', value:1 },
        { id: 'MK09-BK05-CPL05', parent: 'BK05-CPL05', name: 'MK09', value:1 },
        { id: 'MK21-BK05-CPL05', parent: 'BK05-CPL05', name: 'MK21', value:1 },
        { id: 'MK22-BK05-CPL05', parent: 'BK05-CPL05', name: 'MK22', value:1 },
        { id: 'MK23-BK05-CPL05', parent: 'BK05-CPL05', name: 'MK23', value:1 },
        { id: 'MK26-BK05-CPL05', parent: 'BK05-CPL05', name: 'MK26', value:1 },
        { id: 'MK28-BK05-CPL05', parent: 'BK05-CPL05', name: 'MK28', value:1 },
        { id: 'MK26-BK06-CPL05', parent: 'BK06-CPL05', name: 'MK26', value:1 },
        { id: 'MK25-BK07-CPL05', parent: 'BK07-CPL05', name: 'MK25', value:1 },
        { id: 'MK20-BK07-CPL05', parent: 'BK07-CPL05', name: 'MK20', value:1 },
        { id: 'MK24-BK09-CPL05', parent: 'BK09-CPL05', name: 'MK24', value:1 },
        { id: 'MK24-BK10-CPL05', parent: 'BK10-CPL05', name: 'MK24', value:1 },
        { id: 'MK25-BK11-CPL05', parent: 'BK11-CPL05', name: 'MK25', value:1 },
        { id: 'MK26-BK12-CPL05', parent: 'BK12-CPL05', name: 'MK26', value:1 },
        { id: 'MK19-BK15-CPL05', parent: 'BK15-CPL05', name: 'MK19', value:1 },
        { id: 'MK25-BK16-CPL05', parent: 'BK16-CPL05', name: 'MK25', value:1 },

        // CPL06
        { id: 'CPL06', parent: 'CPL', name: 'CPL06' },

        { id: 'BK01-CPL06', parent: 'CPL06', name: 'BK01' },
        { id: 'BK03-CPL06', parent: 'CPL06', name: 'BK03' },

        { id: 'MK34-BK01-CPL06', parent: 'BK01-CPL06', name: 'MK34', value:1 },
        { id: 'MK01-BK01-CPL06', parent: 'BK01-CPL06', name: 'MK01', value:1 },
        { id: 'MK02-BK01-CPL06', parent: 'BK01-CPL06', name: 'MK02', value:1 },
        { id: 'MK03-BK03-CPL06', parent: 'BK03-CPL06', name: 'MK03', value:1 },
        { id: 'MK04-BK03-CPL06', parent: 'BK03-CPL06', name: 'MK04', value:1 },
        { id: 'MK34-BK03-CPL06', parent: 'BK03-CPL06', name: 'MK34', value:1 },
        { id: 'MK32-BK03-CPL06', parent: 'BK03-CPL06', name: 'MK32', value:1 },

        // CPL07
        { id: 'CPL07', parent: 'CPL', name: 'CPL07' },

        { id: 'BK01-CPL07', parent: 'CPL07', name: 'BK01' },
        { id: 'BK03-CPL07', parent: 'CPL07', name: 'BK03' },

        { id: 'MK34-BK01-CPL07', parent: 'BK01-CPL07', name: 'MK34', value:1 },
        { id: 'MK37-BK01-CPL07', parent: 'BK01-CPL07', name: 'MK37', value:1 },
        { id: 'MK34-BK03-CPL07', parent: 'BK03-CPL07', name: 'MK34', value:1 },
        { id: 'MK37-BK03-CPL07', parent: 'BK03-CPL07', name: 'MK37', value:1 },

        // CPL08
        { id: 'CPL08', parent: 'CPL', name: 'CPL08' },

        { id: 'BK05-CPL08', parent: 'CPL08', name: 'BK05' },
        { id: 'BK07-CPL08', parent: 'CPL08', name: 'BK07' },
        { id: 'BK08-CPL08', parent: 'CPL08', name: 'BK08' },
        { id: 'BK10-CPL08', parent: 'CPL08', name: 'BK10' },
        { id: 'BK12-CPL08', parent: 'CPL08', name: 'BK12' },
        { id: 'BK13-CPL08', parent: 'CPL08', name: 'BK13' },
        { id: 'BK14-CPL08', parent: 'CPL08', name: 'BK14' },
        { id: 'BK16-CPL08', parent: 'CPL08', name: 'BK16' },
        { id: 'BK17-CPL08', parent: 'CPL08', name: 'BK17' },

        { id: 'MK08-BK05-CPL07', parent: 'BK05-CPL08', name: 'MK08', value:1 },
        { id: 'MK09-BK05-CPL07', parent: 'BK05-CPL08', name: 'MK09', value:1 },
        { id: 'MK33-BK05-CPL07', parent: 'BK05-CPL08', name: 'MK33', value:1 },
        { id: 'MK35-BK07-CPL07', parent: 'BK07-CPL08', name: 'MK35', value:1 },
        { id: 'MK20-BK07-CPL07', parent: 'BK07-CPL08', name: 'MK20', value:1 },
        { id: 'MK35-BK08-CPL07', parent: 'BK08-CPL08', name: 'MK35', value:1 },
        { id: 'MK09-BK10-CPL07', parent: 'BK10-CPL08', name: 'MK09', value:1 },
        { id: 'MK08-BK10-CPL07', parent: 'BK10-CPL08', name: 'MK08', value:1 },
        { id: 'MK05-BK12-CPL07', parent: 'BK12-CPL08', name: 'MK05', value:1 },
        { id: 'MK06-BK12-CPL07', parent: 'BK12-CPL08', name: 'MK06', value:1 },
        { id: 'MK10-BK13-CPL07', parent: 'BK13-CPL08', name: 'MK10', value:1 },
        { id: 'MK14-BK13-CPL07', parent: 'BK13-CPL08', name: 'MK14', value:1 },
        { id: 'MK14-BK14-CPL07', parent: 'BK14-CPL08', name: 'MK14', value:1 },
        { id: 'MK19-BK16-CPL07', parent: 'BK16-CPL08', name: 'MK19', value:1 },
        { id: 'MK36-BK17-CPL07', parent: 'BK17-CPL08', name: 'MK36', value:1 },

        // CPL 09
        { id: 'CPL09', parent: 'CPL', name: 'CPL09' },

        { id: 'BK04-CPL09', parent: 'CPL09', name: 'BK04' },
        { id: 'BK05-CPL09', parent: 'CPL09', name: 'BK05' },
        { id: 'BK09-CPL09', parent: 'CPL09', name: 'BK09' },
        { id: 'BK10-CPL09', parent: 'CPL09', name: 'BK10' },
        { id: 'BK16-CPL09', parent: 'CPL09', name: 'BK16' },

        { id: 'MK24-BK04-CPL07', parent: 'BK04-CPL09', name: 'MK24', value:1 },
        { id: 'MK08-BK05-CPL07', parent: 'BK05-CPL09', name: 'MK08', value:1 },
        { id: 'MK09-BK05-CPL07', parent: 'BK05-CPL09', name: 'MK09', value:1 },
        { id: 'MK24-BK09-CPL07', parent: 'BK09-CPL09', name: 'MK24', value:1 },
        { id: 'MK24-BK10-CPL07', parent: 'BK10-CPL09', name: 'MK24', value:1 },
        { id: 'MK08-BK10-CPL07', parent: 'BK10-CPL09', name: 'MK08', value:1 },
        { id: 'MK13-BK16-CPL07', parent: 'BK16-CPL09', name: 'MK13', value:1 },

        // CPL 10
        { id: 'CPL10', parent: 'CPL', name: 'CPL10' },

        { id: 'BK10-CPL10', parent: 'CPL10', name: 'BK10' },
        { id: 'BK15-CPL10', parent: 'CPL10', name: 'BK15' },
        { id: 'BK16-CPL10', parent: 'CPL10', name: 'BK16' },

        { id: 'MK09-BK10-CPL07', parent: 'BK10-CPL10', name: 'MK09', value:1 },
        { id: 'MK19-BK15-CPL07', parent: 'BK15-CPL10', name: 'MK19', value:1 },
        { id: 'MK19-BK16-CPL07', parent: 'BK16-CPL10', name: 'MK19', value:1 },
    ]

        Highcharts.chart('CPL-BK-MK', {
            chart: {
                height: '50%'
            },

            // Let the center circle be transparent
            colors: ['transparent'].concat(Highcharts.getOptions().colors),

            title: {
                text: 'CPL-BK-MK'
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
