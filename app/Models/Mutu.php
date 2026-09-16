<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        'sub_cpmk_id',
        'sumber',
        'tahun_ajaran_id',
        'konversi_metode_id',
        'Nilai',
        'nilaiSoal',
    ];

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::saved(function ($mutu) {
            static::autoSyncVisualisasi($mutu);
        });

        static::deleted(function ($mutu) {
            static::autoSyncVisualisasi($mutu);
        });
    }

    public static function autoSyncVisualisasi($mutu)
    {
        try {
            $npm = $mutu->npm ?? $mutu->NPM ?? null;
            $prodiId = $mutu->id_prodi ?? null;
            $angkatan = $mutu->angkatan ?? null;
            $course = $mutu->Course ?? null;

            $syncService = app(\App\Services\EvaluasiSyncService::class);

            if (!empty($npm)) {
                $syncService->syncMahasiswa($npm);
            }
            if (!empty($prodiId) && !empty($angkatan)) {
                $syncService->syncAngkatan($prodiId, $angkatan);
            }
            if (!empty($course) && !empty($angkatan) && !empty($prodiId)) {
                $syncService->syncMataKuliah($course, $angkatan, $prodiId);
            }
            if (!empty($prodiId)) {
                $syncService->syncProdi($prodiId);
                $prodiObj = Prodi::find($prodiId);
                if ($prodiObj && !empty($prodiObj->id_fakultas)) {
                    $syncService->syncFakultas($prodiObj->id_fakultas);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Auto sync visualisasi error: ' . $e->getMessage());
        }
    }

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

    public function subCpmk()
    {
        return $this->belongsTo(SubCpmk::class, 'sub_cpmk_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function konversiMetode()
    {
        return $this->belongsTo(KonversiMetode::class, 'konversi_metode_id');
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
