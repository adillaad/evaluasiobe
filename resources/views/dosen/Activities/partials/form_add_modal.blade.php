<form action="{{ route($currentPrefix . 'activities-store') }}" method="post">
    @csrf
    <input type="hidden" name="id_rps" value="{{ $rps->id }}">

    <h5 class="border-bottom pb-2 mb-3"><i class="ti-info-alt me-2"></i>Informasi Dasar</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="minggu" class="form-label">Minggu Ke- <span class="text-danger">*</span></label>
                <select name="minggu" id="minggu" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Minggu --</option>
                    @foreach($availableWeeks as $week)
                        <option value="{{ $week }}">Minggu ke-{{ $week }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        {{-- CPMK DINAMIS --}}
        <div class="col-md-6">
             <div class="mb-3">
                <label class="form-label">CPMK Terkait <span class="text-danger">*</span></label>
                <div id="dynamic-cpmk-container">
                    <div class="input-group mb-2">
                        <select name="id_cpmk[]" class="form-select form-select-sm" required>
                            <option value="">-- Pilih CPMK Terkait --</option>
                            @foreach($cpmks as $cpmk)
                                <option value="{{ $cpmk->id }}">{{ $cpmk->kode }} - {{ $cpmk->judul }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="dynamic-btn-cpmk" class="btn btn-sm btn-outline-success" title="Tambah CPMK">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: KONTEN PEMBELAJARAN --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-book me-2"></i>Konten Pembelajaran</h5>
    <div class="row"> 
        <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Deskripsi Sub CPMK <span class="text-danger">*</span></label>
            
            <div id="dynamic-sub-cpmk">
                <div class="input-group mb-2">
                    <!-- <select name="sub_cpmk[]" class="form-select form-select-sm" required>
                        <option selected disabled value="">Pilih Sub CPMK...</option>
                        {{-- Loop data sub_cpmk yang dikirim dari controller --}}
                        @foreach ($sub_cpmks as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->uraian }}</option>
                        @endforeach
                    </select> -->
                    <select name="sub_cpmk[]" class="form-select form-select-sm subcpmk-select" disabled>
                        <option value="" selected>Pilih CPMK dulu...</option>
                    </select>
                    <button type="button" id="dynamic-btn-sub-cpmk" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                </div>
            </div>

        </div>
    </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Indikator <span class="text-danger">*</span></label>
                <div id="dynamic-indikator">
                    <div class="input-group mb-2">
                        <input type="text" name="indikator[]" class="form-control form-control-sm" placeholder="Tuliskan Indikator..." required>
                        <button type="button" id="dynamic-btn-indikator" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
             <div class="mb-3">
                <label class="form-label">Materi <span class="text-danger">*</span></label>
                <div id="dynamic-materi">
                    <div class="input-group mb-2">
                        <input type="text" name="materi[]" class="form-control form-control-sm" placeholder="Tuliskan Materi..." required>
                        <button type="button" id="dynamic-btn-materi" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- BAGIAN 3: Bentuk Asesmen --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-check-box me-2"></i>Bentuk Asesmen</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="bentuk_asesmen" class="form-label">Bentuk Asesmen <span class="text-danger">*</span></label>
                {{-- <select name="bentuk_asesmen" id="bentuk_asesmen" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Bentuk Asesmen --</option>
                    @foreach($instrumen as $ins)
                        <option value="{{ $ins->nama }}">{{ $ins->nama }}</option>
                    @endforeach
                </select> --}}
                <div id="dynamic-asesmen">
                    <div class="input-group mb-2">
                        <select name="bentuk_asesmen[]" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Bentuk Asesmen --</option>
                            @foreach($instrumen as $ins)
                                <option value="{{ $ins->nama }}">{{ $ins->nama }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="dynamic-btn-asesmen" class="btn btn-sm btn-outline-success" title="Tambah Asesmen">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- BAGIAN 4: METODE PEMBELAJARAN (SESUAI GAMBAR) --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-blackboard me-2"></i>Metode Pembelajaran</h5>
    <div class="row">
        <div class="col-md-12">
            <label class="form-label">Detail Metode <span class="text-danger">*</span></label>
            <div id="dynamic-metode-container">
                {{-- Template 1 item metode --}}
                <div class="input-group mb-2">
                    <input type="text" name="metode_deskripsi[]" class="form-control form-control-sm" placeholder="Deskripsi (Cth: Ceramah dan Diskusi, Tugas 1)" required>
                    <select name="metode_kategori[]" class="form-select form-select-sm" style="max-width: 150px;" required>
                        <option value="TM">TM (Tatap Muka)</option>
                        <option value="BM">BM (Belajar Mandiri)</option>
                        <option value="BT">BT (Belajar Terstruktur)</option>
                        <option value="DR">DR (Diskusi dan Refleksi)</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <input type="text" name="metode_waktu[]" class="form-control form-control-sm" style="max-width: 170px;" placeholder="Alokasi Waktu (Cth: 1x(3x50'))" required>
                    <button type="button" id="dynamic-btn-metode" class="btn btn-sm btn-outline-success" title="Tambah Metode">+</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-3">
             <div class="mb-3">
                <label class="form-label">Pustaka</label>
                <div id="dynamic-pustaka">
                    <div class="input-group mb-2">
                        <select name="pustaka[]" class="form-select form-select-sm">
                            <option value="">-- Pilih Pustaka --</option>
                            {{-- Loop data pustaka dari controller --}}
                            @foreach ($pustakas as $pustaka)
                                <option value="{{ $pustaka->kode_pustaka }}">
                                    {{ $pustaka->kode_pustaka }} - {{ $pustaka->judul }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="dynamic-btn-pustaka" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 5: BENTUK KEGIATAN (LURING & DARING) --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-desktop me-2"></i>Bentuk Kegiatan (Luring/Daring)</h5>
     <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Kegiatan Luring</label>
                <div id="dynamic-kegiatan-luring">
                    <div class="input-group mb-2">
                        <input type="text" name="kegiatan_luring[]" class="form-control form-control-sm" placeholder="Tuliskan kegiatan luring...">
                        <button type="button" id="dynamic-btn-luring" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Kegiatan Daring</label>
                <div id="dynamic-kegiatan-daring">
                    <div class="input-group mb-2">
                        <input type="text" name="kegiatan_daring[]" class="form-control form-control-sm" placeholder="Tuliskan kegiatan daring...">
                        <button type="button" id="dynamic-btn-daring" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer mt-4 border-top pt-3">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Aktivitas</button>
    </div>
</form>

<script>
$(function() {
    function addDynamicField(containerSelector, name) {
        const placeholder = $(containerSelector).find('input:first').attr('placeholder') || 'Tuliskan...';
        // Menggunakan .form-control-sm pada field yang baru dibuat
        const template = `<div class="input-group mb-2"><input type="text" name="${name}[]" class="form-control form-control-sm" placeholder="${placeholder}"><button type="button" class="btn btn-sm btn-outline-danger remove-field">Hapus</button></div>`;
        $(containerSelector).append(template);
    }
    // 1. Script untuk CPMK
    $('#dynamic-btn-cpmk').click(function() {
        let container = $('#dynamic-cpmk-container');
    
        const newField = container.find('.input-group:first').clone();
        newField.find('select').val(''); // Kosongkan pilihan
    
        // Ubah tombol + jadi tombol Hapus
        newField.find('button')
            .removeAttr('id')
            .removeClass('btn-outline-success').addClass('btn-outline-danger')
            .addClass('remove-field')
            .html('Hapus');
    
        container.append(newField);
    });

    $('#dynamic-btn-sub-cpmk').click(function() {
        // 1. Ambil elemen pertama (yang berisi dropdown asli) dan duplikasi
        const newField = $('#dynamic-sub-cpmk .input-group:first').clone();

        // 2. Kosongkan pilihan di dropdown yang baru
        newField.find('select').addClass('subcpmk-select').val('');

        // 3. Ubah tombol '+' menjadi tombol 'Hapus'
        newField.find('button')
            .removeAttr('id') // Hapus ID agar tidak duplikat
            .removeClass('btn-outline-success').addClass('btn-outline-danger')
            .addClass('remove-field') // Tambahkan class ini agar bisa dihapus
            .html('Hapus');

        // 4. Tambahkan elemen baru ke dalam container
        $('#dynamic-sub-cpmk').append(newField);
    });
    
    $('#dynamic-btn-indikator').click(() => addDynamicField('#dynamic-indikator', 'indikator'));
    $('#dynamic-btn-materi').click(() => addDynamicField('#dynamic-materi', 'materi'));

    $('#dynamic-btn-metode').click(function() {
        const template = `
        <div class="input-group mb-2">
            <input type="text" name="metode_deskripsi[]" class="form-control form-control-sm" placeholder="Deskripsi (Cth: Ceramah dan Diskusi, Tugas 1)" required>
            <select name="metode_kategori[]" class="form-select form-select-sm" style="max-width: 150px;" required>
                <option value="TM">TM (Tatap Muka)</option>
                <option value="BM">BM (Belajar Mandiri)</option>
                <option value="BT">BT (Belajar Terstruktur)</option>
                <option value="DR">DR (Diskusi dan Refleksi)</option>
                <option value="Lainnya">Lainnya</option>
            </select>
            <input type="text" name="metode_waktu[]" class="form-control form-control-sm" style="max-width: 170px;" placeholder="Alokasi Waktu (Cth: 1x(3x50'))" required>
            <button type="button" class="btn btn-sm btn-outline-danger remove-field">Hapus</button>
        </div>`;
        $('#dynamic-metode-container').append(template);
    });

    $('#dynamic-btn-asesmen').click(function() {
        const newField = $('#dynamic-asesmen .input-group:first').clone();

        newField.find('select').val('');

        newField.find('button')
            .removeAttr('id')
            .removeClass('btn-outline-success')
            .addClass('btn-outline-danger remove-field')
            .html('Hapus');

        $('#dynamic-asesmen').append(newField);
    });
 
    $('#dynamic-btn-pustaka').click(function() {
        // 1. Ambil elemen pertama (yang berisi dropdown asli) dan duplikasi
        const newField = $('#dynamic-pustaka .input-group:first').clone();

        // 2. Kosongkan pilihan di dropdown yang baru
        newField.find('select').val('');

        // 3. Ubah tombol '+' menjadi tombol 'Hapus'
        newField.find('button')
            .removeAttr('id') // Hapus ID agar tidak duplikat
            .removeClass('btn-outline-success').addClass('btn-outline-danger')
            .addClass('remove-field') // Tambahkan class ini agar bisa dihapus
            .html('Hapus');

        // 4. Tambahkan elemen baru ke dalam container
        $('#dynamic-pustaka').append(newField);
    }); 
    $('#dynamic-btn-luring').click(() => addDynamicField('#dynamic-kegiatan-luring', 'kegiatan_luring'));
    $('#dynamic-btn-daring').click(() => addDynamicField('#dynamic-kegiatan-daring', 'kegiatan_daring'));

    $(document).on('click', '.remove-field', function() {
        $(this).closest('.input-group').remove(); 
    });

    function fillSubCpmkOptions($select, items) {
        $select.empty();
        $select.append(`<option value="" selected disabled>Pilih Sub CPMK...</option>`);
        items.forEach(it => {
            $select.append(`<option value="${it.id}">${it.kode} - ${it.uraian}</option>`);
        });
        $select.prop('disabled', false);
    }

    // ambil semua CPMK yg dipilih (maks 2), gabung subcpmk dari semuanya
    function refreshSubCpmkAddForm() {
        const cpmkIds = [];
        $('#dynamic-cpmk-container select[name="id_cpmk[]"]').each(function(){
            const v = $(this).val();
            if (v) cpmkIds.push(v);
        });

        const $allSubSelect = $('#dynamic-sub-cpmk select.subcpmk-select');

        if (cpmkIds.length === 0) {
            $allSubSelect.prop('disabled', true).html(`<option value="" selected>Pilih CPMK dulu...</option>`);
            return;
        }

        const requests = cpmkIds.map(id =>
            $.getJSON(`{{ route($currentPrefix.'subcpmk-by-cpmk', ['cpmkId' => '___']) }}`.replace('___', id))
        );

        Promise.all(requests).then(results => {
            // flatten + unik by id
            const map = new Map();
            results.flat().forEach(it => map.set(it.id, it));
            const merged = Array.from(map.values());

            $allSubSelect.each(function(){
                fillSubCpmkOptions($(this), merged);
            });
        });
    }

    // trigger saat CPMK berubah (termasuk field clone)
    $(document).on('change', '#dynamic-cpmk-container select[name="id_cpmk[]"]', refreshSubCpmkAddForm);

    // pas halaman/modal kebuka, set awal
    refreshSubCpmkAddForm();
});
</script>