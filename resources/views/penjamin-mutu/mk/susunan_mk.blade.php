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
    <h3 class="px-4 pb-4 fw-bold text-center">Halaman Susunan Mata Kuliah</h3>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">List Susunan Mata Kuliah</h4>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                    :kurikulums="$kurikulums"
                    :showKurikulum="true"
                />
                @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                    <a href="{{ route($currentPrefix . 'mk.create') }}" class="btn btn-primary">Tambah MK</a>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Kode MK</th>
                                <th rowspan="2">Nama MK</th>
                                <th rowspan="2">Tahun Kurikulum</th>
                                <th rowspan="2">SKS</th>
                                <th colspan="{{ $maxSemester }}" style="text-align:center;">Semester</th>
                                @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                                    <th rowspan="2">Action</th>
                                @endif
                            </tr>
                            <tr>
                                @foreach (range(1, $maxSemester) as $semester)
                                    <th>{{ $semester }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mks as $mk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mk->kode }}</td>
                                    <td>{{ $mk->nama }}</td>
                                    <td>{{ $mk->kurikulum->tahun }}</td>
                                    <td>{{ $mk->bobot_teori + $mk->bobot_praktikum }}</td>
                                    @foreach (range(1, $maxSemester) as $semester)
                                        <td style="color:#1d3cb4; font-weight: bold;">
                                            @if ($mk->semester == $semester)
                                                ✔
                                            @endif
                                        </td>
                                    @endforeach
                                    @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                                        <td>
                                            <div class="d-flex">
                                                <a type="button" href="edit-mk/{{ $mk->kode }}"
                                                    class="btn btn-warning btn-icon-text p-2" style="margin-right:7px">
                                                    Edit
                                                    <i class="ti-pencil btn-icon-append"></i>
                                                </a>
                                                <form action="delete-mk/{{ $mk->kode }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger btn-icon-text p-2"
                                                        onclick="return confirm('Are you sure to delete {{ $mk->nama }}?')">
                                                        Delete
                                                        <i class="ti-trash btn-icon-append"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection
