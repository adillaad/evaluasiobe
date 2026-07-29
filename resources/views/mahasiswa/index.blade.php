@extends($template)
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Mahasiswa</h4>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                />
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead>
                            <tr class="bg-light">
                                <th width='10px'>#</th>
                                <th>NPM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Angkatan</th>
                                @if (in_array($userOtoritas, ['Admin', 'Admin Universitas', 'Penjamin Mutu Universitas','Penjamin Mutu Fakultas', 'Wakil Rektor', 'Wakil Dekan']))
                                    <th>Prodi</th>
                                @endif
                                @if (in_array($userOtoritas, ['Admin', 'Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor']))
                                    <th>Fakultas</th>
                                @endif
                                @if ($userOtoritas == 'Admin')
                                    <th>Universitas</th>
                                @endif
                                @if (in_array($userOtoritas, ['Dosen', 'Penjamin Mutu Program Studi']))
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mahasiswas as $mahasiswa)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mahasiswa->NPM }}</td>
                                    <td>{{ $mahasiswa->Nama }}</td>
                                    <td>{{ $mahasiswa->angkatan }}</td>
                                    @if (in_array($userOtoritas, ['Admin', 'Admin Universitas', 'Penjamin Mutu Universitas','Penjamin Mutu Fakultas', 'Wakil Rektor', 'Wakil Dekan']))
                                        <td>{{ $mahasiswa->prodi->nama }}</td>
                                    @endif
                                    @if (in_array($userOtoritas, ['Admin', 'Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor']))
                                        <td>{{ $mahasiswa->prodi->fakultas->nama }}</td>
                                    @endif
                                    @if ($userOtoritas == 'Admin')
                                        <td>{{ $mahasiswa->prodi->fakultas->universitas->nama }}</td>
                                    @endif
                                    @if ($userOtoritas == 'Dosen')
                                        <td class="py-4">
                                            <div class="d-flex">
                                                <a type="button" href="{{ route('dosen.mahasiswa.edit', $mahasiswa->id) }}"
                                                    class="btn btn-warning btn-icon-text p-2" style="margin-right:7px">
                                                    Edit
                                                    <i class="ti-pencil btn-icon-append"></i>
                                                </a>
                                                <form action="{{ route('dosen.mahasiswa.destroy', $mahasiswa->id) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger btn-icon-text p-2"
                                                        onclick="return confirm('Are you sure to delete {{ $mahasiswa->nama }}?')">
                                                        Delete
                                                        <i class="ti-trash btn-icon-append"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @elseif($userOtoritas == 'Penjamin Mutu Program Studi')
                                        <td class="py-4">
                                            <div class="d-flex">
                                                <a type="button"
                                                    href="{{ route('penjamin-mutu.program-studi.mahasiswa.edit', $mahasiswa->id) }}"
                                                    class="btn btn-warning btn-icon-text p-2" style="margin-right:7px">
                                                    Edit
                                                    <i class="ti-pencil btn-icon-append"></i>
                                                </a>
                                                <form
                                                    action="{{ route('penjamin-mutu.program-studi.mahasiswa.destroy', $mahasiswa->id) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger btn-icon-text p-2"
                                                        onclick="return confirm('Are you sure to delete {{ $mahasiswa->nama }}?')">
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
