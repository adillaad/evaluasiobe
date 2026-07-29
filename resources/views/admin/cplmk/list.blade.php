{{-- @php
    $routePrefix = [
        'admin' => ['prefix' => 'admin'],
        'Admin Universitas' => ['prefix' => 'admin-universitas.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'admin.';
@endphp --}}

@extends('admin.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar CPLMK</h4>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                />
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead>
                            <tr class="bg-light">
                                <th>No</th>
                                <th>Kode MK</th>
                                <th>Nama MK</th>
                                <th>Tanggal</th>
                                <th>Kode CPL</th>
                                <th>Prodi</th>
                                <th>Fakultas</th>
                                @if ($userOtoritas != 'Admin Universitas')
                                    <th>Universitas</th>
                                @endif
                                @if ($userOtoritas != 'Admin')
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cplmks as $cplmk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $cplmk->mk_kode }}</td>
                                    <td>{{ $cplmk->mk->nama }}</td>
                                    <td>{{ date('d-m-Y', strtotime($cplmk->created_at)) }}</td>
                                    <td>{{ $cplmk->cpl->kode }}</td>
                                    <td>{{ $cplmk->prodi->nama }}</td>
                                    <td>{{ $cplmk->prodi->fakultas->nama }}</td>
                                    @if ($userOtoritas != 'Admin Universitas')
                                        <td>{{ $cplmk->prodi->fakultas->universitas->nama }}</td>
                                    @endif
                                    @if ($userOtoritas != 'Admin')
                                    <td class="d-flex">
                                        <form action="{{ route($currentPrefix . 'delete-cplmk', $cplmk->id) }}"
                                            method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-danger btn-icon-text p-2 me-2"
                                                onclick="return confirm('Are you sure to delete CPL {{ $cplmk->cpl->kode }} from CPLMK {{ $cplmk->kode_mk }} ?')">
                                                Delete
                                                <i class="ti-trash btn-icon-append"></i>
                                            </button>
                                        </form>
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
