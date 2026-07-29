@extends('admin.template')
@section('content')
    <style>
        li.select2-selection__choice {
            color: white;
            font-weight: bolder;
        }
    </style>

    <div class="col-lg-12 grid-margin stretch-card mb-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add CPLMK</h4>
                <form action="" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="matakuliah">Mata Kuliah <span class="text-danger"> *</span></label>
                        <select id="matakuliah" name="kode_mk" class="js-example-basic-single w-100" required>
                            <option selected="true" value="" disabled selected>Select...</option>
                            @foreach ($mks as $mk)
                                <option value="{{ $mk->kode }}">{{ $mk->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_cpl">CPL Prodi <span class="text-danger"> *</span></label>
                        <select name="id_cpl[]" class="js-example-basic-multiple w-100" multiple="multiple">
                            @foreach ($cpls as $cpl)
                                {{-- @if ($cpl->aspek != 'Sikap') --}}
                                <option value="{{ $cpl->id }}">{{ $cpl->kurikulum->tahun }} - {{ $cpl->kode }}
                                </option>
                                {{-- @endif --}}
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin stretch-card mb-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">List CPL Prodi</h4>
                <form method="GET" action="{{ route(Request::route()->getName()) }}" class="mb-4">
                    <div class="row">
                        @if (auth()->user()->otoritas->otoritas != 'Admin Universitas')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="universitas_id">Universitas</label>
                                    <select name="universitas_id" id="universitas_id" class="form-control">
                                        <option value="">Pilih Universitas</option>
                                        @foreach ($universities as $univ)
                                            <option value="{{ $univ->id }}"
                                                {{ request('universitas_id') == $univ->id ? 'selected' : '' }}>
                                                {{ $univ->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fakultas_id">Fakultas</label>
                                <select name="fakultas_id" id="fakultas_id" class="form-control"
                                    {{ !request('universitas_id') ? 'disabled' : '' }}>
                                    <option value="">Pilih Fakultas</option>
                                    @foreach ($faculties as $faculty)
                                        <option value="{{ $faculty->id }}"
                                            {{ request('fakultas_id') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="prodi_id">Program Studi</label>
                                <select name="prodi_id" id="prodi_id" class="form-control"
                                    {{ !request('fakultas_id') ? 'disabled' : '' }}>
                                    <option value="">Pilih Program Studi</option>
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->id }}"
                                            {{ request('prodi_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route(Request::route()->getName()) }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Aspek</th>
                                <th>Nomor</th>
                                <th>Kurikulum</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Prodi</th>
                                <th>Fakultas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cpls as $cpl)
                                @if ($cpl->aspek != 'Sikap')
                                    <tr>
                                        <td class="py-4">{{ $loop->iteration }}</td>
                                        <td>{{ $cpl->aspek }}</td>
                                        <td>{{ $cpl->nomor }}</td>
                                        <td>{{ $cpl->kurikulum->tahun }}</td>
                                        <td>{{ $cpl->kode }}</td>
                                        <td>
                                            <div class="text-wrap lh-base" style="width: 300px">
                                                {{ $cpl->judul }}
                                            </div>
                                        </td>
                                        <td>{{ $cpl->prodi->nama }}</td>
                                        <td>{{ $cpl->prodi->fakultas->nama }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('/assets/template/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('/assets/template/js/select2.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const universitasSelect = $('#universitas_id');
            const fakultasSelect = $('#fakultas_id');
            const prodiSelect = $('#prodi_id');
            const userRole = "{{ auth()->user()->otoritas->otoritas }}";

            // Initialize Select2
            universitasSelect.select2({
                placeholder: 'Pilih Universitas',
                allowClear: true
            });

            fakultasSelect.select2({
                placeholder: 'Pilih Fakultas',
                allowClear: true
            });

            prodiSelect.select2({
                placeholder: 'Pilih Program Studi',
                allowClear: true
            });

            // Modify initial state based on user role
            if (userRole === 'Admin Universitas') {
                universitasSelect.closest('.col-md-4').hide();
                fakultasSelect.prop('disabled', false);
            } else {
                fakultasSelect.prop('disabled', !universitasSelect.val());
                prodiSelect.prop('disabled', true);
            }

            // University change event
            universitasSelect.on('change', function() {
                const universitasId = $(this).val();

                // Reset and disable dependent dropdowns
                fakultasSelect.val(null).trigger('change');
                prodiSelect.val(null).trigger('change');
                fakultasSelect.prop('disabled', !universitasId);
                prodiSelect.prop('disabled', true);

                if (universitasId) {
                    const routePrefix = window.location.pathname.split('/')[1];
                    $.ajax({
                        url: `/${routePrefix}/get-faculties/${universitasId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(faculties) {
                            fakultasSelect.find('option:not(:first)').remove();

                            faculties.forEach(function(faculty) {
                                const newOption = new Option(faculty.nama, faculty.id,
                                    false, false);
                                fakultasSelect.append(newOption);
                            });

                            fakultasSelect.trigger('change');
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat mengambil data fakultas');
                        }
                    });
                }
            });

            // Faculty change event
            fakultasSelect.on('change', function() {
                const fakultasId = $(this).val();

                prodiSelect.val(null).trigger('change');
                prodiSelect.prop('disabled', !fakultasId);

                if (fakultasId) {
                    const routePrefix = window.location.pathname.split('/')[1];
                    $.ajax({
                        url: `/${routePrefix}/get-programs/${fakultasId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(programs) {
                            prodiSelect.find('option:not(:first)').remove();

                            programs.forEach(function(program) {
                                const newOption = new Option(program.nama, program.id,
                                    false, false);
                                prodiSelect.append(newOption);
                            });

                            prodiSelect.trigger('change');
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat mengambil data program studi');
                        }
                    });
                }
            });

            // On page load, check if there are pre-selected values
            function initializeDropdowns() {
                if (universitasSelect.val()) {
                    fakultasSelect.prop('disabled', false);
                }

                if (fakultasSelect.val()) {
                    prodiSelect.prop('disabled', false);
                }
            }

            initializeDropdowns();
        });
    </script>
@endsection
