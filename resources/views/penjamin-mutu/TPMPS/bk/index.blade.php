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
            <div id="BK"></div>
        </div>
    </div>
</div>

<!-- BK -->
<script>
    Highcharts.chart('BK', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'BK'
        },
        xAxis: {
            categories: ['BK01', 'BK02', 'BK03', 'BK04', 'BK05', 'BK06', 'BK07', 'BK08', 'BK09', 'BK10'],
            crosshair: true,
            accessibility: {
                description: 'BK'
            }
        },
        yAxis: {
            min: 0,
        },
        plotOptions: {
            column: {
                pointPadding: 0.1,
                borderWidth: 0
            }
        },
        series: [{
                name: 'Bobot Min',
                data: [2, 2, 2, 2, 2, 2, 2, 2, 2, 2]
            },
            {
                name: 'Bobot Max',
                data: [4, 3, 3, 4, null, 4, 4, 4, 3, 4]
            },
            {
                name: 'Bobot CS Score',
                data: [17, 6, null, null, 43, 9, 9, 7, 8, 6]
            },
            {
                name: 'Bobot Ka Score',
                data: [14, 0, null, null, 0, 23, 26, 24, 16, 23]
            }
        ]
    });
</script>
@endsection
