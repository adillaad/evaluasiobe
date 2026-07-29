@extends('dosen.template')
@section('content')

<style>
    .hidden{display:none!important;}
    .sheet-btn{border-radius:999px;}
    .rubrik-preview-table td,.rubrik-preview-table th{vertical-align:top;}
    .soft-hint{font-size:.875rem;}
    .template-item:hover{
        background:#f8f9fa;
    }
</style>
@php

$templateRubriks = [
    [
        'nama' => 'Criteria Percentage',
        'deskripsi' => 'Digunakan untuk penilaian tugas, partisipasi, atau project yang memiliki level skor 100–20.',
        'file' => 'criteria_percentage.xlsx',
    ],
    [
        'nama' => 'Program Report',
        'deskripsi' => 'Digunakan untuk penilaian tugas yang memiliki komponen program dan laporan.',
        'file' => 'program_report.xlsx',
    ],
    [
        'nama' => 'Questions',
        'deskripsi' => 'Digunakan untuk penilaian ujian berbasis soal.',
        'file' => 'questions.xlsx',
    ],
];

@endphp

<div class="container-fluid mb-4">

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h3 class="mb-1">Tambah Rubrik Penilaian</h3>
        </div>
    </div>

    {{-- 0) DAFTAR TEMPLATE --}}
    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="fw-semibold">Template Rubrik</div>
                <div class="text-muted soft-hint">
                    Lihat daftar template rubrik terlebih dahulu, lalu pilih template yang sesuai untuk diunduh.
                </div>
            </div>

            <button type="button"
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#templateRubrikModal">Daftar Template Rubrik
            </button>
        </div>
    </div>

    {{-- 1) PILIH MK --}}
    <div class="card mb-3">
        <div class="card-body">
            <label class="fw-semibold mb-1">Mata Kuliah <span class="text-danger">*</span></label>
            <select id="mkSelect" class="form-select">
                <option value="">-- Pilih Mata Kuliah --</option>
                @foreach($mks as $mk)
                    <option value="{{ $mk->kode }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                @endforeach
            </select>
            <div class="text-muted soft-hint mt-2">Pilih Mata Kuliah terlebih dahulu.</div>
        </div>
    </div>

    {{-- 2) SHEET: JENIS RUBRIK --}}
    <div id="jenisCard" class="card mb-3 hidden">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Jenis Rubrik</h5>
            <span class="badge bg-light text-dark" id="mkBadge">MK: -</span>
        </div>
        <div class="card-body">
            <div id="jenisContainer" class="d-flex flex-wrap gap-2"></div>
            <div id="jenisEmptyText" class="text-muted soft-hint hidden">
                Jenis rubrik belum tersedia untuk mata kuliah ini.
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 3) PANEL AKSI: UPLOAD SAJA --}}
    <form method="POST" action="{{ route('dosen.rubrik-store') }}" enctype="multipart/form-data">
    @csrf
        <div id="aksiCard" class="card mb-3 hidden">
            <div class="card-header bg-white">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0">Rubrik: <span id="selectedJenisText" class="text-primary">-</span></h5>
                        <div class="text-muted soft-hint">
                            Pastikan file yang diupload sudah menggunakan template rubrik yang sesuai.
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="resetBtn" class="btn btn-light" type="button">Reset</button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <input type="hidden" id="mkKodeInput" name="mk_kode">
                <input type="hidden" id="jenisInput" name="jenis_rubrik">

                <label class="fw-semibold mb-1">Upload Rubrik (Excel) <span class="text-danger">*</span></label>
                <input id="fileInput" name="rubrik_file" type="file" class="form-control" accept=".xlsx,.xls">

                <div id="fileHint" class="text-muted soft-hint mt-2 hidden">
                    File dipilih: <b id="fileNameText">-</b>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button id="previewBtn" class="btn btn-primary" type="button" disabled>
                        <i class="ti-eye"></i> Preview
                    </button>

                    <button id="submitBtn" class="btn btn-success" type="submit" disabled>
                        <i class="ti-save"></i> Submit
                    </button>
                </div>

                <div id="uploadSuccess" class="alert alert-success mt-3 hidden mb-0">
                    File berhasil dipilih. Preview rubrik ditampilkan di bawah.
                </div>
            </div>
        </div>
    </form>

    {{-- 4) PREVIEW TABLE --}}
