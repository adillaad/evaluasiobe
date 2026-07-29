<?php

namespace App\Http\Controllers\Admin;

use App\Models\Theme;
use App\Models\Universitas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Universitascontroller extends Controller
{
    public function Add()
    {
        return view('admin.universitas.add');
    }

    public function List()
    {
        $universitas = Universitas::all();
        return view('admin.universitas.list', compact('universitas'));
    }

    public function Store(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'nama' => 'required|unique:universitas,nama',
            'logo' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048', // 2MB dalam kilobytes
            ]
        ], [
            'nama.required' => 'Universitas wajib diisi',
            'nama.unique' => 'Universitas sudah digunakan',
            'logo.required' => 'Logo universitas wajib diupload',
            'logo.image' => 'File harus berupa gambar',
            'logo.mimes' => 'Format file harus JPG, PNG, atau GIF',
            'logo.max' => 'Ukuran file maksimal 2MB'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle upload logo
            $fileName = time() . '_' . $request->file('logo')->getClientOriginalName();
            $path = $request->file('logo')->storeAs('images', $fileName, 'public');

            // Create universitas
            $universitas = Universitas::create([
                'nama' => $request->nama,
                'img' => '/storage/' . $path
            ]);

            // Create theme record
            Theme::create([
                'universitas_id' => $universitas->id,
            ]);

            return redirect()
                ->route('admin.list-universitas')
                ->with('success', 'Universitas berhasil ditambahkan');
        } catch (\Exception $e) {
            // Hapus file yang sudah terupload jika ada error
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data')
                ->withInput();
        }
    }

    public function Delete($id)
    {
        DB::beginTransaction(); // Mulai transaksi

        try {
            $universitas = Universitas::findOrFail($id);

            // Hapus theme terkait
            Theme::where('universitas_id', $id)->delete();

            // Simpan path gambar sebelum menghapus universitas
            $imagePath = $universitas->img ? str_replace('/storage/', '', $universitas->img) : null;

            // Hapus universitas
            $universitas->delete();

            // Jika berhasil, hapus file logo
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            DB::commit(); // Konfirmasi transaksi

            return redirect()
                ->route('admin.list-universitas')
                ->with('success', 'Universitas berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }
}
