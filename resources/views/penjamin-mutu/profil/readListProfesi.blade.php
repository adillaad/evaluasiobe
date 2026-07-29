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
                                <button class="btn btn-sm btn-warning" onclick="showProfesi({{ $item->id }})">
                                    <i class="mdi mdi-pencil me-1"></i>Edit
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteProfesi({{ $item->id }})">
                                    <i class="mdi mdi-delete me-1"></i>Hapus
                                </button>
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
