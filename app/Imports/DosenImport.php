<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Prodi;
use App\Models\UserOtoritas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class DosenImport implements ToCollection
{
    protected ?int $defaultProdiId;

    public function __construct(?int $defaultProdiId = null)
    {
        $this->defaultProdiId = $defaultProdiId;
    }

    public function collection(Collection $rows)
    {
        $importedCount = 0;

        foreach ($rows as $index => $row) {
            $rowArr = $row instanceof Collection ? $row->toArray() : (array) $row;
            $rowArrValues = array_values($rowArr);

            $namaRaw = $rowArr['nama'] ?? $rowArr['Nama'] ?? null;
            $emailRaw = $rowArr['email'] ?? $rowArr['Email'] ?? null;
            $otoritasRaw = $rowArr['otoritas'] ?? $rowArr['Otoritas'] ?? null;
            $prodiRaw = $rowArr['program_studi'] ?? $rowArr['Program Studi'] ?? $rowArr['prodi'] ?? null;
            $passRaw = $rowArr['password'] ?? $rowArr['Password'] ?? null;

            // Jika dibaca via indeks array numeric:
            if (count($rowArrValues) >= 5) {
                // 5 Kolom: Nama (0), Email (1), Otoritas (2), Program Studi (3), Password (4)
                $namaRaw = $namaRaw ?? ($rowArrValues[0] ?? null);
                $emailRaw = $emailRaw ?? ($rowArrValues[1] ?? null);
                $otoritasRaw = $otoritasRaw ?? ($rowArrValues[2] ?? null);
                $prodiRaw = $prodiRaw ?? ($rowArrValues[3] ?? null);
                $passRaw = $passRaw ?? ($rowArrValues[4] ?? null);
            } elseif (count($rowArrValues) == 4) {
                $col2 = trim((string) ($rowArrValues[2] ?? ''));
                if ($this->isOtoritasString($col2)) {
                    // 4 Kolom: Nama (0), Email (1), Otoritas (2), Program Studi (3)
                    $namaRaw = $namaRaw ?? ($rowArrValues[0] ?? null);
                    $emailRaw = $emailRaw ?? ($rowArrValues[1] ?? null);
                    $otoritasRaw = $otoritasRaw ?? ($rowArrValues[2] ?? null);
                    $prodiRaw = $prodiRaw ?? ($rowArrValues[3] ?? null);
                } else {
                    // 4 Kolom (Legacy): Nama (0), Email (1), Program Studi (2), Password (3)
                    $namaRaw = $namaRaw ?? ($rowArrValues[0] ?? null);
                    $emailRaw = $emailRaw ?? ($rowArrValues[1] ?? null);
                    $prodiRaw = $prodiRaw ?? ($rowArrValues[2] ?? null);
                    $passRaw = $passRaw ?? ($rowArrValues[3] ?? null);
                }
            } elseif (count($rowArrValues) > 0) {
                $namaRaw = $namaRaw ?? ($rowArrValues[0] ?? null);
                $emailRaw = $emailRaw ?? ($rowArrValues[1] ?? null);
                $otoritasRaw = $otoritasRaw ?? ($rowArrValues[2] ?? null);
                $prodiRaw = $prodiRaw ?? ($rowArrValues[3] ?? null);
                $passRaw = $passRaw ?? ($rowArrValues[4] ?? null);
            }

            $namaStr = trim((string) $namaRaw);
            $emailStr = trim((string) $emailRaw);
            $otoritasStr = trim((string) $otoritasRaw);
            $prodiStr = trim((string) $prodiRaw);
            $passStr = trim((string) $passRaw);

            // Skip header row
            if (strcasecmp($namaStr, 'Nama') === 0 || strcasecmp($emailStr, 'Email') === 0 || strcasecmp($namaStr, 'Name') === 0) {
                continue;
            }

            if (empty($namaStr) || empty($emailStr)) {
                continue;
            }

            // Tentukan ID/Objek Prodi (Mendukung multiple prodi dipisah koma/garis miring)
            $matchedProdis = collect();

            if (!empty($prodiStr)) {
                $splitProdi = preg_split('/[,;\/]+/', $prodiStr);
                foreach ($splitProdi as $pName) {
                    $pNameClean = trim($pName);
                    if (!empty($pNameClean)) {
                        $found = Prodi::with('fakultas')->where('nama', 'LIKE', '%' . $pNameClean . '%')->first();
                        if (!$found) {
                            $found = Prodi::with('fakultas')->whereRaw('LOWER(?) LIKE CONCAT("%", LOWER(nama), "%")', [$pNameClean])->first();
                        }
                        if ($found) {
                            $matchedProdis->push($found);
                        }
                    }
                }
            }

            // Pastikan defaultProdiId (prodi dari user yang melakukan import) selalu dimasukkan
            if ($this->defaultProdiId) {
                $foundDefault = Prodi::with('fakultas')->find($this->defaultProdiId);
                if ($foundDefault && !$matchedProdis->contains('id', $foundDefault->id)) {
                    $matchedProdis->prepend($foundDefault);
                }
            }

            if ($matchedProdis->isEmpty()) {
                $authUser = auth()->user();
                $authProdiId = $authUser->id_prodiUser ?? $authUser->prodis->first()?->id;
                $foundAuth = $authProdiId ? Prodi::with('fakultas')->find($authProdiId) : null;
                if ($foundAuth) {
                    $matchedProdis->push($foundAuth);
                }
            }

            if ($matchedProdis->isEmpty()) {
                $firstProdi = Prodi::with('fakultas')->first();
                if ($firstProdi) {
                    $matchedProdis->push($firstProdi);
                }
            }

            if ($matchedProdis->isEmpty()) {
                continue;
            }

            $primaryProdi = $matchedProdis->first();

            // Default password selalu Unilajaya! jika kosong
            $userPassword = !empty($passStr) ? $passStr : 'Unilajaya!';

            $existingUser = User::where('email', $emailStr)->first();

            if ($existingUser) {
                $user = $existingUser;
                $updateData = ['name' => $namaStr];
                if (!empty($passStr)) {
                    $updateData['password'] = Hash::make($passStr);
                }
                if (!$user->id_prodiUser) {
                    $updateData['id_prodiUser'] = $primaryProdi->id;
                    $updateData['id_fakultasUser'] = $primaryProdi->id_fakultas;
                    $updateData['id_universitasUser'] = $primaryProdi->fakultas?->id_universitas ?? 1;
                }
                $user->update($updateData);
            } else {
                $user = User::create([
                    'email' => $emailStr,
                    'name' => $namaStr,
                    'password' => Hash::make($userPassword),
                    'img' => 'User-Profile.png',
                    'id_prodiUser' => $primaryProdi->id,
                    'id_fakultasUser' => $primaryProdi->id_fakultas,
                    'id_universitasUser' => $primaryProdi->fakultas?->id_universitas ?? 1,
                ]);
            }

            // Parse Otoritas (Mendukung multiple otoritas dipisah koma/garis miring)
            $roles = [];
            if (!empty($otoritasStr)) {
                $splitRoles = preg_split('/[,;\/]+/', $otoritasStr);
                foreach ($splitRoles as $r) {
                    $rClean = trim($r);
                    if (!empty($rClean)) {
                        $mappedRole = $this->mapOtoritas($rClean);
                        if (!in_array($mappedRole, $roles)) {
                            $roles[] = $mappedRole;
                        }
                    }
                }
            }

            if (empty($roles)) {
                $roles = ['Dosen'];
            }

            $isFirstRole = true;
            foreach ($roles as $roleName) {
                if (!$user->otoritas()->where('otoritas', $roleName)->exists()) {
                    UserOtoritas::create([
                        'user_id' => $user->id,
                        'otoritas' => $roleName,
                        'nama_otoritas' => null,
                        'active' => $isFirstRole && !$user->otoritas()->where('active', true)->exists(),
                    ]);
                }
                $isFirstRole = false;
            }

            // Sync relasi prodi_user untuk semua prodi yang cocok
            $hasActiveProdi = $user->prodis()->wherePivot('active', true)->exists();
            foreach ($matchedProdis as $index => $mProdi) {
                if (!$user->prodis()->where('prodi_id', $mProdi->id)->exists()) {
                    $isActive = !$hasActiveProdi && ($index === 0);
                    $user->prodis()->attach($mProdi->id, ['active' => $isActive]);
                    if ($isActive) {
                        $hasActiveProdi = true;
                    }
                }
            }

            $importedCount++;
        }

        if ($importedCount === 0) {
            throw new \Exception('Tidak ada data user/dosen yang valid untuk diimport dari file Excel ini. Pastikan format kolom sesuai (Nama, Email, Otoritas, Program Studi, Password).');
        }
    }

    private function isOtoritasString(string $val): bool
    {
        $valLower = strtolower($val);
        $keywords = ['dosen', 'kaprodi', 'koorprodi', 'koordinator', 'kepala', 'penjamin', 'mutu', 'dekan', 'rektor', 'admin', 'pmp', 'pmf', 'pmu'];
        foreach ($keywords as $kw) {
            if (str_contains($valLower, $kw)) {
                return true;
            }
        }
        return false;
    }

    private function mapOtoritas(string $raw): string
    {
        $rawLower = strtolower(trim($raw));
        if (str_contains($rawLower, 'koordinator') || str_contains($rawLower, 'koorprodi') || str_contains($rawLower, 'kepala') || str_contains($rawLower, 'kaprodi')) {
            return 'Koordinator Program Studi';
        }
        if (str_contains($rawLower, 'universitas') && (str_contains($rawLower, 'penjamin') || str_contains($rawLower, 'pmu'))) {
            return 'Penjamin Mutu Universitas';
        }
        if (str_contains($rawLower, 'fakultas') && (str_contains($rawLower, 'penjamin') || str_contains($rawLower, 'pmf'))) {
            return 'Penjamin Mutu Fakultas';
        }
        if (str_contains($rawLower, 'prodi') && (str_contains($rawLower, 'penjamin') || str_contains($rawLower, 'pmp'))) {
            return 'Penjamin Mutu Program Studi';
        }
        if (str_contains($rawLower, 'wakil rektor') || str_contains($rawLower, 'warek')) {
            return 'Wakil Rektor';
        }
        if (str_contains($rawLower, 'wakil dekan') || str_contains($rawLower, 'wadek')) {
            return 'Wakil Dekan';
        }
        if (str_contains($rawLower, 'admin universitas')) {
            return 'Admin Universitas';
        }
        if (str_contains($rawLower, 'admin')) {
            return 'Admin';
        }
        if (str_contains($rawLower, 'dosen')) {
            return 'Dosen';
        }
        return ucwords($raw);
    }
}
