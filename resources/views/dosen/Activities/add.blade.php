{{-- @php
    $currentPrefix = auth()->user()->otoritas->otoritas ? 
    str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.' : 
    'admin.';
@endphp --}}
@extends('dosen.template')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="fw-bold">
                    <h3>Tambah Aktifitas Mingguan Baru</h3>
                </div>
                <div class="btn-wrapper">
                    <a download class="btn btn-inverse-primary" href="{{ asset('assets/xlsx_template/template.xlsx') }}">Template</a>
                    <button class="btn btn-primary text-white"
                        onclick="document.getElementById('excel').click()">Import</i></button>
                    <form id="form-import" method="post" enctype="multipart/form-data"
                        action="{{ route($currentPrefix . 'activities-wfile') }}">
                        @csrf
                        <input
                            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                            style="display:none" type="file" name="excel" id="excel">
                    </form>
                    <br>
                    <div style="font-size:11px; margin-top:-8%; "><i class="mdi mdi-information-outline"></i> Note: import
                        excel min. 2 activities</div>
                </div>

            </div>
            <div class="card-body">
                <!-- <form action="" method="post">
                    @csrf
                    <div class="form-group row mb-0">
                        <div class="form-group w-50 mb-0">
                            <div class="form-floating">
                                <select id="rps" name="id_rps" class="form-select form-control-lg"
                                    aria-label="select RPS">
                                    <option disabled> </option>
                                    @foreach ($rpss as $rps)
                                        <option {{ old('id_rps') == $rps->id ? 'selected' : '' }}
                                            value="{{ $rps->id }}">{{ $rps->nomor }} - {{ $rps->kode_mk }} -
                                            {{ $rps->mk?->nama ?? 'MK tidak ditemukan' }}</option>
                                    @endforeach
                                </select>
                                <label for="rps">Nomor RPS <span style="color:red">*</span></label>
                                @error('rps')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="MKHelp" class="form-text mb-3">Silahkan pilih Nomor RPS.</div>
                            </div>
                        </div>
                        <div class="form-group w-50 mb-0">
                            <div class="form-floating">
                                <input type="text" name="minggu" value="{{ old('minggu') }}" class="form-control"
                                    id="minggu" placeholder="minggu" aria-describedby="mingguHelp">
                                <label for="minggu" class="form-label">Minggu Ke- <span style="color:red">*</span></label>
                                @error('minggu')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="form-group w-50">
                            <div class="form-floating">
                                <input type="number" value="{{ old('bobot') }}" min="0" name="bobot"
                                    class="form-control" id="bobot" placeholder="bobot" aria-describedby="bobotHelp">
                                <label for="bobot" class="form-label">Bobot</label>
                                @error('bobot')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group w-50">
                            <div class="form-floating">
                                <input type="text" value="{{ old('kriteria') }}" name="kriteria" class="form-control"
                                    id="kriteria" placeholder="kriteria" aria-describedby="kriteriaHelp">
                                <label for="kriteria" class="form-label">Kriteria <span style="color:red">*</span></label>
                                @error('kriteria')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="form-group w-50">
                            <div class="form-floating h-100">
                                <textarea name="sub_cpmk" id="sub_cpmk" class="form-control" style="height: 85%" placeholder="insert sub_cpmk">{{ old('sub_cpmk') }}</textarea>
                                <label for="sub_cpmk" class="form-label">Rincian Sub CPMK <span
                                        style="color:red">*</span></label>
                                @error('sub_cpmk')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="sub_cpmkHelp" class="form-text">Silahkan masukkan rincian Sub CPMK.</div>
                            </div>
                        </div>
                        <div class="form-group w-50">
                            <div id="dynamic-metode_luring" class="form-group mb-1">
                                <div class="form-floating">
                                    <input type="text" value="{{ old('metode_luring') }}" name="metode_luring"
                                        class="form-control" id="metode_luring" placeholder="metode_luring">
                                    <label for="metode_luring" class="form-label"> Metode Luring </label>
                                </div>
                                @error('metode_luring')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="luringHelp" class="form-text">Silahkan masukkan metode pembelajaran luring.</div>
                            </div>
                            <div id="dynamic-metode_daring" class="form-group mb-1">
                                <div class="form-floating">
                                    <input type="text" value="{{ old('metode_daring') }}" name="metode_daring"
                                        class="form-control" id="metode_daring" placeholder="metode_daring">
                                    <label for="metode_daring" class="form-label"> Metode Daring </label>
                                </div>
                                @error('metode_daring')
                                    <div class="alert alert-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="daringHelp" class="form-text">Silahkan masukkan metode pembelajaran daring.</div>
                            </div>
                        </div>
                    </div>

                    <div id="dynamic-indikator" class="form-group">
                        <div class="form-group row">
                            <div class="form-floating col-10">
                                <input type="text" name="indikator[0]" class="form-control" id="indikator"
                                    placeholder="indikator" required>
                                <label for="indikator" class="form-label ps-4"> Indikator <span
                                        style="color:red">*</span></label>
                            </div>
                            <div class="col-2">
                                <button type="button" name="add" id="dynamic-btn-indikator"
                                    class="ms-3 mt-2 btn btn-primary">Add Field</button>
                            </div>
                        </div>
                        @error('indikator')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <textarea name="materi" id="materi" class="form-control" placeholder="insert materi" style="height: 100px">{{ old('materi') }}</textarea>
                        <label for="materi" class="form-label">Materi <span style="color:red">*</span></label>
                        @error('materi')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                        <div id="materiHelp" class="form-text">Silahkan masukkan Materi Pembelajaran.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form> -->

                <form action="" method="post">
                @csrf

                {{-- Nomor RPS & Minggu --}}
                <div class="form-group row mb-0">
                    <div class="form-group w-50 mb-0">
                        <div class="form-floating">
                            <select id="rps" name="id_rps" class="form-select form-control-lg" aria-label="select RPS">
                                <option disabled selected>Pilih RPS</option>
                                @foreach ($rpss as $rps)
                                    <option {{ old('id_rps') == $rps->id ? 'selected' : '' }} value="{{ $rps->id }}">
                                        {{ $rps->nomor }} - {{ $rps->kode_mk }} - {{ $rps->mk?->nama ?? 'MK tidak ditemukan' }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="rps">Nomor RPS <span class="text-danger">*</span></label>
                        </div>
                    </div>

                    <div class="form-group w-50 mb-0">
                        <div class="form-floating">
                            <input type="text" name="minggu" value="{{ old('minggu') }}" class="form-control"
                                id="minggu" placeholder="minggu">
                            <label for="minggu">Minggu Ke- <span class="text-danger">*</span></label>
                        </div>
                    </div>
                </div>

                {{-- Pilih CPMK --}}
                <div class="form-group mt-3">
                    <label for="id_cpmk">Pilih CPMK</label>
                    <select name="id_cpmk" id="id_cpmk" class="form-control" required>
                        <option value="">-- Pilih CPMK --</option>
                        @foreach($cpmks as $cpmk)
                            <option value="{{ $cpmk->id }}">{{ $cpmk->kode }} - {{ $cpmk->judul }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sub CPMK (Dynamic) --}}
                <div id="dynamic-sub-cpmk" class="form-group mt-3">
                    <div class="form-group row">
                        <div class="form-floating col-10">
                            <input type="text" name="sub_cpmk[0]" class="form-control" placeholder="Deskripsi Sub CPMK" required>
                            <label class="ps-4">Deskripsi Sub CPMK <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-2 d-flex align-items-center">
                            <button type="button" id="dynamic-btn-sub-cpmk" class="btn btn-secondary ms-3">Tambah</button>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="bentuk_asesmen">Bentuk Asesmen <span class="text-danger">*</span></label>
                    <select name="bentuk_asesmen" id="bentuk_asesmen" class="form-control" required>
                        <option value="">-- Pilih Bentuk Asesmen --</option>
                        @foreach($instrumen as $ins)
                            <option value="{{ $ins->nama_kriteria }}"
                                {{ old('bentuk_asesmen') == $ins->nama_kriteria ? 'selected' : '' }}>
                                {{ $ins->nama_kriteria }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Indikator (Dynamic) --}}
                <div id="dynamic-indikator" class="form-group mt-3">
                    <div class="form-group row">
                        <div class="form-floating col-10">
                            <input type="text" name="indikator[0]" class="form-control" placeholder="indikator" required>
                            <label class="ps-4">Indikator <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-2 d-flex align-items-center">
                            <button type="button" id="dynamic-btn-indikator" class="btn btn-primary ms-3">Tambah</button>
                        </div>
                    </div>
                </div>

                {{-- Materi (Dynamic) --}}
                <div id="dynamic-materi" class="form-group mt-3">
                    <div class="form-group row">
                        <div class="form-floating col-10">
                            <input type="text" name="materi[0]" class="form-control" placeholder="materi" required>
                            <label class="ps-4">Materi <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-2 d-flex align-items-center">
                            <button type="button" id="dynamic-btn-materi" class="btn btn-success ms-3">Tambah</button>
                        </div>
                    </div>
                </div>

                {{-- Metode Pembelajaran --}}
                <div class="form-group mt-3">
                    <label for="metode">Metode Pembelajaran <span class="text-danger">*</span></label>
                    <textarea name="metode" style="height: 85%" id="metode" class="form-control" rows="3" placeholder="Contoh : Ceramah dan Diskusi ">{{ old('metode') }}</textarea>
                </div>

                {{-- Metode Luring & Daring (dibungkus satu kotak) --}}
                <div class="border p-3 rounded mb-3 mt-3">
                    <h5 class="mb-3">Kegiatan</h5>

                    <div id="dynamic-kegiatan-luring" class="form-group mb-3">
                        <div class="form-group row">
                            <div class="form-floating col-10">
                                <input type="text" name="kegiatan_luring[0]" class="form-control" placeholder="Kegiatan Luring">
                                <label class="ps-4">Kegiatan Luring</label>
                            </div>
                            <div class="col-2 d-flex align-items-center">
                                <button type="button" id="dynamic-btn-luring" class="btn btn-warning ms-3">Tambah</button>
                            </div>
                        </div>
                    </div>

                    <div id="dynamic-kegiatan-daring" class="form-group">
                        <div class="form-group row">
                            <div class="form-floating col-10">
                                <input type="text" name="kegiatan_daring[0]" class="form-control" placeholder="Kegiatan Daring">
                                <label class="ps-4">Kegiatan Daring</label>
                            </div>
                            <div class="col-2 d-flex align-items-center">
                                <button type="button" id="dynamic-btn-daring" class="btn btn-info ms-3">Tambah</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function addDynamicField(containerId, name, label, index, btnClass) {
        let container = document.getElementById(containerId);
        let newRow = document.createElement('div');
        newRow.classList.add('form-group', 'row', 'mt-2');
        newRow.innerHTML = `
            <div class="form-floating col-10">
                <input type="text" name="${name}[${index}]" class="form-control" placeholder="${label}" required>
                <label class="ps-4">${label}</label>
            </div>
            <div class="col-2 d-flex align-items-center">
                <button type="button" class="btn btn-danger ms-3 remove-field">Hapus</button>
            </div>
        `;
        container.appendChild(newRow);
    }

    let subCpmkIndex = 1;
    document.getElementById('dynamic-btn-sub-cpmk').onclick = () => addDynamicField('dynamic-sub-cpmk', 'sub_cpmk', 'Deskripsi Sub CPMK', subCpmkIndex++, 'btn-secondary');

    let indikatorIndex = 1;
    document.getElementById('dynamic-btn-indikator').onclick = () => addDynamicField('dynamic-indikator', 'indikator', 'Indikator', indikatorIndex++, 'btn-primary');

    let materiIndex = 1;
    document.getElementById('dynamic-btn-materi').onclick = () => addDynamicField('dynamic-materi', 'materi', 'Materi', materiIndex++, 'btn-success');

    let luringIndex = 1;
    document.getElementById('dynamic-btn-luring').onclick = () => addDynamicField('dynamic-kegiatan-luring', 'kegiatan_luring', 'Kegiatan Luring', luringIndex++, 'btn-warning');

    let daringIndex = 1;
    document.getElementById('dynamic-btn-daring').onclick = () => addDynamicField('dynamic-kegiatan-daring', 'kegiatan_daring', 'Kegiatan Daring', daringIndex++, 'btn-info');

    // Hapus field
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-field')) {
            e.target.closest('.row').remove();
        }
    });

    // Auto-submit import excel
    document.getElementById("excel").onchange = function() {
        document.getElementById("form-import").submit();
    };
</script>
@endpush