@extends('dosen.template')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">
                    <h3>Tambah RPS Baru</h3>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="add-rpsStep4" enctype="multipart/form-data">
                    <!-- @csrf
                    <div class="form-floating mb-3">
                        <button type="button" class="btn btn-secondary btn-sm mb-3" data-bs-toggle="tooltip"
                            data-bs-placement="left"
                            title="Gilbert Strang, Introduction to Linear Algebra (Wellesley-Cambridge Press, Wellesley, MA, 2020)"
                            style="float:right;"> Contoh Pengisian
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                            </svg>
                        </button>
                        <label for="pustaka_utama" class="form-label">Pustaka utama <span
                                class="text-danger">*</span></label>
                        <textarea name="pustaka_utama" class="form-control" style="height:100px" placeholder="Pustaka utama">{{ old('pustaka_utama', session('step4_dataRps.pustaka_utama')) }}</textarea>
                        @error('pustaka_utama')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                     --> 
                    @csrf

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
                                // Ambil data lama dari validasi error atau dari session
                                $pustaka_items = old('pustaka_utama', session('step4_dataRps.pustaka_utama'));
                                // Jika data bukan array (mungkin dari textarea lama), jadikan array
                                if (!is_array($pustaka_items)) {
                                    $pustaka_items = $pustaka_items ? [$pustaka_items] : [];
                                }
                            @endphp

                            @if (count($pustaka_items) > 0)
                                @foreach ($pustaka_items as $key => $item)
                                    <div class="input-group mb-2">
                                        <input type="text" name="pustaka_utama[]" class="form-control" style="height:50px" placeholder="Tuliskan pustaka utama..." value="{{ $item }}" required>
                                        @if ($key > 0)
                                            <button class="btn btn-outline-danger remove-pustaka-btn" type="button">Hapus</button>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2">
                                    <input type="text" name="pustaka_utama[]" class="form-control" style="height:100px" placeholder="Tuliskan pustaka utama..." required>
                                </div>
                            @endif
                        </div>

                        <button type="button" id="add-pustaka-btn" class="btn btn-outline-primary btn-sm mt-2">
                            + Tambah Pustaka
                        </button>

                        @error('pustaka_utama')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                        @error('pustaka_utama.*')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div> 
                    
                     <div class="form-floating mb-3">
                        <textarea name="pustaka_pendukung" class="form-control" style="height:100px" placeholder="Pustaka pendukung" id="pustaka_pendukung">{{ old('pustaka_pendukung', session('step4_dataRps.pustaka_pendukung')) }}</textarea>
                        
                        <label for="pustaka_pendukung">Pustaka pendukung</label>
                        
                        @error('pustaka_pendukung')
                            <div class="alert alert-danger mt-2"> {{ $message }}
                            </div>
                        @enderror
                    </div>


                    <div class="form-floating mb-3">
                        <button type="button" class="btn btn-secondary btn-sm mb-3" data-bs-toggle="tooltip"
                            data-bs-placement="left"
                            title="Isikan angka desimal, contoh: 50.01 (gunakan titik)"
                            style="float:right;"> Contoh Pengisian
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                            </svg>
                        </button>
                        <label for="batas_kelulusan_mhs" class="form-label">Ambang Batas Kelulusan Mahasiswa <span class="text-danger">*</span></label>
                        <textarea type="number" step="0.01" min="0" max="100"
                            name="batas_kelulusan_mhs"
                            class="form-control"
                            placeholder="contoh: 50.01">{{ old('batas_kelulusan_mhs', session('step4_dataRps.batas_kelulusan_mhs')) }}</textarea>
                        @error('batas_kelulusan_mhs')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <button type="button" class="btn btn-secondary btn-sm mb-3" data-bs-toggle="tooltip"
                            data-bs-placement="left"
                            title="Isikan persentase kelulusan mata kuliah. Contoh: 75.50 berarti 75,50%"
                            style="float:right;"> Contoh Pengisian
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                            </svg>
                        </button>
                        <label for="batas_kelulusan_mk" class="form-label">Ambang Batas Kelulusan Mata Kuliah <span class="text-danger">*</span></label>
                        <textarea type="number" step="0.01" min="0" max="100"
                            name="batas_kelulusan_mk"
                            class="form-control"
                            placeholder="contoh: 75.50">{{ old('batas_kelulusan_mk', session('step4_dataRps.batas_kelulusan_mk')) }}</textarea>
                        @error('batas_kelulusan_mk')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>



                    <button type="button" class="btn btn-primary"
                        onclick="location.href='{{ route('dosen.addRpsStep3') }}'">Previous</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

        }); 
        // Pastikan DOM sudah siap sebelum menjalankan skrip
        $(function() {
            // Inisialisasi Tooltip Bootstrap (jika masih diperlukan)
            $('[data-bs-toggle="tooltip"]').tooltip();

            /**
             * Fungsi ini bertindak sebagai template.
             * Ia mengembalikan string HTML untuk satu baris input pustaka baru.
             */
            function getPustakaRowTemplate() {
                return `
                    <div class="input-group mb-2">
                        <input type="text" name="pustaka_utama[]" class="form-control" placeholder="Tuliskan pustaka utama..." required>
                        <button class="btn btn-outline-danger remove-pustaka-btn" type="button">Hapus</button>
                    </div>
                `;
            }

            // Event handler untuk tombol "Tambah Pustaka"
            $("#add-pustaka-btn").click(function() {
                const newRow = getPustakaRowTemplate();
                $('#pustaka-fields').append(newRow);
            });

            // Event handler untuk tombol "Hapus" pada setiap baris
            $('#pustaka-fields').on('click', '.remove-pustaka-btn', function() {
                $(this).closest('.input-group').remove();
            });
        }); 
    </script>
@endsection
