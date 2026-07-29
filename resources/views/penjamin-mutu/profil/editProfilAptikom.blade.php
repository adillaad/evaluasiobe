<div class="p2">
    <div class="form-group">
        <label for="id_kurikulum">Kurikulum :</label>
        <select name="id_kurikulum" id="id_kurikulum" class="form-control">
            <option value="">-- Pilih Kurikulum --</option>
            @foreach ($kurikulums as $kurikulum)
                <option value="{{ $kurikulum->id }}"
                    {{ old('id_kurikulum', $profil->id_kurikulum) == $kurikulum->id ? 'selected' : '' }}>
                    {{ $kurikulum->tahun }}
                </option>
            @endforeach
        </select>
        @error('id_kurikulum')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-group">
        <label for="jenis">Jenis :</label>
        <input type="text" name="jenis" id="jenis" class="form-control"
            value="{{ old('jenis', $profil->jenis) }}"
            placeholder="Jenis Profil Lulusan">
        @error('jenis')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-group">
        <label for="deskripsi">Profil Lulusan :</label>
        <textarea name="deskripsi" id="deskripsi" class="form-control" style="height: 100px"
            placeholder="Profil Lulusan">{{ old('deskripsi', $profil->deskripsi) }}</textarea>
        @error('deskripsi')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-group">
        <label for="status">Status :</label>
        <select name="status" id="status" class="form-control">
            <option value="">-- Pilih Status --</option>
            <option value="wajib" {{ old('status', $profil->status) == 'wajib' ? 'selected' : '' }}>Wajib</option>
            <option value="pilihan" {{ old('status', $profil->status) == 'pilihan' ? 'selected' : '' }}>Pilihan</option>
        </select>
        @error('status')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-group">
        <label for="acuan">Acuan :</label>
        <input type="text" name="acuan" id="acuan" class="form-control"
            value="{{ old('acuan', $profil->acuan) }}"
            placeholder="Acuan">
        @error('acuan')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary mt-2" onclick="updateProfilAptikom({{ $profil->id }})">
            Update Profil Lulusan
        </button>
    </div>
</div>