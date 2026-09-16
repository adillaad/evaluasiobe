<div class="table-responsive mt-2">
    <table class="table table-bordered table-hover align-middle table-pemetaan">
        <thead>
            <tr>
                <th style="width: 20%; text-align: center;">Mata Kuliah</th>
                <th style="width: 45%; text-align: center;">CPMK</th>
                <th style="width: 10%; text-align: center;">CPL</th>
                <th style="width: 10%; text-align: center;">Bobot</th>
                @if (in_array($userOtoritas ?? auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                    <th style="width: 10%; text-align: center;">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($mksList as $mkIndex => $mk)
                @php
                    $cpmkCount = $mk->cpmks->count();
                    $rowspan = $cpmkCount > 0 ? $cpmkCount : 1;
                    $rowClass = $mkIndex % 2 === 0 ? 'tr-mk-even' : 'tr-mk-odd';
                @endphp
                @if ($cpmkCount > 0)
                    @foreach ($mk->cpmks as $index => $cpmk)
                        <tr class="{{ $rowClass }}">
                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}" class="align-middle fw-bold">
                                    <span class="fw-bold text-primary d-block">{{ $mk->kode }}</span>
                                    <span class="fw-semibold text-dark">{{ $mk->nama }}</span>
                                </td>
                            @endif

                            <td class="align-middle">
                                <span class="badge-cpmk mb-1 d-inline-block">{{ $cpmk->kode }}</span>
                                <div class="small text-dark">{{ $cpmk->judul }}</div>
                            </td>

                            <td class="align-middle">
                                @if($cpmk->cpl)
                                    <span class="fw-bold text-primary" style="font-size: 13px;">{{ $cpmk->cpl->kode }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td class="text-center align-middle fw-semibold fs-6">
                                @php
                                    $bVal = (float)($cpmk->pivot->bobot ?? 0);
                                @endphp
                                {{ (float)$bVal }}%
                            </td>

                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                    <button type="button" class="btn btn-outline-primary btn-sm edit-mk-cpmk-btn p-1 px-3" 
                                        data-mk-kode="{{ $mk->kode }}" 
                                        data-mk-nama="{{ $mk->nama }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editMkCpmkModal"
                                        title="Edit Pemetaan MK">
                                        <i class="mdi mdi-square-edit-outline fs-6 me-1"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr class="{{ $rowClass }}">
                        <td class="align-middle fw-bold">
                            <span class="fw-bold text-primary d-block">{{ $mk->kode }}</span>
                            <span class="fw-semibold text-dark">{{ $mk->nama }}</span>
                        </td>
                        <td colspan="3" class="text-muted small fst-italic align-middle">- Belum ada CPMK terpetakan -</td>
                        @if (in_array($userOtoritas ?? auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']))
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-outline-primary btn-sm edit-mk-cpmk-btn p-1 px-2" 
                                    data-mk-kode="{{ $mk->kode }}" 
                                    data-mk-nama="{{ $mk->nama }}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editMkCpmkModal"
                                    title="Tambah Pemetaan">
                                    <i class="mdi mdi-square-edit-outline fs-6"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="{{ in_array($userOtoritas ?? auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']) ? 5 : 4 }}" class="text-center text-muted py-4">
                        Tidak ada data Mata Kuliah.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
