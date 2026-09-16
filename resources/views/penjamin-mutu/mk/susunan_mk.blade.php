{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}

@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h4 class="card-title mb-0 me-auto">List Susunan Mata Kuliah</h4>
                    @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                        <a href="{{ route($currentPrefix . 'mk.create') }}" class="btn btn-primary btn-icon-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Tambah MK
                        </a>
                    @endif
                </div>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                    :kurikulums="$kurikulums"
                    :showKurikulum="true"
                />
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Kode MK</th>
                                <th rowspan="2">Nama MK</th>
                                <th rowspan="2">Tahun Kurikulum</th>
                                <th rowspan="2">SKS</th>
                                <th colspan="{{ max(1, (int)($maxSemester ?? 8)) }}" style="text-align:center;">Semester</th>
                                @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                                    <th rowspan="2">Action</th>
                                @endif
                            </tr>
                            <tr>
                                @foreach (range(1, max(1, (int)($maxSemester ?? 8))) as $semester)
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
                                    <td>{{ $mk->kurikulum->tahun ?? '-' }}</td>
                                    <td>{{ ($mk->bobot_teori ?? 0) + ($mk->bobot_praktikum ?? 0) }}</td>
                                    @foreach (range(1, max(1, (int)($maxSemester ?? 8))) as $semester)
                                        <td style="color:#1d3cb4; font-weight: bold;">
                                            @if ($mk->semester == $semester)
                                                ✔
                                            @endif
                                        </td>
                                    @endforeach
                                    @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <a href="edit-mk/{{ $mk->kode }}"
                                                    class="btn btn-warning btn-icons" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                    <i class="ti-pencil"></i>
                                                </a>
                                                <form action="delete-mk/{{ $mk->kode }}" method="post" class="d-inline m-0 p-0">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger btn-icons"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                        onclick="return confirm('Are you sure to delete {{ $mk->nama }}?')">
                                                        <i class="ti-trash"></i>
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
