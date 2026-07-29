<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\UserOtoritas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterMahasiswaController extends Controller
{
    public function index()
    {
        return view('guest.register-mahasiswa');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required|string|exists:mahasiswa,NPM',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'npm.exists' => 'NPM tidak terdaftar di sistem. Silakan hubungi administrator.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $mahasiswa = Mahasiswa::where('NPM', $request->npm)->first();

        $user = User::create([
            'name' => $mahasiswa->Nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_prodiUser' => $mahasiswa->id_prodi,
            'id_fakultasUser' => optional($mahasiswa->prodi)->id_fakultas,
            'id_universitasUser' => optional($mahasiswa->prodi?->fakultas)->id_universitas,
        ]);

        //PENTING: Isi KOLOM 'otoritas' dan 'nama_otoritas'
        UserOtoritas::create([
            'user_id' => $user->id,
            'otoritas' => 'Mahasiswa',          
            'nama_otoritas' => 'Mahasiswa',     
            'active' => true,
        ]);

        return redirect()->route('login')->with('success', 'Akun mahasiswa berhasil diaktifkan! Silakan login.');
    }
}
