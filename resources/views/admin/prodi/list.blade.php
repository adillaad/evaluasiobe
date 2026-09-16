{{-- @php
    $routePrefix = [
        'admin' => ['prefix' => 'admin'],
        'Admin Universitas' => ['prefix' => 'admin-universitas.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'admin.';
@endphp --}}

@extends('admin.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Prodi</h4>
                {{-- Filter Form --}}
                <form method="GET" action="{{ route(Request::route()->getName()) }}" class="mb-4">
                    <div class="row">
                        @if ($userOtoritas != 'Admin Universitas')
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
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route(Request::route()->getName()) }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Prodi</th>
                                <th>Status Aptikom</th>
                                <th>Fakultas</th>
                                @if ($userOtoritas != 'Admin Universitas')
                                    <th>Universitas</th>
                                @endif
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prodis as $prodi)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $prodi->nama }}</td>
                                    <td>
                                        <x-aptikom-badge :value="(bool) $prodi->is_aptikom" />
                                    </td>
                                    <td>{{ $prodi->fakultas->nama }}</td>
                                    @if ($userOtoritas != 'Admin Universitas')
                                        <td>{{ $prodi->fakultas->universitas->nama }}</td>
                                    @endif
                                    <td>
                                        <form action="{{ route($currentPrefix . 'delete-prodi', ['id' => $prodi->id]) }}"
                                            method="post" class="d-inline m-0 p-0">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-danger btn-icons"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                onclick="return confirm('Are you sure to delete?')">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const universitasSelect = $('#universitas_id');
            const fakultasSelect = $('#fakultas_id');
            const prodiSelect = $('#prodi_id');
            const userRole = "{{ $userOtoritas }}";

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
                    $.ajax({
                        url: `/get-faculties/${universitasId}`,
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
            // fakultasSelect.on('change', function() {
            //     const fakultasId = $(this).val();

            //     prodiSelect.val(null).trigger('change');
            //     prodiSelect.prop('disabled', !fakultasId);

            //     if (fakultasId) {
            //         const routePrefix = window.location.pathname.split('/')[1];
            //         $.ajax({
            //             url: `/${routePrefix}/get-programs/${fakultasId}`,
            //             method: 'GET',
            //             dataType: 'json',
            //             success: function(programs) {
            //                 prodiSelect.find('option:not(:first)').remove();

            //                 programs.forEach(function(program) {
            //                     const newOption = new Option(program.nama, program.id,
            //                         false, false);
            //                     prodiSelect.append(newOption);
            //                 });

            //                 prodiSelect.trigger('change');
            //             },
            //             error: function() {
            //                 alert('Terjadi kesalahan saat mengambil data program studi');
            //             }
            //         });
            //     }
            // });

            // On page load, check if there are pre-selected values
            function initializeDropdowns() {
                if (universitasSelect.val()) {
                    fakultasSelect.prop('disabled', false);
                }

                // if (fakultasSelect.val()) {
                //     prodiSelect.prop('disabled', false);
                // }
            }

            initializeDropdowns();
        });
    </script>
@endsection
