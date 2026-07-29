<form method="POST" action="{{ route($currentPrefix . 'rps-store') }}" id="formTambahRps">
    @csrf
    {{-- BAGIAN 1: INFORMASI DASAR --}}
    <h5 class="border-bottom pb-2 mb-3"><i class="ti-info-alt me-2"></i>Informasi Dasar</h5>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label for="matakuliah" class="form-label">Mata Kuliah <span class="text-danger">*</span></label>
                {{-- Tambahkan ID agar bisa ditangkap JS --}}
                <select id="matakuliah" name="matakuliah" class="form-select form-select-sm" required>
                    <option value="" disabled selected>Pilih...</option>
                    @foreach ($mks as $mk)
                        {{-- PENTING: Tambahkan data-semester="{{ $mk->semester }}" --}}
                        <option value="{{ $mk->kode }}" data-semester="{{ $mk->semester }}"
                         data-batas-mhs="{{ $mk->batas_kelulusan_mhs }}"
                         data-batas-mk="{{ $mk->batas_kelulusan_mk }}">
                            {{ $mk->kode }} - {{ $mk->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="semester" class="form-label">Semester</label> 
                <input type="text" name="semester" id="semester" class="form-control form-control-sm bg-light" readonly required placeholder="Pilih Mata Kuliah">
            </div>
        </div>
    </div> 

    {{-- BAGIAN 2: PENANGGUNG JAWAB & TIM DOSEN --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-user me-2"></i>Penanggung Jawab & Tim Dosen</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="pengembang" class="form-label">Pengembang RPS <span class="text-danger">*</span></label>
                <select name="pengembang" id="pengembang" class="form-select form-select-sm" required>
                    <option value="{{ auth()->user()->name }}" selected>{{ auth()->user()->name }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="koordinator" class="form-label">Koordinator RMK</label>
                <select name="koordinator" id="koordinator" class="form-select form-select-sm">
                    <option value="" selected>Pilih jika ada...</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->name }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="border p-3 rounded">
        <h6 class="mb-3">Team Teaching</h6>
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="dosen" class="form-label">Dosen Pengampu <span class="text-danger">*</span></label>
                    <select name="dosen" id="dosen" class="form-select form-select-sm" required>
                        <option value="" disabled selected>Pilih...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="dosen_anggota1" class="form-label">Dosen Anggota 1 <span class="text-danger">*</span></label>
                    <select name="dosen_anggota1" id="dosen_anggota1" class="form-select form-select-sm" required>
                        <option value="" disabled selected>Pilih...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
             <div class="col-md-4">
                <div class="mb-3">
                    <label for="dosen_anggota2" class="form-label">Dosen Anggota 2</label>
                    <select name="dosen_anggota2" id="dosen_anggota2" class="form-select form-select-sm">
                        <option value="" selected>Pilih jika ada...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    {{-- BAGIAN 3: MEDIA PEMBELAJARAN --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-desktop me-2"></i>Media Pembelajaran</h5>
     <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="media_software" class="form-label">Software <span class="text-danger">*</span></label>
                <textarea name="media_software" id="media_software" class="form-control form-control-sm" placeholder="Contoh: Virtual Class, Python, ..." required></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="media_hardware" class="form-label">Hardware <span class="text-danger">*</span></label>
                <textarea name="media_hardware" id="media_hardware" class="form-control form-control-sm" placeholder="Contoh: Komputer/Laptop, Proyektor, ..." required></textarea>
            </div>
        </div>
    </div>
    
    {{-- BAGIAN 4: PUSTAKA & SYARAT KELULUSAN --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-agenda me-2"></i>Pustaka & Syarat Kelulusan</h5>
    {{-- === PUSTAKA UTAMA === --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Pustaka Utama <span class="text-danger">*</span></label>
        <div id="container-pustaka-utama">
            {{-- Item Template Utama --}}
            <div class="pustaka-item mb-2">
                <div class="input-group">
                    <select name="pustaka_utama[]" class="form-select form-select-sm pustaka-dropdown" required disabled>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <option value="tambah_baru" class="fw-bold text-primary">-- Tambah Pustaka Baru --</option>
                    </select>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-pustaka-btn" style="display:none;">Hapus</button>
                </div>
                
                {{-- Form Tambah Baru (Hanya muncul jika pilih Tambah Baru) --}}
                <div class="pustaka-baru-form border p-2 mt-2 rounded bg-light" style="display: none;">
                    <small class="fw-bold text-muted d-block mb-1">Input Pustaka Baru:</small>
                    <div class="row gx-2">
                        <div class="col-12 mb-1"><input type="text" name="new_judul_utama[]" class="form-control form-control-sm" placeholder="Judul Lengkap"></div>
                        <div class="col-6 mb-1"><input type="text" name="new_penulis_utama[]" class="form-control form-control-sm" placeholder="Penulis"></div>
                        <div class="col-4 mb-1"><input type="text" name="new_penerbit_utama[]" class="form-control form-control-sm" placeholder="Penerbit"></div>
                        <div class="col-2 mb-1"><input type="number" name="new_tahun_utama[]" class="form-control form-control-sm" placeholder="Thn"></div>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="btn-add-utama">+ Tambah Baris Utama</button>
    </div>

    {{-- === PUSTAKA PENDUKUNG === --}}
    <div class="mb-3 border-top pt-3">
        <label class="form-label fw-bold">Pustaka Pendukung</label>

        <div id="container-pustaka-pendukung">
            <div class="pustaka-item mb-2">
                <div class="input-group">
                    <select name="pustaka_pendukung[]" class="form-select form-select-sm pustaka-dropdown" disabled>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <option value="tambah_baru" class="fw-bold text-primary">-- Tambah Pustaka Baru --</option>
                    </select>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-pustaka-btn" style="display:none;">Hapus</button>
                </div>

                <div class="pustaka-baru-form border p-2 mt-2 rounded bg-light" style="display: none;">
                    <small class="fw-bold text-muted d-block mb-1">Input Pustaka Baru:</small>
                    <div class="row gx-2">
                        <div class="col-12 mb-1"><input type="text" name="new_judul_pendukung[]" class="form-control form-control-sm" placeholder="Judul Lengkap"></div>
                        <div class="col-6 mb-1"><input type="text" name="new_penulis_pendukung[]" class="form-control form-control-sm" placeholder="Penulis"></div>
                        <div class="col-4 mb-1"><input type="text" name="new_penerbit_pendukung[]" class="form-control form-control-sm" placeholder="Penerbit"></div>
                        <div class="col-2 mb-1"><input type="number" name="new_tahun_pendukung[]" class="form-control form-control-sm" placeholder="Thn"></div>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="btn-add-pendukung">+ Tambah Pustaka Pendukung</button>
    </div>


     <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="batas_kelulusan_mhs" class="form-label">Ambang Batas Kelulusan Mahasiswa <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" max="100" name="batas_kelulusan_mhs" id="batas_kelulusan_mhs" class="form-control form-control-sm" placeholder="contoh: 50.01" readonly required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="batas_kelulusan_mk" class="form-label">Ambang Batas Kelulusan Mata Kuliah <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" max="100" name="batas_kelulusan_mk" id="batas_kelulusan_mk" class="form-control form-control-sm" placeholder="contoh: 75.50" readonly required>
            </div>
        </div>
    </div>

    <div class="modal-footer mt-4 border-top pt-3">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan RPS</button>
    </div>
</form>
@push('scripts')
<script>
// Hapus script lama terkait pustaka, ganti dengan ini:
$(document).ready(function() {

    // --- FUNGSI LOAD PUSTAKA UNTUK SEMUA DROPDOWN ---
    function loadPustakaOptions(kode_mk) {
        console.log("Loading Pustaka MK:", kode_mk);
        
        // Targetkan SEMUA dropdown pustaka (utama & pendukung)
        const allSelects = $('.pustaka-dropdown');
        
        // Set state loading
        allSelects.prop('disabled', true);
        allSelects.find('option[value=""]').text(kode_mk ? 'Loading...' : '-- Pilih Mata Kuliah --');

        if(!kode_mk) return;

        $.ajax({
            url: `{{ route('dosen.get-pustaka-by-mk', ['kode_mk' => ':kode_mk']) }}`.replace(':kode_mk', kode_mk),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log("Data received:", data.length);
                
                allSelects.each(function() {
                    const sel = $(this);
                    // Simpan opsi default (placeholder & tambah baru)
                    const defaultOpts = sel.find('option[value=""], option[value="tambah_baru"]');
                    sel.empty().append(defaultOpts);

                    if (data.length > 0) {
                        $.each(data, function(i, p) {
                            // Format Text Dropdown
                            const label = `${p.kode_pustaka} ${p.judul}, ${p.penulis}. ${p.penerbit}. ${p.tahun}`;
                            // Insert SEBELUM opsi 'Tambah Baru' (jika ada), atau append
                            const opt = `<option value="${p.id}">${label}</option>`;
                            
                            if(sel.find('option[value="tambah_baru"]').length > 0){
                                sel.find('option[value="tambah_baru"]').before(opt);
                            } else {
                                sel.append(opt);
                            }
                        });
                        sel.find('option[value=""]').text('-- Pilih Pustaka --');
                    } else {
                        sel.find('option[value=""]').text('-- Tidak ada data pustaka --');
                    }
                    sel.prop('disabled', false);
                });
            },
            error: function(xhr) {
                console.error(xhr);
                allSelects.find('option[value=""]').text('Error Loading');
            }
        });
    }

    // --- EVENT LISTENER MK ---
    $('body').on('change', '#addRpsModal select[name="matakuliah"]', function() {
        loadPustakaOptions($(this).val());
    });

    // --- LOGIC TAMBAH BARIS (UTAMA) ---
    $('#btn-add-utama').click(function() {
        const template = $('#container-pustaka-utama .pustaka-item:first').clone();
        resetRow(template);
        $('#container-pustaka-utama').append(template);
    });

    // --- LOGIC TAMBAH BARIS (PENDUKUNG) ---
    $('#btn-add-pendukung').click(function() {
        const template = $('#container-pustaka-pendukung .pustaka-item:first').clone();
        resetRow(template);
        $('#container-pustaka-pendukung').append(template);
    });

    // --- LOGIC HAPUS BARIS ---
    $(document).on('click', '.remove-pustaka-btn', function() {
        // Cek jumlah baris dalam container yang sama
        const container = $(this).closest('div[id^="container-pustaka"]');
        if(container.find('.pustaka-item').length > 1) {
            $(this).closest('.pustaka-item').remove();
        } else {
            alert("Minimal satu baris harus ada!");
        }
    });

    // --- LOGIC MUNCUL FORM TAMBAH BARU ---
    $(document).on('change', '.pustaka-dropdown', function() {
        const formBaru = $(this).closest('.pustaka-item').find('.pustaka-baru-form');
        if($(this).val() === 'tambah_baru') {
            formBaru.slideDown();
            formBaru.find('input').prop('required', true);
        } else {
            formBaru.slideUp();
            formBaru.find('input').prop('required', false).val('');
        }
    });

    // Helper Reset Row saat Clone
    function resetRow(row) {
        row.find('select').val(''); // Reset pilihan
        row.find('.pustaka-baru-form').hide().find('input').val(''); // Reset form baru
        row.find('.remove-pustaka-btn').show(); // Pastikan tombol hapus muncul
    }
});
</script>
@endpush

<script>
    const dosenSelectors = '#dosen, #dosen_anggota1, #dosen_anggota2';

    function updateDosenOptions() { 
        let selectedDosen = [];
        $(dosenSelectors).each(function() {
            if ($(this).val()) {
                selectedDosen.push($(this).val());
            }
        });
 
        $(dosenSelectors).each(function() {
            const currentDropdown = $(this);
            const currentValue = currentDropdown.val();
 
            currentDropdown.find('option').each(function() {
                const option = $(this);
                const optionValue = option.val();
 
                if (optionValue === "") {
                    option.prop('disabled', false);
                    return;
                }
 
                if (selectedDosen.includes(optionValue) && optionValue !== currentValue) {
                    option.prop('disabled', true);
                } else {
                    // Jika tidak, aktifkan kembali
                    option.prop('disabled', false);
                }
            });
        });
    } 
    $(dosenSelectors).on('change', function() {
        updateDosenOptions();
    }); 
    updateDosenOptions();
</script>
<script>
$(document).ready(function () {
    function updateMKDefaults() {
        const selected = $('#matakuliah').find(':selected');

        // Semester
        $('#semester').val(selected.data('semester') || '');

        // Ambang batas
        const batasMhs = selected.data('batas-mhs');
        const batasMk  = selected.data('batas-mk');

        $('#batas_kelulusan_mhs').val(
            (batasMhs !== undefined && batasMhs !== null) ? batasMhs : ''
        );

        $('#batas_kelulusan_mk').val(
            (batasMk !== undefined && batasMk !== null) ? batasMk : ''
        );
    }

    $('#matakuliah').on('change', updateMKDefaults);
});
</script>