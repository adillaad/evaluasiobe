@extends('admin.template')
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">List RPS</h4>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                />
                <div class="table-responsive mt-4">
                    <table class="table table-hover dataTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode MK</th>
                                <th>Tanggal Penyusunan</th>
                                <!--<th>Nomor</th>-->
                                <th>Semester</th>
                                <th>Pengembang RPS</th>
                                <th>Prodi</th>
                                <th>Fakultas</th>
                                @if (auth()->user()->otoritas->otoritas != 'Admin Universitas')
                                    <th>Universitas</th>
                                @endif
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rpss as $rps)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $rps->kode_mk }}</td>
                                    <td>{{ date('d-m-Y', strtotime($rps->created_at)) }}</td>
                                    <!--<td>{{ $rps->nomor }}</td>-->
                                    <td>{{ $rps->semester }}</td>
                                    <td>{{ $rps->pengembang }}</td>
                                    <td>{{ $rps->prodi->nama }}</td>
                                    <td>{{ $rps->prodi->fakultas->nama }}</td>
                                    @if (auth()->user()->otoritas->otoritas != 'Admin Universitas')
                                        <td>{{ $rps->prodi->fakultas->universitas->nama }}</td>
                                    @endif
                                    <td class="d-flex">
                                            @if (auth()->user()->otoritas->otoritas != 'Admin')
                                            <!--<a type="button" href="edit-rps/{{ $rps->kode_mk }}"-->
                                            <!--    class="btn btn-warning btn-icon-text p-2"-->
                                            <!--    style="margin-right:7px; width:35px; height:35px">-->
                                            <!--    <i class="ti-pencil btn-icon"></i>-->
                                            <!--</a>-->
                                            <form action="delete-rps/{{ encrypt($rps->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-danger btn-icon-text me-2 p-2"
                                                    style="width:35px; height:35px"
                                                    onclick="return confirm('Are you sure to delete RPS {{ $rps->kode_mk }}?')">
                                                    <i class="ti-trash btn-icon"></i>
                                                </button>
                                            </form>
                                            @endif
                                            <a href="/admin/print-rps/{{ encrypt($rps->id) }}" style="width:35px; height:35px"
                                                target="_blank" type="button" class="btn btn-info btn-icon-text p-2">
                                                <i class="ti-download btn-icon"></i>
                                            </a>
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
    {{-- <script>
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
            fakultasSelect.on('change', function() {
                const fakultasId = $(this).val();

                prodiSelect.val(null).trigger('change');
                prodiSelect.prop('disabled', !fakultasId);

                if (fakultasId) {
                    $.ajax({
                        url: `/get-programs/${fakultasId}`,
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
    </script> --}}
@endsection
