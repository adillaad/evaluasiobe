{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}

@extends('penjamin-mutu.template')
@section('content')
<div class="stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tambah CPL untuk Program Studi: {{ $prodi->nama }}</h4>
            <p class="card-description">
                Fakultas: {{ $prodi->fakultas->nama }}
            </p>
            <form method="POST" action="{{ route($currentPrefix . 'cpl.store') }}">
                @csrf
                <hr>
                
                <div id="cpl-fields-container">
                    {{-- Template Baris Awal --}}
                    <div class="cpl-row border rounded p-3 mb-4 position-relative">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tahun Kurikulum <span class="text-danger">*</span></label>
                                    <select class="form-control kurikulum-dropdown" name="id_kurikulum[]">
                                        <option value="" disabled selected>Pilih Kurikulum...</option>
                                        @foreach($kurikulums as $k)
                                            <option value="{{ $k->id }}">{{ $k->tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Aspek <span class="text-danger">*</span></label>
                                    <select class="form-control" name="aspek[]">
                                        <option value="" disabled selected>Pilih Aspek...</option>
                                        <option value="Sikap">Sikap</option>
                                        <option value="Keterampilan Umum">Keterampilan Umum</option>
                                        <option value="Keterampilan Khusus">Keterampilan Khusus</option>
                                        <option value="Pengetahuan">Pengetahuan</option>
                                        <option value="Pengetahuan & Keterampilan">Pengetahuan & Keterampilan</option>
                                        <option value="Pengetahuan Interdisipliner">Pengetahuan Interdisipliner</option>
                                        <option value="Keterampilan Umum & Khusus">Keterampilan Umum & Khusus</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kode <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">CPL</span></div>
                                        <input type="text" class="form-control" name="kode[]" placeholder="Nomor" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label>Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul[]" placeholder="Masukkan judul CPL" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="button" id="add-cpl-row" class="btn btn-primary me-2">Tambah Baris</button>
                    <button type="submit" class="btn btn-success">Simpan Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Fungsi untuk membuat template HTML baris baru.
        function getNewRowTemplate() {
            // Ambil opsi kurikulum dari baris pertama yang sudah ada
            const kurikulumOptions = $('.kurikulum-dropdown:first').html();

            return `
            <div class="cpl-row border rounded p-3 mb-4 position-relative">
                <button type="button" class="btn btn-danger btn-sm remove-cpl-row" style="position: absolute; top: 1rem; right: 1rem;">Hapus</button>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Tahun Kurikulum <span class="text-danger">*</span></label><select class="form-control kurikulum-dropdown" name="id_kurikulum[]">${kurikulumOptions}</select></div></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Aspek <span class="text-danger">*</span></label>
                            <select class="form-control" name="aspek[]">
                                        <option value="" disabled selected>Pilih Aspek...</option>
                                        <option value="Sikap">Sikap</option>
                                        <option value="Keterampilan Umum">Keterampilan Umum</option>
                                        <option value="Keterampilan Khusus">Keterampilan Khusus</option>
                                        <option value="Pengetahuan">Pengetahuan</option>
                                        <option value="Pengetahuan & Keterampilan">Pengetahuan & Keterampilan</option>
                                        <option value="Pengetahuan Interdisipliner">Pengetahuan Interdisipliner</option>
                                        <option value="Keterampilan Umum & Khusus">Keterampilan Umum & Khusus</option>
                                        <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4"><div class="form-group"><label>Kode <span class="text-danger">*</span></label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">CPL</span></div><input type="text" class="form-control" name="kode[]" placeholder="Nomor" autocomplete="off"></div></div></div>
                </div>
                <div class="form-group mb-0"><label>Judul <span class="text-danger">*</span></label><input type="text" class="form-control" name="judul[]" placeholder="Masukkan judul CPL" autocomplete="off"></div>
            </div>`;
        }

        // Event handler untuk tombol "Tambah Baris"
        $("#add-cpl-row").click(function() {
            $('#cpl-fields-container').append(getNewRowTemplate());
        });

        // Event handler untuk tombol "Hapus"
        $("#cpl-fields-container").on('click', '.remove-cpl-row', function() {
            $(this).closest('.cpl-row').remove();
        });
    });
</script>
@endsection