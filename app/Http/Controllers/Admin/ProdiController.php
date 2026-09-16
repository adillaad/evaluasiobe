<?php

namespace App\Http\Controllers\Admin;

use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\Universitas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\UniversityFilterTrait;
use Illuminate\Support\Facades\Validator;

class ProdiController extends Controller
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

    public function daftarAkunProdi(Request $request)
    {
        $authUser = auth()->user();
        $userOtoritas = optional($authUser->otoritas)->otoritas;

        $query = Prodi::query()
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('prodi.*')
            ->with(['fakultas', 'fakultas.universitas'])
            ->withCount('users');

        if ($userOtoritas === 'Admin Universitas') {
            $query->where('universitas.id', $authUser->id_universitasUser);
        }

        $prodis = $query->get();

        return view('admin.prodi.daftar_akun', compact('prodis'));
    }

    public function gateMenu(Request $request)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return redirect()->route('login');
        }

        $userOtoritas = optional($authUser->otoritas)->otoritas;

        // 1. Utamakan prodi yang secara spesifik terdaftar di relasi prodi_user milik user tersebut
        $prodis = $authUser->prodis()->with(['fakultas', 'fakultas.universitas'])->withCount('users')->get();

        // 2. Jika relasi prodi_user kosong (misal Admin Universitas / Admin), tampilkan prodi berdasarkan universitas user
        if ($prodis->isEmpty()) {
            if (in_array($userOtoritas, ['Admin', 'Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor']) || !$authUser->id_prodiUser) {
                $query = Prodi::query()
                    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                    ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
                    ->select('prodi.*')
                    ->with(['fakultas', 'fakultas.universitas'])
                    ->withCount('users');

                if ($authUser->id_universitasUser) {
                    $query->where('universitas.id', $authUser->id_universitasUser);
                }

                $prodis = $query->get();
            } else if ($authUser->prodi) {
                $prodis = collect([$authUser->prodi]);
            }
        }

        $univName = optional($authUser->universitas)->nama ?? optional(optional(optional($prodis->first())->fakultas)->universitas)->nama ?? 'UNIVERSITAS LAMPUNG';

        return view('auth.gate_menu', compact('prodis', 'univName'));
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
