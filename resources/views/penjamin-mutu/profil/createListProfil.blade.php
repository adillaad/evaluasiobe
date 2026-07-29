{{-- 
    Partial: create-profil.blade.php
    Diload via AJAX ke dalam #page di modal.
    Notif error → #notif-modal (sudah ada di luar, di modal body atas)
    Sukses → ditangani di list-profil-lulusan.blade.php (showNotif ke #notif-wrapper)
--}}
<div class="p-2">
    <div class="form-group mb-3">
        <label for="namaProfil" class="form-label fw-semibold">Profile Career :</label>
        <input type="text" value="{{ old('namaProfil') }}" name="namaProfil" id="namaProfil" class="form-control"
            placeholder="Profile / Profession Name">
    </div>

    <div class="form-group mb-3">
        <label for="id_kurikulum" class="form-label fw-semibold">Kurikulum :</label>
        <select name="id_kurikulum" id="id_kurikulum" class="form-control">
            <option value="">-- Pilih Kurikulum --</option>
            @foreach ($kurikulums as $kurikulum)
                <option value="{{ $kurikulum->id }}" {{ old('id_kurikulum') == $kurikulum->id ? 'selected' : '' }}>
                    {{ $kurikulum->tahun }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group mb-3">
        <label for="deskripsi" class="form-label fw-semibold">Graduate Profile :</label>
        <textarea name="deskripsi" id="deskripsi" class="form-control" style="height: 100px" placeholder="Profile Description">{{ old('deskripsi') }}</textarea>
    </div>

    <div class="form-group">
        <button type="button" class="btn btn-success mt-1" onclick="store()">
            Tambah Profil Lulusan
        </button>
    </div>
</div>
