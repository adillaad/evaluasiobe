{{-- @php
    $routePrefix = [
        'Penjamin Mutu Program Studi' => ['prefix' => 'penjamin-mutu.program-studi.'],
        'Kepala Program Studi' => ['prefix' => 'kepala-program-studi.'],
    ];

    $userOtoritas = auth()->user()->otoritas->otoritas;
    $currentPrefix = $routePrefix[$userOtoritas]['prefix'] ?? 'penjamin-mutu.program-studi.';
@endphp --}}
@extends(in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan', 'Dosen']) ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div id="notif-wrapper" style="display:none;" class="mb-3"></div>

                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h4 class="card-title mb-0 me-auto">List Profil Lulusan - Profesi</h4>
                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                        <button type="button" class="btn btn-success btn-icon-text" onclick="create()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Tambah Profil Lulusan</span>
                        </button>
                    @endif
                </div>
                <x-filter-form :universities="$universities" :faculties="$faculties" :programs="$programs" :kurikulums="$kurikulums" :showKurikulum="true" />

                <div id="read" class="mt-3"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="notif-modal" style="display:none;" class="mb-2"></div>
                    <div id="page" class="p-2"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showNotif(msg, type = 'success', targetId = 'notif-wrapper') {
            const el = document.getElementById(targetId);
            if (!el) return;

            const cls = type === 'success' ? 'alert-success' : 'alert-danger';
            el.innerHTML = `
                <div class="alert ${cls} alert-dismissible fade show mb-0" role="alert">
                    ${msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            el.style.display = 'block';

            setTimeout(() => {
                const alertEl = el.querySelector('.alert');
                if (alertEl) {
                    alertEl.classList.remove('show');
                    setTimeout(() => {
                        el.style.display = 'none';
                        el.innerHTML = '';
                    }, 300);
                }
            }, 3500);
        }

        function getBaseUrl() {
            const otoritas = "{{ $userOtoritas }}";
            switch (otoritas) {
                case 'Wakil Rektor':
                    return 'wakil-rektor';
                case 'Wakil Dekan':
                    return 'wakil-dekan';
                case 'Kepala Program Studi':
                    return 'kepala-program-studi';
                case 'Dosen':
                    return 'dosen';
                case 'Penjamin Mutu Universitas':
                    return 'penjamin-mutu/universitas';
                case 'Penjamin Mutu Fakultas':
                    return 'penjamin-mutu/fakultas';
                case 'Penjamin Mutu Program Studi':
                    return 'penjamin-mutu/program-studi';
                default:
                    return '';
            }
        }

        function read(queryString = '') {
            const baseUrl = getBaseUrl();
            const fullUrl = `/${baseUrl}/readListProfilProf${queryString}`;

            $('#read').html(
                '<div class="text-center p-4"><div class="spinner-border" role="status"></div><p>Loading...</p></div>'
            );

            $.ajax({
                url: fullUrl,
                method: "GET",
                timeout: 10000,
                success: function(res) {
                    $('#read').html(res);
                },
                error: function(xhr) {
                    $('#read').html(`<div class='alert alert-danger'>Gagal memuat data (${xhr.status})</div>`);
                }
            });
        }

        $(document).ready(function() {
            read(window.location.search);

            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                read('?' + $(this).serialize());
            });

            $('#reset-filter').on('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ route(Request::route()->getName()) }}";
            });

            // Reset notif modal setiap kali modal ditutup
            $('#tambahModal').on('hidden.bs.modal', function() {
                $('#notif-modal').hide().html('');
            });
        });

        function create() {
            if ("{{ $userOtoritas }}" === 'Dosen') return;
            $.get(`/${getBaseUrl()}/createListProfil`, {}, function(data) {
                $("#exampleModalLabel").text("Add Graduate Profile");
                $('#page').html(data);
                $('#notif-modal').hide().html('');
                $('#tambahModal').modal('show');
            });
        }

        function store() {
            if ("{{ $userOtoritas }}" === 'Dosen') return;
            $.ajax({
                type: "GET",
                url: `/${getBaseUrl()}/storeListProfil`,
                data: {
                    'namaProfil': $("#namaProfil").val(),
                    'deskripsi': $("#deskripsi").val(),
                    'kurikulum_id': $("#id_kurikulum").val(),
                },
                success: function(res) {
                    $('#tambahModal').modal('hide');
                    showNotif(res.message ?? 'Data berhasil disimpan', 'success', 'notif-wrapper');
                    read();
                },
                error: function(xhr) {
                    handleValidationError(xhr, 'notif-modal');
                }
            });
        }

        function storeAptikom() {
            if ("{{ $userOtoritas }}" === 'Dosen') return;
            $.ajax({
                type: "GET",
                url: `/${getBaseUrl()}/storeListProfilAptikom`,
                data: {
                    'jenis': $("#jenis").val(),
                    'deskripsi': $("#deskripsi").val(),
                    'status': $("#status").val(),
                    'acuan': $("#acuan").val(),
                    'kurikulum_id': $("#id_kurikulum").val(),
                },
                success: function(res) {
                    $('#tambahModal').modal('hide');
                    showNotif(res.message ?? 'Data berhasil disimpan', 'success', 'notif-wrapper');
                    read();
                },
                error: function(xhr) {
                    handleValidationError(xhr, 'notif-modal');
                }
            });
        }

        function showProfil(id) {
            if ("{{ $userOtoritas }}" === 'Dosen') return;
            $.get(`/${getBaseUrl()}/showProfil/${id}`, {}, function(data) {
                $("#exampleModalLabel").text("Edit Graduate Profile");
                $('#page').html(data);
                $('#notif-modal').hide().html('');
                $('#tambahModal').modal('show');
            }).fail(function() {
                showNotif('Gagal memuat form edit', 'error', 'notif-wrapper');
            });
        }

        function updateProfil(id) {
            if ("{{ $userOtoritas }}" === 'Dosen') return;
            $.ajax({
                type: "GET",
                url: `/${getBaseUrl()}/updateProfil/${id}`,
                data: {
                    'namaProfil': $("#namaProfil").val(),
                    'deskripsi': $("#deskripsi").val(),
                },
                success: function(res) {
                    $('#tambahModal').modal('hide');
                    showNotif(res.message ?? 'Data berhasil diperbarui', 'success', 'notif-wrapper');
                    read();
                },
                error: function(xhr) {
                    handleValidationError(xhr, 'notif-modal');
                }
            });
        }

        function updateProfilAptikom(id) {
            if ("{{ $userOtoritas }}" === 'Dosen') return;
            $.ajax({
                type: "GET",
                url: `/${getBaseUrl()}/updateProfilAptikom/${id}`,
                data: {
                    'jenis': $("#jenis").val(),
                    'deskripsi': $("#deskripsi").val(),
                    'status': $("#status").val(),
                    'acuan': $("#acuan").val(),
                    'kurikulum_id': $("#id_kurikulum").val(),
                },
                success: function(res) {
                    $('#tambahModal').modal('hide');
                    showNotif(res.message ?? 'Data berhasil diperbarui', 'success', 'notif-wrapper');
                    read();
                },
                error: function(xhr) {
                    handleValidationError(xhr, 'notif-modal');
                }
            });
        }

        function deleteProfil(id) {
            if ("{{ $userOtoritas }}" === 'Dosen') return;
            if (!confirm("Apakah Anda yakin ingin menghapus data ini?")) return;

            $.ajax({
                type: "GET",
                url: `/${getBaseUrl()}/deleteProfil/${id}`,
                success: function(res) {
                    showNotif(res.message ?? 'Data berhasil dihapus', 'success', 'notif-wrapper');
                    read();
                },
                error: function(xhr) {
                    showNotif('Gagal menghapus data', 'error', 'notif-wrapper');
                    console.error(xhr.responseText);
                }
            });
        }

        function handleValidationError(xhr, targetId = 'notif-wrapper') {
            if (xhr.status === 422 && xhr.responseJSON?.validation_errors) {
                let msgs = [];
                $.each(xhr.responseJSON.validation_errors, function(k, v) {
                    msgs.push(v[0]);
                });
                showNotif(msgs[0], 'error', targetId);
            } else {
                showNotif('Gagal memproses permintaan', 'error', targetId);
            }
        }
    </script>
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection
