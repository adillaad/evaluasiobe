<?php

namespace App\Http\Controllers\Admin;

use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\Universitas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\UniversityFilterTrait;
use Illuminate\Support\Facades\Validator;

class Prodicontroller extends Controller
{
    use UniversityFilterTrait;

    public function Add()
    {
        $universitas = Universitas::select('id', 'nama')->distinct()->get();
        $fakultas = Fakultas::select('id', 'nama')->distinct()->get();

        return view('admin.prodi.add', compact('universitas', 'fakultas'));
    }

    public function List(Request $request)
    {
        $query = Prodi::query()
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('prodi.*');

        // If User is University Admin, filter by their specific prodi
        if (auth()->user()->otoritas->otoritas === 'Admin Universitas') {
            $query->where('universitas.id', auth()->user()->id_universitasUser);
        }

        // Use trait method for filtering
        $query = $this->getFilteredQuery($query, $request);
        $prodis = $query->get();

        // Use trait method to get filter data
        $filterData = $this->getFilterData($request);

        return view('admin.prodi.list', array_merge(
            ['prodis' => $prodis],
            $filterData
        ));
    }

    public function Store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_universitas' => 'required',
            'id_fakultas' => 'required',
            'prodi' => 'required',
            'is_aptikom' => 'required|in:0,1',
        ], [
            'id_universitas.required' => 'Universitas wajib dipilih',
            'id_fakultas.required' => 'Fakultas wajib dipilih',
            'prodi.required' => 'Prodi wajib diisi',
            'is_aptikom.required' => 'Status Aptikom wajib dipilih',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Buat data prodi
            Prodi::create([
                'id_fakultas' => $request->id_fakultas,
                'nama' => $request->prodi,
                'is_aptikom' => $request->is_aptikom, 
            ]);

            return redirect()->route($this->getRouteByAuthority())->with('success', 'Prodi berhasil ditambahkan');
        } catch (\Illuminate\Database\QueryException $e) {
            // Periksa error kode 23000 (Integrity constraint violation)
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Prodi dengan nama yang sama sudah ada dalam fakultas tersebut.')->withInput();
            }

            // Pesan error default untuk error lainnya
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi Sebuah kesalahan')->withInput();
        }
    }

    public function Delete($id)
    {
        try {
            $prodi = Prodi::findOrFail($id);

            $prodi->delete();

            return redirect()
                ->route($this->getRouteByAuthority())
                ->with('success', 'Prodi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }

    private function getRouteByAuthority(): string
    {
        $routes = [
            'Admin' => 'admin.list-prodi',
            'Admin Universitas' => 'admin-universitas.list-prodi',
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }
}
