{{-- @php ... @endphp --}}
@extends(in_array($userOtoritas, ['Wakil Rektor', 'Wakil Dekan', 'Dosen']) ? 'dosen.template' : 'penjamin-mutu.template')
@section('content')
    @if (session()->has('failed'))
        <div class="alert alert-danger" role="alert" id="box">
            <div>{{ session('failed') }}</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert greenAdd" role="alert" id="box">
            <div>{{ session('success') }}</div>
        </div>
    @endif
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h4 class="card-title mb-0 me-auto">List Profesi</h4>
                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                        <button type="button" class="btn btn-success btn-icon-text" onclick="createProfesi()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Tambah Profesi</span>
                        </button>
                    @endif
                </div>
                <x-filter-form :universities="$universities" :faculties="$faculties" :programs="$programs" :kurikulums="$kurikulums" :showKurikulum="true" />
                <div id="notif-area" class="mt-3"></div>
                <div id="read" class="mt-3"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-notif"></div>
                    <div id="page" class="p-2"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        function showNotif(type, msg) {
            const icon = type === 'success' ? '&#10003;' : '&#10007;';
            const html = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <strong>${icon}</strong> ${msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            $('#notif-area').html(html);
            setTimeout(function() {
                $('#notif-area .alert').alert('close');
            }, 4000);
        }

        function showModalError(msg) {
            const html = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>&#10007;</strong> ${msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            $('#modal-notif').html(html);
        }

        function readProfesi(queryString = '') {
            const baseUrl = getBaseUrl();
            const fullUrl = `${window.location.origin}/${baseUrl}/readListProfesi${queryString}`;

            $('#read').html(
                '<div class="d-flex justify-content-center align-items-center" style="height: 200px;"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
            );

            $.get(fullUrl, function(data) {
                $('#read').html(data);
            }).fail(function() {
                $('#read').html('<p class="text-danger text-center">Gagal memuat data.</p>');
            });
        }

        $(document).ready(function() {
            readProfesi(window.location.search);

            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                const formData = '?' + $(this).serialize();
                readProfesi(formData);
            });

            $('#reset-filter').on('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ route(Request::route()->getName()) }}";
            });

            $('#tambahModal').on('hidden.bs.modal', function() {
                $('#modal-notif').html('');
            });
        });

        function createProfesi() {
            const baseUrl = getBaseUrl();
            $.ajax({
                url: `/${baseUrl}/createListProfesi`,
                method: "GET",
                success: function(data) {
                    $("#exampleModalLabel").text("Tambah Profesi");
                    $("#modal-notif").html('');
                    $("#page").html(data);
                    $("#tambahModal").modal("show");
                },
                error: function() {
                    alert('Gagal memuat form tambah profesi');
                }
            });
        }

        function storeProfesi() {
            if ("{{ $userOtoritas }}" === 'Dosen') return;

            const baseUrl = getBaseUrl();
            var kurikulum = $("#tambahModal").find("#kurikulum_id").val();
            var nama = $("#tambahModal").find("#nama").val();

            if (!kurikulum) {
                showModalError('Silakan pilih Kurikulum terlebih dahulu!');
                return;
            }
            if (!nama || nama.trim() === '') {
                showModalError('Nama profesi tidak boleh kosong!');
                return;
            }

            $.ajax({
                type: "POST",
                url: `/${baseUrl}/store-profesi`,
                data: {
                    nama: nama,
                    kurikulum_id: kurikulum,
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    $('.btn-close').click();
                    readProfesi();
                    showNotif('success', 'Profesi berhasil ditambahkan!');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var msg = '';
                        for (var key in errors) {
                            msg += errors[key][0] + '<br>';
                        }
                        showModalError(msg);
                    } else {
                        showModalError('Gagal menyimpan data. Silakan coba lagi.');
                    }
                }
            });
        }
        function showProfesi(id) {
            if ("{{ $userOtoritas }}" === 'Dosen') return;

            const baseUrl = getBaseUrl();
            $.get(`/${baseUrl}/showProfesi/${id}`, function(data) {
                $("#exampleModalLabel").html("Edit Profesi").css("font-weight", "bold");
                $("#modal-notif").html('');
                $('#page').html(data);
                $('#tambahModal').modal('show');
            }).fail(function() {
                showNotif('danger', 'Gagal memuat data profesi. Silakan coba lagi.');
            });
        }

        function updateProfesi(id) {
            if ("{{ $userOtoritas }}" === 'Dosen') return;

            const baseUrl = getBaseUrl();
            var nama = $("#tambahModal").find("#nama").val();
            var kurikulum = $("#tambahModal").find("#kurikulum_id").val();

            if (!kurikulum) {
                showModalError('Silakan pilih Kurikulum terlebih dahulu!');
                return;
            }
            if (!nama || nama.trim() === '') {
                showModalError('Nama profesi tidak boleh kosong!');
                return;
            }

            $.ajax({
                type: "PUT",
                url: `/${baseUrl}/updateProfesi/${id}`,
                data: {
                    nama: nama,
                    kurikulum_id: kurikulum,
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    $(".btn-close").click();
                    readProfesi();
                    showNotif('success', 'Profesi berhasil diperbarui!');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var msg = '';
                        for (var key in errors) {
                            msg += errors[key][0] + '<br>';
                        }
                        showModalError(msg);
                    } else {
                        showModalError('Gagal memperbarui data. Silakan coba lagi.');
                    }
                }
            });
        }

        function deleteProfesi(id) {
            if ("{{ $userOtoritas }}" === 'Dosen') return;
            if (!confirm("Apakah Anda yakin ingin menghapus data ini?")) return;

            const baseUrl = getBaseUrl();
            $.ajax({
                type: "DELETE",
                url: `${window.location.origin}/${baseUrl}/deleteProfesi/${id}`,
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function() {
                    readProfesi();
                    showNotif('success', 'Profesi berhasil dihapus!');
                },
                error: function() {
                    showNotif('danger', 'Gagal menghapus profesi. Silakan coba lagi.');
                }
            });
        }
    </script>
    <script src="{{ asset('assets/js/dynamic-filters.js') }}"></script>
@endsection
