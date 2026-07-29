{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}

@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
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

    <h3 class="px-4 pb-4 fw-bold text-center">Halaman Metode Penilaian</h3>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Metode Penilaian</h4>
                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                <a href="{{ route($currentPrefix . 'asesmen.metode-penilaian-add') }}"
                    class="btn btn-primary">Tambah Metode Penilaian</a>
                @endif
                <div class=" mt-4 table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>CPL</th>
                                <th>MK</th>
                                <th>CPMK</th>
                                @foreach ($metodes as $metodeNama)
                                    <th>{{ $metodeNama }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penilaian as $rowId => $rows)
                                @php
                                    $row = $rows[0];
                                    $cplKode = $cpls[$row->cpl_id]->kode ?? '-';
                                    $mkKode = $mks[$row->mk_kode]->kode ?? '-';
                                    $cpmkKode = $cpmks[$row->cpmk_id]->kode ?? '-';
                                @endphp
                                <tr>
                                    <td>{{ $cplKode }}</td>
                                    <td>{{ $mkKode }}</td>
                                    <td>{{ $cpmkKode }}</td>
                                    @foreach ($metodes as $metodeId => $metodeNama)
                                        <td>
                                            @php
                                                $penilaianMetode = $rows->where('metode_id', $metodeId)->first();
                                            @endphp
                                            {{ $penilaianMetode ? '✔' : '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
