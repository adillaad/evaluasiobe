<style>
    .table-responsive table td.wrap-content {
        white-space: normal;
        word-wrap: break-word;
        max-width: 300px;
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
                                    <button type="button" class="btn btn-warning btn-icons"
                                        onclick="showProfil({{ $profil->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="ti-pencil"></i>
                                    </button>

                                    <button type="button" class="btn btn-danger btn-icons"
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
