@extends('penjamin-mutu.template')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end align-items-center mb-3">
                    <div class="d-flex gap-2">
                        @if (in_array(auth()->user()->otoritas->otoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
                            <button type="button" class="btn btn-primary text-white btn-icon-text" data-bs-toggle="modal" data-bs-target="#assignDosenModal">
                                <i class="ti-user me-1"></i> Tambah Dosen Pengampu
                            </button>
                        @endif
                        <button type="button" class="btn btn-outline-primary btn-icon-text" data-bs-toggle="modal" data-bs-target="#tambahUserModal">
                            <i class="ti-plus me-1"></i> Tambah User Baru
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
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Otoritas</th>
                                @if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                                    <th>Program Studi</th>
                                @endif
                                @if (auth()->user()->otoritas->otoritas == 'Penjamin Mutu Universitas')
                                    <th>Fakultas</th>
                                @endif
                                <th>Password</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $currentViewProdiId = request('prodi_id') ?? auth()->user()->id_prodiUser;
                                $rankedRoles = [
                                    'Admin',
                                    'Admin Universitas',
                                    'Wakil Rektor',
                                    'Wakil Dekan',
                                    'Dosen',
                                    'Kepala Program Studi',
                                    'Penjamin Mutu Universitas',
                                    'Penjamin Mutu Fakultas',
                                    'Penjamin Mutu Program Studi',
                                ];

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
                                $isKaprodiRole = in_array(auth()->user()->otoritas->otoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']);
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
                                    @if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']))
                                        <td>
                                            <div class="text-wrap lh-base" style="width: 400px">
                                                {{ $user->prodis->pluck('nama')->implode(', ') ?: '-' }}
                                            </div>
                                        </td>
                                    @endif
                                    @if (auth()->user()->otoritas->otoritas == 'Penjamin Mutu Universitas')
                                        <td>{{ $user->fakultas?->nama ?? '-' }}</td>
                                    @endif
                                     <td>
                                         <form action="reset-password/{{ encrypt($user->id) }}" method="post" class="d-inline m-0 p-0">
                                             @csrf
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
                                                 class="btn btn-warning btn-icons" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                 <i class="ti-pencil"></i>
                                             </a>
                                             <form action="delete-user/{{ encrypt($user->id) }}" method="post" class="d-inline m-0 p-0">
                                                 @csrf
                                                 @method('delete')
                                                 <button type="submit" class="btn btn-danger btn-icons"
                                                     data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $isKaprodiRole ? 'Keluarkan dari Prodi' : 'Hapus' }}"
                                                     onclick="return confirm('{{ $isKaprodiRole ? 'Apakah Anda yakin ingin mengeluarkan dosen ' . addslashes($user->name) . ' dari prodi ini?' : 'Are you sure to delete ' . addslashes($user->name) . '?' }}')">
                                                     <i class="{{ $isKaprodiRole ? 'ti-user-minus' : 'ti-trash' }}"></i>
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
                                Download Template Excel
                            </a>
                        </div>
                        <div class="mb-3">
                            <label for="excel_file_dosen" class="form-label">Pilih File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" class="form-control" id="excel_file_dosen" name="excel_file" accept=".xlsx,.xls,.csv" required>
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
                            <input type="hidden" name="universitas" value="{{ auth()->user()->id_universitasUser }}">
                            <input type="text" class="form-control" value="{{ auth()->user()->universitas->nama ?? '-' }}" readonly>
                        </div>

                        {{-- FAKULTAS --}}
                        <div class="mb-3">
                            <label class="form-label">Fakultas <span class="text-danger">*</span></label>
                            @php $isFakultasDisabled = in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi', 'Kepala Program Studi']); @endphp
                            @if ($isFakultasDisabled)
                                <input type="hidden" name="fakultas" value="{{ auth()->user()->id_fakultasUser }}">
                            @endif
                            <select class="form-select w-100" name="{{ $isFakultasDisabled ? '' : 'fakultas' }}" {{ $isFakultasDisabled ? 'disabled' : '' }}>
                                @foreach ($faculties ?? ($fakultas ?? []) as $f)
                                    <option value="{{ $f->id }}" {{ old('fakultas', $isFakultasDisabled ? auth()->user()->id_fakultasUser : '') == $f->id ? 'selected' : '' }}>
                                        {{ $f->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fakultas') <div class="alert alert-danger mt-1 py-1 small">{{ $message }}</div> @enderror
                        </div>

                        {{-- PRODI --}}
                        <div class="mb-3">
                            <label class="form-label">Prodi <span class="text-danger">*</span></label>
                            @php $isProdiLocked = in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']); @endphp
                            @if ($isProdiLocked)
                                <input type="hidden" name="prodi[]" value="{{ auth()->user()->id_prodiUser }}">
                            @endif
                            <select class="form-select w-100" name="{{ $isProdiLocked ? '' : 'prodi[]' }}" {{ $isProdiLocked ? 'disabled' : 'multiple' }}>
                                @if ($isProdiLocked)
                                    <option value="{{ auth()->user()->id_prodiUser }}" selected>{{ auth()->user()->prodi->nama ?? '-' }}</option>
                                @else
                                    @foreach($programs ?? ($prodi ?? []) as $p)
                                        <option value="{{ $p->id }}" {{ (is_array(old('prodi')) && in_array($p->id, old('prodi'))) ? 'selected' : '' }}>
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

    <!-- Modal Tambah Dosen Pengampu ke Prodi (Untuk Kaprodi) -->
    @if (in_array(auth()->user()->otoritas->otoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']))
        <div class="modal fade" id="assignDosenModal" tabindex="-1" aria-labelledby="assignDosenModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ Route::has(($currentPrefix ?? '') . 'assign-dosen') ? route(($currentPrefix ?? '') . 'assign-dosen') : route('penjamin-mutu.program-studi.assign-dosen') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="assignDosenModalLabel">Tambah Dosen Pengampu Prodi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info py-2" role="alert" style="font-size: 13px;">
                                <i class="ti-info-alt me-1"></i> Pilih dosen dari daftar seluruh dosen yang tersedia untuk ditambahkan ke daftar dosen pengampu prodi <strong>{{ auth()->user()->prodi?->nama }}</strong>.
                            </div>
                            <div class="mb-3">
                                <label for="select_dosen_user_id" class="form-label font-weight-bold">Pilih Dosen <span class="text-danger">*</span></label>
                                <select class="form-select" id="select_dosen_user_id" name="user_id" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    @if (isset($availableDosen) && count($availableDosen) > 0)
                                        @foreach ($availableDosen as $dosenOpt)
                                            <option value="{{ $dosenOpt->id }}">
                                                {{ $dosenOpt->name }} ({{ $dosenOpt->email }}) {{ $dosenOpt->prodi ? '- Prodi Utama: ' . $dosenOpt->prodi->nama : '' }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Semua dosen di sistem sudah terdaftar di prodi ini.</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" {{ (isset($availableDosen) && count($availableDosen) > 0) ? '' : 'disabled' }}>
                                <i class="ti-check me-1"></i> Tambahkan ke Prodi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
