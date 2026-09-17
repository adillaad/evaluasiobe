<div class="table-responsive">
    <table class="table table-bordered table-hover dataTable align-middle">
        <thead>
            <tr>
                <th scope="col" class="text-center" style="width: 50px;">No</th>
                <th scope="col">Kode PL</th>
                <th scope="col">CPL Code</th>
                <th scope="col">Titles of CPL</th>
                <th scope="col">Profile Weights</th>
                @if (in_array($userOtoritas, [
                        'Penjamin Mutu Program Studi',
                        'Kepala Program Studi',
                    ]))
                    <th class="text-center" style="width: 100px;">Action</th>
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
                @foreach ($profilCpls as $key => $profilCpl)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ ucfirst($profilCpl->profilLulusan->kode ?? '-') }}</td>
                        <td>{{ $profilCpl->cpl->kode ?? '-' }}</td>
                        <td>{{ $profilCpl->cpl->judul ?? '-' }}</td>
                        <td>{{ (float)$profilCpl->bobot == (int)$profilCpl->bobot ? (int)$profilCpl->bobot : $profilCpl->bobot }}%</td>
                        @if (in_array($userOtoritas, [
                                'Penjamin Mutu Program Studi',
                                'Kepala Program Studi',
                            ]))
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <button type="button" class="btn btn-warning btn-icons btn-edit"
                                        data-id="{{ $profilCpl->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="ti-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-icons btn-delete"
                                        data-id="{{ $profilCpl->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
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
