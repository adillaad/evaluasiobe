<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Models\ProfilCpl;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProfilCplController extends Controller
{
    public function indexkompetensi()
    {
        return view('penjamin-mutu.profil.listProfilCpl');
    }
    public function readListProfilCpl()
    {
        try {
            $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
            $query = ProfilCpl::query()
                ->select('profil_cpl.*')
                ->join('prodi', 'profil_cpl.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->join('profil_lulusan', 'profil_cpl.idProfil', '=', 'profil_lulusan.id')
                ->join('cpls', 'profil_cpl.idCpl', '=', 'cpls.id');

            if (in_array($userOtoritas, ['Penjamin Mutu Universitas','Wakil Rektor'])) {
                $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } else if (in_array($userOtoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $query->where('fakultas.id', auth()->user()->id_fakultasUser);
            } else if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $query->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $profilCpls = $query->get();

            return view('penjamin-mutu.profil.readListProfilCpl', compact('profilCpls', 'userOtoritas'));
        } catch (\Exception $e) {
            return ($e->getMessage());
        }
    }
    public function createListProfilCpl()
    {
        $profil  = DB::table('profil_lulusan')
            ->join('prodi', 'profil_lulusan.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->select('profil_lulusan.id', 'namaProfil','deskripsi')
            ->distinct()
            ->get();
        $cplData = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->select('cpls.id', 'kode', 'judul')
            ->distinct()
            ->get();
        // return "Masuk sini";
        return view('penjamin-mutu.profil.createListProfilCpl', ['profil' => $profil, 'cplData' => $cplData]);
    }

    public function storeListProfilCpl(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'idProfil' => 'required',
            'idCpl' => [
                'required',
                Rule::unique('profil_cpl')->where(function ($query) use ($request) {
                    return $query->where('idProfil', $request->idProfil);
                }),
            ],
            'bobot' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null && $value !== '') {
                        $valNum = (float)$value <= 1.0 ? (float)$value * 100.0 : (float)$value;
                        $totalBobot = ProfilCpl::where('idProfil', $request->idProfil)->sum('bobot');
                        if (($totalBobot + $valNum) > 100.0) {
                            $fail('Total bobot tidak boleh melebihi 100% untuk Profil ini.');
                        }
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Gagal menyimpan data', 'validation_errors' => $validator->errors()], 422);
        }

        try {
            $rawBobot = null;
            if ($request->bobot !== null && $request->bobot !== '') {
                $num = (float)$request->bobot;
                $rawBobot = ($num > 0 && $num <= 1.0) ? round($num * 100.0, 2) : $num;
                $rawBobot = (float)$rawBobot == (int)$rawBobot ? (int)$rawBobot : $rawBobot;
            }

            ProfilCpl::create([
                'idProfil' => $request->idProfil,
                'idCpl' => $request->idCpl,
                'bobot' => $rawBobot,
                'id_prodi' => auth()->user()->id_prodiUser,
            ]);

            \App\Http\Controllers\PenjaminMutu\CPLController::recalculateCplPlBobot($request->idProfil);

            return response()->json(['message' => 'Data berhasil disimpan'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan data'], 422);
        }
    }


    public function showProfilCpl($id)
    {
        // return "Masuk sini kok";
        $profilCpl = ProfilCpl::findOrFail($id);

        $profil = DB::table('profil_lulusan')
            ->select('id', 'namaProfil')
            ->distinct()
            ->get();
        // return $profil;
        $cplData = DB::table('cpls')
            ->select('id', 'kode', 'judul')
            ->distinct()
            ->get();
        return view('penjamin-mutu.profil.editProfilCpl', [
            'profilCpl' => $profilCpl,
            'profil' => $profil,
            'cplData' => $cplData
        ]);
    }

    public function updateProfilCpl(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'idProfil' => 'required',
            'idCpl' => [
                'required',
                Rule::unique('profil_cpl')->where(function ($query) use ($request, $id) {
                    return $query->where('idProfil', $request->idProfil)->where('id', '!=', $id);
                }),
            ],
            'bobot' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($request, $id) {
                    if ($value !== null && $value !== '') {
                        $valNum = (float)$value <= 1.0 ? (float)$value * 100.0 : (float)$value;
                        $existingProfilCpl = ProfilCpl::find($id);
                        $totalBobot = ProfilCpl::where('idProfil', $request->idProfil)->where('id', '!=', $id)->sum('bobot');

                        if (($totalBobot + $valNum) > 100.0) {
                            $fail('Total bobot tidak boleh melebihi 100% untuk Profil ini.');
                        }
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Gagal menyimpan data', 'validation_errors' => $validator->errors()], 422);
        }

        try {
            $profilCpl = ProfilCpl::findOrFail($id);
            $profilCpl->idProfil = $request->idProfil;
            $profilCpl->idCpl = $request->idCpl;
            $rawBobot = null;
            if ($request->bobot !== null && $request->bobot !== '') {
                $num = (float)$request->bobot;
                $rawBobot = ($num > 0 && $num <= 1.0) ? round($num * 100.0, 2) : $num;
                $rawBobot = (float)$rawBobot == (int)$rawBobot ? (int)$rawBobot : $rawBobot;
            }
            $profilCpl->bobot = $rawBobot;
            $profilCpl->save();

            \App\Http\Controllers\PenjaminMutu\CPLController::recalculateCplPlBobot($request->idProfil);

            return response()->json(['message' => 'Data berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan data', 'exception' => $e->getMessage()], 422);
        }
    }


    public function deleteProfilCpl($id)
    {
        $profil = ProfilCpl::findOrFail($id);
        $idProfil = $profil->idProfil;
        $profil->delete();

        \App\Http\Controllers\PenjaminMutu\CPLController::recalculateCplPlBobot($idProfil);

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
