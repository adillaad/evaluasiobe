<div class="form-group mb-3">
    <label for="kurikulum_id" class="form-label">
        Kurikulum <span class="text-danger">*</span>
    </label>
    <select id="kurikulum_id" name="kurikulum_id" class="form-select">
        <option value="">-- Pilih Kurikulum --</option>
        @foreach ($kurikulums as $kurikulum)
            <option value="{{ $kurikulum->id }}"
                {{ isset($profesi) && $profesi->kurikulum_id == $kurikulum->id ? 'selected' : '' }}>
                {{ $kurikulum->tahun }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="nama" class="form-label">
        Nama Profesi <span class="text-danger">*</span>
    </label>
    <input type="text" class="form-control" id="nama" name="nama" value="{{ $profesi->nama ?? '' }}"
        placeholder="Masukkan nama profesi">
</div>

<div class="d-flex justify-content-end gap-2">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

    @if (isset($profesi))
        <button type="button" class="btn btn-warning" onclick="updateProfesi({{ $profesi->id }})">
            <i class="mdi mdi-content-save-edit me-1"></i> Update
        </button>
    @else
        <button type="button" class="btn btn-primary" onclick="storeProfesi()">
            <i class="mdi mdi-content-save me-1"></i> Simpan
        </button>
    @endif
</div>
