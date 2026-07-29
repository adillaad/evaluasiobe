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

    <h3 class="px-4 pb-4 fw-bold text-center">Halaman Pemenuhan CPL</h3>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pemenuhan CPL</h4>
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th rowspan="2">CPL</th>
                                <th colspan="{{ $maxSemester }}" style="text-align: center;">Semester</th>
                            </tr>
                            <tr>
                                @foreach (range(1, $maxSemester) as $semester)
                                    <th>{{ $semester }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpls as $cpl)
                                @php
                                    $mkGroupedBySemester = $cpl->mk->groupBy('semester');
                                @endphp
                                <tr>
                                    <td>{{ $cpl->kode }}</td>
                                    @foreach (range(1, $maxSemester) as $semester)
                                        <td>
                                            @foreach ($mkGroupedBySemester->get($semester, []) as $mk)
                                                <span
                                                    style="color: @if ($mk->rumpun === 'Wajib') darkgreen @elseif($mk->rumpun === 'Peminatan') goldenrod @elseif($mk->rumpun === 'Wajib_kurikulum') deepskyblue @else black @endif">
                                                    {{ $mk->kode }}<br>
                                                </span>
                                            @endforeach
                                        </td>
                                    @endforeach
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
                            <p><strong>{{ $cpl->kode }} :</strong> {{ $cpl->judul }}</p>
                            <p><strong>Mata Kuliah:</strong></p>
                            @if ($cpl->mk->isNotEmpty())
                                <ul>
                                    @foreach ($cpl->mk as $mk)
                                        <li> <span
                                                style="color: @if ($mk->rumpun === 'Wajib') darkgreen @elseif($mk->rumpun === 'Peminatan') goldenrod @elseif($mk->rumpun === 'Wajib_kurikulum') deepskyblue @else black @endif">{{ $mk->kode }}</span>
                                            - {{ $mk->nama }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <em>Tidak ada mata kuliah terkait.</em>
                            @endif
                            <hr>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
