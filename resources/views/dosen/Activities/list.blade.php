@php
    // $currentPrefix = auth()->user()->otoritas->otoritas
    //     ? str_replace(' ', '-', strtolower(auth()->user()->otoritas->otoritas)) . '.'
    //     : 'admin.';
    $isDosen = $userOtoritas == 'Dosen';
@endphp
@extends('dosen.template')
<style>
    #activitiesTable td:nth-child(6) {
        min-width: 600px;
        max-width: 600px;
    }
</style>
@section('content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Activities</h4>
                <x-filter-form
                    :universities="$universities"
                    :faculties="$faculties"
                    :programs="$programs"
                />
                <div class="table-responsive">
                    <table class="table table-hover" id="activitiesTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Nama MK</th>
                                <th>Nomor RPS</th>
                                <th>Minggu</th>
                                <th>Sub CPMK</th>
                                <th>Indikator</th>
                                <th>Materi</th>
                                <th>Bentuk Asesmen</th>
                                <th>Metode</th>
                                <th>Kegiatan Luring</th>
                                <th>Kegiatan Daring</th>
                                @if ($userOtoritas == 'Dosen')
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
        <script>
            $(document).ready(function() {
            const columns = [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'mk_nama',
                    name: 'mk_nama'
                },
                {
                    data: 'rps_nomor',
                    name: 'rps_nomor'
                },
                {
                    data: 'minggu',
                    name: 'minggu'
                },
                {
                    data: 'sub_cpmk',
                    name: 'sub_cpmk'
                },
                {
                    data: 'indikator',
                    name: 'indikator',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data; // biar tetap <ul><li>..</li></ul>
                        }
                        return $(data).text(); // buat search
                    }
                },
                {
                    data: 'materi',
                    name: 'materi'
                },
                {
                    data: 'bentuk_asesmen',
                    name: 'bentuk_asesmen'
                },
                {
                    data: 'metode',
                    name: 'metode'
                },
                {
                    data: 'kegiatan_luring',
                    name: 'kegiatan_luring'
                },
                {
                    data: 'kegiatan_daring',
                    name: 'kegiatan_daring'
                }
            ];

            @if ($isDosen)
                columns.push({
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                });
            @endif

            $('#activitiesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route($currentPrefix . 'activities-data') }}',
                    type: 'GET',
                    data: function(d) {
                        d.universitas_id = $('#universitas_id').val();
                        d.fakultas_id = $('#fakultas_id').val();
                        d.prodi_id = $('#prodi_id').val();
                    }
                },
                columns: columns,
                pageLength: 10,
                order: [[3, 'asc']],
                columnDefs: [
                    { targets: 5, className: 'text-wrap' }, // indikator
                    { targets: 6, className: 'text-wrap' }, // materi
                    { targets: 9, className: 'text-wrap' }, // kegiatan luring
                    { targets: 10, className: 'text-wrap' } // kegiatan daring
                ]
            });
        });

        </script>
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
                if (['Wakil Rektor', 'Wakil Dekan', 'Kepala Program Studi', 'Dosen'].includes(
                        userRole)) {
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
                        const routePrefix = window.location.pathname.split('/')[1];
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
        </script>
    @endpush
@endsection
