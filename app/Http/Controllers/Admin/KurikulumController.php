<?php

namespace App\Http\Controllers\Admin;

use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\UniversityFilterTrait;
use Illuminate\Auth\Access\AuthorizationException;

class KurikulumController extends Controller
{
    use UniversityFilterTrait;

    public function list(Request $request)
    {
        $query = Kurikulum::query()
            ->join('prodi', 'kurikulums.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id');

        if (auth()->user()->otoritas->otoritas === 'Admin Universitas') {
            $query->where('universitas.id', auth()->user()->id_universitasUser);
        } elseif (auth()->user()->otoritas->otoritas === 'Kepala Program Studi') {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $query = $this->getFilteredQuery($query, $request);

        $kurikulumsForTable = $query->select('kurikulums.*')
            ->orderby('universitas.id', 'asc')
            ->orderby('fakultas.id', 'asc')
            ->orderby('prodi.id', 'asc')
            ->orderby('kurikulums.tahun', 'asc')->get();

        // Cukup panggil getFilterData satu kali, datanya sudah lengkap
        $filterData = $this->getFilterData($request);

        $editMode = session('edit_mode', false);
        $editedId = session('edited_id', null);

        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas;

        $dataForAddForm = [
            'fakultas' => collect(),
            'prodi' => collect(),
        ];

        switch ($userOtoritas) {
            case 'Admin Universitas':
                // Admin Univ bisa memilih fakultas di universitasnya.
                $dataForAddForm['fakultas'] = Fakultas::select('id', 'nama')
                    ->where('id_universitas', $user->id_universitasUser)
                    ->orderBy('nama', 'asc')
                    ->get();
                break;

            case 'Kepala Program Studi':
                // Kaprodi memiliki fakultas dan prodi yang sudah terkunci.
                // Kita tetap ambil datanya untuk ditampilkan di dropdown yang disabled.
                $dataForAddForm['fakultas'] = Fakultas::select('id', 'nama')
                    ->where('id_universitas', $user->id_universitasUser)
                    ->orderBy('nama', 'asc')
                    ->get();
                $dataForAddForm['prodi'] = Prodi::select('id', 'nama')
                    ->where('id_fakultas', $user->id_fakultasUser)
                    ->orderBy('nama', 'asc')
                    ->get();
                break;
        }

        // Kirim data ke view dengan lebih rapi
        return view('admin.kurikulum.list', array_merge(
            [
                'kurikulumsForTable' => $kurikulumsForTable,
                'editMode' => $editMode,
                'editedId' => $editedId
            ],
            $filterData,
            $dataForAddForm
        ));
    }

    public function cancelEdit()
    {
        return redirect()->route($this->getRouteByAuthority())
            ->with('edit_mode', false)
            ->with('edited_id', null);
    }

    public function enterEditMode(Kurikulum $kurikulum)
    {
        return redirect()->route($this->getRouteByAuthority())
            ->with('edit_mode', true)
            ->with('edited_id', $kurikulum->id);
    }

    public function store(Request $request)
    {
        $userRole = auth()->user()->otoritas->otoritas;

        $rules = [
            'tahun' => ['required', 'integer', 'digits:4'],
        ];

        if ($userRole === 'Admin Universitas') {
            $rules['id_prodi'] = 'required|exists:prodi,id';
        }

        $request->validate($rules, [], [
            'id_prodi' => 'Program Studi',
            'tahun'    => 'Tahun Kurikulum',
            'fakultas' => 'Fakultas',
        ]);

        try {
            $prodiId = ($userRole === 'Admin Universitas')
                        ? $request->id_prodi
                        : auth()->user()->id_prodiUser;

            Kurikulum::create([
                'tahun' => $request->tahun,
                'id_prodi' => $prodiId,
            ]);
            return redirect()->route($this->getRouteByAuthority())->with('success', 'Kurikulum berhasil ditambahkan!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->with('error', 'Terjadi kesalahan, tahun kurikulum ' . $request->tahun . ' untuk prodi tersebut sudah ada.');
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan pada database.');
        }
    }

    // public function edit($tahun)
    // {
    //     $tahuns = Crypt::decrypt($tahun);
    //     $kurikulums = Kurikulum::where('id_prodi', auth()->user()->id_prodiUser);
    //     $kurikulum = Kurikulum::join('prodi', 'kurikulums.id_prodi', '=', 'prodi.id')
    //         ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
    //         ->where('tahun', $tahuns)
    //         ->where('fakultas.id_universitas', auth()->user()->id_universitasUser)
    //         ->firstOrFail();

    //     return view('admin.kurikulum.edit', compact('kurikulums', 'kurikulum'));
    // }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        $userRole = auth()->user()->otoritas->otoritas;
        $rules = [
            'tahun' => ['required', 'integer', 'digits:4'],
        ];
        if ($userRole === 'Admin Universitas') {
            $rules['id_prodi'] = ['required', 'exists:prodi,id'];
        }
        $request->validate($rules, [], [
            'tahun'    => 'Tahun Kurikulum',
            'id_prodi' => 'Program Studi',
        ]);

        try {
            $this->authorizeKurikulumAccess($kurikulum);
            $prodiIdToCheck = $request->id_prodi ?? $kurikulum->id_prodi;
            // Cek duplikasi
            $existing = Kurikulum::where('tahun', $request->tahun)
                ->where('id_prodi', $prodiIdToCheck)
                ->where('id', '!=', $kurikulum->id)
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->with('error', 'Kombinasi Tahun dan Program Studi tersebut sudah ada.')
                    ->with('edit_mode', true)
                    ->with('edited_id', $kurikulum->id);
            }

            $updateData = [
                'tahun' => $request->tahun,
            ];

            if ($request->has('id_prodi')) {
                $updateData['id_prodi'] = $request->id_prodi;
            }

            $kurikulum->update($updateData);

            return redirect()->route($this->getRouteByAuthority())
                ->with('success', 'Kurikulum berhasil diubah!')
                ->with('edit_mode', false)
                ->with('edited_id', null);
        } catch (AuthorizationException $e) {
            return redirect()->route($this->getRouteByAuthority())->with('error', 'Anda tidak memiliki wewenang untuk aksi ini.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', value: 'Gagal memperbarui kurikulum. Terjadi kesalahan.')
                ->with('edit_mode', true)
                ->with('edited_id', $kurikulum->id);
        }
    }

    public function delete(Kurikulum $kurikulum)
    {
        try {
            // Otorisasi: pastikan user berhak menghapus kurikulum ini
            $this->authorizeKurikulumAccess($kurikulum);
            $kurikulum->delete();
            return redirect()->route($this->getRouteByAuthority())->with('success', 'Kurikulum berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'Gagal menghapus. Kurikulum ini masih memiliki data CPL/MK terkait.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kurikulum. ' . $e->getMessage());
        }
    }

    private function authorizeKurikulumAccess($kurikulum)
    {
        $user = auth()->user();
        $otoritas = $user->otoritas->otoritas;

        if ($otoritas === 'Admin Universitas') {
            if ($kurikulum->prodi->fakultas->id_universitas != $user->id_universitasUser) {
                throw new AuthorizationException;
            }
        } elseif ($otoritas === 'Kepala Program Studi') {
            if ($kurikulum->id_prodi != $user->id_prodiUser) {
                throw new AuthorizationException;
            }
        }
    }

    private function getRouteByAuthority(): string
    {
        $routes = [
            'Admin' => 'admin.list-kurikulum',
            'Admin Universitas' => 'admin-universitas.list-kurikulum',
            'Kepala Program Studi' => 'kepala-program-studi.list-kurikulum',
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }
}
