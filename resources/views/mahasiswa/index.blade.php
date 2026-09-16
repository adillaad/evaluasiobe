@extends($template)
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Mahasiswa</h4>
                    <div class="d-flex gap-2">
                        @if (in_array($userOtoritas, ['Dosen', 'Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Admin', 'Admin Universitas']))
                            <button type="button" class="btn btn-primary btn-icon-text" data-bs-toggle="modal" data-bs-target="#tambahMahasiswaModal">
                                <i class="ti-plus me-1"></i> Tambah Mahasiswa
                            </button>
                        @endif
                        <button type="button" class="btn btn-success text-white btn-icon-text" data-bs-toggle="modal" data-bs-target="#importMahasiswaModal">
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
                                @if (in_array($userOtoritas, ['Dosen', 'Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Admin', 'Admin Universitas']))
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
                                    @if (in_array($userOtoritas, ['Dosen', 'Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Admin', 'Admin Universitas']))
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-warning btn-icons text-white" data-bs-toggle="modal" data-bs-target="#editMahasiswaModal_{{ $mahasiswa->id }}" title="Edit">
                                                    <i class="ti-pencil"></i>
                                                </button>
                                                <form action="{{ Route::has(($currentPrefix ?? '') . 'mahasiswa.destroy') ? route(($currentPrefix ?? '') . 'mahasiswa.destroy', $mahasiswa->id) : route('dosen.mahasiswa.destroy', $mahasiswa->id) }}"
                                                    method="post" class="d-inline m-0 p-0">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger btn-icons"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa {{ $mahasiswa->Nama }}?')">
                                                        <i class="ti-trash"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Modal Edit Mahasiswa -->
                                            <div class="modal fade" id="editMahasiswaModal_{{ $mahasiswa->id }}" tabindex="-1" aria-labelledby="editMahasiswaModalLabel_{{ $mahasiswa->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content text-start">
                                                        <form action="{{ Route::has(($currentPrefix ?? '') . 'mahasiswa.update') ? route(($currentPrefix ?? '') . 'mahasiswa.update', $mahasiswa->id) : route('dosen.mahasiswa.update', $mahasiswa->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editMahasiswaModalLabel_{{ $mahasiswa->id }}">Edit Data Mahasiswa</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="npm_{{ $mahasiswa->id }}" class="form-label">NPM</label>
                                                                    <input value="{{ old('npm', $mahasiswa->NPM) }}" type="text" class="form-control" id="npm_{{ $mahasiswa->id }}" name="npm" placeholder="Masukkan NPM" autocomplete="off" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="nama_{{ $mahasiswa->id }}" class="form-label">Nama Lengkap</label>
                                                                    <input value="{{ old('nama', $mahasiswa->Nama) }}" type="text" class="form-control" id="nama_{{ $mahasiswa->id }}" name="nama" placeholder="Masukkan Nama Lengkap" autocomplete="off" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="angkatan_{{ $mahasiswa->id }}" class="form-label">Angkatan</label>
                                                                    <input value="{{ old('angkatan', $mahasiswa->angkatan) }}" type="text" class="form-control" id="angkatan_{{ $mahasiswa->id }}" name="angkatan" placeholder="Contoh: 2024" autocomplete="off" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
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

    <!-- Modal Import Mahasiswa -->
    <div class="modal fade" id="importMahasiswaModal" tabindex="-1" aria-labelledby="importMahasiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ Route::has(($currentPrefix ?? '') . 'mahasiswa.import') ? route(($currentPrefix ?? '') . 'mahasiswa.import') : route('mahasiswa.import.global') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importMahasiswaModalLabel">Import Data Mahasiswa (Excel)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2" role="alert" style="font-size: 13px;">
                            <i class="ti-info-alt me-1"></i> Pastikan format file Excel sesuai dengan template.
                            <a href="{{ Route::has(($currentPrefix ?? '') . 'mahasiswa.template-excel') ? route(($currentPrefix ?? '') . 'mahasiswa.template-excel') : route('mahasiswa.template-excel.global') }}" class="fw-bold text-decoration-underline ms-1">
                                Download Template Excel
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

    <!-- Modal Tambah Mahasiswa -->
    <div class="modal fade" id="tambahMahasiswaModal" tabindex="-1" aria-labelledby="tambahMahasiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ Route::has(($currentPrefix ?? '') . 'mahasiswa.store') ? route(($currentPrefix ?? '') . 'mahasiswa.store') : route('dosen.mahasiswa.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahMahasiswaModalLabel">Tambah Mahasiswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="npm" class="form-label">NPM</label>
                            <input value="{{ old('npm') }}" type="text" class="form-control" id="npm" name="npm" placeholder="Masukkan NPM" autocomplete="off" required>
                            @error('npm')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input value="{{ old('nama') }}" type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama Lengkap" autocomplete="off" required>
                            @error('nama')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="angkatan" class="form-label">Angkatan</label>
                            <input value="{{ old('angkatan') }}" type="text" class="form-control" id="angkatan" name="angkatan" placeholder="Contoh: 2024" autocomplete="off" required>
                            @error('angkatan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Mahasiswa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
