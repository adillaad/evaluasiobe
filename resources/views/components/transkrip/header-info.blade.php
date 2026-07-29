<div class="header">
    <h2>Transkrip Akademik Berbasis Kompetensi</h2>
    <p class="text-muted">Capaian Pembelajaran Lulusan dan Mata Kuliah</p>
    <div class="info-grid">
        <div class="info-item">
            <span class="label">Nama Mahasiswa</span>
            <span class="value">{{ $mahasiswaData->nama_mhs }}</span>
        </div>
        <div class="info-item">
            <span class="label">NPM</span>
            <span class="value">{{ $mahasiswaData->npm }}</span>
        </div>
        <div class="info-item">
            <span class="label">Program Studi</span>
            <span class="value">{{ $prodi->nama }}</span>
        </div>
        <div class="info-item">
            <span class="label">Angkatan</span>
            <span class="value">{{ $mahasiswaData->angkatan }}</span>
        </div>
    </div>
</div>
