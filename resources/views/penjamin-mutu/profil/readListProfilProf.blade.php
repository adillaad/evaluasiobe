<style>
    .table-responsive table td.wrap-content {
        white-space: normal;
    }
</style>

<div class="table-responsive">
    <table class="table table-bordered table-hover">

        <thead>
            <tr>

                <th>No</th>
                <th>Kode PL</th>
                <th>Profil Lulusan</th>
                <th>Profesi</th>

                @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
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

                    {{-- PROFESI --}}
                    @if ($key === 0)
                        <td rowspan="{{ count($listProfil) }}" style="vertical-align: top">

                            <ul>
                                @foreach ($listProfesi as $profesi)
                                    <li>{{ $profesi->nama }}</li>
                                @endforeach
                            </ul>

                        </td>
                    @endif

                    @if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                        <td>

                            <button class="btn btn-sm btn-warning" onclick="showProfil({{ $profil->id }})">
                                <i class="mdi mdi-pencil me-1"></i>Edit
                            </button>

                            <button class="btn btn-sm btn-danger" onclick="deleteProfil({{ $profil->id }})">
                                <i class="mdi mdi-delete me-1"></i>Delete
                            </button>

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
