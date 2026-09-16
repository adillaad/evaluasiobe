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

            $namaRaw = $rowArr['nama'] ?? $rowArr['Nama'] ?? null;
            $emailRaw = $rowArr['email'] ?? $rowArr['Email'] ?? null;
            $otoritasRaw = $rowArr['otoritas'] ?? $rowArr['Otoritas'] ?? null;
            $prodiRaw = $rowArr['program_studi'] ?? $rowArr['Program Studi'] ?? $rowArr['prodi'] ?? null;
            $passRaw = $rowArr['password'] ?? $rowArr['Password'] ?? null;

            // Jika dibaca via indeks array numeric:
            if (isset($rowArr[4])) {
                // 5 Kolom: Nama (0), Email (1), Otoritas (2), Program Studi (3), Password (4)
                $namaRaw = $rowArr[0];
                $emailRaw = $rowArr[1];
                $otoritasRaw = $rowArr[2];
                $prodiRaw = $rowArr[3];
                $passRaw = $rowArr[4];
            } elseif (isset($rowArr[3]) && !$otoritasRaw) {
                // 4 Kolom (Legacy): Nama (0), Email (1), Program Studi (2), Password (3)
                $namaRaw = $rowArr[0];
                $emailRaw = $rowArr[1];
                $prodiRaw = $rowArr[2];
                $passRaw = $rowArr[3];
            } elseif (isset($rowArr[0])) {
                $namaRaw = $rowArr[0] ?? null;
                $emailRaw = $rowArr[1] ?? null;
                $otoritasRaw = $rowArr[2] ?? null;
                $prodiRaw = $rowArr[3] ?? null;
                $passRaw = $rowArr[4] ?? null;
            }

            $namaStr = trim((string) $namaRaw);
            $emailStr = trim((string) $emailRaw);
            $otoritasStr = trim((string) $otoritasRaw);
            $prodiStr = trim((string) $prodiRaw);
            $passStr = trim((string) $passRaw);

            // Skip header row
            if (strcasecmp($namaStr, 'Nama') === 0 || strcasecmp($emailStr, 'Email') === 0) {
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
                        if ($found) {
                            $matchedProdis->push($found);
                        }
                    }
                }
            }

            if ($matchedProdis->isEmpty() && $this->defaultProdiId) {
                $foundDefault = Prodi::with('fakultas')->find($this->defaultProdiId);
                if ($foundDefault) {
                    $matchedProdis->push($foundDefault);
                }
            }

            if ($matchedProdis->isEmpty()) {
                $authUser = auth()->user();
                $foundAuth = Prodi::with('fakultas')->find($authUser->id_prodiUser ?? null) ?? Prodi::with('fakultas')->first();
                if ($foundAuth) {
                    $matchedProdis->push($foundAuth);
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
                $user->update([
                    'name' => $namaStr,
                    'password' => !empty($passStr) ? Hash::make($passStr) : $user->password,
                ]);
            } else {
                $user = User::create([
                    'email' => $emailStr,
                    'name' => $namaStr,
                    'password' => Hash::make($userPassword),
                    'img' => 'User-Profile.png',
                    'id_prodiUser' => $primaryProdi->id,
                    'id_fakultasUser' => $primaryProdi->id_fakultas,
                    'id_universitasUser' => $primaryProdi->fakultas->id_universitas ?? 1,
                ]);
            }

            // Parse Otoritas (Mendukung multiple otoritas dipisah koma/garis miring)
            $roles = [];
            if (!empty($otoritasStr)) {
                $splitRoles = preg_split('/[,;\/]+/', $otoritasStr);
                foreach ($splitRoles as $r) {
                    $rClean = trim($r);
                    if (!empty($rClean)) {
                        $roles[] = $rClean;
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
}
