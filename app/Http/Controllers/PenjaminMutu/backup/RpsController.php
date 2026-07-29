<?php

namespace App\Http\Controllers\PenjaminMutu\backup;

use App\Http\Controllers\Controller;
use App\Models\RPS;
use App\Models\RpsValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RpsController extends Controller
{ 

    public function index()
    {
        $user = Auth::user(); 
        $query = Rps::query()->where('status', 'published');

        if (in_array($user->otoritas->otoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi'])) {  
            $query->whereHas('mk', fn($q) => $q->where('id_prodi', $user->id_prodiUser));
        } elseif ($user->otoritas->otoritas == 'Penjamin Mutu Fakultas') { 
            // $fakultasId = $user->fakultas_id; 
            $fakultasId = $user->id_fakultasUser; 
            $query->whereHas('mk.prodi', fn($q) => $q->where('id_fakultas', $fakultasId));
        }
 
        $rpss = $query->with(['mk.prodi', 'latestValidation'])->latest()->get();

        return view('penjamin-mutu.rps.rps_list', compact('rpss'));
    }
 
    public function validationList()
    {
        $user = Auth::user();
        $rpss = Rps::where('status', 'pending')
            ->whereHas('mk', fn($q) => $q->where('id_prodi', $user->id_prodiUser))  
            ->with('mk.prodi')
            ->latest()
            ->get();
            
        return view('penjamin-mutu.rps.rps_validation_list', compact('rpss'));
    }
 
    public function approve(Request $request, $id)
    {
        $rps = Rps::where('status', 'pending')->findOrFail($id);

        DB::transaction(function () use ($rps, $request) {
            $rps->status = 'published';
            $rps->save();

            $rps->validations()->create([
                'validator_id' => auth()->id(),
                'status'       => 'published',
                'catatan'      => 'RPS telah disetujui.',
            ]);
        });

        return redirect()->route('kepala-program-studi.rps.validation.list')->with('success', 'RPS berhasil dipublikasikan.');
    }
 
    public function reject(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string|min:10']);
        $rps = Rps::where('status', 'pending')->findOrFail($id);

        DB::transaction(function () use ($rps, $request) {
            $rps->status = 'rejected';
            $rps->save();

            $rps->validations()->create([
                'validator_id' => auth()->id(),
                'status'       => 'rejected',
                'catatan'      => $request->catatan,
            ]);
        });

        return redirect()->route('kepala-program-studi.rps.validation.list')->with('error', 'RPS telah ditolak dan dikembalikan ke dosen.');
    }
}