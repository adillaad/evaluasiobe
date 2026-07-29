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
    <h3 class="px-4 pb-4 fw-bold text-center">Halaman CPL Program Studi</h3>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">CPL Program Studi</h4>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                    :kurikulums="$kurikulums"
                    :showKurikulum="true"
                />
                @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                <a href="{{ route($currentPrefix . 'cpl.create') }}" class="btn btn-primary">Tambah
                    CPL Prodi</a>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode CPL</th>
                                <th>Deskripsi CPL</th>
                                @if ($userOtoritas != 'Penjamin Mutu Program Studi')
                                    <th>Prodi</th>
                                @endif
                                @if (!in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi']))
                                    <th>Fakultas</th>
                                @endif
                                @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpls as $cpl)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $cpl->kode }}</td>
                                    <td>{{ $cpl->judul }}</td>
                                    @if ($userOtoritas != 'Penjamin Mutu Program Studi')
                                        <td>{{ $cpl->prodi->nama }}</td>
                                    @endif
                                    @if (!in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi']))
                                        <td>{{ $cpl->prodi->fakultas->nama }}</td>
                                    @endif
                                    @if (in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route($currentPrefix . 'cpl.edit', encrypt($cpl->id)) }}"
                                                class="btn btn-warning btn-icon-text p-2 me-2">
                                                Edit
                                                <i class="ti-pencil btn-icon-append"></i>
                                            </a>
                                            <form
                                                action="{{ route($currentPrefix . 'cpl.delete', encrypt($cpl->id)) }}"
                                                method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-danger btn-icon-text p-2"
                                                    onclick="return confirm('Hapus CPL {{ $cpl->kode }}?')">
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
