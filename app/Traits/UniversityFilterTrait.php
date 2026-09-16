<?php

namespace App\Traits;

use App\Models\Fakultas;
use App\Models\Kurikulum;
use App\Models\Prodi;
use App\Models\Universitas;
use Illuminate\Http\Request;
use Schema;

trait UniversityFilterTrait
{
    protected function getFilteredQuery($query, Request $request)
    {
        $mainTable = $query->getModel()->getTable();
        $isUserModel = $query->getModel() instanceof \App\Models\User;

        // Filter by Universitas
        $query->when($request->universitas_id, function ($q) use ($request, $isUserModel, $mainTable) {
            if ($isUserModel) {
                // Cari user yang terhubung ke prodi di dalam universitas yang dipilih.
                return $q->whereHas('prodis.fakultas.universitas', function ($subQuery) use ($request) {
                    $subQuery->where('universitas.id', $request->universitas_id);
                });
            }
            
            $joins = $q->getQuery()->joins ?? [];
            $joinedTables = array_map(function($j) { return $j->table; }, $joins);

            if (in_array('universitas', $joinedTables)) {
                return $q->where('universitas.id', $request->universitas_id);
            }
            if (in_array('fakultas', $joinedTables)) {
                return $q->where('fakultas.id_universitas', $request->universitas_id);
            }
            if (Schema::hasColumn($mainTable, 'id_universitas')) {
                return $q->where($mainTable . '.id_universitas', $request->universitas_id);
            }
            if (Schema::hasColumn($mainTable, 'universitas_id')) {
                return $q->where($mainTable . '.universitas_id', $request->universitas_id);
            }

            return $q;
        });

        // Filter by Fakultas
        $query->when($request->fakultas_id, function ($q) use ($request, $isUserModel, $mainTable) {
            if ($isUserModel) {
                // Cari user yang terhubung ke prodi di dalam fakultas yang dipilih.
                return $q->whereHas('prodis.fakultas', function ($subQuery) use ($request) {
                    $subQuery->where('fakultas.id', $request->fakultas_id);
                });
            }

            $joins = $q->getQuery()->joins ?? [];
            $joinedTables = array_map(function($j) { return $j->table; }, $joins);

            if (in_array('fakultas', $joinedTables)) {
                return $q->where('fakultas.id', $request->fakultas_id);
            }
            if (in_array('prodi', $joinedTables)) {
                return $q->where('prodi.id_fakultas', $request->fakultas_id);
            }
            if (Schema::hasColumn($mainTable, 'id_fakultas')) {
                return $q->where($mainTable . '.id_fakultas', $request->fakultas_id);
            }
            if (Schema::hasColumn($mainTable, 'fakultas_id')) {
                return $q->where($mainTable . '.fakultas_id', $request->fakultas_id);
            }

            return $q;
        });

        // Filter by Prodi
        $query->when($request->prodi_id, function ($q) use ($request, $isUserModel, $mainTable) {
            if ($isUserModel) {
                // Cari user yang terhubung ke prodi yang dipilih.
                return $q->whereHas('prodis', function ($subQuery) use ($request) {
                    $subQuery->where('prodi.id', $request->prodi_id);
                });
            }

            if (Schema::hasColumn($mainTable, 'id_prodi')) {
                return $q->where($mainTable . '.id_prodi', $request->prodi_id);
            }
            if (Schema::hasColumn($mainTable, 'prodi_id')) {
                return $q->where($mainTable . '.prodi_id', $request->prodi_id);
            }

            $joins = $q->getQuery()->joins ?? [];
            $joinedTables = array_map(function($j) { return $j->table; }, $joins);
            if (in_array('prodi', $joinedTables)) {
                return $q->where('prodi.id', $request->prodi_id);
            }

            return $q;
        });

        // Filter by Kurikulum
        $query->when($request->filled('kurikulum_id'), function ($q) use ($request, $mainTable) {
            if (Schema::hasColumn($mainTable, 'kurikulum_id')) {
                return $q->where($mainTable . '.kurikulum_id', $request->kurikulum_id);
            }
            if (Schema::hasColumn($mainTable, 'id_kurikulum')) {
                return $q->where($mainTable . '.id_kurikulum', $request->kurikulum_id);
            }

            $joins = $q->getQuery()->joins ?? [];
            $hasKurikulumJoin = false;
            foreach ($joins as $join) {
                if ($join->table === 'kurikulums') {
                    $hasKurikulumJoin = true;
                    break;
                }
            }
            if ($hasKurikulumJoin) {
                return $q->where('kurikulums.id', $request->kurikulum_id);
            }
            
            return $q;
        });

        // Filter by CPL
        $query->when($request->filled('cpl_id'), function ($q) use ($request, $mainTable) {
            if (Schema::hasColumn($mainTable, 'cpl_id')) {
                return $q->where($mainTable . '.cpl_id', $request->cpl_id);
            }
            if (Schema::hasColumn($mainTable, 'id_cpl')) {
                return $q->where($mainTable . '.id_cpl', $request->cpl_id);
            }

            $joins = $q->getQuery()->joins ?? [];
            $hasCplJoin = false;
            foreach ($joins as $join) {
                if ($join->table === 'cpls') {
                    $hasCplJoin = true;
                    break;
                }
            }
            if ($hasCplJoin) {
                return $q->where('cpls.id', $request->cpl_id);
            }

            return $q;
        });

        return $query;
    }

    protected function getFilterData(Request $request)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas;

