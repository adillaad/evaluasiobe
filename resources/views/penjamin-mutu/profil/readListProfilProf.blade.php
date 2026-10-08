<style>
    .table-responsive table td.wrap-content {
        white-space: normal;
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
    <table class="table table-bordered table-hover">

        <thead>
            <tr>

                <th>No</th>
                <th>Kode PL</th>
                <th>Profil Lulusan</th>
                <th class="text-center">Kurikulum</th>
                <th>Profesi</th>

                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']))
                    <th>Action</th>
                @endif

            </tr>
        </thead>

        <tbody>

            @forelse ($listProfil as $key => $profil)

                <tr>

                    <td>{{ $key + 1 }}</td>

                    <td>
                        {{ ucfirst($profil->kode) }}
                    </td>

                    <td class="wrap-content">
                        {{ ucfirst($profil->deskripsi) }}
                    </td>

                    <td class="text-center align-middle">
                        <span class="badge bg-light text-dark border px-2 py-1 fw-semibold">
                            {{ $profil->kurikulum->tahun ?? '-' }}
                        </span>
                    </td>

                    {{-- PROFESI --}}
                    <td style="vertical-align: top;">
                        @php
                            $profesisForProfil = $listProfesi->where('kurikulum_id', $profil->kurikulum_id);
                        @endphp
                        @if ($profesisForProfil->isNotEmpty())
                            <ul class="ps-3 mb-0">
                                @foreach ($profesisForProfil as $profesi)
                                    <li>{{ $profesi->nama }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted fst-italic">- Belum ada profesi -</span>
                        @endif
                    </td>

                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']))
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <button class="btn btn-outline-primary btn-icons" onclick="showProfil({{ $profil->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                    <i class="ti-pencil"></i>
                                </button>

                                <button class="btn btn-outline-danger btn-icons" onclick="deleteProfil({{ $profil->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                    <i class="ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    @endif

                </tr>

            @empty

                <tr>
                    <td colspan="5" class="text-center">
                        Tidak ada data
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>
</div>
