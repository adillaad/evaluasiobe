@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Bobot Penilaian</h4>
            <div class="table-responsive">
                <!-- <table class="table table-hover" >
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
                                    {{ $penilaianMetode ? $penilaianMetode->bobot : '-' }}
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table> -->
                <table class="table table-hover" >
                    <thead class="bg-light">
                        <tr>
                            <th>MK</th>
                            <th>CPL</th>
                            <th>CPMK</th>
                            @foreach ($metodes as $metodeNama)
                                <th>{{ $metodeNama }}</th>
                            @endforeach
                        </tr>
                    </thead>
                        <tbody>
                            @foreach ($penilaian->groupBy('0.mk_kode') as $mkKode => $mkRows)
                                @php $firstMk = true; @endphp
                                {{-- ambil kombinasi CPL-CPMK unik --}}
                                @php
                                    $uniqueRows = collect($mkRows)->flatMap(fn($rows) => $rows)
                                                    ->unique(fn($row) => $row->cpl_id.'-'.$row->cpmk_id);
                                @endphp
                                @foreach ($uniqueRows as $i => $row)
                                    @php
                                        $cplKode = $cpls[$row->cpl_id]->kode ?? '-';
                                        $cpmkKode = $cpmks[$row->cpmk_id]->kode ?? '-';
                                    @endphp
                                    <tr>
                                        {{-- tampilkan MK hanya sekali --}}
                                        @if ($firstMk && $i === 0)
                                            <td rowspan="{{ $uniqueRows->count() }}">{{ $mks[$mkKode]->kode ?? '-' }}</td>
                                            @php $firstMk = false; @endphp
                                        @endif

                                        <td>{{ $cplKode }}</td>
                                        <td>{{ $cpmkKode }}</td>

                                        @foreach ($metodes as $metodeId => $metodeNama)
                                            <td>
                                                @php
                                                    $penilaianMetode = collect($mkRows)->flatMap(fn($rows) => $rows)
                                                                        ->where('cpl_id', $row->cpl_id)
                                                                        ->where('cpmk_id', $row->cpmk_id)
                                                                        ->where('metode_id', $metodeId)
                                                                        ->first();
                                                @endphp
                                                {{ $penilaianMetode ? $penilaianMetode->bobot : '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
  </div>

@endsection