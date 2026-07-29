@extends('dosen.template')

@section('content')
    {{-- HEADER --}}
    <div>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="left"
            title="Form ini digunakan untuk membuat template penilaian tanpa soal" style="float:right;">
            <i class="bi bi-info-circle-fill"></i>
        </button>

        <h3 class="px-4 pb-4 fw-bold text-center">
            Download Template Penilaian Tanpa Soal
        </h3>
    </div>

    {{-- CARD --}}
    <div class="form-group stretch-card">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('dosen.ExcelTanpaSoal') }}" method="GET">
                    @csrf

                    <div class="row">

                        {{-- Universitas --}}
                        <div class="col-5">
                            <div class="form-group">
                                <label>Universitas <span class="text-danger">*</span></label>
                                <select name="univ" class="form-control" required>
                                    <option value="">Pilih...</option>
                                    @foreach ($universitas as $u)
                                        <option value="{{ $u->nama }}">{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Semester --}}
                        <div class="col-5">
                            <div class="form-group">
                                <label>Semester <span class="text-danger">*</span></label>
                                <select name="semester" class="form-control" required>
                                    <option value="">Pilih...</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                        </div>

                        {{-- Prodi --}}
                        <div class="col-5">
                            <div class="form-group">
                                <label>Prodi <span class="text-danger">*</span></label>
                                <select name="prodi" class="form-control" required>
                                    @foreach ($prodi as $p)
                                        <option value="{{ $p->nama }}">{{ $p->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- MK --}}
                        <div class="col-5">
                            <div class="form-group">
                                <label>Nama MK <span class="text-danger">*</span></label>
                                <select name="kode_mk" id="kode_mk" class="form-control" required>
                                    <option value="">Pilih...</option>
                                    @foreach ($rpss as $rps)
                                        <option value="{{ $rps->kode_mk }}">
                                            {{ $rps->kode_mk }} - {{ $rps->mk?->nama ?? 'MK tidak ditemukan' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Jenis --}}
                        <div class="col-5">
                            <div class="form-group">
                                <label>Jenis <span class="text-danger">*</span></label>
                                <select name="jenis" id="jenis" class="form-control" required disabled>
                                    <option value="">Pilih MK terlebih dahulu</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    {{-- TABLE --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Daftar Instrumen Tanpa Soal</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableInstrumen">

                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Instrumen</th>
                                            <th>CPL</th>
                                            <th>CPMK</th>
                                            <th>Bobot (%)</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                Pilih Mata Kuliah dan Metode Penilaian
                                            </td>
                                        </tr>
                                    </tbody>

                                    {{-- TAMBAHAN (BIAR SAMA) --}}
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Total Bobot</th>
                                            <th id="totalBobot">0%</th>
                                        </tr>
                                    </tfoot>

                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- BUTTON --}}
                    <div class="row mt-3">
                        <div class="col-5">
                            <input type="submit" class="btn btn-primary" value="Download">
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- JS TETAP (TIDAK DIUBAH) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $('#kode_mk').on('change', function() {
            let kode_mk = $(this).val();

            $('#jenis').prop('disabled', false)
                .html('<option>Loading...</option>');

            $.ajax({
                url: "{{ route('dosen.getJenisByMk') }}",
                type: "GET",
                data: {
                    kode_mk: kode_mk
                },
                success: function(data) {
                    let options = '<option value="">-- Pilih Metode --</option>';
                    data.forEach(item => {
                        options += `<option value="${item.id}">
                    ${item.nama} (${parseFloat(item.bobot).toFixed(2)}%)
                </option>`;
                    });
                    $('#jenis').html(options);
                }
            });
        });

        $('#jenis').on('change', function() {

            let kode_mk = $('#kode_mk').val();
            let jenis = $(this).val();

            $.ajax({
                url: "{{ route('dosen.getInstrumenTanpaSoal') }}",
                type: "GET",
                data: {
                    kode_mk: kode_mk,
                    jenis: jenis
                },
                success: function(response) {

                    let rows = '';
                    let total = 0;

                    response.data.forEach((item, index) => {

                        let bobot = parseFloat(item.bobot ?? 0);
                        total += bobot;

                        rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nama_instrumen}</td>
                    <td>${item.cpl_kode ?? '-'}</td>
                    <td>${item.cpmk_kode ?? '-'}</td>
                    <td>${bobot.toFixed(2)}%</td>
                </tr>`;
                    });

                    $('#tableInstrumen tbody').html(rows);
                    $('#totalBobot').text(total.toFixed(2) + '%');
                }
            });
        });
    </script>
@endsection
