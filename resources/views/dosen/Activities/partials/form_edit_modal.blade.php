<form action="{{ route($currentPrefix . 'activity-update', $activity->id) }}" method="post">
    @csrf
    @method('PUT') {{-- Method untuk update adalah PUT --}}
    
    {{-- Input hidden id_rps tetap diperlukan untuk redirect --}}
    <input type="hidden" name="id_rps" value="{{ $activity->id_rps }}">

    {{-- BAGIAN 1: INFORMASI DASAR --}}
    <h5 class="border-bottom pb-2 mb-3"><i class="ti-info-alt me-2"></i>Informasi Dasar</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Minggu Ke-</label>
                {{-- Input minggu dibuat 'readonly' --}}
                <input type="text" class="form-control form-control-sm" value="Minggu ke-{{ $activity->minggu }}" readonly>
                <small class="form-text text-muted">Minggu kegiatan tidak dapat diubah.</small>
            </div>
        </div>
        
        {{-- CPMK DINAMIS  --}}
        <div class="col-md-6">
             <div class="mb-3">
                <label class="form-label">CPMK Terkait <span class="text-danger">*</span></label>
                {{-- Gunakan ID unik untuk container dan tombol --}}
                <div id="dynamic-cpmk-container-{{ $activity->id }}">
                    
                    @forelse((array) $activity->id_cpmk as $selected_cpmk_id)
                    <div class="input-group mb-2">
                        <select name="id_cpmk[]" class="form-select form-select-sm" required>
                            <option value="">-- Pilih CPMK Terkait --</option>
                            @foreach($cpmks as $cpmk)
                                <option value="{{ $cpmk->id }}" {{ $cpmk->id == $selected_cpmk_id ? 'selected' : '' }}>
                                    {{ $cpmk->kode }} - {{ $cpmk->judul }}
                                </option>
                            @endforeach
                        </select>
                        {{-- Tombol + hanya di item pertama, Hapus di item berikutnya --}}
                        @if($loop->first)
                            <button type="button" id="dynamic-btn-cpmk-{{ $activity->id }}" class="btn btn-sm btn-outline-success" title="Tambah CPMK">+</button>
                        @else
                            <button type="button" class="btn btn-sm btn-outline-danger remove-field">Hapus</button>
                        @endif
                    </div>
                    @empty
                    {{-- Tampilkan satu field kosong jika data tidak ada --}}
                    <div class="input-group mb-2">
                        <select name="id_cpmk[]" class="form-select form-select-sm" required>
                            <option value="">-- Pilih CPMK Terkait --</option>
                            @foreach($cpmks as $cpmk)
                                <option value="{{ $cpmk->id }}">{{ $cpmk->kode }} - {{ $cpmk->judul }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="dynamic-btn-cpmk-{{ $activity->id }}" class="btn btn-sm btn-outline-success" title="Tambah CPMK (Maks 2)">+</button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: KONTEN PEMBELAJARAN --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-book me-2"></i>Konten Pembelajaran</h5>
    <div class="row"> 
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Deskripsi Sub CPMK </label>
                <div id="dynamic-sub-cpmk-{{ $activity->id }}">
                    @forelse((array) $activity->sub_cpmk as $selected_sub_id)
                    <div class="input-group mb-2">
                        <!-- <select name="sub_cpmk[]" class="form-select form-select-sm" required>
                            <option selected disabled value="">Pilih Sub CPMK...</option>
                            @foreach ($sub_cpmks as $sub)
                                <option value="{{ $sub->id }}" {{ $sub->id == $selected_sub_id ? 'selected' : '' }}>{{ $sub->uraian }}</option>
                            @endforeach
                        </select> -->
                        <select name="sub_cpmk[]" class="form-select form-select-sm subcpmk-select" data-selected="{{ $selected_sub_id ?? '' }}">
                            <option value="" selected disabled>Loading...</option>
                        </select>
                        <button type="button" class="btn btn-sm {{ $loop->first ? 'btn-outline-success' : 'btn-outline-danger remove-field' }}" {{ $loop->first ? 'id=dynamic-btn-sub-cpmk-'.$activity->id : '' }} title="Tambah Field">{{ $loop->first ? '+' : 'Hapus' }}</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <select name="sub_cpmk[]" class="form-select form-select-sm">
                            <option selected disabled value="">Pilih Sub CPMK...</option>
                            @foreach ($sub_cpmks as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->uraian }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="dynamic-btn-sub-cpmk-{{$activity->id}}" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Indikator <span class="text-danger">*</span></label>
                <div id="dynamic-indikator-{{ $activity->id }}">
                    @forelse((array) $activity->indikator as $item)
                    <div class="input-group mb-2">
                        <input type="text" name="indikator[]" class="form-control form-control-sm" value="{{ $item }}" placeholder="Tuliskan Indikator..." required>
                        <button type="button" class="btn btn-sm {{ $loop->first ? 'btn-outline-success' : 'btn-outline-danger remove-field' }}" {{ $loop->first ? 'id=dynamic-btn-indikator-'.$activity->id : '' }} title="Tambah Field">{{ $loop->first ? '+' : 'Hapus' }}</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <input type="text" name="indikator[]" class="form-control form-control-sm" placeholder="Tuliskan Indikator..." required>
                        <button type="button" id="dynamic-btn-indikator-{{$activity->id}}" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-4">
             <div class="mb-3">
                <label class="form-label">Materi <span class="text-danger">*</span></label>
                <div id="dynamic-materi-{{ $activity->id }}">
                    @forelse((array) $activity->materi as $item)
                    <div class="input-group mb-2">
                        <input type="text" name="materi[]" class="form-control form-control-sm" value="{{ $item }}" placeholder="Tuliskan Materi..." required>
                         <button type="button" class="btn btn-sm {{ $loop->first ? 'btn-outline-success' : 'btn-outline-danger remove-field' }}" {{ $loop->first ? 'id=dynamic-btn-materi-'.$activity->id : '' }} title="Tambah Field">{{ $loop->first ? '+' : 'Hapus' }}</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <input type="text" name="materi[]" class="form-control form-control-sm" placeholder="Tuliskan Materi..." required>
                        <button type="button" id="dynamic-btn-materi-{{$activity->id}}" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    @php
        $asesmenCast = $activity->bentuk_asesmen;
        $asesmenRaw  = $activity->getRawOriginal('bentuk_asesmen');

        if (is_array($asesmenCast) && count(array_filter($asesmenCast)) > 0) {
            $asesmenList = array_values(array_filter($asesmenCast));
        } elseif (!empty($asesmenRaw)) {
            $decoded = json_decode($asesmenRaw, true);

            if (is_array($decoded) && count(array_filter($decoded)) > 0) {
                $asesmenList = array_values(array_filter($decoded));
            } else {
                $asesmenList = [$asesmenRaw];
            }
        } else {
            $asesmenList = [];
        }
    @endphp

    {{-- BAGIAN 3: Bentuk Asesmen --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-check-box me-2"></i>Bentuk Asesmen</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Bentuk Asesmen <span class="text-danger">*</span></label>
                <div id="dynamic-asesmen-{{ $activity->id }}">
                    @forelse($asesmenList as $asesmen)
                        <div class="input-group mb-2">
                            <select name="bentuk_asesmen[]" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Bentuk Asesmen --</option>
                                @foreach($instrumen as $ins)
                                    <option value="{{ $ins->nama }}" {{ trim($asesmen) == trim($ins->nama) ? 'selected' : '' }}>
                                        {{ $ins->nama }}
                                    </option>
                                @endforeach
                            </select>

                            @if($loop->first)
                                <button type="button" id="dynamic-btn-asesmen-{{ $activity->id }}" class="btn btn-sm btn-outline-success" title="Tambah Asesmen">+</button>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-danger remove-field">Hapus</button>
                            @endif
                        </div>
                    @empty
                        <div class="input-group mb-2">
                            <select name="bentuk_asesmen[]" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Bentuk Asesmen --</option>
                                @foreach($instrumen as $ins)
                                    <option value="{{ $ins->nama }}">{{ $ins->nama }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="dynamic-btn-asesmen-{{ $activity->id }}" class="btn btn-sm btn-outline-success" title="Tambah Asesmen">+</button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    {{-- BAGIAN 4: METODE PEMBELAJARAN --}}
    <h5 class="mt-4 border-bottom pb-2 mb-3"><i class="ti-blackboard me-2"></i>Metode Pembelajaran</h5>
    <div class="row">
        <div class="col-md-12">
            <label class="form-label">Detail Metode <span class="text-danger">*</span></label>
            <div id="dynamic-metode-container-{{ $activity->id }}">
                @forelse($activity->metode['detail_metode'] ?? [] as $metode_item)
                <div class="input-group mb-2">
                    <input type="text" name="metode_deskripsi[]" class="form-control form-control-sm" placeholder="Deskripsi (Cth: Ceramah dan Diskusi, Tugas 1)" value="{{ $metode_item['deskripsi'] ?? '' }}" required>
                    <select name="metode_kategori[]" class="form-select form-select-sm" style="max-width: 150px;" required>
                        <option value="TM" {{ ($metode_item['kategori'] ?? '') == 'TM' ? 'selected' : '' }}>TM (Tatap Muka)</option>
                        <option value="BM" {{ ($metode_item['kategori'] ?? '') == 'BM' ? 'selected' : '' }}>BM (Belajar Mandiri)</option>
                        <option value="BT" {{ ($metode_item['kategori'] ?? '') == 'BT' ? 'selected' : '' }}>BT (Belajar Terstruktur)</option>
                        <option value="DR" {{ ($metode_item['kategori'] ?? '') == 'DR' ? 'selected' : '' }}>DR (Diskusi dan Refleksi)</option>
                        <option value="Lainnya" {{ ($metode_item['kategori'] ?? '') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    <input type="text" name="metode_waktu[]" class="form-control form-control-sm" style="max-width: 170px;" placeholder="Alokasi Waktu (Cth: 1x(3x50'))" value="{{ $metode_item['waktu'] ?? '' }}" required>
                    <button type="button" class="btn btn-sm {{ $loop->first ? 'btn-outline-success' : 'btn-outline-danger remove-field' }}" {{ $loop->first ? 'id=dynamic-btn-metode-'.$activity->id : '' }} title="Tambah Metode">{{ $loop->first ? '+' : 'Hapus' }}</button>
                </div>
                @empty
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
                    <button type="button" id="dynamic-btn-metode-{{$activity->id}}" class="btn btn-sm btn-outline-success" title="Tambah Metode">+</button>
                </div>
                @endforelse
            </div>
        </div>
        <div class="col-md-6 mt-3">
             <div class="mb-3">
                <label class="form-label">Pustaka</label>
                <div id="dynamic-pustaka-{{ $activity->id }}">
                    @forelse($activity->metode['pustaka'] ?? [] as $selected_pustaka_kode)
                    <div class="input-group mb-2">
                        <select name="pustaka[]" class="form-select form-select-sm">
                            <option value="">-- Pilih Pustaka --</option>
                            @foreach ($pustakas as $pustaka)
                                <option value="{{ $pustaka->kode_pustaka }}" {{ $pustaka->kode_pustaka == $selected_pustaka_kode ? 'selected' : '' }}>
                                    {{ $pustaka->kode_pustaka }} - {{ $pustaka->judul }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-sm {{ $loop->first ? 'btn-outline-success' : 'btn-outline-danger remove-field' }}" {{ $loop->first ? 'id=dynamic-btn-pustaka-'.$activity->id : '' }} title="Tambah Field">{{ $loop->first ? '+' : 'Hapus' }}</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <select name="pustaka[]" class="form-select form-select-sm">
                            <option value="">-- Pilih Pustaka --</option>
                            @foreach ($pustakas as $pustaka)
                                <option value="{{ $pustaka->kode_pustaka }}">
                                    {{ $pustaka->kode_pustaka }} - {{ $pustaka->judul }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="dynamic-btn-pustaka-{{$activity->id}}" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                    @endforelse
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
                <div id="dynamic-kegiatan-luring-{{ $activity->id }}">
                    @forelse((array) $activity->kegiatan_luring as $item)
                    <div class="input-group mb-2">
                        <input type="text" name="kegiatan_luring[]" class="form-control form-control-sm" value="{{ $item }}" placeholder="Tuliskan kegiatan luring...">
                        <button type="button" class="btn btn-sm {{ $loop->first ? 'btn-outline-success' : 'btn-outline-danger remove-field' }}" {{ $loop->first ? 'id=dynamic-btn-luring-'.$activity->id : '' }} title="Tambah Field">{{ $loop->first ? '+' : 'Hapus' }}</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <input type="text" name="kegiatan_luring[]" class="form-control form-control-sm" placeholder="Tuliskan kegiatan luring...">
                        <button type="button" id="dynamic-btn-luring-{{$activity->id}}" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Kegiatan Daring</label>
                <div id="dynamic-kegiatan-daring-{{ $activity->id }}">
                    @forelse((array) $activity->kegiatan_daring as $item)
                    <div class="input-group mb-2">
                        <input type="text" name="kegiatan_daring[]" class="form-control form-control-sm" value="{{ $item }}" placeholder="Tuliskan kegiatan daring...">
                        <button type="button" class="btn btn-sm {{ $loop->first ? 'btn-outline-success' : 'btn-outline-danger remove-field' }}" {{ $loop->first ? 'id=dynamic-btn-daring-'.$activity->id : '' }} title="Tambah Field">{{ $loop->first ? '+' : 'Hapus' }}</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <input type="text" name="kegiatan_daring[]" class="form-control form-control-sm" placeholder="Tuliskan kegiatan daring...">
                        <button type="button" id="dynamic-btn-daring-{{$activity->id}}" class="btn btn-sm btn-outline-success" title="Tambah Field">+</button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer mt-4 border-top pt-3">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<script>
$(function () {
    const activityId = '{{ $activity->id }}';

    // =========================================================
    // STYLE: bikin select "terkunci" tapi tetap terkirim (tidak disabled)
    // =========================================================
    if (!document.getElementById('subcpmk-lock-style')) {
        const style = document.createElement('style');
        style.id = 'subcpmk-lock-style';
        style.innerHTML = `
            select[data-locked="1"]{
                background-color:#f8f9fa;
                pointer-events:none; /* blok klik tapi tetap submit */
                opacity:.85;
            }
        `;
        document.head.appendChild(style);
    }

    // ===== helper input text =====
    function addDynamicField(containerSelector, name) {
        const placeholder = $(containerSelector).find('input:first').attr('placeholder') || 'Tuliskan...';
        const template = `
            <div class="input-group mb-2">
                <input type="text" name="${name}[]" class="form-control form-control-sm" placeholder="${placeholder}">
                <button type="button" class="btn btn-sm btn-outline-danger remove-field">Hapus</button>
            </div>`;
        $(containerSelector).append(template);
    }

    // ===== get CPMK ids selected =====
    function getSelectedCpmkIds() {
        const ids = [];
        $('#dynamic-cpmk-container-' + activityId + ' select[name="id_cpmk[]"]').each(function () {
            const v = $(this).val();
            if (v) ids.push(String(v));
        });
        return ids;
    }

    function keyOf(ids) {
        return (ids || []).map(String).sort().join(',');
    }

    // CPMK kondisi awal (record lama)
    const initialCpmkKey = keyOf(getSelectedCpmkIds());
    let userChangedCpmk = false;

    const $subContainer = $('#dynamic-sub-cpmk-' + activityId);
    const $cpmkContainer = $('#dynamic-cpmk-container-' + activityId);

    // pastikan select subcpmk punya class
    $subContainer.find('select[name="sub_cpmk[]"]').addClass('subcpmk-select');

    // ===== lock/unlock subcpmk (tanpa disabled) =====
    function lockSubSelects() {
        $subContainer.find('select.subcpmk-select').attr('data-locked', '1');
    }

    function unlockSubSelects() {
        $subContainer.find('select.subcpmk-select').removeAttr('data-locked');
    }

    // backup: cegah interaksi via event (kalau browser aneh)
    $(document).on('mousedown keydown', '#dynamic-sub-cpmk-' + activityId + ' select.subcpmk-select', function (e) {
        if ($(this).attr('data-locked') === '1') e.preventDefault();
    });

    // ===== isi option subcpmk =====
    function fillSubOptions($select, items, selectedId) {
        $select.empty();
        $select.append(`<option value="" disabled ${!selectedId ? 'selected' : ''}>Pilih Sub CPMK...</option>`);
        items.forEach(it => {
            const sel = (String(it.id) === String(selectedId)) ? 'selected' : '';
            $select.append(`<option value="${it.id}" ${sel}>${it.kode} - ${it.uraian}</option>`);
        });
    }

    // ===== load subcpmk by cpmk (pakai $.when biar aman) =====
    function loadSubCpmkBySelectedCpmk() {
        const cpmkIds = getSelectedCpmkIds();
        const $subSelects = $subContainer.find('select.subcpmk-select');

        if (cpmkIds.length === 0) {
            $subSelects.html(`<option value="" selected>Pilih CPMK dulu...</option>`);
            lockSubSelects(); // tetap terkirim (kalau ada value lama), tapi terkunci
            return;
        }

        // set "Loading..." dulu (JANGAN disable)
        $subSelects.each(function () {
            $(this).html(`<option value="" selected disabled>Loading...</option>`);
        });

        const requests = cpmkIds.map(id => {
            const url = `{{ route($currentPrefix.'subcpmk-by-cpmk', ['cpmkId' => '___']) }}`.replace('___', id);
            return $.getJSON(url);
        });

        $.when.apply($, requests)
            .done(function () {
                // normalize hasil:
                let results = [];
                if (requests.length === 1) {
                    results = [arguments[0]]; // data saja
                } else {
                    for (let i = 0; i < arguments.length; i++) {
                        results.push(arguments[i][0]); // data
                    }
                }

                // gabung unik by id
                const map = new Map();
                results.flat().forEach(it => map.set(String(it.id), it));
                const merged = Array.from(map.values());

                $subSelects.each(function () {
                    const $sel = $(this);

                    // selected lama dari blade: data-selected="..."
                    const selectedId = $sel.attr('data-selected') || $sel.data('selected') || $sel.val() || '';

                    fillSubOptions($sel, merged, selectedId);

                    // RULE:
                    // - kalau CPMK belum berubah (edit awal) -> LOCK (bukan disabled)
                    // - kalau CPMK berubah -> UNLOCK
                    if (!userChangedCpmk && keyOf(getSelectedCpmkIds()) === initialCpmkKey) {
                        lockSubSelects();
                    } else {
                        unlockSubSelects();
                    }
                });
            })
            .fail(function () {
                $subContainer.find('select.subcpmk-select')
                    .html(`<option value="" selected>Gagal load Sub CPMK</option>`);
                lockSubSelects();
            });
    }
 
    // CPMK DINAMIS (tanpa limit) 
    $(document).on('click', '#dynamic-btn-cpmk-' + activityId, function () {
        const container = $('#dynamic-cpmk-container-' + activityId);
    
        const newField = container.find('.input-group:first').clone();
        newField.find('select').val(''); // kosongkan pilihan
    
        // tombol di field clone jadi Hapus
        newField.find('button')
            .removeAttr('id')
            .removeClass('btn-outline-success').addClass('btn-outline-danger')
            .addClass('remove-field')
            .html('Hapus');
    
        container.append(newField);
    
        // tandai CPMK berubah, reload subcpmk biar sinkron
        userChangedCpmk = true;
        unlockSubSelects();
        loadSubCpmkBySelectedCpmk();
    });

    // ===== Sub CPMK add (+) =====
    $('#dynamic-btn-sub-cpmk-' + activityId).click(function () {
        const newField = $subContainer.find('.input-group:first').clone();

        newField.find('select')
            .addClass('subcpmk-select')
            .val('')
            .attr('data-selected', ''); // reset selected lama

        newField.find('button')
            .removeAttr('id')
            .removeClass('btn-outline-success').addClass('btn-outline-danger')
            .addClass('remove-field')
            .html('Hapus');

        $subContainer.append(newField);

        // kalau CPMK sudah berubah -> load option & unlock
        // kalau belum berubah -> tetap lock
        if (userChangedCpmk) {
            loadSubCpmkBySelectedCpmk();
        } else {
            lockSubSelects();
        }
    });

    // ===== input text dynamic =====
    $('#dynamic-btn-indikator-' + activityId).click(() => addDynamicField('#dynamic-indikator-' + activityId, 'indikator'));
    $('#dynamic-btn-materi-' + activityId).click(() => addDynamicField('#dynamic-materi-' + activityId, 'materi'));
    $('#dynamic-btn-luring-' + activityId).click(() => addDynamicField('#dynamic-kegiatan-luring-' + activityId, 'kegiatan_luring'));
    $('#dynamic-btn-daring-' + activityId).click(() => addDynamicField('#dynamic-kegiatan-daring-' + activityId, 'kegiatan_daring'));

    // ===== metode add (+) =====
    $('#dynamic-btn-metode-' + activityId).click(function () {
        const container = $('#dynamic-metode-container-' + activityId);
        const newField = container.find('.input-group:first').clone();
        newField.find('input, select').val('');
        newField.find('select[name="metode_kategori[]"]').val('TM');
        newField.find('button')
            .removeAttr('id')
            .removeClass('btn-outline-success').addClass('btn-outline-danger')
            .addClass('remove-field')
            .html('Hapus');
        container.append(newField);
    });

    // ===== pustaka add (+) =====
    $('#dynamic-btn-pustaka-' + activityId).click(function () {
        const container = $('#dynamic-pustaka-' + activityId);
        const newField = container.find('.input-group:first').clone();
        newField.find('select').val('');
        newField.find('button')
            .removeAttr('id')
            .removeClass('btn-outline-success').addClass('btn-outline-danger')
            .addClass('remove-field')
            .html('Hapus');
        container.append(newField);
    });

    $('#dynamic-btn-asesmen-' + activityId).click(function () {
        const container = $('#dynamic-asesmen-' + activityId);
        const newField = container.find('.input-group:first').clone();

        newField.find('select').val('');
        newField.find('button')
            .removeAttr('id')
            .removeClass('btn-outline-success')
            .addClass('btn-outline-danger remove-field')
            .html('Hapus');

        container.append(newField);
    });

    // ===== CPMK berubah => subcpmk reset + unlock + load ulang =====
    $(document).on('change', '#dynamic-cpmk-container-' + activityId + ' select[name="id_cpmk[]"]', function () {
        const nowKey = keyOf(getSelectedCpmkIds());

        if (nowKey === initialCpmkKey) {
            // balik ke kondisi awal: LOCK (tapi tetap submit value)
            userChangedCpmk = false;
            lockSubSelects();
            return;
        }

        // CPMK benar-benar berubah
        userChangedCpmk = true;
        unlockSubSelects();
        loadSubCpmkBySelectedCpmk();
    });

    // ===== remove field universal =====
    $('#editActivityModal' + activityId).on('click', '.remove-field', function () {
        $(this).closest('.input-group').remove(); 

        // setelah hapus CPMK, recalculates rules
        const nowKey = keyOf(getSelectedCpmkIds());
        if (nowKey === initialCpmkKey) {
            userChangedCpmk = false;
            lockSubSelects();
        } else {
            userChangedCpmk = true;
            unlockSubSelects();
            loadSubCpmkBySelectedCpmk();
        }
    });

    // ===== INIT: WAJIB load SEKALI agar record lama keluar dari "Loading..." =====
    // setelah load, karena CPMK belum berubah -> otomatis LOCK (bukan disabled)
    loadSubCpmkBySelectedCpmk();
});
</script>