@extends(auth()->user()->otoritas->otoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
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

    <h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL-CPMK-MK</h3>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">List Pemetaan CPL-CPMK-MK</h4>
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-hover ">
                        <thead style="position: sticky; top: 0;  z-index: 10;" class="bg-light">
                            <tr>
                                <th>CPL</th>
                                <th>Deskripsi CPL</th>
                                <th>CPMK</th>
                                <th>Deskripsi CPMK</th>
                                <th>Kode MK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpls as $cpl)
                                @php
                                    $rowspanCPL = $cpl->cpmk->count() ?: 1;
                                @endphp
                                <tr>
                                    <td rowspan="{{ $rowspanCPL }}">{{ $cpl->kode }}</td>
                                    <td style="word-wrap:break-word; white-space:normal;" rowspan="{{ $rowspanCPL }}">
                                        {{ $cpl->judul }}</td>
                                    @foreach ($cpl->cpmk as $indexCpmk => $cpmk)
                                        @if ($indexCpmk > 0)
                                <tr>
                            @endif
                            <td>{{ $cpmk->kode }}</td>
                            <td style="word-wrap:break-word; white-space:normal;">{{ $cpmk->judul }}</td>
                            <td style="word-wrap:break-word; white-space:normal;">
                                @foreach ($cpmk->mks as $mk)
                                    @if ($cpl->mk->contains('kode', $mk->kode))
                                        {{ $mk->kode }},
                                    @endif
                                @endforeach
                            </td>
                            @if ($indexCpmk < $cpl->cpmk->count() - 1)
                                </tr>
                            @endif
                            @endforeach
                            @if ($cpl->cpmk->isEmpty())
                                <td colspan="3">Tidak ada CPMK terkait</td>
                            @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