        // Definisikan peran berdasarkan level untuk mempermudah pembacaan
        $isUniversityLevel = in_array($userOtoritas, ['Admin Universitas', 'Wakil Rektor', 'Penjamin Mutu Universitas']);
        $isFacultyLevel = in_array($userOtoritas, ['Wakil Dekan', 'Penjamin Mutu Fakultas']);
        $isProdiLevel = in_array($userOtoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Dosen']);

        // --- Filter Universitas ---
        if ($isUniversityLevel || $isFacultyLevel || $isProdiLevel) {
            $universities = Universitas::where('id', $user->id_universitasUser)->get();
            // Paksa universitas_id ada di request untuk trigger filter selanjutnya
            if (!$request->has('universitas_id')) {
                $request->merge(['universitas_id' => $user->id_universitasUser]);
            }
        } else {
            // Hanya Super Admin yang bisa memilih universitas
            $universities = Universitas::all();
        }

        // --- Filter Fakultas ---
        $faculties = collect();
        if ($request->filled('universitas_id')) {
            if ($isFacultyLevel || $isProdiLevel) {
                $faculties = Fakultas::where('id', $user->id_fakultasUser)->get();
                // Paksa fakultas_id ada di request
                if (!$request->has('fakultas_id')) {
                    $request->merge(['fakultas_id' => $user->id_fakultasUser]);
                }
            } else {
                $faculties = Fakultas::where('id_universitas', $request->universitas_id)->get();
            }
        }

        // --- Filter Program Studi ---
        $programs = collect();
        if ($request->filled('fakultas_id')) {
            if ($isProdiLevel) {
                $programs = Prodi::where('id', $user->id_prodiUser)->get();
                // Paksa prodi_id ada di request
                if (!$request->has('prodi_id')) {
                    $request->merge(['prodi_id' => $user->id_prodiUser]);
                }
            } else {
                $programs = Prodi::where('id_fakultas', $request->fakultas_id)->get();
            }
        }

        // --- FILTER KURIKULUM
        $kurikulumsQuery = Kurikulum::query()
            ->join('prodi', 'kurikulums.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select(
                'kurikulums.id',
                'kurikulums.tahun',
                'prodi.nama as nama_prodi',
                'fakultas.nama as nama_fakultas',
                'universitas.nama as nama_univ'
            );

        // Terapkan filter berdasarkan hak akses pengguna
        if ($isUniversityLevel) {
            $kurikulumsQuery->where('universitas.id', $user->id_universitasUser);
        } elseif ($isFacultyLevel) {
            $kurikulumsQuery->where('fakultas.id', $user->id_fakultasUser);
        } elseif ($isProdiLevel) {
            $kurikulumsQuery->where('prodi.id', $user->id_prodiUser);
        }

        // BUAT DROPDOWN MENJADI KONTEKSTUAL
        // Jika pengguna sudah memilih filter lain, persempit juga opsi kurikulum
        $kurikulumsQuery->when($request->universitas_id, function ($q) use ($request) {
            return $q->where('universitas.id', $request->universitas_id);
        });

        $kurikulumsQuery->when($request->fakultas_id, function ($q) use ($request) {
            return $q->where('fakultas.id', $request->fakultas_id);
        });

        $kurikulumsQuery->when($request->prodi_id, function ($q) use ($request) {
            return $q->where('prodi.id', $request->prodi_id);
        });

        $kurikulums = $kurikulumsQuery->distinct()->orderBy('tahun', 'desc')->get();

        // --- FILTER CPL ---
        $cplsQuery = \App\Models\CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul');

        if ($isUniversityLevel) {
            $cplsQuery->where('universitas.id', $user->id_universitasUser);
        } elseif ($isFacultyLevel) {
            $cplsQuery->where('fakultas.id', $user->id_fakultasUser);
        } elseif ($isProdiLevel) {
            $cplsQuery->where('prodi.id', $user->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $cplsQuery->where('cpls.id_kurikulum', $request->kurikulum_id);
        } elseif ($request->filled('prodi_id')) {
            $cplsQuery->where('cpls.id_prodi', $request->prodi_id);
        }

        $cplsFilter = $cplsQuery->distinct()->orderBy('cpls.kode', 'asc')->get();

        $tahunAjarans = \App\Models\TahunAjaran::orderBy('tahun', 'desc')->get();

        $metodeQuery = \App\Models\MetodePenilaian::query();
        if ($isProdiLevel) {
            $metodeQuery->where(function ($q) use ($user) {
                $q->where('id_prodi', $user->id_prodiUser)->orWhereNull('id_prodi');
            });
        }
        $metodePenilaians = $metodeQuery->orderBy('nama', 'asc')->get();

        $mkQuery = \App\Models\MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('mks.kode', 'mks.nama');

        if ($isUniversityLevel) {
            $mkQuery->where('universitas.id', $user->id_universitasUser);
        } elseif ($isFacultyLevel) {
            $mkQuery->where('fakultas.id', $user->id_fakultasUser);
        } elseif ($isProdiLevel) {
            $mkQuery->where('prodi.id', $user->id_prodiUser);
        }

        if ($request->filled('prodi_id')) {
            $mkQuery->where('mks.id_prodi', $request->prodi_id);
        }

        $mks = $mkQuery->distinct()->orderBy('mks.nama', 'asc')->get();

        return compact('universities', 'faculties', 'programs', 'kurikulums', 'cplsFilter', 'tahunAjarans', 'metodePenilaians', 'mks');
    }
    public function getFaculties($universitasId)
    {
        $faculties = Fakultas::where('id_universitas', $universitasId)
            ->select('id', 'nama')
            ->get();

        return response()->json($faculties);
    }

    public function getPrograms($fakultasId)
    {
        $programs = Prodi::where('id_fakultas', $fakultasId)
            ->select('id', 'nama')
            ->get();

        return response()->json($programs);
    }
}
