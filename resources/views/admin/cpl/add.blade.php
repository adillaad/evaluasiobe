@extends('admin.template')
@section('content')
<div class="stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah CPL Program Studi</h4>
            <form method="POST" action="{{ route('admin-universitas.store-cpl') }}">
                @csrf
                {{-- =============================================== --}}
                {{--         DROPDOWN STATIS UNTUK KONTEKS         --}}
                {{-- =============================================== --}}
                @php
                    $user = auth()->user();
                    $otoritas = $user->otoritas->otoritas;
                    $allowedRoles = ['Admin Universitas', 'Penjamin Mutu Universitas'];
                    $isFakultasDisabled = !in_array($otoritas, $allowedRoles);
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fakultas <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_fakultas" id="fakultas-select" {{ $isFakultasDisabled ? 'disabled' : '' }}>
                                <option value="" disabled selected>Pilih Fakultas...</option>
                                @foreach($fakultas as $f)
                                <option value="{{ $f->id }}" {{ old('id_fakultas') == $f->id || ($isFakultasDisabled && $user->id_fakultasUser == $f->id) ? 'selected' : '' }}>
                                    {{ $f->nama }}
                                </option>
                                @endforeach
                            </select>
                            @if($isFakultasDisabled)
                                <input type="hidden" name="id_fakultas" value="{{ $user->id_fakultasUser }}">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Program Studi <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_prodi" id="prodi-select" {{ old('id_fakultas') || $isFakultasDisabled ? '' : 'disabled' }}>
                                <option value="" disabled selected>Pilih Fakultas Terlebih Dahulu...</option>
                                @foreach($prodi as $p)
                                    <option value="{{ $p->id }}" {{ old('id_prodi') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
                {{-- =============================================== --}}
                {{--     CONTAINER UNTUK BARIS-BARIS CPL DINAMIS     --}}
                {{-- =============================================== --}}
                <div id="cpl-fields-container">
                    @foreach (old('kode', [null]) as $key => $value)
                    {{-- Setiap baris CPL dibungkus dalam div ini agar rapi dan mudah dihapus --}}
                    <div class="cpl-row border rounded p-3 mb-4 position-relative">
                        {{-- Tombol Hapus Baris (hanya muncul jika bukan baris pertama) --}}
                        @if($key > 0)
                            <button type="button" class="btn btn-danger btn-sm remove-cpl-row" style="position: absolute; top: 1rem; right: 1rem;">Hapus</button>
                        @endif
                        {{-- BARIS PERTAMA UNTUK INPUT --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                                    <select class="form-control kurikulum-dropdown" name="id_kurikulum[]" {{ !old('id_prodi') ? 'disabled' : '' }}>
                                        <option value="" disabled selected>Pilih Prodi Dahulu</option>
                                        {{-- Opsi diisi oleh JavaScript --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Aspek <span class="text-danger">*</span></label>
                                    <select class="form-control" name="aspek[]">
                                        <option value="" disabled {{ !old('aspek.'.$key) ? 'selected' : '' }}>Pilih Aspek...</option>
                                        <option value="Sikap" {{ old('aspek.'.$key) == 'Sikap' ? 'selected' : '' }}>Sikap</option>
                                        <option value="Keterampilan Umum" {{ old('aspek.'.$key) == 'Keterampilan Umum' ? 'selected' : '' }}>Keterampilan Umum</option>
                                        <option value="Keterampilan Khusus" {{ old('aspek.'.$key) == 'Keterampilan Khusus' ? 'selected' : '' }}>Keterampilan Khusus</option>
                                        <option value="Pengetahuan" {{ old('aspek.'.$key) == 'Pengetahuan' ? 'selected' : '' }}>Pengetahuan</option>
                                        <option value="Lainnya" {{ old('aspek.'.$key) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kode <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">CPL</span></div>
                                        <input type="text" class="form-control" name="kode[]" value="{{ old('kode.'.$key) }}" placeholder="Nomor" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- BARIS KEDUA UNTUK JUDUL (LEBAR) --}}
                        <div class="form-group mb-0">
                            <label>Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul[]" value="{{ old('judul.'.$key) }}" placeholder="Masukkan judul CPL" autocomplete="off">
                        </div>
                    </div>
                    @endforeach
                </div>
                {{-- Tombol utama --}}
                <div class="form-group mt-4">
                    <button type="button" id="add-cpl-row" class="btn btn-primary me-2">Add Row</button>
                    <button type="submit" class="btn btn-success">Submit All</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Variabel ini menyimpan HTML untuk opsi kurikulum
        let kurikulumOptions = '<option value="" disabled selected>Pilih Prodi...</option>';

        // Fungsi untuk membuat template HTML baris baru. Strukturnya SAMA PERSIS dengan di atas.
        function getNewRowTemplate() {
            return `
            <div class="cpl-row border rounded p-3 mb-4 position-relative">
                <button type="button" class="btn btn-danger btn-sm remove-cpl-row" style="position: absolute; top: 1rem; right: 1rem;">Hapus</button>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Tahun Kurikulum <span class="text-danger">*</span></label><select class="form-control kurikulum-dropdown" name="id_kurikulum[]">${kurikulumOptions}</select></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Aspek <span class="text-danger">*</span></label><select class="form-control" name="aspek[]"><option value="" disabled selected>Pilih Aspek...</option><option value="Sikap">Sikap</option><option value="Umum">Umum</option><option value="Pengetahuan">Pengetahuan</option><option value="Keterampilan">Keterampilan</option></select></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Kode <span class="text-danger">*</span></label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">CPL</span></div><input type="text" class="form-control" name="kode[]" placeholder="Nomor" autocomplete="off"></div></div></div>
                </div>
                <div class="form-group mb-0"><label>Judul <span class="text-danger">*</span></label><input type="text" class="form-control" name="judul[]" placeholder="Masukkan judul CPL" autocomplete="off"></div>
            </div>`;
        }

        // Event handler untuk tombol "Add Row"
        $("#add-cpl-row").click(function() {
            $('#cpl-fields-container').append(getNewRowTemplate());
        });

        // Event handler untuk tombol "Hapus" (delegasi event)
        $("#cpl-fields-container").on('click', '.remove-cpl-row', function() {
            $(this).closest('.cpl-row').remove();
        });
        
        // == LOGIKA DROPDOWN BERANTAI (TIDAK ADA PERUBAHAN DI SINI) ==
        const prodiSelect = $('#prodi-select');
        const kurikulumDropdowns = () => $('.kurikulum-dropdown');

        $('#fakultas-select').on('change', function() {
            const fakultasId = $(this).val();
            prodiSelect.html('<option value="" disabled selected>Loading...</option>').prop('disabled', true);
            kurikulumDropdowns().html('<option value="" disabled selected>Pilih Prodi Dahulu</option>').prop('disabled', true);
            kurikulumOptions = '<option value="" disabled selected>Pilih Prodi Dahulu</option>';

            if (fakultasId) {
                $.ajax({
                    url: `/admin-universitas/get-prodi/${fakultasId}`,
                    type: 'GET',
                    success: function(data) {
                        prodiSelect.html('<option value="" disabled selected>Pilih Prodi...</option>').prop('disabled', false);
                        $.each(data, function(key, value) {
                            prodiSelect.append(`<option value="${value.id}">${value.nama}</option>`);
                        });
                    }
                });
            }
        });

        prodiSelect.on('change', function(){
            const prodiId = $(this).val();
            kurikulumDropdowns().html('<option value="" disabled selected>Loading...</option>').prop('disabled', true);
            kurikulumOptions = '<option value="" disabled selected>Loading...</option>';

            if (prodiId) {
                $.ajax({
                    url: `/admin-universitas/get-kurikulum/${prodiId}`,
                    type: 'GET',
                    success: function(data) {
                        let optionsHtml = '<option value="" disabled selected>Pilih Kurikulum...</option>';
                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                optionsHtml += `<option value="${value.id}">${value.tahun}</option>`;
                            });
                        } else {
                            optionsHtml = '<option value="" disabled selected>Kurikulum tidak ditemukan</option>';
                        }
                        kurikulumOptions = optionsHtml;
                        kurikulumDropdowns().html(kurikulumOptions).prop('disabled', false);
                    }
                });
            }
        });

        // Handle old input (jika validasi gagal) atau pre-selected user
        if ($('#fakultas-select').val()) {
            $('#fakultas-select').trigger('change');
            setTimeout(() => {
                const oldProdi = "{{ old('id_prodi', $isFakultasDisabled ? auth()->user()->id_prodiUser : '') }}";
                if(oldProdi){
                    prodiSelect.val(oldProdi);
                    prodiSelect.trigger('change');
                }
            }, 500);
        }
    });
</script>
@endsection