<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CPL;
use App\Models\MK;
use App\Models\RPS;
use App\Models\Universitas;

class Universitascontroller extends Controller
{
    public function Add()
    {
        return view('dosen.universitas.add');
    }

    public function List()
    {
        $universitas = Universitas::all();
        
        return view('dosen.universitas.list', compact('universitas'));
    }

    public function Store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        $universitas = Universitas::create($data);
        
        return redirect(route('Universitas-add'))->with('success', 'Berhasil dibuat');
    }

    public function Delete($id)
    {
        Universitas::where('id', $id)->delete();
        return redirect(route('Universitas-list'))->with('success', 'Berhasil Dihapus');
    }
}