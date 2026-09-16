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
            <div id="MK"></div>
        </div>
    </div>
</div>

{{-- MK --}}
<script>
Highcharts.chart('MK', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'MK'
    },
    xAxis: {
        categories: ['MK01', 'MK02', 'MK03', 'MK04', 'MK05', 'MK06', 'MK07', 'MK08', 'MK09', 'MK10'],
        crosshair: true,
        accessibility: {
            description: 'MK'
        }
    },
    yAxis: {
        min: 0,
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
            name: 'SKS',
            data: [2, 2, 3, 4, 4, 3, 3, 3, 3, 3]
        },
        {
            name: 'Semester',
            data: [1, 1, 3, 3, 2, 1, 6, 4, 5, 1]
        },
    ]
});
</script>
@endsection
