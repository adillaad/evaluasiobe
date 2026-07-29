{{-- @php
    $currentPrefix = auth()->user()->otoritas->otoritas
        ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
        : 'admin.';
@endphp --}}
@extends('dosen.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Kriteria Penilaian</h4>
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kriteria</th>
                                @if ($userOtoritas == 'Dosen')
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jenis as $jenis)
                                <tr>
                                    <td class="py-4">{{ $loop->iteration }}</td>
                                    <td>{{ $jenis->nama_kriteria }}</td>
                                    @if ($userOtoritas == 'Dosen')
                                        <td class="py-4 d-flex">
                                            <form action="{{ route($currentPrefix . 'Jenis-delete', ['id' => $jenis->id]) }}"
                                                method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-danger btn-icon-text p-2 me-2"
                                                    onclick="return confirm('Are you sure to delete ?')">
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
@endsection
