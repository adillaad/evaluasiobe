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
                <th>Jenis</th>
                <th>Kode PL</th>
                <th>Profil Lulusan</th>
                <th>Status</th>
                <th>Profesi</th>
                <th>Acuan</th>
                @if (in_array($userOtoritas, [
                        
                        'Penjamin Mutu Program Studi',
                        'Kepala Program Studi',
                    ]))
                    <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if ($listProfil->isEmpty())
                <tr>
                    <td colspan="{{ in_array($userOtoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas', 'Penjamin Mutu Program Studi']) ? 4 : 3 }}"
                        style="text-align: center;">Tidak ada data</td>
                </tr>
            @else
                @foreach ($listProfil as $key => $profil)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ ucfirst($profil->jenis) }}</td>
                        <td>{{ ucfirst($profil->kode) }}</td>
                        <td class="wrap-content">{{ ucfirst($profil->deskripsi) }}</td>
                        <td>{{ ucfirst($profil->status) }}</td>
                        @if ($key === 0)
                            <td rowspan="{{ count($listProfil) }}" style="vertical-align: top">
                                <ul>
                                    @foreach ($listProfesi as $profesi)
                                        <li>{{ $profesi->nama }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        @endif
                        <td>{{ ucfirst($profil->acuan) }}</td>
                        @if (in_array($userOtoritas, [
                        
                                'Penjamin Mutu Program Studi',
                                'Kepala Program Studi',
                            ]))
                            <td>
                                <button type="submit" class="btn btn-sm btn-warning"
                                    onclick="showProfil({{ $profil->id }})"><i
                                        class="mdi mdi-pencil me-1"></i>Edit</button>
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="deleteProfil({{ $profil->id }})"><i
                                        class="mdi mdi-delete me-1"></i>Delete</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
