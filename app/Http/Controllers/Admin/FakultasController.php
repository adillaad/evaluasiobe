<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Universitas;
use App\Traits\UniversityFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FakultasController extends Controller
{
    use UniversityFilterTrait;

    public function Add()
    {
        $universitas = Universitas::select('id', 'nama')->distinct()->get();

        return view('admin.fakultas.add', compact('universitas'));
    }

    public function List(Request $request)
    {
        // Ambil daftar universitas untuk dropdown filter
        $query = Fakultas::query()
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('fakultas.*');

        if (auth()->user()->otoritas->otoritas === 'Admin Universitas') {
            $query->where('id_universitas', auth()->user()->id_universitasUser);
        }

        // Gunakan method dari trait untuk filter
        $query = $this->getFilteredQuery($query, $request);
        $fakultass = $query->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        return view('admin.fakultas.list', array_merge(
            ['fakultass' => $fakultass],
            $filterData
        ));

        return view('admin.fakultas.list', compact('fakultass', 'universities'));
    }

    public function Store(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'id_universitas' => 'required',
            'fakultas' => 'required',
        ], [
            'id_universitas.required' => 'Universitas wajib dipilih',
            'fakultas.required' => 'Fakultas wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Buat data fakultas
            Fakultas::create([
                'id_universitas' => $request->id_universitas,
                'nama' => $request->fakultas
            ]);

            return redirect()
                ->route($this->getRouteByAuthority())
                ->with('success', 'Fakultas berhasil ditambahkan');
        } catch (\Exception $e) {
            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'unique_nama_per_universitas')) {
                return redirect()
                    ->back()
                    ->with('error', 'Fakultas dengan nama yang sama sudah ada di ' . Universitas::find($request->id_universitas)->nama)
                    ->withInput();
            }

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data')
                ->withInput();
        }
    }

    public function Delete($id)
    {
        try {
            $fakultas = Fakultas::findOrFail($id);
            $fakultas->delete();

            return redirect()->route($this->getRouteByAuthority())->with('success', 'Fakultas berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }

    private function getRouteByAuthority(): string
    {
        $routes = [
            'Admin' => 'admin.list-fakultas',
            'Admin Universitas' => 'admin-universitas.list-fakultas',
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }
}
