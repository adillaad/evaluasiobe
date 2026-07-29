<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutu extends Model
{
    use HasFactory;

    protected $table = 'mutus';
    public $timestamps = false;

    protected $fillable = [
        'id_mahasiswa',
        'universitas_id',
        'tahun',
        'npm',
        'NPM',
        'angkatan',
        'nama_mhs',
        'Nama_mhs',
        'id_prodi',
        'Course',
        'namaCourse',
        'Jenis',
        'examWeight',
        'soal',
        'idSoal',
        'BobotSoal',
        'Cpl',
        'Cpmk',
        'Nilai',
        'nilaiSoal',
    ];

    protected $guarded = ['id'];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    public function cpl()
    {
        return $this->belongsTo(CPL::class, 'Cpl', 'id');
    }

    public function cpmk()
    {
        return $this->belongsTo(CPMK::class, 'Cpmk', 'id');
    }

    protected function resolvedMahasiswa(): ?Mahasiswa
    {
        if ($this->relationLoaded('mahasiswa')) {
            $mahasiswa = $this->getRelation('mahasiswa');
            if ($mahasiswa) {
                return $mahasiswa;
            }
        }

        $npm = $this->attributes['npm'] ?? $this->attributes['NPM'] ?? null;
        if ($npm === null || $npm === '' || (string) $npm === '0') {
            return null;
        }

        return Mahasiswa::where('NPM', $npm)->first();
    }

    public function getNamaMhsAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        if (!empty($this->attributes['Nama_mhs'] ?? null)) {
            return $this->attributes['Nama_mhs'];
        }

        return $this->resolvedMahasiswa()?->Nama;
    }

    public function getNpmAttribute($value)
    {
        $rawNpm = $this->attributes['npm'] ?? null;
        if ($rawNpm !== null && $rawNpm !== '' && (string) $rawNpm !== '0') {
            return $rawNpm;
        }

        $rawNPM = $this->attributes['NPM'] ?? null;
        if ($rawNPM !== null && $rawNPM !== '' && (string) $rawNPM !== '0') {
            return $rawNPM;
        }

        return $this->resolvedMahasiswa()?->NPM ?? $value;
    }
}
