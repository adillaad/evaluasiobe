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
        max-width: 400px;
    }
</style>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th width="5%" class="text-center">No</th>
                <th>Graduate Profile</th>
                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                    <th width="20%" class="text-center">Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if ($listProfil->isEmpty())
                <tr>
                    <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']) ? 3 : 2 }}"
                        class="text-center text-muted py-4">
                        Tidak ada data
                    </td>
                </tr>
            @else
                @foreach ($listProfil as $key => $profil)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="wrap-content">{{ ucfirst($profil->deskripsi) }}</td>

                        @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm"
                                    onclick="showProfil({{ $profil->id }})" title="Edit data">
                                    <i class="mdi mdi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteProfil({{ $profil->id }})"
                                    title="Hapus data">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
