    <div class="p2">
        <div class="form-group">
            <label for="idProfil">Profile Name :</label>
            <select name="idProfil" id="idProfil" class="form-control">
                <option value="" selected disabled>Select Profile Name</option>
                @foreach ($profil as $item)
                    @if (auth()->user()->prodi->is_aptikom)
                        <option value="{{ $item->id }}">{{ $item->deskripsi }}</option>
                    @else
                        <option value="{{ $item->id }}">{{ $item->namaProfil }}</option>
                    @endif
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
                <option value="" selected disabled>Select CPL</option>
                @foreach ($cplData as $item)
                    <option value="{{ $item->id }}">{{ $item->judul }}</option>
                @endforeach
            </select>
            @error('idCpl')
                <div class="alert alert-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-group">
            <label for="bobot">Profile Weight :</label>
            <input type="number" value="{{ old('bobot') }}" name="bobot" id="bobot" class="form-control"
                min="0" step=".01" placeholder="Profile Weight">
            @error('bobot')
                <div class="alert alert-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success mt-2" onclick="store()">Tambah Profil Kompetensi</button>
        </div>
    </div>
