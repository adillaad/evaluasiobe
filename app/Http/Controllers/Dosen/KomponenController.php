<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CPL;
use App\Models\InstrumenPenilaian;
use App\Models\MK;
use App\Models\RPS;
use App\Models\Komponen;

class KomponenController extends Controller
{
    public function Add()
    {
        // $rpss = RPS::where('dosen', auth()->user()->name)->get();
        $rpss = RPS::where('dosen', auth()->user()->name)->get();
        $rps_id = $rpss->pluck('id');

        return view('dosen.jenis.add', compact('rpss'));
    }

    public function List()
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $jenis = InstrumenPenilaian::query()
            ->join('prodi', 'instrumen_penilaian.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('instrumen_penilaian.*');

        if ($otoritas == 'Wakil Rektor') {
            $jenis->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if ($otoritas == 'Wakil Dekan') {
            $jenis->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array($otoritas, ['Kepala Program Studi', 'Dosen'])) {
            $jenis->where('prodi.id', auth()->user()->id_prodiUser);
        }

        return view('dosen.jenis.list', ['jenis' => $jenis->get()]);
    }


    public function Store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        $jenis = $request->input('jenis');
        // $validator = Validator::make($data, [
        //     'jenis' => 'required|unique:komponen', 
        // ]);
        // if ($validator->fails()) {
        //     // Handle validation errors
        //     return redirect()->back()->with('error', 'Nama telah digunakan');
        // }
        InstrumenPenilaian::firstOrCreate([
            "nama_kriteria" => $jenis,
            "id_prodi" => auth()->user()->id_prodiUser,
        ]);

        return redirect()->route('dosen.Jenis-list')->with('success', 'Berhasil dibuat');
    }

    public function Delete($id)
    {
        InstrumenPenilaian::where('id', $id)->delete();
        return redirect()->route('dosen.Jenis-list')->with('success', 'Berhasil Dihapus');
    }
}
