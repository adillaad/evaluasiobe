<style>
    .table-responsive table td.wrap-content {
        white-space: normal;
        word-wrap: break-word;
        max-width: 300px;
    }
    .btn-outline-primary {
        color: #0284c7 !important;
        border-color: #0284c7 !important;
        background-color: #ffffff !important;
    }
    .btn-outline-primary i {
        color: #0284c7 !important;
        transition: color 0.2s ease;
    }
    .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
        background-color: #0284c7 !important;
        border-color: #0284c7 !important;
        color: #ffffff !important;
    }
    .btn-outline-primary:hover i, .btn-outline-primary:focus i, .btn-outline-primary:active i {
        color: #ffffff !important;
    }

    .btn-outline-danger {
        color: #dc3545 !important;
        border-color: #dc3545 !important;
        background-color: #ffffff !important;
    }
    .btn-outline-danger i {
        color: #dc3545 !important;
        transition: color 0.2s ease;
    }
    .btn-outline-danger:hover, .btn-outline-danger:focus, .btn-outline-danger:active {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
    }
    .btn-outline-danger:hover i, .btn-outline-danger:focus i, .btn-outline-danger:active i {
        color: #ffffff !important;
    }
</style>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th width="5%">No</th>
                <th width="10%">Jenis</th>
                <th width="10%">Kode PL</th>
                <th>Profil Lulusan</th>
                <th width="10%">Status</th>
                <th width="15%">Acuan</th>
                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                    <th width="15%" class="text-center">Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if ($listProfil->isEmpty())
                <tr>
                    <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']) ? 7 : 6 }}"
                        class="text-center text-muted py-4">
                        Tidak ada data
                    </td>
                </tr>
            @else
                @foreach ($listProfil as $key => $profil)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ ucfirst($profil->jenis) }}</td>
                        <td><code>{{ ucfirst($profil->kode) }}</code></td>
                        <td class="wrap-content">{{ ucfirst($profil->deskripsi) }}</td>
                        <td>
                            <span class="badge bg-{{ $profil->status == 'aktif' ? 'success' : 'secondary' }}">
                                {{ ucfirst($profil->status) }}
                            </span>
                        </td>
                        <td>{{ ucfirst($profil->acuan) }}</td>

                        @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <button type="button" class="btn btn-outline-primary btn-icons"
                                        onclick="showProfil({{ $profil->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="ti-pencil"></i>
                                    </button>

                                    <button type="button" class="btn btn-outline-danger btn-icons"
                                        onclick="deleteProfil({{ $profil->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                        <i class="ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
