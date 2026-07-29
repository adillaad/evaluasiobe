<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Models\Universitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThemeController extends Controller
{
    public function edit()
    {
        $theme = Theme::with('universitas')
            ->where('universitas_id', auth()->user()->id_universitasUser)
            ->first();

        $universitas = Universitas::find(auth()->user()->id_universitasUser);

        if (!$theme) {
            return redirect()->back()->with('error', 'Theme not found');
        }

        return view('admin.theme.edit', compact('theme', 'universitas'));
    }

    public function update(Request $request)
    {
        $theme = Theme::with('universitas')
            ->where('universitas_id', auth()->user()->id_universitasUser)
            ->first();

        if (!$theme) return back()->with('error', 'Theme not found');

        // Ganti warna tema
        if ($request->has('theme_color')) {
            $validated = $request->validate([
                'theme_color' => 'required|string',
            ]);

            $theme->theme_color = $validated['theme_color'];
            $theme->save();
        }

        // Ganti logo universita
        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'required|image|max:2048'
            ]);

            $universitas = Universitas::find($theme->universitas_id);

            // Hapus logo lama jika ada
            if ($universitas->img) {
                $oldPath = str_replace('/storage/', '', $universitas->img);
                Storage::delete('public/' . $oldPath);
            }
            // Simpan logo baru
            $fileName = time() . $request->file('logo')->getClientOriginalName();
            $path = $request->file('logo')->storeAs('images', $fileName, 'public');
            $universitas->img = '/storage/' . $path;
            $universitas->save();
        }

        return back()->with('success', 'Tema Universitas berhasil diperbarui');
    }

}
