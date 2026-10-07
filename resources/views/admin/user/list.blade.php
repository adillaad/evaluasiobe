@extends('admin.template')
@section('content')
    <style>
        .btn-outline-primary {
            color: #0284c7 !important;
            border-color: #0284c7 !important;
            background-color: #ffffff !important;
        }
        .btn-outline-primary i {
            color: #0284c7 !important;
            transition: color 0.2s ease;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            background-color: #0284c7 !important;
            border-color: #0284c7 !important;
            color: #ffffff !important;
        }
        .btn-outline-primary:hover i, .btn-outline-primary:focus i, .btn-outline-primary:active i {
            color: #ffffff !important;
        }

        .btn-outline-danger {
            color: #dc3545 !important;
            border-color: #dc3545 !important;
            background-color: #ffffff !important;
        }
        .btn-outline-danger i {
            color: #dc3545 !important;
            transition: color 0.2s ease;
        }
        .btn-outline-danger:hover, .btn-outline-danger:focus, .btn-outline-danger:active {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
        }
        .btn-outline-danger:hover i, .btn-outline-danger:focus i, .btn-outline-danger:active i {
            color: #ffffff !important;
        }
    </style>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end align-items-center mb-3">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary text-white btn-icon-text" data-bs-toggle="modal" data-bs-target="#tambahUserModal">
                            <i class="ti-plus me-1"></i> Tambah User
                        </button>
                        <button type="button" class="btn btn-success text-white btn-icon-text" data-bs-toggle="modal" data-bs-target="#importDosenModal">
                            <i class="ti-import me-1"></i> Import Excel
                        </button>
                    </div>
                </div>
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
                                $currentViewProdiId = request('prodi_id') ?? auth()->user()->id_prodiUser;
                                $rankedRoles = ['Admin','Admin Universitas','Wakil Rektor','Wakil Dekan','Dosen','Kepala Program Studi','Penjamin Mutu Universitas','Penjamin Mutu Fakultas','Penjamin Mutu Program Studi'];

                                $sortedUsers = $users->sortBy([
                                    fn($user) => $user->universitas->nama ?? '', // Sorting berdasarkan Universitas
                                    fn($user) => $user->fakultas->nama ?? '', // Sorting berdasarkan Fakultas
                                    fn($user) => $user->prodi->nama ?? '', // Sorting berdasarkan Program Studi
                                    function ($user) use ($rankedRoles, $currentViewProdiId) {
                                        // Sorting berdasarkan otoritas konteks prodi
                                        $userOtoritasStr = $user->getOtoritasDisplayForProdi($currentViewProdiId);
                                        $userRoles = array_map('trim', explode(',', $userOtoritasStr));
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
                                            {{ $user->getOtoritasDisplayForProdi($currentViewProdiId) }}
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
                                         <form action="reset-user/{{ encrypt($user->id) }}" method="post" class="d-inline m-0 p-0">
                                             @csrf
                                             @method('put')
                                             <button type="submit" class="btn btn-info btn-icons"
                                                 data-bs-toggle="tooltip" data-bs-placement="top" title="Reset Password"
                                                 onclick="return confirm('Are you sure to reset password {{ $user->name }}?')">
                                                 <i class="ti-reload"></i>
                                             </button>
                                         </form>
                                     </td>
                                     <td>
                                         <div class="d-flex align-items-center gap-1">
                                             <a href="edit-user/{{ encrypt($user->id) }}"
                                                 class="btn btn-outline-primary btn-icons" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                 <i class="ti-pencil"></i>
                                             </a>
                                             <form action="delete-user/{{ encrypt($user->id) }}" method="post" class="d-inline m-0 p-0">
                                                 @csrf
                                                 @method('delete')
                                                 <button type="submit" class="btn btn-outline-danger btn-icons"
                                                     data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                     onclick="return confirm('Are you sure to delete {{ $user->name }}?')">
                                                      <i class="ti-trash"></i>
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

    <!-- Modal Import Dosen -->
    <div class="modal fade" id="importDosenModal" tabindex="-1" aria-labelledby="importDosenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ Route::has(($currentPrefix ?? '') . 'import-dosen') ? route(($currentPrefix ?? '') . 'import-dosen') : route('dosen.import.global') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importDosenModalLabel">Import Data Dosen (Excel)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2" role="alert" style="font-size: 13px;">
                            <i class="ti-info-alt me-1"></i> Format Excel: Nama, Email, Otoritas, Program Studi, Password.<br>
                            Contoh Otoritas: <strong>Dosen</strong> atau <strong>Dosen, Kepala Program Studi</strong> (pisahkan dengan koma jika lebih dari 1).<br>
                            Password default otomatis: <strong>Unilajaya!</strong> (bisa dikosongkan pada Excel).
                            <a href="{{ Route::has(($currentPrefix ?? '') . 'template-dosen-excel') ? route(($currentPrefix ?? '') . 'template-dosen-excel') : route('dosen.template-excel.global') }}" class="fw-bold text-decoration-underline ms-1 d-block mt-1">
                                Download Template Excel Dosen
                            </a>
                        </div>
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Pilih File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Tambah User -->
    <div class="modal fade" id="tambahUserModal" tabindex="-1" aria-labelledby="tambahUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-start">
                <form method="POST" action="{{ Route::has(($currentPrefix ?? '') . 'store-user') ? route(($currentPrefix ?? '') . 'store-user') : route('admin.store-user') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahUserModalLabel">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger py-2 small mb-3">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                                @error('name') <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}" required>
                                @error('email') <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="new-password" required>
                                @error('password') <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
                            </div>
                        </div>

                        @php $userOtoritas = auth()->user()->otoritas->otoritas ?? ''; @endphp
                        <div class="mb-3">
                            <label class="form-label">Otoritas <span class="text-danger">*</span></label>
                            <select class="form-select w-100" name="otoritas[]" required>
                                @if (in_array($userOtoritas, ['Admin', 'Admin Universitas', 'Penjamin Mutu Universitas']))
                                    <option value="Wakil Rektor">Wakil Rektor</option>
                                    <option value="Wakil Dekan">Wakil Dekan</option>
                                    <option value="Penjamin Mutu Fakultas">Penjamin Mutu Fakultas</option>
                                    <option value="Penjamin Mutu Program Studi">Penjamin Mutu Program Studi</option>
                                    <option value="Kepala Program Studi">Kepala Program Studi</option>
                                    <option value="Dosen" selected>Dosen</option>
                                @elseif ($userOtoritas == 'Penjamin Mutu Fakultas')
                                    <option value="Wakil Dekan">Wakil Dekan</option>
                                    <option value="Penjamin Mutu Program Studi">Penjamin Mutu Program Studi</option>
                                    <option value="Kepala Program Studi">Kepala Program Studi</option>
                                    <option value="Dosen" selected>Dosen</option>
                                @elseif (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                                    <option value="Kepala Program Studi">Kepala Program Studi</option>
                                    <option value="Dosen" selected>Dosen</option>
                                @else
                                    <option value="Dosen" selected>Dosen</option>
                                @endif
                            </select>
                            @error('otoritas') <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div> @enderror
                        </div>

                        {{-- UNIVERSITAS --}}
                        <div class="mb-3">
                            <label class="form-label">Universitas</label>
                            <input type="hidden" name="universitas" value="{{ auth()->user()->id_universitasUser ?? 1 }}">
                            <input type="text" class="form-control" value="{{ auth()->user()->universitas->nama ?? '-' }}" readonly>
                        </div>

                        {{-- FAKULTAS --}}
                        <div class="mb-3">
                            <label class="form-label">Fakultas <span class="text-danger">*</span></label>
                            @php 
                                $isFakultasDisabled = in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi', 'Kepala Program Studi']); 
                                $userFakultasId = auth()->user()->id_fakultasUser ?? auth()->user()->prodi?->id_fakultas ?? auth()->user()->prodis->first()?->id_fakultas;
                            @endphp
                            @if ($isFakultasDisabled)
                                <input type="hidden" name="fakultas" value="{{ $userFakultasId }}">
                            @endif
                            <select class="form-select w-100" name="{{ $isFakultasDisabled ? '' : 'fakultas' }}" {{ $isFakultasDisabled ? 'disabled' : '' }}>
                                @foreach ($faculties ?? ($fakultas ?? []) as $f)
                                    <option value="{{ $f->id }}" {{ old('fakultas', $userFakultasId) == $f->id ? 'selected' : '' }}>
                                        {{ $f->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fakultas') <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div> @enderror
                        </div>

                        {{-- PRODI --}}
                        <div class="mb-3">
                            <label class="form-label">Prodi <span class="text-danger">*</span></label>
                            @php 
                                $isProdiLocked = in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']); 
                                $userProdiId = auth()->user()->id_prodiUser ?? auth()->user()->prodis->first()?->id;
                            @endphp
                            @if ($isProdiLocked)
                                <input type="hidden" name="prodi[]" value="{{ $userProdiId }}">
                            @endif
                            <select class="form-select w-100" name="{{ $isProdiLocked ? '' : 'prodi[]' }}" {{ $isProdiLocked ? 'disabled' : '' }}>
                                @if ($isProdiLocked)
                                    <option value="{{ $userProdiId }}" selected>{{ auth()->user()->prodi->nama ?? auth()->user()->prodis->first()?->nama ?? '-' }}</option>
                                @else
                                    @foreach($programs ?? ($prodi ?? []) as $p)
                                        <option value="{{ $p->id }}" {{ (is_array(old('prodi')) && in_array($p->id, old('prodi'))) || $loop->first ? 'selected' : '' }}>
                                            {{ $p->nama }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('prodi') <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile Picture (Opsional)</label>
                            <input type="file" accept="image/png, image/jpeg" name="img" class="form-control">
                            @error('img') <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Register User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
