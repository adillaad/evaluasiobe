<div class="p2">
    <div class="form-group">
        <label for="idProfil">Profile Name :</label>
        <select name="idProfil" id="idProfil" class="form-control">
            <option value="" disabled>Select Profile Name</option>
            @foreach ($profil as $item)
                <option value="{{ $item->id }}" {{ $item->id == $profilCpl->idProfil ? 'selected' : '' }}>
                    {{ $item->namaProfil }}
                </option>
            @endforeach
        </select>
        @error('idProfil')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-group">
        <label for="idCpl">CPL :</label>
        <select name="idCpl" id="idCpl" class="form-control">
            <option value="" disabled>Pilih CPL</option>
            @foreach ($cplData as $item)
                <option value="{{ $item->id }}" {{ $item->id == $profilCpl->idCpl ? 'selected' : '' }}>
                    {{ $item->judul }}
                </option>
            @endforeach
        </select>
        @error('idCpl')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-group">
        <label for="bobot">Profile Weight (% / Opsional)</label>
        <input type="number" value="{{ (float)$profilCpl->bobot == (int)$profilCpl->bobot ? (int)$profilCpl->bobot : $profilCpl->bobot }}" name="bobot" id="bobot" class="form-control"
            min="0" max="100" step="any" placeholder="Bobot (0-100, misal: 25)">
        <small class="text-muted d-block mt-1">* Jika dikosongkan, bobot akan dihitung sama rata secara otomatis.</small>
        @error('bobot')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-group">
        <input type="hidden" id="edit_id" value="{{ $profilCpl->id }}">

        <button type="button" class="btn btn-success mt-2" onclick="updateProfilCpl()">Update Profil
            Kompetensi</button>

    </div>
</div>
