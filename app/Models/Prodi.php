<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;
    protected $table = 'prodi';

    protected $fillable = ['nama', 'id_fakultas', 'is_aptikom', 'jenjang'];

    protected $casts = [
        'is_aptikom' => 'boolean',
    ];

    public function getJenjangAttribute($value): string
    {
        if (!empty($value)) {
            return strtoupper(trim($value));
        }

        $nama = strtoupper(trim($this->nama ?? ''));
        if (str_contains($nama, 'S3') || str_contains($nama, 'DOKTOR')) return 'S3';
        if (str_contains($nama, 'S2') || str_contains($nama, 'MAGISTER')) return 'S2';
        if (str_contains($nama, 'D3') || str_contains($nama, 'DIPLOMA 3') || str_contains($nama, 'D-3') || str_contains($nama, 'D-III')) return 'D3';
        if (str_contains($nama, 'D4') || str_contains($nama, 'DIPLOMA 4') || str_contains($nama, 'D-4') || str_contains($nama, 'SARJANA TERAPAN')) return 'D4';
        if (str_contains($nama, 'S1') || str_contains($nama, 'SARJANA')) return 'S1';
        return 'S1';
    }

    public function getTargetSks(): int
    {
        $jenjang = strtoupper(trim($this->jenjang ?? ''));
        if (in_array($jenjang, ['S2', 'S-2', 'MAGISTER', 'SPESIALIS', 'SP-1', 'SP1', 'S2 TERAPAN']) || str_contains($jenjang, 'MAGISTER') || str_contains($jenjang, 'S2')) {
            return 36;
        }
        if (in_array($jenjang, ['S3', 'S-3', 'DOKTOR', 'SUB SPESIALIS', 'SP-2', 'SP2', 'S3 TERAPAN']) || str_contains($jenjang, 'DOKTOR') || str_contains($jenjang, 'S3')) {
            return 42;
        }
        if (in_array($jenjang, ['D3', 'D-3', 'D-III', 'DIPLOMA 3', 'DIPLOMA III']) || str_contains($jenjang, 'D3')) {
            return 108;
        }
        return 144;
    }

    public function getJenjangFullLabel(): string
    {
        $jenjang = strtoupper(trim($this->jenjang ?? ''));
        if (in_array($jenjang, ['S3', 'S-3', 'DOKTOR', 'SUB SPESIALIS', 'SP-2', 'SP2', 'S3 TERAPAN']) || str_contains($jenjang, 'DOKTOR') || str_contains($jenjang, 'S3')) {
            return 'S3 / Doktor';
        }
        if (in_array($jenjang, ['S2', 'S-2', 'MAGISTER', 'SPESIALIS', 'SP-1', 'SP1', 'S2 TERAPAN']) || str_contains($jenjang, 'MAGISTER') || str_contains($jenjang, 'S2')) {
            return 'S2 / Magister';
        }
        if (in_array($jenjang, ['D3', 'D-3', 'D-III', 'DIPLOMA 3', 'DIPLOMA III']) || str_contains($jenjang, 'D3')) {
            return 'D3 / Ahli Madya';
        }
        if (in_array($jenjang, ['D4', 'D-4', 'D-IV', 'DIPLOMA 4', 'SARJANA TERAPAN']) || str_contains($jenjang, 'D4')) {
            return 'D4 / Sarjana Terapan';
        }
        return 'S1 / Sarjana';
    }

    public function getPredikatKelulusan(float $ipk): array
    {
        $jenjang = strtoupper(trim($this->jenjang ?? ''));
        $isPascasarjana = in_array($jenjang, ['S2', 'S-2', 'MAGISTER', 'SPESIALIS', 'SP-1', 'SP1', 'S2 TERAPAN', 'S3', 'S-3', 'DOKTOR']) || str_contains($jenjang, 'S2') || str_contains($jenjang, 'S3') || str_contains($jenjang, 'MAGISTER') || str_contains($jenjang, 'DOKTOR');

        if ($isPascasarjana) {
            if ($ipk >= 3.76) {
                return ['Dengan Pujian (Cumlaude)', 'bg-success-subtle text-success'];
            } elseif ($ipk >= 3.51) {
                return ['Sangat Memuaskan', 'bg-primary-subtle text-primary'];
            } elseif ($ipk >= 3.00) {
                return ['Memuaskan', 'bg-info-subtle text-info'];
            } elseif ($ipk > 0) {
                return ['Cukup', 'bg-secondary-subtle text-secondary'];
            }
            return ['Belum Ada Data', 'bg-light text-muted'];
        }

        // Diploma / Sarjana
        if ($ipk >= 3.51) {
            return ['Dengan Pujian (Cumlaude)', 'bg-success-subtle text-success'];
        } elseif ($ipk >= 3.00) {
            return ['Sangat Memuaskan', 'bg-primary-subtle text-primary'];
        } elseif ($ipk >= 2.76) {
            return ['Memuaskan', 'bg-info-subtle text-info'];
        } elseif ($ipk > 0) {
            return ['Cukup', 'bg-secondary-subtle text-secondary'];
        }
        return ['Belum Ada Data', 'bg-light text-muted'];
    }

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
        if ($nilai >= 75) return 'Baik';
        if ($nilai >= 51) return 'Cukup';
        return 'Perlu Peningkatan';
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
        if ($score >= 75) return 'b';
        if ($score >= 51) return 'c';
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
            'Baik', 'Sangat Baik' => 'baik',
            'Cukup'               => 'cukup',
            'Perlu Peningkatan'   => 'kurang',
            default               => 'cukup',
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
