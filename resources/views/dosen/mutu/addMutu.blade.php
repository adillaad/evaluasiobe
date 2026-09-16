@extends('dosen.template')
@section('content')
    <style>
        .cpmk-status-card {
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 8px;
            border: 1.5px solid #dee2e6;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
        }

        .cpmk-status-card.mapped {
            border-color: #198754;
            background: #d1e7dd;
        }

        .cpmk-status-card.unmapped {
            border-color: #dc3545;
            background: #f8d7da;
        }

        .cpmk-status-card.partial {
            border-color: #ffc107;
            background: #fff3cd;
        }

        .cpmk-check-icon {
            font-size: 1.25rem;
            min-width: 24px;
            text-align: center;
        }

        .item-row.selected-item {
            background: #e8f4fd !important;
        }

        .item-row.disabled-item {
            background: #f5f5f5 !important;
            opacity: .65;
        }

        #item-selection-panel {
            display: none;
        }

        .persen-input-cell input {
            width: 80px;
            text-align: center;
        }

        #btn-download {
            display: none;
        }

        .badge-soal {
            background-color: #0d6efd;
        }

        .badge-ts {
            background-color: #6f42c1;
        }

        .badge-belum {
            background-color: #6c757d;
        }

        .section-divider {
            background: #f0f0f0;
            font-weight: 600;
            font-size: .85rem;
            color: #555;
            padding: 6px 12px;
            border-top: 2px solid #dee2e6;
        }
    </style>

    @if (session()->has('failed'))
        <div class="alert alert-danger">{{ session('failed') }}</div>
    @elseif(session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div>
        <button type="button" class="btn btn-secondary btn-sm" style="float:right;" data-bs-toggle="tooltip"
            data-bs-placement="left"
            title="Form ini untuk download template penilaian gabungan. Hanya soal/instrumen yang sudah tervalidasi (Valid) yang bisa dipilih.">
            <i class="ti ti-info-circle"></i>
        </button>
        <h3 class="px-4 pb-4 fw-bold text-center">Download Template Penilaian</h3>
    </div>

    <div class="form-group stretch-card">
        <div class="card">
            <div class="card-body">

                {{-- ===== PERINGATAN GLOBAL jika ada soal belum divalidasi ===== --}}
                <div id="global-warning" class="alert alert-warning d-none">
                    <i class="ti ti-alert-triangle me-1"></i>
                    <strong>Perhatian:</strong> Beberapa soal/instrumen belum divalidasi oleh Penjamin Mutu atau
                    Kepala Program Studi. Item tersebut tidak dapat dipilih sampai disetujui.
                    <strong>Hubungi Penjamin Mutu Prodi / Kepala Program Studi untuk menyetujui soal Anda.</strong>
                </div>

                <form action="{{ route('dosen.excelGabungan') }}" method="GET" id="form-download">
                    @csrf
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <label>Universitas <span class="text-danger">*</span></label>
                                <select class="form-control" name="univ" id="univ" required>
                                    <option value="" disabled selected>Select...</option>
                                    @foreach ($universitas as $u)
                                        <option value="{{ $u->nama }}">{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Tahun Ajaran <span class="text-danger">*</span></label>
                                <select class="form-control" name="semester" id="semester" required>
                                    <option value="" disabled selected>Select...</option>
                                    @if(isset($tahunAjarans))
                                        @foreach ($tahunAjarans as $ta)
                                            <option value="{{ $ta->tahun }} - {{ $ta->jenis_semester }}">
                                                {{ $ta->tahun }} - {{ $ta->jenis_semester }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="2024 - Ganjil">2024 - Ganjil</option>
                                        <option value="2024 - Genap">2024 - Genap</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Prodi <span class="text-danger">*</span></label>
                                <select class="form-control" name="prodi" id="prodi" required>
                                    <option value="" disabled selected>Select...</option>
                                    @foreach ($prodi as $p)
                                        <option value="{{ $p->nama }}">{{ $p->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Nama MK <span class="text-danger">*</span></label>
                                <select class="form-control" name="kode_mk" id="kode_mk" required>
                                    <option value="" disabled selected>Select...</option>
                                    @foreach ($rpss as $rps)
                                        <option value="{{ $rps->kode_mk }}">
                                            {{ $rps->kode_mk }} - {{ $rps->mk->nama ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Jenis / Metode Penilaian <span class="text-danger">*</span></label>
                                <select class="form-control" name="jenis" id="jenis" required disabled>
                                    <option value="" disabled selected>Select...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ===== PANEL PEMILIHAN ITEM ===== --}}
                    <div id="item-selection-panel" class="mt-4">

                        {{-- STATUS CPMK --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light fw-bold">
                                Status Pemetaan CPMK
                                <small class="text-muted fw-normal ms-2">— Semua CPMK wajib terpetakan 100%.</small>
                            </div>
                            <div class="card-body" id="cpmk-checklist-container">
                                <p class="text-muted">Pilih Mata Kuliah dan Jenis terlebih dahulu.</p>
                            </div>
                            <div class="card-footer">
                                <div id="cpmk-summary" class="small text-muted"></div>
                            </div>
                        </div>

                        {{-- TABEL GABUNGAN --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light fw-bold">
                                Pilih Item yang Masuk ke Template
                                <small class="ms-2 text-muted fw-normal">
                                    <span class="badge badge-soal text-white">Soal</span> = soal ujian &nbsp;
                                    <span class="badge badge-ts text-white">Instrumen</span> = penilaian tanpa soal &nbsp;
                                    <span class="badge badge-belum text-white">Belum Valid</span> = menunggu persetujuan
                                </small>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0" id="tableItem">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="4%"><input type="checkbox" id="checkAll"
                                                        title="Pilih semua item yang valid"></th>
                                                <th width="6%">ID</th>
                                                <th width="8%">Tipe</th>
                                                <th width="10%">CPL</th>
                                                <th width="12%">CPMK</th>
                                                <th width="14%" class="text-center">% ke CPMK</th>
                                                <th>Deskripsi</th>
                                                <th width="10%" class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="item-tbody">
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    Pilih Mata Kuliah dan Jenis terlebih dahulu.
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-end">Total Item Dipilih</th>
                                                <th class="text-center" id="totalDipilih">0 item</th>
                                                <th colspan="2"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="hidden-inputs-container"></div>

                        <div class="row">
                            <div class="col-12">
                                <div id="cpmk-warning-banner" class="alert alert-warning d-none">
                                    <span id="cpmk-warning-text"></span>
                                </div>
                                <button type="submit" class="btn btn-primary" id="btn-download" disabled>
                                    Download Template Gabungan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

        $(document).ready(function() {

            $('#kode_mk').on('change', function() {
                var kode_mk = $(this).val();
                if (!kode_mk) {
                    $('#jenis').prop('disabled', true);
                    return;
                }
                $('#jenis').prop('disabled', false).html('<option value="">Loading...</option>');
                resetPanel();

                $.ajax({
                    url: "{{ route('dosen.getJenisByMk') }}",
                    type: "GET",
                    data: {
                        kode_mk: kode_mk
                    },
                    success: function(data) {
                        var opts =
                            '<option value="" disabled selected>-- Pilih Metode Penilaian --</option>';
                        data.forEach(function(item) {
                            opts += '<option value="' + item.id + '">' + item.nama +
                                ' (' + parseFloat(item.bobot).toFixed(2) +
                                '%)</option>';
                        });
                        $('#jenis').html(opts);
                    },
                    error: function() {
                        alert('Gagal memuat Metode Penilaian.');
                    }
                });
            });

            $('#jenis').on('change', function() {
                var kode_mk = $('#kode_mk').val();
                var jenis = $(this).val();
                if (!kode_mk || !jenis) return;
                loadGabungan(kode_mk, jenis);
            });

            $(document).on('change', '#checkAll', function() {
                // Hanya centang item yang can_select = true (tidak disabled)
                $('.item-checkbox:not(:disabled)').prop('checked', $(this).is(':checked'));
                recalculate();
            });
            $(document).on('change', '.item-checkbox', recalculate);
            $(document).on('input', '.persen-input', recalculate);

            function resetPanel() {
                $('#item-selection-panel').hide();
                $('#item-tbody').html(
                    '<tr><td colspan="8" class="text-center text-muted">Pilih Mata Kuliah dan Jenis terlebih dahulu.</td></tr>'
                    );
                $('#cpmk-checklist-container').html(
                    '<p class="text-muted">Pilih Mata Kuliah dan Jenis terlebih dahulu.</p>');
                $('#totalDipilih').text('0 item');
                $('#btn-download').prop('disabled', true).hide();
                $('#hidden-inputs-container').empty();
                $('#cpmk-warning-banner').addClass('d-none');
                $('#global-warning').addClass('d-none');
                window._allCpmks = [];
            }

            function loadGabungan(kode_mk, jenis) {
                $('#item-tbody').html(
                    '<tr><td colspan="8" class="text-center"><span class="spinner-border spinner-border-sm"></span> Memuat data...</td></tr>'
                    );
                $('#cpmk-checklist-container').html(
                    '<p class="text-muted"><span class="spinner-border spinner-border-sm"></span> Memuat CPMK...</p>'
                    );
                $('#item-selection-panel').show();

                $.ajax({
                    url: "{{ route('dosen.getGabunganByMkJenis') }}",
                    type: "GET",
                    data: {
                        kode_mk: kode_mk,
                        jenis: jenis
                    },
                    success: function(response) {
                        renderItemTable(response.soals ?? [], response.instrumens ?? []);
                        renderCpmkChecklist(response.cpmks ?? []);
                        recalculate();

                        // Tampilkan warning global jika ada item belum valid
                        var adaBelumValid = (response.soals ?? []).some(function(s) {
                                return !s.can_select;
                            }) ||
                            (response.instrumens ?? []).some(function(i) {
                                return !i.can_select;
                            });
                        $('#global-warning').toggleClass('d-none', !adaBelumValid);
                    },
                    error: function() {
                        $('#item-tbody').html(
                            '<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>'
                            );
                    }
                });
            }

            function statusBadge(statusLabel, canSelect) {
                if (canSelect) {
                    return '<span class="badge bg-success">Tervalidasi</span>';
                }
                var color = statusLabel === 'Ditolak' ? 'danger' : 'secondary';
                return '<span class="badge bg-' + color + '">' + statusLabel + '</span>' +
                    '<br><small class="text-danger" style="font-size:.7rem">Hubungi PM / Kaprodi</small>';
            }

            function renderItemTable(soals, instrumens) {
                var rows = '';
                var hasData = false;

                if (soals && soals.length > 0) {
                    hasData = true;
                    rows += '<tr class="section-divider"><td colspan="8">📝 Soal Ujian (' + soals.length +
                        ' soal)</td></tr>';
                    soals.forEach(function(soal) {
                        var cpmkId = soal.cpmk_id ?? '';
                        var soalId = soal.id;
                        var canSelect = soal.can_select;
                        var rowClass = canSelect ? '' : ' disabled-item';
                        var disAttr = canSelect ? '' :
                            ' disabled title="Soal belum divalidasi — hubungi PM/Kaprodi"';

                        rows += '<tr class="item-row' + rowClass + '" data-item-id="soal-' + soalId +
                            '" data-cpmk-id="' + cpmkId + '" data-type="soal">' +
                            '<td class="text-center">' +
                            '<input type="checkbox" class="item-checkbox"' +
                            ' data-item-id="soal-' + soalId + '"' +
                            ' data-raw-id="' + soalId + '"' +
                            ' data-cpmk-id="' + cpmkId + '"' +
                            ' data-type="soal"' + disAttr + '>' +
                            '</td>' +
                            '<td>' + soalId + '</td>' +
                            '<td><span class="badge badge-soal text-white">Soal</span></td>' +
                            '<td>' + (soal.kode_cpl ?? '-') + '</td>' +
                            '<td><span class="badge bg-success">' + (soal.kode_cpmk ?? '-') +
                            '</span></td>' +
                            '<td class="persen-input-cell text-center">' +
                            '<input type="number" class="form-control form-control-sm persen-input d-none"' +
                            ' data-item-id="soal-' + soalId + '" data-cpmk-id="' + cpmkId + '"' +
                            ' min="1" max="100" placeholder="%">' +
                            '<span class="persen-display text-muted small">—</span>' +
                            '</td>' +
                            '<td>' + (soal.pertanyaan ?? '-') + '</td>' +
                            '<td class="text-center">' + statusBadge(soal.status_label, canSelect) +
                            '</td>' +
                            '</tr>';
                    });
                }

                if (instrumens && instrumens.length > 0) {
                    hasData = true;
                    rows += '<tr class="section-divider"><td colspan="8">🗂️ Instrumen Tanpa Soal (' + instrumens
                        .length + ' instrumen)</td></tr>';
                    instrumens.forEach(function(item) {
                        var cpmkId = item.cpmk_id ?? '';
                        var tsId = item.id;
                        var canSelect = item.can_select;
                        var rowClass = canSelect ? '' : ' disabled-item';
                        var disAttr = canSelect ? '' :
                            ' disabled title="Instrumen belum divalidasi — hubungi PM/Kaprodi"';

                        rows += '<tr class="item-row' + rowClass + '" data-item-id="ts-' + tsId +
                            '" data-cpmk-id="' + cpmkId + '" data-type="ts">' +
                            '<td class="text-center">' +
                            '<input type="checkbox" class="item-checkbox"' +
                            ' data-item-id="ts-' + tsId + '"' +
                            ' data-raw-id="' + tsId + '"' +
                            ' data-cpmk-id="' + cpmkId + '"' +
                            ' data-type="ts"' + disAttr + '>' +
                            '</td>' +
                            '<td>' + tsId + '</td>' +
                            '<td><span class="badge badge-ts text-white">Instrumen</span></td>' +
                            '<td>' + (item.cpl_kode ?? '-') + '</td>' +
                            '<td><span class="badge bg-success">' + (item.cpmk_kode ?? '-') +
                            '</span></td>' +
                            '<td class="persen-input-cell text-center">' +
                            '<input type="number" class="form-control form-control-sm persen-input d-none"' +
                            ' data-item-id="ts-' + tsId + '" data-cpmk-id="' + cpmkId + '"' +
                            ' min="1" max="100" placeholder="%">' +
                            '<span class="persen-display text-muted small">—</span>' +
                            '</td>' +
                            '<td>' + (item.nama_instrumen ?? '-') + '</td>' +
                            '<td class="text-center">' + statusBadge(item.status_label, canSelect) +
                            '</td>' +
                            '</tr>';
                    });
                }

                if (!hasData) {
                    rows =
                        '<tr><td colspan="8" class="text-center text-muted">Tidak ada soal maupun instrumen untuk kombinasi ini.</td></tr>';
                }

                $('#item-tbody').html(rows);
            }

            function renderCpmkChecklist(cpmks) {
                if (!cpmks || cpmks.length === 0) {
                    $('#cpmk-checklist-container').html('<p class="text-muted small">Tidak ada CPMK terkait.</p>');
                    return;
                }
                window._allCpmks = cpmks;
                var html = '<div class="row g-2">';
                cpmks.forEach(function(c) {
                    html += '<div class="col-md-6">' +
                        '<div class="cpmk-status-card unmapped" id="cpmk-card-' + c.id + '">' +
                        '<span class="cpmk-check-icon" id="cpmk-icon-' + c.id + '">❌</span>' +
                        '<div>' +
                        '<span class="badge bg-success me-1">' + (c.kode ?? 'CPMK') + '</span>' +
                        '<span class="small">' + (c.judul ?? '') + '</span>' +
                        '<div class="small text-muted mt-1" id="cpmk-persen-info-' + c.id +
                        '">Belum ada item dipilih</div>' +
                        '</div></div></div>';
                });
                html += '</div>';
                $('#cpmk-checklist-container').html(html);
            }

            function recalculate() {
                var cpmks = window._allCpmks || [];

                var cpmkCheckedCount = {};
                $('.item-checkbox:not(:disabled)').each(function() {
                    var cpmkId = String($(this).data('cpmk-id') || '');
                    if (!cpmkId) return;
                    if (!cpmkCheckedCount[cpmkId]) cpmkCheckedCount[cpmkId] = 0;
                    if ($(this).is(':checked')) cpmkCheckedCount[cpmkId]++;
                });

                $('.item-checkbox:not(:disabled)').each(function() {
                    var itemId = $(this).data('item-id');
                    var cpmkId = String($(this).data('cpmk-id') || '');
                    var checked = $(this).is(':checked');
                    var row = $('tr[data-item-id="' + itemId + '"]');
                    var input = row.find('.persen-input');
                    var display = row.find('.persen-display');

                    row.toggleClass('selected-item', checked);

                    if (checked) {
                        var checkedInCpmk = cpmkCheckedCount[cpmkId] || 0;
                        if (checkedInCpmk > 1) {
                            if (input.hasClass('was-auto')) {
                                input.val('').removeClass('was-auto');
                            }
                            input.removeClass('d-none');
                            display.addClass('d-none');
                        } else {
                            input.val(100).addClass('was-auto d-none');
                            display.text('100%').removeClass('d-none');
                        }
                    } else {
                        input.val('').removeClass('was-auto').addClass('d-none');
                        display.text('—').removeClass('d-none');
                    }
                });

                var cpmkPersen = {};
                $('.item-checkbox:checked:not(:disabled)').each(function() {
                    var itemId = $(this).data('item-id');
                    var cpmkId = String($(this).data('cpmk-id') || '');
                    if (!cpmkId) return;
                    var row = $('tr[data-item-id="' + itemId + '"]');
                    var persen = parseFloat(row.find('.persen-input').val()) || 0;
                    if (!cpmkPersen[cpmkId]) cpmkPersen[cpmkId] = 0;
                    cpmkPersen[cpmkId] += persen;
                });

                var allMapped = (cpmks.length > 0);
                cpmks.forEach(function(c) {
                    var cId = String(c.id);
                    var persen = cpmkPersen[cId] || 0;
                    var mapped = (Math.round(persen) === 100);
                    if (!mapped) allMapped = false;

                    var card = $('#cpmk-card-' + c.id);
                    var icon = $('#cpmk-icon-' + c.id);
                    var info = $('#cpmk-persen-info-' + c.id);

                    card.removeClass('mapped unmapped partial');
                    if (mapped) {
                        card.addClass('mapped');
                        icon.text('✅');
                        info.text('Terpetakan: 100%').removeClass('text-danger').addClass('text-success');
                    } else if (persen > 0) {
                        card.addClass('partial');
                        icon.text('⚠️');
                        info.text('Terisi: ' + persen.toFixed(1) + '% (butuh 100%)').addClass('text-danger')
                            .removeClass('text-success');
                    } else {
                        card.addClass('unmapped');
                        icon.text('❌');
                        info.text('Belum ada item dipilih').removeClass('text-danger text-success');
                    }
                });

                var mappedCount = cpmks.filter(function(c) {
                    return Math.round(cpmkPersen[String(c.id)] || 0) === 100;
                }).length;
                $('#cpmk-summary').text(mappedCount + ' dari ' + cpmks.length + ' CPMK sudah terpetakan 100%.');

                var anyChecked = $('.item-checkbox:checked:not(:disabled)').length > 0;
                var downloadOk = anyChecked && allMapped;
                $('#totalDipilih').text($('.item-checkbox:checked:not(:disabled)').length + ' item');
                $('#btn-download').prop('disabled', !downloadOk).toggle(anyChecked);

                if (anyChecked && !allMapped) {
                    var unmapped = cpmks
                        .filter(function(c) {
                            return Math.round(cpmkPersen[String(c.id)] || 0) !== 100;
                        })
                        .map(function(c) {
                            return c.kode ?? ('CPMK ' + c.id);
                        });
                    $('#cpmk-warning-text').text('CPMK berikut belum terpetakan 100%: ' + unmapped.join(', ') +
                    '.');
                    $('#cpmk-warning-banner').removeClass('d-none');
                } else {
                    $('#cpmk-warning-banner').addClass('d-none');
                }

                $('#hidden-inputs-container').empty();
                $('.item-checkbox:checked:not(:disabled)').each(function() {
                    var rawId = $(this).data('raw-id');
                    var type = $(this).data('type');
                    var itemId = $(this).data('item-id');
                    var row = $('tr[data-item-id="' + itemId + '"]');
                    var persen = parseFloat(row.find('.persen-input').val()) || 100;

                    if (type === 'soal') {
                        $('#hidden-inputs-container').append(
                            '<input type="hidden" name="soal_ids[]" value="' + rawId + '">' +
                            '<input type="hidden" name="persen_soal[' + rawId + ']" value="' + persen +
                            '">'
                        );
                    } else {
                        $('#hidden-inputs-container').append(
                            '<input type="hidden" name="ts_ids[]" value="' + rawId + '">' +
                            '<input type="hidden" name="persen_ts[' + rawId + ']" value="' + persen +
                            '">'
                        );
                    }
                });
            }

        });
    </script>
@endsection
