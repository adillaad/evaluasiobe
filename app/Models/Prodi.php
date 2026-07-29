<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;
    protected $table = 'prodi';

    protected $fillable = ['nama', 'id_fakultas','is_aptikom'];

    protected $casts = [
        'is_aptikom' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'prodi_user', 'prodi_id', 'user_id')
                    ->withPivot('id', 'active') // Mengambil kolom 'active' dari pivot
                    ->withTimestamps();
    }

    // public function users()
    // {
    //     return $this->hasMany(User::class, 'id_prodiUser');
    // }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas');
    }
    public function mks()
    {
        return $this->hasMany(MK::class, 'id_prodi', 'id');
    }
    
    public function convertGrade(?float $numeric): array
    {
        if ($numeric === null) return [null, null];

        $jenjang = strtoupper($this->jenjang ?? '');

        return match (true) {
            $jenjang === 'S3' => $this->gradeS3($numeric),
            $jenjang === 'S2' => $this->gradeS2($numeric),
            default           => $this->gradeS1D3($numeric),   // S1, D3, Profesi
        };
    }
    public function getMinNilaiLulus(): float
    {
        return match (strtoupper($this->jenjang ?? '')) {
            'S3'    => 75.0,
            'S2'    => 65.0,
            default => 50.0,
        };
    }
    public function isLulus(float $nilai): bool
    {
        return $nilai >= $this->getMinNilaiLulus();
    }
    public function getStatusKelulusan(float $nilai): string
    {
        return $this->isLulus($nilai) ? 'Lulus' : 'Tidak Lulus';
    }
    public function nilaiToMutu(float $nilai): float
    {
        return $this->convertGrade($nilai)[1] ?? 0.0;
    }

    public static function gradeColorClass(?string $grade): string
    {
        if (!$grade) return '';
        return match (strtoupper(trim($grade))) {
            'A', 'B+', 'B', 'C+', 'C' => 'text-success',
            'D'                         => 'text-warning',
            'E'                         => 'text-danger',
            default                     => '',
        };
    }
    public static function progressClass(float $score): string
    {
        if ($score >= 85) return 'sb';
        if ($score >= 70) return 'b';
        if ($score >= 60) return 'c';
        return 'k';
    }
    public static function badgeClassKelulusan(string $status): string
    {
        return match ($status) {
            'Lulus'       => 'lulus',
            'Tidak Lulus' => 'tidak-lulus',
            default       => '',
        };
    }

    public static function badgeClassKompetensi(string $status): string
    {
        return match ($status) {
            'Sangat Baik' => 'sangat-baik',
            'Baik'        => 'baik',
            'Cukup'       => 'cukup',
            'Kurang'      => 'kurang',
            default       => 'cukup',
        };
    }

    // PRIVATE — tabel konversi per jenjang
    private function gradeS3(float $n): array
    {
        return match (true) {
            $n >= 85 => ['A',  4.0],
            $n >= 80 => ['B+', 4.0],
            $n >= 75 => ['B',  4.0],
            $n >= 70 => ['C+', 4.0],
            $n >= 65 => ['C',  4.0],
            $n >= 55 => ['D',  4.0],
            default  => ['E',  0.0],
        };
    }

    private function gradeS2(float $n): array
    {
        return match (true) {
            $n >= 81 => ['A',  4.0],
            $n >= 75 => ['B+', 4.0],
            $n >= 70 => ['B',  4.0],
            $n >= 65 => ['C+', 4.0],
            $n >= 55 => ['C',  4.0],
            $n >= 50 => ['D',  4.0],
            default  => ['E',  0.0],
        };
    }

    private function gradeS1D3(float $n): array
    {
        return match (true) {
            $n >= 76 => ['A',  4.00],
            $n >= 71 => ['B+', 3.50],
            $n >= 66 => ['B',  3.00],
            $n >= 61 => ['C+', 2.50],
            $n >= 56 => ['C',  2.00],
            $n >= 50 => ['D',  1.00],
            default  => ['E',  0.00],
        };
    }
}
