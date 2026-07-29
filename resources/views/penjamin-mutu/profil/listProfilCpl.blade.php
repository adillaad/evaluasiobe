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

    <h3 class="px-4 pb-4 fw-bold text-center">Halaman List Profil Kompetensi</h3>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                @if (in_array($userOtoritas, [
                        'Penjamin Mutu Universitas',
                        'Penjamin Mutu Fakultas',
                        'Penjamin Mutu Program Studi',
                        'Kepala Program Studi',
                    ]))
                    <button type="submit" class="btn btn-success" onclick="create()">Add Competency Profile</button>
                @endif
                <div id="read" class="mt-3"></div>
            </div>
        </div>
    </div>

    {{-- MODAL UNTUK TAMBAH DATA --}}

    <!-- Modal -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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

        $(document).ready(function() {
            read();
        });

        // READ
        function read() {
            $.get(`/${getBaseUrl()}/readListProfilCpl`, {}, function(data, status) {
                $('#read').html(data);
            });
        }

        // MODAL HALAMAN CREATE
        function create() {
            if ("{{ $userOtoritas }}" === 'Dosen') return;
            $.get(`/${getBaseUrl()}/createListProfilCpl`, {}, function(data, status) {
                $("#exampleModalLabel").html("Add Competency Profile").css("font-weight", "bold");
                $('#page').html(data);
                $('#tambahModal').modal('show');
            });
        }

        // PROSES SIMPAN
        function store() {
            if ("{{ $userOtoritas }}" === 'Dosen') return;

            const idProfil = $("#idProfil").val();
            const idCpl = $("#idCpl").val();
            const bobot = $("#bobot").val();

            $.ajax({
                type: "GET",
                url: `/${getBaseUrl()}/storeListProfilCpl`,
                data: {
                    idProfil,
                    idCpl,
                    bobot
                },
                success: function() {
                    $('.btn-close').click();
                    read();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const validationErrors = xhr.responseJSON.validation_errors;
                        let errorMessage = "Gagal menyimpan data:\n";
                        for (let key in validationErrors) {
                            errorMessage += "- " + validationErrors[key][0] + "\n";
                        }
                        alert(errorMessage);
                    } else {
                        alert("Gagal menyimpan data");
                    }
                }
            });
        }

        function showProfilCpl(id) {
            if ("{{ $userOtoritas }}" === 'Dosen') return;

            $.get(`/${getBaseUrl()}/showProfilCpl/${id}`, {}, function(data, status) {
                    $("#exampleModalLabel").html("Edit Competency Profile").css("font-weight", "bold");
                    $('#page').html(data);
                    $('#tambahModal').modal('show');
                })
                .fail(function(xhr, status, error) {
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    alert("Gagal memuat data edit. Status: " + xhr.status + ". Cek console (F12) untuk detail.");
                });
        }

        // UPDATE
        function updateProfilCpl() {
            const id = $('#edit_id').val();
            const idProfil = $("#idProfil").val();
            const idCpl = $("#idCpl").val();
            const bobot = $("#bobot").val();

            $.ajax({
                type: "GET",
                url: `/${getBaseUrl()}/updateProfilCpl/${id}`,
                data: {
                    idProfil,
                    idCpl,
                    bobot
                },
                success: function() {
                    $('.btn-close').click();
                    read();
                },
                error: function() {
                    alert("Gagal memperbarui data");
                }
            });
        }


        // DELETE
        function deleteProfilCpl(id) {
            if ("{{ $userOtoritas }}" === 'Dosen') return;

            if (!confirm("Apakah yakin ingin hapus data?")) return;

            $.ajax({
                type: "GET",
                url: `/${getBaseUrl()}/deleteProfilCpl/${id}`,
                success: function() {
                    $('.btn-close').click();
                    read();
                },
                error: function() {
                    alert("Gagal menghapus data");
                }
            });
        }

        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            showProfilCpl(id);
        });

        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            deleteProfilCpl(id);
        });
    </script>
@endsection
