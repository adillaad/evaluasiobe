<form id="form-create-profesi">
    @csrf

    <div class="form-group">
        <label>Kurikulum</label>
        <select name="kurikulum_id" class="form-control">
            <option value="">-- Pilih Kurikulum --</option>
            @foreach ($kurikulums as $k)
                <option value="{{ $k->id }}">{{ $k->tahun }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Nama Profesi</label>
        <input type="text" name="nama" class="form-control">
    </div>

    <button onclick="storeProfesi()" type="button" class="btn btn-primary">
        Simpan
    </button>
</form>
