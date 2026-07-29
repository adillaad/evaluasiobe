@extends('dosen.template')
@section('content')

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        color: #495057;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .select2-container .select2-selection--single, 
    .select2-container .select2-selection--multiple {
        min-height: 31px !important; /* Menyesuaikan dengan form-sm */
    }
</style>

<div class="container-fluid mb-4">
    
    {{-- CARD 1: FORM MAPPING --}}
    <div class="card mb-4">
        <div class="card-header py-3">
            <h4 class="mb-0 fw-bold"></i>Pemetaan CPL ke Mata Kuliah</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('dosen.cplmk-store') }}" method="post">
                @csrf
                
                {{-- Bagian Mata Kuliah --}}
                <div class="mb-4">
                    <label for="mataKuliah" class="form-label">Pilih Mata Kuliah <span class="text-danger">*</span></label>
                    <select id="mataKuliah" name="kode_mk" class="form-select form-select-sm" required>
                        <option value="" selected disabled>-- Pilih Mata Kuliah --</option>
                        @foreach ($mks as $mk)
                            <option value="{{$mk->kode}}">{{$mk->kode}} - {{$mk->nama}}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Bagian Pilih CPL (Multiple) --}}
                <div class="mb-4">
                    <label for="id_cpl" class="form-label">Pilih CPL Prodi <span class="text-danger">*</span></label>
                    <select name="id_cpl[]" id="id_cpl" class="js-example-basic-multiple form-select form-select-sm" multiple="multiple" required>
                        @foreach ($cpls as $cpl)
                            <option value="{{$cpl->id}}">
                                {{-- Format tampilan di dropdown --}}
                                <!-- {{$cpl->kode}} ({{$cpl->aspek}}) - {{ Str::limit($cpl->judul, 80) }} -->
                                {{$cpl->kurikulum->tahun}} - {{$cpl->kode}} - {{ Str::limit($cpl->judul, 80) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text mt-2">
                        <i class="ti-info-alt me-1"></i>Anda dapat memilih lebih dari satu CPL untuk mata kuliah ini.
                    </div>
                </div>

                <div class="border-top pt-3">
                    <button type="reset" class="btn btn-light me-2">Batal</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header py-3">
            <h4 class="mb-0 fw-bold">Daftar CPL Prodi</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover dataTable">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Aspek</th>
                            <th>Nomor</th>
                            <th>Kurikulum</th>
                            <th>Kode</th>
                            <th>Judul</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cpls as $cpl)
                            {{-- HAPUS BAGIAN IF INI --}}
                            <!-- {{-- @if($cpl->aspek == "Pengetahuan" || $cpl->aspek == "Keterampilan" || $cpl->aspek == "Umum") --}} -->
                            
                            <tr>
                                <td class="py-4">{{$loop->iteration}}</td>
                                <td>{{$cpl->aspek}}</td>
                                <td>{{$cpl->nomor}}</td>
                                <td>{{$cpl->kurikulum->tahun}}</td>
                                <td>{{$cpl->kode}}</td>
                                <td>{{$cpl->judul}}</td>
                            </tr>

                            {{-- @endif --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
@push('scripts')
        <script src="{{ asset('/assets/template/vendors/select2/select2.min.js')}}"></script>
        <script src="{{ asset('/assets/template/js/select2.js')}}"></script>
        <script>
            var i = 0;
            $("#dynamic-ar-sik").click(function() {
                ++i;
                $("#dynamicAddRemoveSik").append('<div class="form-group row clone"><div class="col-2"><div class="form-group"><label>Kurikulum <span class="text-danger">*</span></label><input type="text"class="form-control" name="kurikulum[' + i +
                    ']"placeholder="Kurikulum" autocomplete="off"></div></div><div class="col-3"><div class="form-group"><label>Kode <span class="text-danger">*</span></label><div class="input-group mb-2"><div class="input-group-prepend"><span class="input-group-text">S</span></div><input type="text" class="form-control" name="kode[' + i +
                    ']" placeholder="Nomor" autocomplete="off"></div></div></div><div class="col-5"><div class="form-group"><label>Judul <span class="text-danger">*</span></label><input type="text" class="form-control"name="judul[' + i +
                    ']" placeholder="Judul" autocomplete="off"></div></div><input hidden type="text" name="aspek" value="Keterampilan"><div class="col-2"><label>Action</label><div class="form-group"><button type="button" class="btn btn-sm btn-danger remove-input-field-sik">Delete</button></div></div></div>'
                );
            });
            $(document).on('click', '.remove-input-field-sik', function() {
                $(this).parents('.clone').remove();
            });
        </script>
    @endpush
@endsection
