<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('jenis_semester', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.tahun_ajaran.index', compact('tahunAjarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2000|max:2100',
            'jenis_semester' => [
                'required',
                'in:Ganjil,Genap',
                Rule::unique('tahun_ajaran', 'jenis_semester')->where(function ($query) use ($request) {
                    return $query->where('tahun', $request->tahun);
                }),
            ],
        ], [
            'jenis_semester.unique' => 'Tahun ajaran dan jenis semester tersebut sudah ada.',
        ]);

        try {
            TahunAjaran::create([
                'tahun' => $request->tahun,
                'jenis_semester' => $request->jenis_semester,
            ]);

            return redirect()->back()->with('success', 'Tahun Ajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Gagal menambahkan Tahun Ajaran: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        $request->validate([
            'tahun' => 'required|integer|min:2000|max:2100',
            'jenis_semester' => [
                'required',
                'in:Ganjil,Genap',
                Rule::unique('tahun_ajaran', 'jenis_semester')
                    ->where(function ($query) use ($request) {
                        return $query->where('tahun', $request->tahun);
                    })
                    ->ignore($tahunAjaran->id),
            ],
        ], [
            'jenis_semester.unique' => 'Tahun ajaran dan jenis semester tersebut sudah ada.',
        ]);

        try {
            $tahunAjaran->update([
                'tahun' => $request->tahun,
                'jenis_semester' => $request->jenis_semester,
            ]);

            return redirect()->back()->with('success', 'Tahun Ajaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Gagal memperbarui Tahun Ajaran: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $tahunAjaran = TahunAjaran::findOrFail($id);
            $tahunAjaran->delete();

            return redirect()->back()->with('success', 'Tahun Ajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Gagal menghapus Tahun Ajaran (mungkin sedang digunakan data lain).');
        }
    }
}
