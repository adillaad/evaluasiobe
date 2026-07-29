<div class="p2">
    <div class="form-group">
        <label for="id_kurikulum">Kurikulum :</label>
        <select name="id_kurikulum" id="id_kurikulum" class="form-control">
            <option value="">-- Pilih Kurikulum --</option>
            @foreach ($kurikulums as $kurikulum)
                <option value="{{ $kurikulum->id }}" {{ old('id_kurikulum') == $kurikulum->id ? 'selected' : '' }}>
                    {{ $kurikulum->tahun }}</option>
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
        <input type="text" value="{{ old('jenis') }}" name="jenis" id="jenis" class="form-control"
            placeholder="Jenis Profil Lulusan">
        @error('jenis')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </div>
    <div class="form-group">
        <label for="deskripsi">Profil Lulusan :</label>
        <textarea name="deskripsi" id="deskripsi" class="form-control" style="height: 100px" placeholder="Profil Lulusan">{{ old('deskripsi') }}</textarea>
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
            <option value="wajib" {{ old('status') == 'Wajib' ? 'selected' : '' }}>Wajib</option>
            <option value="pilihan" {{ old('status') == 'Pilihan' ? 'selected' : '' }}>Pilihan</option>
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
            placeholder="Acuan">{{ old('acuan') }}</input>
        @error('acuan')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-success mt-2" onclick="storeAptikom()">Tambah Profil Lulusan</button>
    </div>
</div>
