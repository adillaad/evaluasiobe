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

        $jenjang = strtoupper(trim($this->jenjang ?? ''));

        // c. Program Doktor / Doktor Terapan / Subspesialis
        if (in_array($jenjang, ['S3', 'S-3', 'DOKTOR', 'SUB SPESIALIS', 'SP-2', 'SP2', 'S3 TERAPAN']) || str_contains($jenjang, 'DOKTOR') || str_contains($jenjang, 'S3')) {
            return $this->gradeDoktor($numeric);
        }

        // b. Program Magister / Magister Terapan / Spesialis
        if (in_array($jenjang, ['S2', 'S-2', 'MAGISTER', 'SPESIALIS', 'SP-1', 'SP1', 'S2 TERAPAN']) || str_contains($jenjang, 'MAGISTER') || str_contains($jenjang, 'S2') || str_contains($jenjang, 'SPESIALIS')) {
            return $this->gradeMagister($numeric);
        }

        // a. Program Diploma / Sarjana / Sarjana Terapan / Profesi (Default untuk S1, D3, D4, dll.)
        return $this->gradeDiplomaSarjana($numeric);
    }

    public function getMinNilaiLulus(): float
    {
        $jenjang = strtoupper(trim($this->jenjang ?? ''));

        if (in_array($jenjang, ['S3', 'S-3', 'DOKTOR', 'SUB SPESIALIS', 'SP-2', 'SP2', 'S3 TERAPAN']) || str_contains($jenjang, 'DOKTOR') || str_contains($jenjang, 'S3')) {
            return 75.0; // Minimal B
        }

        if (in_array($jenjang, ['S2', 'S-2', 'MAGISTER', 'SPESIALIS', 'SP-1', 'SP1', 'S2 TERAPAN']) || str_contains($jenjang, 'MAGISTER') || str_contains($jenjang, 'S2') || str_contains($jenjang, 'SPESIALIS')) {
            return 65.0; // Minimal C+
        }

        return 50.0; // Minimal D
    }

    public function isLulus(float $nilai): bool
    {
        return $nilai >= $this->getMinNilaiLulus();
    }

    public function getStatusKelulusan(float $nilai): string
    {
        return $this->isLulus($nilai) ? 'Lulus' : 'Tidak Lulus';
    }

    public function getStatusKompetensi(float $nilai): string
    {
        if ($nilai >= 85) return 'Sangat Baik';
        if ($nilai >= 70) return 'Baik';
        if ($nilai >= 60) return 'Cukup';
        return 'Kurang';
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

    /**
     * a. Program Diploma / Sarjana / Sarjana Terapan / Profesi (Peraturan Unila 2025)
     * >= 76        : A  (4.00) - Lulus
     * >= 71 - < 76 : B+ (3.50) - Lulus
     * >= 66 - < 71 : B  (3.00) - Lulus
     * >= 61 - < 66 : C+ (2.50) - Lulus
     * >= 56 - < 61 : C  (2.00) - Lulus
     * >= 50 - < 56 : D  (1.00) - Lulus
     * < 50         : E  (0.00) - Tidak Lulus
     */
    private function gradeDiplomaSarjana(float $n): array
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

    /**
     * b. Program Magister / Magister Terapan / Spesialis (Peraturan Unila 2025)
     * >= 81        : A  (4.00) - Lulus
     * >= 75 - < 81 : B+ (3.50) - Lulus
     * >= 70 - < 75 : B  (3.00) - Lulus
     * >= 65 - < 70 : C+ (2.50) - Lulus (Batas Min. Lulus)
     * >= 55 - < 65 : C  (2.00) - Tidak Lulus
     * >= 50 - < 55 : D  (1.00) - Tidak Lulus
     * < 50         : E  (0.00) - Tidak Lulus
     */
    private function gradeMagister(float $n): array
    {
        return match (true) {
            $n >= 81 => ['A',  4.00],
            $n >= 75 => ['B+', 3.50],
            $n >= 70 => ['B',  3.00],
            $n >= 65 => ['C+', 2.50],
            $n >= 55 => ['C',  2.00],
            $n >= 50 => ['D',  1.00],
            default  => ['E',  0.00],
        };
    }

    /**
     * c. Program Doktor / Doktor Terapan / Subspesialis (Peraturan Unila 2025)
     * >= 85        : A  (4.00) - Lulus
     * >= 80 - < 85 : B+ (3.50) - Lulus
     * >= 75 - < 80 : B  (3.00) - Lulus (Batas Min. Lulus)
     * >= 70 - < 75 : C+ (2.50) - Tidak Lulus
     * >= 65 - < 70 : C  (2.00) - Tidak Lulus
     * >= 55 - < 65 : D  (1.00) - Tidak Lulus
     * < 55         : E  (0.00) - Tidak Lulus
     */
    private function gradeDoktor(float $n): array
    {
        return match (true) {
            $n >= 85 => ['A',  4.00],
            $n >= 80 => ['B+', 3.50],
            $n >= 75 => ['B',  3.00],
            $n >= 70 => ['C+', 2.50],
            $n >= 65 => ['C',  2.00],
            $n >= 55 => ['D',  1.00],
            default  => ['E',  0.00],
        };
    }
}