<div id="previewCard" class="card hidden">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Preview Rubrik</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered rubrik-preview-table">
                <thead class="table-light" id="previewHead">
                    <tr>
                        <th>Aspek/Kriteria</th>
                        <th>Skor 1</th>
                        <th>Skor 2</th>
                        <th>Skor 3</th>
                        <th>Skor 4</th>
                        <th>Skor 5</th>
                    </tr>
                </thead>
                <tbody id="previewBody"></tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-light" onclick="window.scrollTo({top:0, behavior:'smooth'})">
                Kembali
            </button>
        </div>
    </div>
</div>
</div>

{{-- MODAL DAFTAR TEMPLATE RUBRIK --}}
<div class="modal fade" id="templateRubrikModal" tabindex="-1" aria-labelledby="templateRubrikModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="templateRubrikModalLabel">Daftar Template Rubrik</h5>
                    <small class="text-muted">Pilih template yang ingin diunduh sesuai kebutuhan penilaian.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="list-group">

                    @foreach($templateRubriks as $template)
                        <div class="list-group-item template-item">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $template['nama'] }}</div>
                                    <div class="text-muted soft-hint">{{ $template['deskripsi'] }}</div>
                                    <div class="text-muted soft-hint">
                                        <code>{{ $template['file'] }}</code>
                                    </div>
                                </div>

                                <a href="{{ asset('assets/xlsx_template/' . $template['file']) }}"
                                   class="btn btn-outline-success btn-sm">
                                    <i class="ti-download"></i> Unduh
                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    const mkSelect = document.getElementById('mkSelect');
    const mkBadge = document.getElementById('mkBadge');

    const jenisCard = document.getElementById('jenisCard');
    const aksiCard = document.getElementById('aksiCard');
    const previewCard = document.getElementById('previewCard');

    const jenisContainer = document.getElementById('jenisContainer');
    const jenisEmptyText = document.getElementById('jenisEmptyText');

    const selectedJenisText = document.getElementById('selectedJenisText');

    const mkKodeInput = document.getElementById('mkKodeInput');
    const jenisInput = document.getElementById('jenisInput');

    const fileInput = document.getElementById('fileInput');
    const fileHint = document.getElementById('fileHint');
    const fileNameText = document.getElementById('fileNameText');

    const previewBtn = document.getElementById('previewBtn');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');
    const uploadSuccess = document.getElementById('uploadSuccess');

    const previewHead = document.getElementById('previewHead');
    const previewBody = document.getElementById('previewBody');

    let selectedJenis = null;

    const show = el => el.classList.remove('hidden');
    const hide = el => el.classList.add('hidden');

    function resetUpload(){
        fileInput.value = '';
        previewBtn.disabled = true;
        submitBtn.disabled = true;
        hide(fileHint);
        hide(uploadSuccess);
        hide(previewCard);
        previewBody.innerHTML = '';
    }

    function resetJenisButtons(){
        document.querySelectorAll('.jenisBtn').forEach(b => {
            b.classList.remove('btn-primary');
            b.classList.add('btn-outline-primary');
        });
    }

    function renderJenisButtons(jenisList) {
        jenisContainer.innerHTML = '';

        if (!jenisList.length) {
            show(jenisEmptyText);
            return;
        }

        hide(jenisEmptyText);

        jenisList.forEach(jenis => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-primary sheet-btn jenisBtn';
            btn.dataset.jenis = jenis;
            btn.innerText = jenis;

            btn.addEventListener('click', function(){
                selectedJenis = this.dataset.jenis;
                jenisInput.value = selectedJenis;
                selectedJenisText.innerText = selectedJenis;

                resetUpload();
                show(aksiCard);

                resetJenisButtons();
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');

                aksiCard.scrollIntoView({behavior:'smooth', block:'start'});
            });

            jenisContainer.appendChild(btn);
        });
    }

    mkSelect.addEventListener('change', async function(){
        const mk = this.value;

        mkKodeInput.value = mk;
        selectedJenis = null;
        jenisInput.value = '';

        selectedJenisText.innerText = '-';
        mkBadge.innerText = 'MK: ' + (mk || '-');

        resetUpload();
        hide(aksiCard);
        hide(jenisCard);
        jenisContainer.innerHTML = '';

        if(!mk) return;

        try {
            const response = await fetch(`{{ url('dosen/rubrik/jenis') }}/${mk}`);
            const data = await response.json();

            show(jenisCard);
            renderJenisButtons(data);

            jenisCard.scrollIntoView({behavior:'smooth', block:'start'});
        } catch (error) {
            console.error('Gagal memuat jenis rubrik:', error);
            show(jenisCard);
            show(jenisEmptyText);
        }
    });

    fileInput.addEventListener('change', function(){
        const hasFile = this.files && this.files.length;
        previewBtn.disabled = !hasFile;
        submitBtn.disabled = !hasFile;

        if(hasFile){
            fileNameText.innerText = this.files[0].name;
            show(fileHint);
        } else {
            hide(fileHint);
        }
    });

    previewBtn.addEventListener('click', async function(){
        if(!fileInput.files.length){
            alert('Pilih file terlebih dahulu');
            return;
        }

        let formData = new FormData();
        formData.append('rubrik_file', fileInput.files[0]);

        try {
            const response = await fetch("{{ route('dosen.rubrik.preview') }}",{
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const res = await response.json();

            if(!res.success){
                alert(res.message || 'Preview gagal ditampilkan.');
                return;
            }

            renderPreview(res.type, res.headers, res.data);
            show(uploadSuccess);
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan saat preview file.');
        }
    });

    function renderPreview(type, headers, data){
        previewBody.innerHTML = '';

        let headRow = '<tr>';
        headers.forEach(header => {
            headRow += `<th>${header}</th>`;
        });
        headRow += '</tr>';
        previewHead.innerHTML = headRow;

        if(type === 'criteria_percentage'){
            data.forEach(row => {
                previewBody.innerHTML += `
                    <tr>
                        <td>${row.criteria ?? ''}</td>
                        <td>${row.percentage ?? ''}</td>
                        <td>${row['100'] ?? ''}</td>
                        <td>${row['80'] ?? ''}</td>
                        <td>${row['60'] ?? ''}</td>
                        <td>${row['40'] ?? ''}</td>
                        <td>${row['20'] ?? ''}</td>
                    </tr>
                `;
            });
        }

        if(type === 'program_report'){
            data.forEach(row => {
                previewBody.innerHTML += `
                    <tr>
                        <td>${row.criteria ?? ''}</td>
                        <td>${row.program_100 ?? ''}</td>
                        <td>${row.program_50 ?? ''}</td>
                        <td>${row.program_0 ?? ''}</td>
                        <td>${row.report_100 ?? ''}</td>
                        <td>${row.report_50 ?? ''}</td>
                        <td>${row.report_0 ?? ''}</td>
                    </tr>
                `;
            });
        }

        if(type === 'questions'){
            data.forEach(row => {
                previewBody.innerHTML += `
                    <tr>
                        <td>${row.question ?? ''}</td>
                        <td>${row.criteria ?? ''}</td>
                        <td>${row.no ?? ''}</td>
                        <td>${row.yes ?? ''}</td>
                    </tr>
                `;
            });
        }

        show(previewCard);
        previewCard.scrollIntoView({behavior:'smooth', block:'start'});
    }

    resetBtn.addEventListener('click', function(){
        resetUpload();
    });
</script>
@endsection