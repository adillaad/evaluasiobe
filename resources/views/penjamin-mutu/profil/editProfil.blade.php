{{-- 
    Partial: edit-profil.blade.php
    Diload via AJAX ke dalam #page di modal.
    Notif error → #notif-modal (sudah ada di luar, di modal body atas)
    Sukses → ditangani di list-profil-lulusan.blade.php (showNotif ke #notif-wrapper)
--}}
<div class="p-2">
    <div class="form-group mb-3">
        <label for="namaProfil" class="form-label fw-semibold">Profile Career :</label>
        <input type="text" value="{{ $profil->namaProfil }}" name="namaProfil" id="namaProfil" class="form-control"
            placeholder="Profile / Profession Name">
    </div>

    <div class="form-group mb-3">
        <label for="deskripsi" class="form-label fw-semibold">Graduate Profile :</label>
        <textarea name="deskripsi" id="deskripsi" class="form-control" style="height: 100px" placeholder="Profile Description">{{ $profil->deskripsi }}</textarea>
    </div>

    <div class="form-group">
        <button type="button" class="btn btn-warning mt-1" onclick="updateProfil({{ $profil->id }})">
            Update
        </button>
    </div>
</div>
