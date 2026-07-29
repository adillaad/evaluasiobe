@extends('dosen.template')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">
                    <h3>Edit RPS</h3>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ $rps->id }}" method="POST">
                    @csrf
                    @method('put')
                    <div class="form-floating mb-3">
                        <input type="text" name="nomor" class="form-control" id="nomor" placeholder="nomor"
                            value="{{ $rps->nomor }}" aria-describedby="nomorHelp" readonly>
                        <label for="nomor" class="form-label">nomor <span class="text-danger">*</span></label>
                        @error('nomor')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                        <!-- <div id="nomorHelp" class="form-text">Silahkan masukkan nomor RPS.</div> -->
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select form-control-lg" name="prodi">
                            <option selected="true" value="" disabled selected> </option>
                            @foreach ($prodis as $prodi)
                                <option value={{ $prodi->id }} {{ $rps->id_prodi == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                        <label for="prodi">Program studi <span style="color:red">*</span></label>
                        @error('prodi')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select form-control-lg" name="semester" id="semester">
                            <option selected="true" value="" disabled selected> </option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ $rps->semester == $i ? 'selected' : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        <label>Semester <span class="text-danger">*</span></label>
                        @error('semester')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <select id="matakuliah" name="matakuliah" class="form-select form-control-lg">
                            <option selected="true" value="" disabled selected> </option>
                            @foreach ($mks as $mk)
                                <option value="{{ $mk->kode }}" {{ $rps->kode_mk == $mk->kode ? 'selected' : '' }}>
                                    {{ $mk->nama }}</option>
                            @endforeach
                        </select>
                        <label for="matakuliah">Mata kuliah <span style="color:red">*</span></label>
                        @error('matakuliah')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <select name="pengembang" class="form-select form-control-lg">
                            <option selected="true" value="" disabled selected> </option>
                            @foreach ($pengembang as $user)
                                <option value="{{ $user->name }}"
                                    {{ $rps->pengembang == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <label for="pengembang">Pengembang RPS <span style="color:red">*</span></label>
                        @error('pengembang')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <select name="koordinator" class="form-select form-control-lg">
                            <option selected="true" value="" disabled selected> </option>
                            @foreach ($users as $user)
                                <option value="{{ $user->name }}"
                                    {{ $rps->koordinator == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <label for="koordinator">Koordinator RMK </label>
                        @error('koordinator')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <select name="dosen" class="form-select form-control-lg">
                            <option selected="true" value="" disabled selected> </option>
                            @foreach ($users as $user)
                                <option value="{{ $user->name }}" {{ $rps->dosen == $user->name ? 'selected' : '' }}>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                        <label for="dosen">Dosen pengampu <span style="color:red">*</span></label>
                        @error('dosen')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div> 
                    
                    <div class="form-floating mb-3">
                        <select name="dosen_anggota1" class="form-select form-control-lg">
                            <option selected="true" value="" disabled selected> </option>
                            @foreach ($users as $user)
                                <option value="{{ $user->name }}" {{ $rps->dosen_anggota1 == $user->name ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="dosen_anggota1">Dosen anggota 1<span style="color:red">*</span></label>
                        @error('dosen_anggota1')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <select name="dosen_anggota2" class="form-select form-control-lg">
                            <option selected="true" value="" disabled selected> </option>
                            @foreach ($users as $user)
                                <option value="{{ $user->name }}" {{ $rps->dosen_anggota2 == $user->name ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="dosen_anggota2">Dosen anggota 2<span style="color:red">*</span></label>
                        @error('dosen_anggota2')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select name="kaprodi" class="form-select form-control-lg">
                            <option selected="true" value="" disabled selected> </option>
                            @foreach ($kaprodis as $user)
                                <option value="{{ $user->name }}" {{ $rps->kaprodi == $user->name ? 'selected' : '' }}>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                        <label for="kaprodi">Kepala program studi <span style="color:red">*</span></label>
                        @error('kaprodi')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <!-- <div class="form-floating mb-3">
                        <input type="text" name="tipe" value="{{ $rps->tipe }}" class="form-control"
                            placeholder="Jenis pengajaran" autocomplete="off">
                        <label for="tipe">Jenis pengajaran <span class="text-danger">*</span></label>
                        @error('tipe')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-6 form-floating mb-3">
                            <textarea name="waktu" class="form-control" placeholder="Workload" style="height: 100px">{{ $rps->waktu }}</textarea>
                            <label for="waktu" class="form-label ms-3">Workload <span
                                    class="text-danger">*</span></label>
                            @error('waktu')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-6 form-floating mb-3">
                            <textarea name="kontrak" class="form-control" placeholder="Kontrak kuliah" style="height: 100px">{{ $rps->kontrak }}</textarea>
                            <label class="form-label ms-3" for="kontrak">Kontrak kuliah <span
                                    class="text-danger">*</span></label>
                            @error('kontrak')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea name="materi_mk" class="form-control" placeholder="Materi MK" style="height: 100px">{{ $rps->materi_mk }}</textarea>
                        <label for="materi_mk">Materi MK <span class="text-danger">*</span></label>
                        @error('materi_mk')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-floating mb-3">
                                <textarea name="syarat_ujian" class="form-control" style="height: 100px" placeholder="Syarat ujian">{{ $rps->syarat_ujian }}</textarea>
                                <label for="syarat_ujian">Syarat ujian <span class="text-danger">*</span></label>
                                @error('syarat_ujian')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6 form-floating mb-3">
                            <textarea name="syarat_studi" class="form-control" placeholder="Syarat studi" style="height: 100px">{{ $rps->syarat_studi }}</textarea>
                            <label class="form-label ms-3" for="syarat_studi">Syarat studi <span
                                    class="text-danger">*</span></label>
                            @error('syarat_studi')
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div> -->

                    
                    <div class="form-floating mb-3">
                        <input type="text" name="media_software" value="{{ old('media_software', $rps->media_software) }}" class="form-control"
                            placeholder="Media Software" autocomplete="off">
                        <label for="media_software">Media Software <span class="text-danger">*</span></label>
                        @error('media_software')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="media_hardware" value="{{ old('media_hardware', $rps->media_hardware) }}" class="form-control"
                            placeholder="Media Hardware" autocomplete="off">
                        <label for="media_hardware">Media Hardware <span class="text-danger">*</span></label>
                        @error('media_hardware')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="pustaka_utama" class="form-label mb-0">
                                Pustaka Utama <span class="text-danger">*</span>
                            </label>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left" title="Contoh: Gilbert Strang, Introduction to Linear Algebra (Wellesley-Cambridge Press, Wellesley, MA, 2020)">
                                Contoh Pengisian
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                                </svg>
                            </button>
                        </div>

                        <div id="pustaka-fields">
                            @php
                                // KUNCI PERUBAHAN ADA DI SINI:
                                // Ambil data dari old() dulu, jika tidak ada, baru ambil dari database ($rps->pustaka_utama)
                                $pustaka_items = old('pustaka_utama', $rps->pustaka_utama);
                            @endphp

                            {{-- Gunakan @forelse untuk menampilkan data yang ada --}}
                            @forelse ($pustaka_items as $key => $item)
                                <div class="input-group mb-2">
                                    <input type="text" name="pustaka_utama[]" class="form-control" placeholder="Tuliskan pustaka utama..." value="{{ $item }}" required>
                                    {{-- Tombol hapus hanya muncul jika ada lebih dari 1 item --}}
                                    @if ($key > 0)
                                        <button class="btn btn-outline-danger remove-pustaka-btn" type="button">Hapus</button>
                                    @endif
                                </div>
                            @empty
                                {{-- Jika tidak ada data sama sekali, tampilkan satu field kosong --}}
                                <div class="input-group mb-2">
                                    <input type="text" name="pustaka_utama[]" class="form-control" placeholder="Tuliskan pustaka utama..." required>
                                </div>
                            @endforelse
                        </div>

                        <button type="button" id="add-pustaka-btn" class="btn btn-outline-primary btn-sm mt-2">
                            + Tambah Pustaka
                        </button>

                        @error('pustaka_utama')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                        @error('pustaka_utama.*')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div> 

                    <div class="form-floating mb-3">
                        <textarea name="pustaka_pendukung" class="form-control" style="height:100px" placeholder="Pustaka pendukung" id="pustaka_pendukung">{{ old('pustaka_pendukung', $rps->pustaka_pendukung) }}</textarea>
                        <label for="pustaka_pendukung">Pustaka pendukung</label>
                        @error('pustaka_pendukung')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // ... (kode inisialisasi tooltip jika ada)

            function getPustakaRowTemplate() {
                return `
                    <div class="input-group mb-2">
                        <input type="text" name="pustaka_utama[]" class="form-control" placeholder="Tuliskan pustaka utama..." required>
                        <button class="btn btn-outline-danger remove-pustaka-btn" type="button">Hapus</button>
                    </div>
                `;
            }

            $("#add-pustaka-btn").click(function() {
                $('#pustaka-fields').append(getPustakaRowTemplate());
            });

            $('#pustaka-fields').on('click', '.remove-pustaka-btn', function() {
                $(this).closest('.input-group').remove();
            });
        });
    </script>
@endpush