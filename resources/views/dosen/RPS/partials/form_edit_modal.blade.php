<form method="POST" action="{{ route($currentPrefix . 'rps-update', $rps->id) }}" id="formEditRps{{$rps->id}}">
    @csrf
    @method('PUT')

{{-- BAGIAN 1: INFORMASI DASAR --}}
    <h5 class="border-bottom pb-2 mb-3">
        <i class="ti-info-alt me-2"></i>Informasi Dasar
    </h5>

    <div class="row">
        {{-- Kolom Mata Kuliah --}}
        <div class="col-md-8">
            <div class="mb-3">
                <label class="form-label">Mata Kuliah</label>
                {{-- Note: Class form-control ditambahkan agar kotak inputnya tampil penuh & rapi --}}
                <input type="text" 
                       class="form-control form-control-sm bg-light" 
                       value="{{ ($rps->mk?->kode ?? $rps->kode_mk) }} - {{ $rps->mk?->nama ?? 'MK tidak ditemukan' }}" 
                       readonly>
            </div>
        </div>

        {{-- Kolom Semester --}}
        <div class="col-md-4">
            <div class="mb-3">
                <label for="semester_edit_{{$rps->id}}" class="form-label">Semester</label>
                <input type="text" 
                       name="semester" 
                       id="semester_edit_{{$rps->id}}" 
                       class="form-control form-control-sm bg-light" 
                       value="{{ $rps->mk?->semester ?? $rps->semester ?? '-' }}" 
                       readonly>
            </div>
        </div>
    </div>
    
    {{-- BAGIAN 2: PENANGGUNG JAWAB & TIM DOSEN --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-user me-2"></i>Penanggung Jawab & Tim Dosen</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="pengembang_edit_{{$rps->id}}" class="form-label">Pengembang RPS <span class="text-danger">*</span></label>
                <select name="pengembang" id="pengembang_edit_{{$rps->id}}" class="form-select form-select-sm" required>
                    <option value="{{ auth()->user()->name }}" selected>{{ auth()->user()->name }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="koordinator_edit_{{$rps->id}}" class="form-label">Koordinator RMK</label>
                <select name="koordinator" id="koordinator_edit_{{$rps->id}}" class="form-select form-select-sm">
                    <option value="">Pilih jika ada...</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->name }}" {{ $rps->koordinator == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
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
                    <label for="dosen_edit_{{$rps->id}}" class="form-label">Dosen Pengampu <span class="text-danger">*</span></label>
                    <select name="dosen" id="dosen_edit_{{$rps->id}}" class="form-select form-select-sm team-teaching-select" required>
                        <option value="" disabled>Pilih...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}" {{ $rps->dosen == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="dosen_anggota1_edit_{{$rps->id}}" class="form-label">Dosen Anggota 1 <span class="text-danger">*</span></label>
                    <select name="dosen_anggota1" id="dosen_anggota1_edit_{{$rps->id}}" class="form-select form-select-sm team-teaching-select" required>
                        <option value="" disabled>Pilih...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}" {{ $rps->dosen_anggota1 == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="dosen_anggota2_edit_{{$rps->id}}" class="form-label">Dosen Anggota 2</label>
                    <select name="dosen_anggota2" id="dosen_anggota2_edit_{{$rps->id}}" class="form-select form-select-sm team-teaching-select">
                        <option value="">Pilih jika ada...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}" {{ $rps->dosen_anggota2 == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
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
                <label for="media_software_edit_{{$rps->id}}" class="form-label">Software <span class="text-danger">*</span></label>
                <textarea name="media_software" id="media_software_edit_{{$rps->id}}" class="form-control form-control-sm" required>{{ $rps->media_software }}</textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="media_hardware_edit_{{$rps->id}}" class="form-label">Hardware <span class="text-danger">*</span></label>
                <textarea name="media_hardware" id="media_hardware_edit_{{$rps->id}}" class="form-control form-control-sm" required>{{ $rps->media_hardware }}</textarea>
            </div>
        </div>
    </div>
    
    {{-- BAGIAN 4: PUSTAKA --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-agenda me-2"></i>Pustaka & Syarat Kelulusan</h5>

    {{-- === PUSTAKA UTAMA === --}}
    <div class="mb-4">
        <label class="form-label fw-bold">Pustaka Utama <span class="text-danger">*</span></label>
        
        <div id="container-pustaka-utama-{{ $rps->id }}" data-type="utama">
            {{-- Loop Pustaka Utama dari Relasi Eloquent --}}
            @forelse($rps->pustakaUtama as $pustaka)
                <div class="pustaka-item mb-2"> 
                    <div class="input-group">
                        <select name="pustaka_utama[]" class="form-select form-select-sm pustaka-utama" data-selected-id="{{ $pustaka->id }}"> 
                            <option value="{{ $pustaka->id }}" selected>{{ $pustaka->judul }}</option>
                        </select>
                        <button class="btn btn-sm btn-outline-danger remove-pustaka-btn" type="button">Hapus</button>
                    </div>
                    
                    {{-- Form Tambah Baru (Hidden Default) --}}
                    <div class="pustaka-baru-form border p-2 mt-2 rounded bg-light" style="display: none;"> 
                        <small class="fw-bold mb-1 d-block">Pustaka Baru (Utama)</small>
                        <div class="row gx-2">
                            <div class="col-12 mb-1"><input type="text" name="new_pustaka_judul[]" class="form-control form-control-sm" placeholder="Judul Buku"></div>
                            <div class="col-6 mb-1"><input type="text" name="new_pustaka_penulis[]" class="form-control form-control-sm" placeholder="Penulis"></div>
                            <div class="col-4 mb-1"><input type="text" name="new_pustaka_penerbit[]" class="form-control form-control-sm" placeholder="Penerbit"></div>
                            <div class="col-2 mb-1"><input type="number" name="new_pustaka_tahun[]" class="form-control form-control-sm" placeholder="Tahun"></div>
                        </div>
                    </div>
                </div> 
            @empty
                {{-- Template Jika Kosong --}}
                <div class="pustaka-item mb-2"> 
                    <div class="input-group">
                        <select name="pustaka_utama[]" class="form-select form-select-sm pustaka-utama" required>
                             <option value="">-- Loading Pustaka... --</option>
                        </select>
                         <button class="btn btn-sm btn-outline-danger remove-pustaka-btn" type="button" style="display: none;">Hapus</button>
                    </div>
                     <div class="pustaka-baru-form border p-2 mt-2 rounded bg-light" style="display: none;"> 
                        <small class="fw-bold mb-1 d-block">Pustaka Baru (Utama)</small>
                        <div class="row gx-2">
                            <div class="col-12 mb-1"><input type="text" name="new_pustaka_judul[]" class="form-control form-control-sm" placeholder="Judul Buku"></div>
                            <div class="col-6 mb-1"><input type="text" name="new_pustaka_penulis[]" class="form-control form-control-sm" placeholder="Penulis"></div>
                            <div class="col-4 mb-1"><input type="text" name="new_pustaka_penerbit[]" class="form-control form-control-sm" placeholder="Penerbit"></div>
                            <div class="col-2 mb-1"><input type="number" name="new_pustaka_tahun[]" class="form-control form-control-sm" placeholder="Tahun"></div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        <button type="button" class="btn btn-sm btn-outline-success add-pustaka-btn" data-target="#container-pustaka-utama-{{ $rps->id }}">+ Tambah Utama</button>
    </div>

    {{-- === PUSTAKA PENDUKUNG === --}}
    <div class="mb-4 border-top pt-3">
        <label class="form-label fw-bold">Pustaka Pendukung</label>

        <div id="container-pustaka-pendukung-{{ $rps->id }}" data-type="pendukung">
            @forelse($rps->pustakaPendukung as $pustaka)
                <div class="pustaka-item mb-2">
                    <div class="input-group">
                        <select name="pustaka_pendukung[]" class="form-select form-select-sm pustaka-pendukung" data-selected-id="{{ $pustaka->id }}">
                            <option value="{{ $pustaka->id }}" selected>{{ $pustaka->judul }}</option>
                        </select>
                        <button class="btn btn-sm btn-outline-danger remove-pustaka-btn" type="button">Hapus</button>
                    </div>

                    <div class="pustaka-baru-form border p-2 mt-2 rounded bg-light" style="display: none;">
                        <small class="fw-bold mb-1 d-block">Pustaka Baru (Pendukung)</small>
                        <div class="row gx-2">
                            <div class="col-12 mb-1"><input type="text" name="new_pustaka_judul_pendukung[]" class="form-control form-control-sm" placeholder="Judul Buku"></div>
                            <div class="col-6 mb-1"><input type="text" name="new_pustaka_penulis_pendukung[]" class="form-control form-control-sm" placeholder="Penulis"></div>
                            <div class="col-4 mb-1"><input type="text" name="new_pustaka_penerbit_pendukung[]" class="form-control form-control-sm" placeholder="Penerbit"></div>
                            <div class="col-2 mb-1"><input type="number" name="new_pustaka_tahun_pendukung[]" class="form-control form-control-sm" placeholder="Tahun"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="pustaka-item mb-2">
                    <div class="input-group">
                        <select name="pustaka_pendukung[]" class="form-select form-select-sm pustaka-pendukung">
                            <option value="">-- Loading Pustaka... --</option>
                        </select>
                        <button class="btn btn-sm btn-outline-danger remove-pustaka-btn" type="button" style="display: none;">Hapus</button>
                    </div>

                    <div class="pustaka-baru-form border p-2 mt-2 rounded bg-light" style="display: none;">
                        <small class="fw-bold mb-1 d-block">Pustaka Baru (Pendukung)</small>
                        <div class="row gx-2">
                            <div class="col-12 mb-1"><input type="text" name="new_pustaka_judul_pendukung[]" class="form-control form-control-sm" placeholder="Judul Buku"></div>
                            <div class="col-6 mb-1"><input type="text" name="new_pustaka_penulis_pendukung[]" class="form-control form-control-sm" placeholder="Penulis"></div>
                            <div class="col-4 mb-1"><input type="text" name="new_pustaka_penerbit_pendukung[]" class="form-control form-control-sm" placeholder="Penerbit"></div>
                            <div class="col-2 mb-1"><input type="number" name="new_pustaka_tahun_pendukung[]" class="form-control form-control-sm" placeholder="Tahun"></div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div> {{-- ✅ INI penutup container-pustaka-pendukung --}}

        <button type="button"
            class="btn btn-sm btn-outline-secondary add-pustaka-btn"
            data-target="#container-pustaka-pendukung-{{ $rps->id }}">
            + Tambah Pendukung
        </button>
    </div>


    {{-- SYARAT KELULUSAN (default dari MK) --}}
    <div class="row border-top pt-3">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="batas_kelulusan_mhs_edit_{{$rps->id}}" class="form-label">
                    Ambang Batas Kelulusan Mahasiswa <span class="text-danger">*</span>
                </label>
                <input type="number" step="0.01" min="0" max="100"
                    name="batas_kelulusan_mhs"
                    id="batas_kelulusan_mhs_edit_{{$rps->id}}"
                    class="form-control form-control-sm bg-light"
                    value="{{ old('batas_kelulusan_mhs', $rps->mk?->batas_kelulusan_mhs ?? $rps->batas_kelulusan_mhs) }}"
                    readonly required>
            </div>
        </div>
    
        <div class="col-md-6">
            <div class="mb-3">
                <label for="batas_kelulusan_mk_edit_{{$rps->id}}" class="form-label">
                    Ambang Batas Kelulusan Mata Kuliah <span class="text-danger">*</span>
                </label>
                <input type="number" step="0.01" min="0" max="100"
                    name="batas_kelulusan_mk"
                    id="batas_kelulusan_mk_edit_{{$rps->id}}"
                    class="form-control form-control-sm bg-light"
                    value="{{ old('batas_kelulusan_mk', $rps->mk?->batas_kelulusan_mk ?? $rps->batas_kelulusan_mk) }}"
                    readonly required>
            </div>
        </div>
    </div>

    <div class="modal-footer mt-4 border-top pt-3">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<script>
$(function() {
    const modalId = '#editRpsModal{{ $rps->id }}';
    const rpsId   = '{{ $rps->id }}';
    const urlBase = `{{ route($currentPrefix.'pustaka-get') }}`;

    // =========================================
    // Helper: show/hide form tambah baru sesuai value select
    // =========================================
    function syncPustakaBaruForm($select) {
        const val = $select.val();
        const $formBaru = $select.closest('.pustaka-item').find('.pustaka-baru-form');

        if (val === 'tambah_baru') {
            $formBaru.stop(true, true).slideDown(200);
            $formBaru.find('input').prop('required', true);
        } else {
            $formBaru.stop(true, true).slideUp(200);
            $formBaru.find('input').prop('required', false).val('');
        }
    }

    // =========================================
    // Helper: render option list (UTAMA & PENDUKUNG)
    // =========================================
    function renderOptions($select, data, savedId) {
        $select.empty();

        if (data && data.length) {
            $select.append('<option value="">-- Pilih Pustaka --</option>');
            $.each(data, function(_, p) {
                const text = `${p.kode_pustaka} ${p.judul}, ${p.penulis}. ${p.penerbit}. ${p.tahun}`;
                $select.append(`<option value="${p.id}">${text}</option>`);
            });
        } else {
            $select.append('<option value="">-- Belum Ada Data Pustaka --</option>');
        }

        // ✅ Tambah baru untuk BOTH utama & pendukung
        $select.append('<option value="tambah_baru" class="text-primary fw-bold">-- Tambah Pustaka Baru --</option>');

        // set selected kalau ada
        if (savedId) {
            $select.val(String(savedId));
        } else {
            $select.val('');
        }

        $select.prop('disabled', false);

        // ✅ penting: sinkronkan state form tambah baru setelah options diganti
        syncPustakaBaruForm($select);
    }

    // =========================================
    // Load pustaka per sifat: utama / pendukung
    // =========================================
    function loadPustakaBySifat(sifat) {
        const $dropdowns = (sifat === 'utama')
            ? $(modalId).find('select.pustaka-utama')
            : $(modalId).find('select.pustaka-pendukung');

        // state loading
        $dropdowns.prop('disabled', true).html('<option value="" selected>Loading...</option>');

        return $.ajax({
            url: urlBase,
            type: 'GET',
            dataType: 'json',
            data: { rpsId: rpsId, sifat: sifat },
            success: function(data) {
                $dropdowns.each(function() {
                    const $sel = $(this);
                    const savedId = $sel.data('selected-id') || $sel.val();
                    renderOptions($sel, data, savedId);
                });
            },
            error: function() {
                $dropdowns.prop('disabled', false).html('<option value="" selected>Gagal memuat data</option>');
            }
        });
    }

    // =========================================
    // Dosen logic (team teaching) - tetap
    // =========================================
    const dosenSelectors = $(modalId).find('.team-teaching-select');

    function updateDosenOptions() {
        const selected = dosenSelectors.map(function() { return $(this).val(); }).get();
        dosenSelectors.each(function() {
            const cur = $(this).val();
            $(this).find('option').each(function() {
                const val = $(this).val();
                if (!val) return $(this).prop('disabled', false);
                $(this).prop('disabled', selected.includes(val) && val !== cur);
            });
        });
    }
    dosenSelectors.on('change', updateDosenOptions);

    // =========================================
    // Event handlers
    // =========================================

    // 1) Modal edit muncul -> load pustaka + sync state
    $(modalId).on('shown.bs.modal', function() {
        loadPustakaBySifat('utama');
        loadPustakaBySifat('pendukung');
        updateDosenOptions();

        // ✅ safety: sync semua form baru (kalau ada yang nyangkut)
        $(modalId).find('select.pustaka-utama, select.pustaka-pendukung').each(function() {
            syncPustakaBaruForm($(this));
        });
    });

    // 2) Tambah baris (generic)
    // 2) Tambah baris (generic) TANPA reload semua dropdown
    $(modalId).on('click', '.add-pustaka-btn', function() {
        const $targetContainer = $($(this).data('target'));
        const $firstItem = $targetContainer.find('.pustaka-item:first');
        let $template = $firstItem.clone();

        // ambil option dari select pertama yang sudah ada
        const existingOptions = $firstItem.find('select').html();

        // pasang option yang sama ke baris baru
        $template.find('select')
            .html(existingOptions)
            .val('')
            .removeAttr('data-selected-id')
            .prop('disabled', false);

        // reset form pustaka baru
        $template.find('.pustaka-baru-form')
            .hide()
            .find('input')
            .val('')
            .prop('required', false);

        $template.find('.remove-pustaka-btn').show();

        $targetContainer.append($template);
    });

    // 3) Hapus baris
    $(modalId).on('click', '.remove-pustaka-btn', function() {
        const $item = $(this).closest('.pustaka-item');
        const $container = $item.parent();

        if ($container.find('.pustaka-item').length > 1) {
            $item.remove();
        } else {
            alert('Minimal sisakan satu baris.');
        }
    });

    // 4) Change dropdown -> toggle form tambah baru
    $(modalId).on('change', 'select.pustaka-utama, select.pustaka-pendukung', function() {
        syncPustakaBaruForm($(this));
    });
});
</script>
