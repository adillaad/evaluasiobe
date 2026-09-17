{{-- 
    Partial: read-profil.blade.php
    Diload via AJAX ke dalam #read.
    Tombol Edit → showProfil(id) → buka modal edit
    Tombol Delete → deleteProfil(id) → notif di #notif-wrapper
--}}
<style>
    .table-responsive table td.wrap-content {
        white-space: normal;
        word-wrap: break-word;
    }
    .table-responsive table td.profil-karir-col {
        white-space: normal;
        word-wrap: break-word;
        max-width: 260px;
    }
</style>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="28%">Profil Karir</th>
                <th>Graduate Profile</th>
                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                    <th width="12%" class="text-center">Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if ($listProfil->isEmpty())
                <tr>
                    <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']) ? 4 : 3 }}"
                        class="text-center text-muted py-4">
                        Tidak ada data
                    </td>
                </tr>
            @else
                @foreach ($listProfil as $key => $profil)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="profil-karir-col">
                            <span class="fw-bold text-dark">{{ $profil->namaProfil ?? '-' }}</span>
                        </td>
                        <td class="wrap-content">{{ ucfirst($profil->deskripsi) }}</td>

                        @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <button type="button" class="btn btn-warning btn-icons"
                                        onclick="showProfil({{ $profil->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="ti-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-icons" onclick="deleteProfil({{ $profil->id }})"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
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
