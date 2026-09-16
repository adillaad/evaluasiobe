@if ($profesi->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Profesi</th>
                    <th>Kurikulum</th>
                    @if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                        <th style="width: 160px;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($profesi as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->kurikulum->tahun ?? 'N/A' }}</td>
                        @if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <button class="btn btn-warning btn-icons" onclick="showProfesi({{ $item->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="ti-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-icons" onclick="deleteProfesi({{ $item->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                        <i class="ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-center text-muted mt-3">Tidak ada data profesi ditemukan.</p>
@endif
