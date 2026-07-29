@extends('admin.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar User</h4>
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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Otoritas</th>
                                <th>Program Studi</th>
                                <th>Fakultas</th>
                                @if (auth()->user()->otoritas->otoritas != 'Admin Universitas')
                                    <th>Universitas</th>
                                @endif
                                <th>Password</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $rankedRoles = ['Admin','Admin Universitas','Wakil Rektor','Wakil Dekan','Dosen','Kepala Program Studi','Penjamin Mutu Universitas','Penjamin Mutu Fakultas','Penjamin Mutu Program Studi'];

                                $sortedUsers = $users->sortBy([
                                    fn($user) => $user->universitas->nama ?? '', // Sorting berdasarkan Universitas
                                    fn($user) => $user->fakultas->nama ?? '', // Sorting berdasarkan Fakultas
                                    fn($user) => $user->prodi->nama ?? '', // Sorting berdasarkan Program Studi
                                    function ($user) use ($rankedRoles) {
                                        // Sorting berdasarkan otoritas
                                        $userRoles = $user->otoritas()->pluck('otoritas')->toArray();
                                        foreach ($rankedRoles as $index => $role) {
                                            if (in_array($role, $userRoles)) {
                                                return $index;
                                            }
                                        }
                                        return count($rankedRoles); // Jika tidak ditemukan, letakkan di akhir
                                    },
                                ]);
                            @endphp
                            @foreach ($sortedUsers as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <div class="text-wrap lh-base" style="width: 400px">
                                            {{ $user->otoritas()->pluck('otoritas')->implode(', ') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap lh-base" style="width: 400px">
                                            {{ $user->prodis->pluck('nama')->implode(', ') ?: '-' }}
                                        </div>
                                    </td>
                                    <td>{{ $user->fakultas?->nama ?? '-' }}</td>
                                    @if (auth()->user()->otoritas->otoritas != 'Admin Universitas')
                                        <td>{{ $user->universitas?->nama ?? '-' }}</td>
                                    @endif
                                    <td>
                                        <form action="reset-user/{{ encrypt($user->id) }}" method="post">
                                            @csrf
                                            @method('put')
                                            <button type="submit" class="btn btn-warning btn-icon-text p-2"
                                                onclick="return confirm('Are you sure to reset password {{ $user->name }}?')">
                                                <i class="ti-reload btn-icon-prepend"></i>
                                                Reset
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a type="button" href="edit-user/{{ encrypt($user->id) }}"
                                                class="btn btn-inverse-dark btn-icon-text p-2" style="margin-right:7px">
                                                Edit
                                                <i class="ti-pencil btn-icon-append"></i>
                                            </a>
                                            <form action="delete-user/{{ encrypt($user->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-danger btn-icon-text p-2"
                                                    onclick="return confirm('Are you sure to delete {{ $user->name }}?')">
                                                    Delete
                                                    <i class="ti-trash btn-icon-append"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
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
