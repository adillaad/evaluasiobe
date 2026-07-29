<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $beritas = Berita::where('status', 'published')->paginate(5);

        if (auth()->check() && request()->is('admin/berita')) {
            $beritas = Berita::orderByRaw("FIELD(status, 'published', 'draft')")->get();
            return view('admin.berita.index', compact('beritas'));
        }

        return view('guest.berita.index', compact('beritas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.berita.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'judul.required' => 'Judul wajib diisi.',
            'konten.required' => 'Konten wajib diisi.',
            'gambar.required' => 'Gambar wajib diunggah.',
            'gambar.image' => 'File yang diunggah harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus berformat jpeg, png, atau jpg.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ];

        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], $messages);

        // Buat slug dari judul
        $slug = Str::slug($request->judul);

        // Cek apakah slug sudah ada di database
        $count = Berita::where('slug', 'LIKE', "$slug%")->count();

        // Jika slug sudah ada, tambahkan angka di akhir untuk membedakan
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        Berita::create([
            'judul' => $request->judul,
            'slug' => $slug, // Simpan slug
            'konten' => $request->konten,
            'gambar' => $request->file('gambar')->store('berita', 'public'),
            'author_id' => auth()->id(),
        ]);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $berita = Berita::where('slug', $slug)->firstOrFail();

        if (auth()->check() && auth()->user()->otoritas->otoritas == 'Admin') {
            return view('admin.berita.show', compact('berita'));
        } else {
            return view('guest.berita.show', compact('berita'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\Http\Response
     */
    public function edit($encryptedId)
    {
        // Dekripsi ID yang diterima dari URL
        $id = decrypt($encryptedId);

        // Ambil berita berdasarkan ID yang telah didekripsi
        $berita = Berita::findOrFail($id);

        return view('admin.berita.edit', compact('berita'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $encryptedId)
    {
        // Dekripsi ID yang diterima dari URL
        $id = decrypt($encryptedId);

        // Ambil berita berdasarkan ID yang telah didekripsi
        $berita = Berita::findOrFail($id);

        $messages = [
            'judul.required' => 'Judul wajib diisi.',
            'konten.required' => 'Konten wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'gambar.image' => 'File yang diunggah harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus berformat jpeg, png, atau jpg.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ];

        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'status' => 'required|in:draft,published',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], $messages);

        // Periksa apakah judul berubah
        if ($request->judul !== $berita->judul) {
            // Buat slug baru
            $slug = Str::slug($request->judul);

            // Cek apakah slug sudah ada di database
            $count = Berita::where('slug', 'LIKE', "$slug%")->where('id', '!=', $berita->id)->count();

            // Jika slug sudah ada, tambahkan angka di akhir untuk membedakan
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }
        } else {
            $slug = $berita->slug; // Gunakan slug lama jika judul tidak berubah
        }

        $data = [
            'judul' => $request->judul,
            'slug' => $slug,
            'konten' => $request->konten,
            'status' => $request->status,
        ];

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        $berita->update($data);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\Http\Response
     */
    public function destroy(Berita $berita)
    {
        // Hapus gambar
        if ($berita->gambar) {
            Storage::delete($berita->gambar);
        }

        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }
}
