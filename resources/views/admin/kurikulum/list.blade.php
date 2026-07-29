{{-- @php
    $routePrefix = [
        'Admin Universitas' => ['prefix' => 'admin-universitas.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'admin-universitas.';
@endphp --}}

@extends(in_array($userOtoritas, ['Admin Universitas', 'Admin']) ? 'admin.template' : 'penjamin-mutu.template')
@section('content')
    <div class="d-flex row gap-4">
        @if (in_array($userOtoritas, ['Admin Universitas', 'Kepala Program Studi']))
            <div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Kurikulum</h4>
                        <form method="POST" action="{{ route($currentPrefix . 'add-kurikulum') }}"
                            class="{{ $editMode ? 'opacity-50 pointer-events-none' : '' }}">
                            @csrf
                            @php
                                $user = auth()->user();
                                $userOtoritas = $user->otoritas->otoritas;
                                $isFakultasDisabled = $userOtoritas === 'Kepala Program Studi';
                                $isProdiDisabled = $userOtoritas === 'Kepala Program Studi';
                            @endphp
                            {{-- Universitas (Otomatis terpilih dan disembunyikan) --}}
                            <input type="hidden" name="universitas" value="{{ $user->id_universitasUser }}">
                            <div class="form-group">
                                <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="tahun" placeholder="Tahun Kurikulum"
                                    autofocus autocomplete="off" {{ $editMode ? 'disabled' : '' }}>
                                @error('tahun')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            @if ($userOtoritas === 'Kepala Program Studi')
                                {{-- Untuk Kaprodi: Fakultas & Prodi disembunyikan, nilainya dikirim via hidden input --}}
                                <input type="hidden" name="fakultas" value="{{ $user->id_fakultasUser }}">
                                <input type="hidden" name="id_prodi" value="{{ $user->id_prodiUser }}">
                            @else
                                {{-- Untuk Admin Universitas: Tampilkan dropdown Fakultas dan Prodi --}}
                                {{-- FAKULTAS --}}
                                <div class="form-group">
                                    <label>Fakultas <span class="text-danger">*</span></label>
                                    <select class="single-select w-100" name="fakultas" id="fakultas_add">
                                        <option value="" disabled selected>Pilih Fakultas</option>
                                        @foreach ($fakultas as $f)
                                            <option value="{{ $f->id }}" {{ old('fakultas') == $f->id ? 'selected' : '' }}>
                                                {{ $f->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('fakultas')
                                        <div class="alert alert-danger mt-1">
                                        {{ $errors->first('fakultas') }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- PRODI --}}
                                <div class="form-group">
                                    <label>Program Studi <span class="text-danger">*</span></label>
                                    <select class="single-select w-100" name="id_prodi" id="prodi_add" disabled>
                                        <option value="" disabled selected>Pilih Fakultas Terlebih Dahulu</option>
                                        @foreach ($prodi as $p)
                                            <option value="{{ $p->id }}" {{ old('id_prodi') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_prodi')
                                        <div class="alert alert-danger mt-1">
                                            {{ $errors->first('id_prodi') }}
                                        </div>
                                    @enderror
                                </div>
                            @endif
                            <button type="submit" class="btn btn-primary me-2"
                                {{ $editMode ? 'disabled' : '' }}>Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Kurikulum</h4>
                    {{-- Filter Form --}}
                    <x-filter-form
                        :universities="$universities"
                        :faculties="$faculties"
                        :programs="$programs"
                    />
                    <div class="table-responsive mt-3">
                        <table class="table table-hover dataTable">
                            <thead>
                                <tr class="bg-light">
                                    <th>No</th>
                                    <th>Tahun</th>
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
                                @foreach ($kurikulumsForTable as $kurikulum)
                                    {{-- DIUBAH: Gunakan ID untuk pengecekan --}}
                                    <tr class="{{ $editMode && $editedId != $kurikulum->id ? 'opacity-50' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        {{-- DIUBAH: Gunakan ID untuk pengecekan --}}
                                        @if ($editMode && $editedId == $kurikulum->id)
                                            <form
                                                {{-- DIUBAH: Kirim ID, bukan tahun --}}
                                                action="{{ route($currentPrefix .'update-kurikulum', $kurikulum->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                {{-- Input Tahun (Tetap) --}}
                                                <td>
                                                    <input type="number" name="tahun" class="form-control"
                                                        value="{{ $kurikulum->tahun }}" required>
                                                </td>

                                                {{-- Tampilkan dropdown untuk Admin Universitas --}}
                                                @if ($userOtoritas === 'Admin Universitas')
                                                    <td>
                                                        {{-- Dropdown Prodi --}}
                                                        <select class="form-control prodi-edit-select" name="id_prodi" required data-prodi-id="{{ $kurikulum->id_prodi }}">
                                                            {{-- Opsi akan diisi oleh JavaScript --}}
                                                            <option value="">Memuat...</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        {{-- Dropdown Fakultas --}}
                                                        <select class="form-control fakultas-edit-select" name="fakultas_edit" required>
                                                            <option value="" disabled>Pilih Fakultas</option>
                                                            @foreach ($fakultas as $f)
                                                                <option value="{{ $f->id }}" {{ $kurikulum->prodi->id_fakultas == $f->id ? 'selected' : '' }}>
                                                                    {{ $f->nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                @else
                                                    {{-- Untuk Kaprodi, tampilan tetap statis --}}
                                                    <td>{{ $kurikulum->prodi->nama }}</td>
                                                    <td>{{ $kurikulum->prodi->fakultas->nama }}</td>
                                                @endif

                                                @if ($userOtoritas != 'Admin Universitas')
                                                    <td>{{ $kurikulum->prodi->fakultas->universitas->nama }}</td>
                                                @endif
                                                <td>
                                                    <button type="submit" class="btn btn-success btn-icon-text p-2" style="margin-right:7px">
                                                        Confirm
                                                    </button>
                                                    <a href="{{ route($currentPrefix . 'cancel-edit') }}"
                                                        class="btn btn-secondary btn-icon-text p-2">Cancel</a>
                                                </td>
                                            </form>
                                        @else
                                            <td>{{ $kurikulum->tahun }}</td>
                                            <td>{{ $kurikulum->prodi?->nama }}</td>
                                            <td>{{ $kurikulum->prodi?->fakultas->nama }}</td>
                                            @if ($userOtoritas != 'Admin Universitas')
                                                <td>{{ $kurikulum->prodi?->fakultas->universitas->nama }}</td>
                                            @endif
                                            @if ($userOtoritas != 'Admin')
                                                <td>
                                                    <div class="d-flex">
                                                        {{-- DIUBAH: Kirim ID yang dienkripsi --}}
                                                        <a href="{{ route($currentPrefix . 'enter-edit-mode', $kurikulum->id) }}"
                                                            class="btn btn-warning btn-icon-text p-2" style="margin-right:7px"
                                                            {{ $editMode ? 'disabled' : '' }}>
                                                            Edit
                                                            <i class="ti-pencil btn-icon-append"></i>
                                                        </a>
                                                        <form
                                                            {{-- DIUBAH: Kirim ID yang dienkripsi --}}
                                                            action="{{ route($currentPrefix . 'delete-kurikulum', $kurikulum->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-icon-text p-2"
                                                                {{ $editMode ? 'disabled' : '' }}
                                                                onclick="return confirm('Anda yakin ingin menghapus kurikulum tahun {{ $kurikulum->tahun }}?')">
                                                                Delete
                                                                <i class="ti-trash btn-icon-append"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endif
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // --- LOGIKA HANYA UNTUK FORM TAMBAH KURIKULUM ---
            // 1. Inisialisasi variabel khusus untuk form 'Tambah'
            const fakultasAddSelect = $('#fakultas_add');
            const prodiAddSelect = $('#prodi_add');

            // 2. Inisialisasi Select2 untuk form 'Tambah'
            fakultasAddSelect.select2({
                placeholder: 'Pilih Fakultas',
                width: '100%'
            });
            prodiAddSelect.select2({
                placeholder: 'Pilih Program Studi',
                width: '100%'
            });

            // 3. Event listener HANYA untuk dropdown fakultas di form 'Tambah'
            fakultasAddSelect.on('change', function() {
                const fakultasId = $(this).val();
                // Reset dropdown prodi 'Tambah'
                prodiAddSelect.val(null).trigger('change');

                if (fakultasId) {
                    prodiAddSelect.prop('disabled', false);
                    prodiAddSelect.html('<option value="">Memuat...</option>'); // Tampilkan status loading

                    // 4. AJAX call menggunakan endpoint yang sudah ada
                    $.ajax({
                        url: `/get-programs/${fakultasId}`, // Endpoint ini bisa digunakan kembali, bagus!
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            // Hapus status loading dan isi dengan data baru
                            prodiAddSelect.html('<option value="" disabled selected>Pilih Program Studi</option>');
                            $.each(data, function(key, value) {
                                prodiAddSelect.append(`<option value="${value.id}">${value.nama}</option>`);
                            });
                        },
                        error: function() {
                            prodiAddSelect.html('<option value="" disabled selected>Gagal memuat data</option>');
                        }
                    });
                } else {
                    // Jika tidak ada fakultas yang dipilih, nonaktifkan dan reset prodi
                    prodiAddSelect.html('<option value="" disabled selected>Pilih Fakultas Terlebih Dahulu</option>');
                    prodiAddSelect.prop('disabled', true);
                }
            });

            // --- LOGIKA UNTUK FORM EDIT INLINE ---

            // 1. Cek apakah ada form edit di halaman
            const fakultasEditSelect = $('.fakultas-edit-select');
            if (fakultasEditSelect.length > 0) {
                
                // 2. Fungsi untuk memuat prodi berdasarkan fakultas pada baris edit
                function loadProdiForEdit(fakultasSelectElement) {
                    const prodiSelect = fakultasSelectElement.closest('tr').find('.prodi-edit-select');
                    const fakultasId = fakultasSelectElement.val();
                    const targetProdiId = prodiSelect.data('prodi-id'); // Ambil ID prodi awal dari data attribute

                    if (fakultasId) {
                        prodiSelect.prop('disabled', false).html('<option value="">Memuat...</option>');
                        
                        $.ajax({
                            url: `/get-programs/${fakultasId}`, // Gunakan endpoint yang sudah ada
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                prodiSelect.html('<option value="" disabled>Pilih Program Studi</option>');
                                $.each(data, function(key, value) {
                                    const isSelected = value.id == targetProdiId;
                                    prodiSelect.append(`<option value="${value.id}" ${isSelected ? 'selected' : ''}>${value.nama}</option>`);
                                });
                                // Hapus data-prodi-id setelah digunakan agar tidak mengganggu pilihan manual oleh user
                                prodiSelect.data('prodi-id', null);
                            },
                            error: function() {
                                prodiSelect.html('<option value="" disabled>Gagal memuat data</option>');
                            }
                        });
                    }
                }

                // 3. Panggil fungsi di atas saat halaman dimuat untuk mengisi dropdown prodi
                loadProdiForEdit(fakultasEditSelect);
                
                // 4. Tambahkan event listener untuk perubahan pada dropdown fakultas di baris edit
                fakultasEditSelect.on('change', function() {
                    loadProdiForEdit($(this));
                });
            }
        });
    </script>
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection
