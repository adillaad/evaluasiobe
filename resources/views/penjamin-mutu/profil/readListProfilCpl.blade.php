<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Kode PL</th>
                <th scope="col">CPL Code</th>
                <th scope="col">Titles of CPL</th>
                <th scope="col">Profile Weights</th>
                @if (in_array($userOtoritas, [
                        // 'Penjamin Mutu Universitas',
                        // 'Penjamin Mutu Fakultas',
                        'Penjamin Mutu Program Studi',
                        'Kepala Program Studi',
                    ]))
                    <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if ($profilCpls->isEmpty())
                <tr>
                    <td colspan="{{ in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']) ? 6 : 5 }}"
                        style="text-align: center;">Tidak ada data</td>
                </tr>
            @else
                @foreach ($profilCpls as $profilCpl)
                    <tr>
                        <td scope="row">{{ $loop->iteration }}</td>
                        <td>{{ ucfirst($profilCpl->profilLulusan->kode) }}</td>
                        <td>{{ $profilCpl->cpl->kode }}</td>
                        <td>{{ $profilCpl->cpl->judul }}</td>
                        <td>{{ $profilCpl->bobot }}</td>
                        @if (in_array($userOtoritas, [
                                // 'Penjamin Mutu Universitas',
                                // 'Penjamin Mutu Fakultas',
                                'Penjamin Mutu Program Studi',
                                'Kepala Program Studi',
                            ]))
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                    data-id="{{ $profilCpl->id }}">Edit</button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                    data-id="{{ $profilCpl->id }}">Delete</button>

                            </td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
