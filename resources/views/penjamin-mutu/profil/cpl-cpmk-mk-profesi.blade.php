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

    @php
        $otoritas = auth()->user()->otoritas->otoritas;

        if ($otoritas === 'Kepala Program Studi') {
            $pdfRoute = 'kepala-program-studi.generate-pdf-cpl-cpmk-mk-profesi';
            $printRoute = 'kepala-program-studi.print-cpl-cpmk-mk-profesi';
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Dosen'])) {
            $pdfRoute = 'penjamin-mutu.program-studi.generate-pdf-cpl-cpmk-mk-profesi';
            $printRoute = 'penjamin-mutu.program-studi.print-cpl-cpmk-mk-profesi';
        } else {
            $pdfRoute = null;
            $printRoute = null;
        }
    @endphp

    @if ($pdfRoute || $printRoute)
        <div class="text-start px-4 mb-3 d-flex gap-2 flex-wrap">
            @if ($pdfRoute)
                <a href="{{ route($pdfRoute) }}" class="btn btn-danger btn-icon-text" target="_blank">
                    <i class="ti-file me-1"></i> Unduh PDF
                </a>
            @endif

            @if ($printRoute)
                <a href="{{ route($printRoute) }}" class="btn btn-info btn-icon-text" target="_blank">
                    <i class="ti-printer me-1"></i> Cetak
                </a>
            @endif
        </div>
    @endif



    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">List Pemetaan CPL-CPMK-MK-Profesi</h4>
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-hover dataTable">
                        <thead style="position: sticky; top: 0; z-index: 10;" class="bg-light">
                            <tr>
                                <th>CPL</th>
                                <th>Deskripsi CPL</th>
                                <th>CPMK</th>
                                <th>Deskripsi CPMK</th>
                                <th>Kode MK</th>
                                <th>Profesi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpls as $cpl)
                                @php
                                    // Hitung total baris untuk CPL (berdasarkan CPMK dan profesi di dalamnya)
                                    $totalRowsForCpl = 0;
                                    foreach ($cpl->cpmk as $cpmk) {
                                        $totalRowsForCpl += max($cpmk->profesis->count(), 1);
                                    }
                                    $totalRowsForCpl = max($totalRowsForCpl, 1);
                                @endphp
                                <tr>
                                    <td rowspan="{{ $totalRowsForCpl }}">{{ $cpl->kode }}</td>
                                    <td style="word-wrap:break-word; white-space:normal;" rowspan="{{ $totalRowsForCpl }}">
                                        {{ $cpl->judul }}</td>
                                    @php $cpmkIndex = 0; @endphp
                                    @foreach ($cpl->cpmk as $cpmk)
                                        @php
                                            $rowspanCpmk = max($cpmk->profesis->count(), 1);
                                            $mkList = '';
                                            foreach ($cpmk->mks as $mk) {
                                                if ($cpl->mk->contains('kode', $mk->kode)) {
                                                    $mkList .= $mk->kode . ', ';
                                                }
                                            }
                                            $mkList = rtrim($mkList, ', ');
                                        @endphp
                                        @if ($cpmkIndex > 0)
                                <tr>
                            @endif
                            <td rowspan="{{ $rowspanCpmk }}">{{ $cpmk->kode }}</td>
                            <td style="word-wrap:break-word; white-space:normal;" rowspan="{{ $rowspanCpmk }}">
                                {{ $cpmk->judul }}</td>
                            <td style="word-wrap:break-word; white-space:normal;" rowspan="{{ $rowspanCpmk }}">
                                {{ $mkList ?: 'Tidak ada MK terkait' }}</td>
                            @if ($cpmk->profesis->isNotEmpty())
                                @foreach ($cpmk->profesis as $indexProfesi => $profesi)
                                    @if ($indexProfesi > 0)
                                        <tr>
                                    @endif
                                    <td>{{ $profesi->nama }}</td>
                                    @if ($indexProfesi < $cpmk->profesis->count() - 1)
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <td>Tidak ada profesi terkait</td>
                            @endif
                            @php $cpmkIndex++; @endphp
                            @if ($cpmkIndex < $cpl->cpmk->count())
                                </tr>
                            @endif
                            @endforeach
                            @if ($cpl->cpmk->isEmpty())
                                <td colspan="4">Tidak ada CPMK terkait</td>
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
