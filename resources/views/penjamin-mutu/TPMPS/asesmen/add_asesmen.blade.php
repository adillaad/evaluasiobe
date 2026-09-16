@extends($userOtoritas === 'Dosen' ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Tambah Asesmen</h4>

            <form action="{{ route($currentPrefix . 'asesmen.asesmen-store') }}" method="POST">
                @csrf
                <!-- Select MK -->
                <div class="form-group mb-3">
                    <label for="mk_kode" class="fw-bold">Pilih Mata Kuliah <span class="text-danger">*</span></label>
                    <select class="form-control" id="mk_kode" name="mk_kode" required>
                        <option value="" selected disabled>-- Pilih Mata Kuliah --</option>
                        @foreach ($mks as $mk)
                            <option value="{{ $mk->kode }}">{{ $mk->kode }} - {{ $mk->nama }}</option>  
                        @endforeach
                    </select>
                </div>

                <!-- Select CPL -->
                <div class="form-group mb-3">
                    <label for="cpl_id" class="fw-bold">Pilih CPL <span class="text-danger">*</span></label>
                    <select id="cpl_id" name="cpl_id" class="form-control" required disabled>
                        <option value="">-- Pilih CPL --</option>
                    </select>
                </div>

                <!-- Select CPMK -->
                <div class="form-group mb-3">
                    <label for="cpmk-select" class="fw-bold">Pilih CPMK <span class="text-danger">*</span></label>
                    <select id="cpmk-select" name="cpmk_id" class="form-control" required disabled>
                        <option value="">-- Pilih CPMK --</option>
                    </select>
                </div>

                <!-- Select Instrumen -->
                <div class="form-group mb-3">
                    <label for="instrumen-select" class="fw-bold">Pilih Instrumen <span class="text-danger">*</span></label>
                    <select id="instrumen-select" name="instrumen" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Instrumen --</option>
                        <option value="Rubrik">Rubrik</option>
                        <option value="Panduan Proyek Akhir">Panduan Proyek Akhir</option>
                    </select>
                </div>

                <!-- Dynamic Blocks Container (Metode & Kriteria Paket) -->
                <div id="dynamic-blocks-container">
                    {{-- Default Block 0 --}}
                    <div class="card mb-3 border block-item" data-block-index="0">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="fw-bold text-primary small"><i class="mdi mdi-layers-outline me-1"></i> Metode Penilaian & Kriteria</span>
                            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-block" style="display:none;" title="Hapus Paket Ini">
                                <i class="mdi mdi-trash-can-outline me-1"></i> Hapus Paket
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <!-- Select Single Metode Penilaian -->
                            <div class="form-group mb-3">
                                <label class="fw-bold small mb-1">Pilih Metode Penilaian & Isi Bobot Metode <span class="text-danger">*</span></label>
                                <div class="row align-items-center">
                                    <div class="col-md-7 col-sm-6 mb-2 mb-sm-0">
                                        <select class="form-control form-control-sm select-metode-single" name="blocks[0][metode_id]" required>
                                            <option value="" disabled selected>-- Pilih Metode Penilaian --</option>
                                            @foreach ($metodepenilaians as $metodepenilaian)
                                                <option value="{{ $metodepenilaian->id }}">{{ $metodepenilaian->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5 col-sm-6">
                                        <div class="input-group input-group-sm" style="max-width: 180px;">
                                            <input type="number" step="0.01" min="0" max="100" class="form-control input-bobot-metode" name="blocks[0][bobot_metode]" placeholder="Bobot Metode" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Select Kriteria Penilaian Terikat -->
                            <div class="form-group mb-0">
                                <label class="fw-bold small mb-1">Pilih Kriteria Penilaian<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm mb-2 search-kriteria-block" placeholder="Cari Kriteria...">
                                <div class="border rounded p-2 bg-light kriteria-block-list" style="max-height: 220px; overflow-y: auto;">
                                    @foreach ($instrumenPenilaians as $instrumenPenilaian)
                                        <div class="row align-items-center mb-2 p-1 border-bottom kriteria-item">
                                            <div class="col-md-8 col-sm-7 d-flex align-items-center">
                                                <input class="form-check-input kriteria-checkbox me-2 ms-0 mt-0" 
                                                       type="checkbox" 
                                                       name="blocks[0][kriteria][]" 
                                                       value="{{ $instrumenPenilaian->id }}" 
                                                       id="kriteria_0_{{ $instrumenPenilaian->id }}">
                                                <label class="form-check-label text-dark fw-semibold mb-0 small" for="kriteria_0_{{ $instrumenPenilaian->id }}">
                                                    {{ $instrumenPenilaian->nama_kriteria }}
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-5">
                                                <input type="number" step="0.01" min="0" max="100" 
                                                       class="form-control form-control-sm kriteria-bobot" 
                                                       name="blocks[0][bobot_kriteria][{{ $instrumenPenilaian->id }}]" 
                                                       placeholder="Bobot" disabled>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-outline-primary btn-sm px-3" id="btn-add-block">
                        <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Paket Metode & Kriteria Lainnya
                    </button>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2"><i class="mdi mdi-content-save me-1"></i> Simpan Asesmen</button>
            </form>
        </div>
    </div>
</div>

{{-- Template Tersembunyi untuk Pasangan Metode & Kriteria Baru --}}
<template id="block-template">
    <div class="card mb-3 border block-item" data-block-index="__INDEX__">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
            <span class="fw-bold text-primary small"><i class="mdi mdi-layers-outline me-1"></i> Metode Penilaian & Kriteria</span>
            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-remove-block" title="Hapus Paket Ini">
                <i class="mdi mdi-trash-can-outline me-1"></i> Hapus Paket
            </button>
        </div>
        <div class="card-body p-3">
            <!-- Select Single Metode Penilaian -->
            <div class="form-group mb-3">
                <label class="fw-bold small mb-1">Pilih Metode Penilaian & Isi Bobot Metode <span class="text-danger">*</span></label>
                <div class="row align-items-center">
                    <div class="col-md-7 col-sm-6 mb-2 mb-sm-0">
                        <select class="form-control form-control-sm select-metode-single" name="blocks[__INDEX__][metode_id]" required>
                            <option value="" disabled selected>-- Pilih Metode Penilaian --</option>
                            @foreach ($metodepenilaians as $metodepenilaian)
                                <option value="{{ $metodepenilaian->id }}">{{ $metodepenilaian->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 col-sm-6">
                        <div class="input-group input-group-sm" style="max-width: 180px;">
                            <input type="number" step="0.01" min="0" max="100" class="form-control input-bobot-metode" name="blocks[__INDEX__][bobot_metode]" placeholder="Bobot Metode" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Select Kriteria Penilaian Terikat -->
            <div class="form-group mb-0">
                <label class="fw-bold small mb-1">Pilih Kriteria Penilaian  <span class="text-muted fw-normal">*</span></label>
                <input type="text" class="form-control form-control-sm mb-2 search-kriteria-block" placeholder="Cari Kriteria...">
                <div class="border rounded p-2 bg-light kriteria-block-list" style="max-height: 220px; overflow-y: auto;">
                    @foreach ($instrumenPenilaians as $instrumenPenilaian)
                        <div class="row align-items-center mb-2 p-1 border-bottom kriteria-item">
                            <div class="col-md-8 col-sm-7 d-flex align-items-center">
                                <input class="form-check-input kriteria-checkbox me-2 ms-0 mt-0" 
                                       type="checkbox" 
                                       name="blocks[__INDEX__][kriteria][]" 
                                       value="{{ $instrumenPenilaian->id }}" 
                                       id="kriteria___INDEX___{{ $instrumenPenilaian->id }}">
                                <label class="form-check-label text-dark fw-semibold mb-0 small" for="kriteria___INDEX___{{ $instrumenPenilaian->id }}">
                                    {{ $instrumenPenilaian->nama_kriteria }}
                                </label>
                            </div>
                            <div class="col-md-4 col-sm-5">
                                <input type="number" step="0.01" min="0" max="100" 
                                       class="form-control form-control-sm kriteria-bobot" 
                                       name="blocks[__INDEX__][bobot_kriteria][{{ $instrumenPenilaian->id }}]" 
                                       placeholder="Bobot" disabled>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</template>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    const userOtoritas = "{{ $userOtoritas }}";
    let blockCount = 1;

    function getSelectedCPLValue() {
        var mkKode = document.getElementById("mk_kode").value;
        var cplSelect = document.getElementById('cpl_id'); 
        let urlget = '';
        if(userOtoritas === "Kepala Program Studi"){
            urlget = `/kepala-program-studi/asesmen/get-cpl-by-mk/${mkKode}`;
        } else if (userOtoritas === 'Penjamin Mutu Program Studi'){
            urlget = `/penjamin-mutu/program-studi/asesmen/get-cpl-by-mk/${mkKode}`;
        } else {
            urlget = `/penjamin-mutu/program-studi/asesmen/get-cpl-by-mk/${mkKode}`;
        }

        if (mkKode) {
            $.ajax({
                url: urlget,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
                    let cplsList = (data && data.cpls) ? data.cpls : (Array.isArray(data) ? data : []);
                    if (cplsList && cplsList.length > 0) {
                        cplsList.forEach(function(cpl) {
                            var option = document.createElement('option');
                            option.value = cpl.id;
                            option.text = cpl.kode + (cpl.judul ? ' - ' + cpl.judul : (cpl.deskripsi ? ' - ' + cpl.deskripsi : ''));
                            cplSelect.appendChild(option);
                        });
                        cplSelect.disabled = false;
                    } else {
                        cplSelect.innerHTML = '<option value="">-- Tidak ada data CPL --</option>';
                        cplSelect.disabled = true;
                    }
                },
                error: function() {
                    alert('Gagal memuat data CPL.');
                    cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
                    cplSelect.disabled = true;
                }
            });
        } else {
            cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
            cplSelect.disabled = true;
        }
    }

    function getSelectedCpmkValue() {
        var mkKode = document.getElementById("mk_kode").value;
        var cplId = document.getElementById("cpl_id").value;
        var cpmkSelect = document.getElementById('cpmk-select'); 

        let urlget = '';
        if(userOtoritas === "Kepala Program Studi"){
            urlget = `/kepala-program-studi/asesmen/get-cpmk-by-cpl/${cplId}?mk_kode=${mkKode}`;
        } else if (userOtoritas === 'Penjamin Mutu Program Studi'){
            urlget = `/penjamin-mutu/program-studi/asesmen/get-cpmk-by-cpl/${cplId}?mk_kode=${mkKode}`;
        } else {
            urlget = `/penjamin-mutu/program-studi/asesmen/get-cpmk-by-cpl/${cplId}?mk_kode=${mkKode}`;
        }

        if (mkKode && cplId) {
            $.ajax({
                url: urlget,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
                    let cpmksList = (data && data.cpmks) ? data.cpmks : (Array.isArray(data) ? data : []);
                    if (cpmksList && cpmksList.length > 0) {
                        cpmksList.forEach(function(cpmk) {
                            var option = document.createElement('option');
                            option.value = cpmk.id;
                            option.text = cpmk.kode + (cpmk.judul ? ' - ' + cpmk.judul : (cpmk.deskripsi ? ' - ' + cpmk.deskripsi : ''));
                            cpmkSelect.appendChild(option);
                        });
                        cpmkSelect.disabled = false;
                    } else {
                        cpmkSelect.innerHTML = '<option value="">-- Tidak ada data CPMK --</option>';
                        cpmkSelect.disabled = true;
                    }
                },
                error: function() {
                    alert('Gagal memuat data CPMK.');
                    cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
                    cpmkSelect.disabled = true;
                }
            });
        } else {
            cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
            cpmkSelect.disabled = true;
        }
    }

    // Event Listeners Filter CPL / CPMK
    $('#mk_kode').change(function() {
        getSelectedCPLValue();
    });

    if ($('#mk_kode').val()) {
        getSelectedCPLValue();
    }

    $('#cpl_id').change(function() {
        getSelectedCpmkValue();
    });

    // Toggle Input Bobot Kriteria per Block
    $(document).on('change', '.kriteria-checkbox', function() {
        let isChecked = $(this).is(':checked');
        let $row = $(this).closest('.kriteria-item');
        let $bobotInput = $row.find('.kriteria-bobot');
        $bobotInput.prop('disabled', !isChecked);
        if (!isChecked) {
            $bobotInput.val('');
        }
    });

    // Search Kriteria di dalam blok
    $(document).on('input', '.search-kriteria-block', function() {
        let val = $(this).val().toLowerCase();
        let $block = $(this).closest('.block-item');
        $block.find('.kriteria-item').each(function() {
            let text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(val));
        });
    });

    // Tambah Paket Metode & Kriteria Baru (Dynamic Block)
    $('#btn-add-block').click(function() {
        blockCount++;
        let templateHtml = $('#block-template').html();
        templateHtml = templateHtml.replace(/__INDEX__/g, blockCount - 1)
                                   .replace(/__NUMBER__/g, blockCount);

        $('#dynamic-blocks-container').append(templateHtml);
        updateRemoveButtonsVisibility();
    });

    // Hapus Paket Metode & Kriteria
    $(document).on('click', '.btn-remove-block', function() {
        if ($('.block-item').length > 1) {
            $(this).closest('.block-item').remove();
            reindexBlocks();
            updateRemoveButtonsVisibility();
        }
    });

    function updateRemoveButtonsVisibility() {
        let count = $('.block-item').length;
        if (count > 1) {
            $('.btn-remove-block').show();
        } else {
            $('.btn-remove-block').hide();
        }
    }

    function reindexBlocks() {
        $('.block-item').each(function(idx) {
            let num = idx + 1;
            $(this).attr('data-block-index', idx);
            $(this).find('.card-header span').html('<i class="mdi mdi-layers-outline me-1"></i> Paket Metode Penilaian & Kriteria #' + num);
        });
    }

    // Submit Validation
    $('form').submit(function(e) {
        let isValid = true;
        $('.block-item').each(function(idx) {
            let metodeVal = $(this).find('.select-metode-single').val();
            let bobotVal = parseFloat($(this).find('.input-bobot-metode').val()) || 0;

            if (!metodeVal) {
                alert('Harap pilih metode penilaian pada Paket #' + (idx + 1));
                isValid = false;
                return false;
            }
            if (bobotVal <= 0) {
                alert('Bobot metode pada Paket #' + (idx + 1) + ' harus diisi dan lebih besar dari 0.');
                isValid = false;
                return false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection