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

    <h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL-CPMK-MK-Semester</h3>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                {{-- <div id="CPL-CPMK-MK-Semester"></div> --}}
                <h4 class="card-title">List Pemetaan CPL-CPMK-MK-Semester</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center" rowspan="2">CPL</th>
                                <th style="text-align: center" rowspan="2">CPMK</th>
                                <th style="text-align: center" colspan="{{ $semesters->count() }}">Semester</th>
                            </tr>
                            <tr>
                                @foreach ($semesters as $semester)
                                    <th style="text-align: center">{{ $semester->semester }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpls as $cpl)
                                @php
                                    $rowSpanCPL = $cpl->cpmk->count();
                                @endphp
                                <tr>
                                    <td rowspan="{{ $rowSpanCPL ?: 1 }}">{{ $cpl->kode }}</td>
                                    @if ($rowSpanCPL > 0)
                                        @foreach ($cpl->cpmk as $index => $cpmk)
                                            @if ($index > 0)
                                <tr>
                            @endif
                            <td>{{ $cpmk->kode }}</td>

                            @foreach ($semesters as $semester)
                                <td>
                                    @php
                                        $mkInSemester = $cpmk->mks->where('semester', $semester->semester);
                                    @endphp
                                    @if ($mkInSemester->count())
                                        @foreach ($mkInSemester as $mk)
                                            @if ($cpl->mk->contains('kode', $mk->kode))
                                                {{ $mk->kode }},
                                            @endif
                                        @endforeach
                                        {{-- {{ $mkInSemester->pluck('kode')->implode(', ') }} --}}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach
                            @if ($index > 0)
                                </tr>
                            @endif
                            @endforeach
                        @else
                            <td colspan="{{ 2 + count($semesters) }}">CPMK tidak tersedia</td>
                            @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Deskripsi</h4>
                <div class="scrollable-descriptions" style="max-height: 400px; overflow-y: auto;">
                    @foreach ($cpls as $cpl)
                        <div class="mb-3">
                            <h5><strong>{{ $cpl->kode }} - {{ $cpl->judul }}</strong></h5>
                            <p><strong>CPMK:</strong></p>
                            <ul>
                                @forelse ($cpl->cpmk as $cpmk)
                                    <li>
                                        {{ $cpmk->kode }} - {{ $cpmk->judul }}
                                        <ul>
                                            <li><strong>MK Terkait:</strong>
                                                @if ($cpmk->mks->count())
                                                    @foreach ($cpmk->mks as $mk)
                                                        {{ $mk->kode }} - {{ $mk->nama }} ,
                                                    @endforeach
                                                @else
                                                    Tidak ada mata kuliah terkait
                                                @endif
                                            </li>
                                        </ul>
                                    </li>
                                @empty
                                    <li>CPMK tidak tersedia</li>
                                @endforelse
                            </ul>
                            <hr>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable({
                "aaSorting": []
            });
        });
    </script>
@endsection
