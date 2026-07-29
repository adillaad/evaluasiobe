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

    <h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL-PL</h3>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div id="CPL-PL"></div>
            </div>
        </div>
    </div>

{{-- CPL-PL --}}
<script>
    const data = [{
            id: 'CPL',
            parent: '',
            name: 'CPL-PL'
        },

        // CPL01
        {
            id: 'CPL01',
            parent: 'CPL',
            name: 'CPL01'
        },

        {
            id: 'PL03-CPL01',
            parent: 'CPL01',
            name: 'PL03',
            value: 1
        },
        {
            id: 'PL04-CPL01',
            parent: 'CPL01',
            name: 'PL04',
            value: 1
        },

        // CPL02
        {
            id: 'CPL02',
            parent: 'CPL',
            name: 'CPL02'
        },

        {
            id: 'PL01-CPL02',
            parent: 'CPL02',
            name: 'PL01',
            value: 1
        },
        {
            id: 'PL02-CPL02',
            parent: 'CPL02',
            name: 'PL02',
            value: 1
        },
        {
            id: 'PL03-CPL02',
            parent: 'CPL02',
            name: 'PL03',
            value: 1
        },

        // CPL03
        {
            id: 'CPL03',
            parent: 'CPL',
            name: 'CPL03'
        },

        {
            id: 'PL01-CPL03',
            parent: 'CPL03',
            name: 'PL01',
            value: 1
        },
        {
            id: 'PL04-CPL03',
            parent: 'CPL03',
            name: 'PL04',
            value: 1
        },

        // CPL04
        {
            id: 'CPL04',
            parent: 'CPL',
            name: 'CPL04'
        },

        {
            id: 'PL01-CPL04',
            parent: 'CPL04',
            name: 'PL01',
            value: 1
        },

        // CPL05
        {
            id: 'CPL05',
            parent: 'CPL',
            name: 'CPL05'
        },

        {
            id: 'PL01-CPL05',
            parent: 'CPL05',
            name: 'PL01',
            value: 1
        },
        {
            id: 'PL04-CPL05',
            parent: 'CPL05',
            name: 'PL04',
            value: 1
        },

        // CPL06
        {
            id: 'CPL06',
            parent: 'CPL',
            name: 'CPL06'
        },

        {
            id: 'PL03-CPL06',
            parent: 'CPL06',
            name: 'PL03',
            value: 1
        },

        // CPL07
        {
            id: 'CPL07',
            parent: 'CPL',
            name: 'CPL07'
        },

        {
            id: 'PL01-CPL07',
            parent: 'CPL07',
            name: 'PL01',
            value: 1
        },
        {
            id: 'PL02-CPL07',
            parent: 'CPL07',
            name: 'PL02',
            value: 1
        },
        {
            id: 'PL04-CPL07',
            parent: 'CPL07',
            name: 'PL04',
            value: 1
        },

        // CPL08
        {
            id: 'CPL08',
            parent: 'CPL',
            name: 'CPL08'
        },

        {
            id: 'PL02-CPL08',
            parent: 'CPL08',
            name: 'PL02',
            value: 1
        },
        {
            id: 'PL04-CPL08',
            parent: 'CPL08',
            name: 'PL04',
            value: 1
        },

        // CPL09
        {
            id: 'CPL09',
            parent: 'CPL',
            name: 'CPL09'
        },

        {
            id: 'PL02-CPL09',
            parent: 'CPL09',
            name: 'PL02',
            value: 1
        },
        {
            id: 'PL04-CPL09',
            parent: 'CPL09',
            name: 'PL04',
            value: 1
        },

        // CPL10
        {
            id: 'CPL10',
            parent: 'CPL',
            name: 'CPL10'
        },

        {
            id: 'PL02-CPL10',
            parent: 'CPL10',
            name: 'PL02',
            value: 1
        },
        {
            id: 'PL03-CPL10',
            parent: 'CPL10',
            name: 'PL03',
            value: 1
        },
    ]

    Highcharts.chart('CPL-PL', {
        chart: {
            height: '50%'
        },

        // Let the center circle be transparent
        colors: ['transparent'].concat(Highcharts.getOptions().colors),

        title: {
            text: 'CPL-PL'
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