<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\CPMK;
use App\Models\CplMkCpmkPenilaian;
use App\Models\Fakultas;
use App\Models\Kurikulum;
use App\Models\InstrumenPenilaian;
use App\Models\MetodePenilaian;
use App\Models\MK;
use App\Models\PenilaianInstrumen;
use App\Models\PenilaianMetode;
use App\Models\Prodi;
use Illuminate\Http\Request;

class DaftarAsesmenMkController extends Controller
{
    // Daftar MK (index)
    public function index(Request $request)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        $allowedRoles = [
            'Penjamin Mutu Universitas',
            'Penjamin Mutu Fakultas',
            'Penjamin Mutu Program Studi',
            'Kepala Program Studi',
        ];

        if (!in_array($userOtoritas, $allowedRoles)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        $selectedFakultasId = $request->get('fakultas_id');
        $selectedProdiId    = $request->get('prodi_id');
        $selectedKurikulumId = $request->get('kurikulum_id');

        $fakultasOptions = collect();
        $prodiOptions    = collect();

        if ($userOtoritas === 'Penjamin Mutu Universitas') {
            $fakultasOptions = Fakultas::where('id_universitas', $user->id_universitasUser ?: 1)->get();
            $prodiOptions = $selectedFakultasId
                ? Prodi::where('id_fakultas', $selectedFakultasId)->get()
                : Prodi::all();
        } elseif ($userOtoritas === 'Penjamin Mutu Fakultas') {
            $selectedFakultasId = $user->id_fakultasUser;
            $prodiOptions = Prodi::where('id_fakultas', $selectedFakultasId)->get();
        } else {
            // PM Prodi / Kaprodi — locked to their prodi
            $selectedProdiId = $user->id_prodiUser;
            $userProdi = Prodi::find($selectedProdiId);
            if ($userProdi) {
                $selectedFakultasId = $userProdi->id_fakultas;
            }
        }

        $kurikulumOptions = Kurikulum::orderBy('tahun', 'desc')->get();

        $queryMks = MK::with(['prodi.fakultas', 'kurikulum']);

        if ($selectedProdiId) {
            $queryMks->where('id_prodi', $selectedProdiId);
        } elseif ($selectedFakultasId) {
            $queryMks->whereHas('prodi', fn($q) => $q->where('id_fakultas', $selectedFakultasId));
        }

        if ($selectedKurikulumId) {
            $queryMks->where('id_kurikulum', $selectedKurikulumId);
        }

        // Hanya tampilkan MK yang sudah memiliki asesmen
        $queryMks->has('cplMkCpmkPenilaians');

        // Hitung jumlah asesmen per MK dan tambahkan pagination
        $mks = $queryMks->withCount(['cplMkCpmkPenilaians as jumlah_asesmen'])
            ->paginate(10)
            ->withQueryString();

        return view('penjamin-mutu.asesmen.daftar_asesmen_mk', compact(
            'userOtoritas',
            'mks',
            'fakultasOptions',
            'prodiOptions',
            'kurikulumOptions',
            'selectedFakultasId',
            'selectedProdiId',
            'selectedKurikulumId'
        ));
    }

    // Hapus semua asesmen pada suatu mata kuliah
    public function destroyAll(Request $request, $mkKode)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        $allowedDeleteRoles = [
            'Penjamin Mutu Program Studi',
            'Kepala Program Studi',
        ];

        if (!in_array($userOtoritas, $allowedDeleteRoles)) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus seluruh asesmen pada mata kuliah ini.');
        }

        $penilaians = CplMkCpmkPenilaian::where('mk_kode', $mkKode)->get();

        foreach ($penilaians as $penilaian) {
            foreach ($penilaian->penilaianMetode as $pm) {
                $pm->instrumens()->delete();
                $pm->delete();
            }
            $penilaian->penilaianInstrumen()->delete();
            $penilaian->delete();
        }

        return redirect()->back()->with('success', 'Semua data asesmen untuk mata kuliah ini berhasil dihapus.');
    }

    // Detail asesmen untuk satu MK
    public function show(Request $request, $mkKode)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        $allowedRoles = [
            'Penjamin Mutu Universitas',
            'Penjamin Mutu Fakultas',
            'Penjamin Mutu Program Studi',
            'Kepala Program Studi',
        ];

        if (!in_array($userOtoritas, $allowedRoles)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        $mk = MK::with(['prodi.fakultas', 'kurikulum'])->findOrFail($mkKode);

        $allPenilaiansForSummary = CplMkCpmkPenilaian::where('mk_kode', $mkKode)
            ->with(['cpmk.cpl', 'penilaianMetode.metode'])
            ->get();

        $penilaians = CplMkCpmkPenilaian::where('cpl_mk_cpmk_penilaian.mk_kode', $mkKode)
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->leftJoin('cpmks', 'cpl_mk_cpmk_penilaian.cpmk_id', '=', 'cpmks.id')
            ->select('cpl_mk_cpmk_penilaian.*')
            ->orderBy('cpls.kode', 'asc')
            ->orderBy('cpmks.kode', 'asc')
            ->with(['cpl', 'cpmk', 'penilaianMetode.metode', 'penilaianMetode.instrumens.instrumenPenilaian'])
            ->paginate(10);

        // Ambil CPMK yang terpetakan ke MK ini (via relasi mks / pivot table cpmk_mk)
        $mappedCpmks = CPMK::with('cpl')
            ->whereHas('mks', function ($q) use ($mkKode) {
                $q->where('kode', $mkKode);
            })
            ->get()
            ->unique('id');

        // Jika tidak ada di pivot, sertakan CPMK dari penilaian yang sudah terdaftar
        if ($mappedCpmks->isEmpty() && $allPenilaiansForSummary->isNotEmpty()) {
            $mappedCpmks = $allPenilaiansForSummary->pluck('cpmk')->filter()->unique('id');
        }

        $assessedCpmkIds = $allPenilaiansForSummary->pluck('cpmk_id')->unique()->filter()->toArray();

        $prodiId = $mk->id_prodi ?: auth()->user()->id_prodiUser;
        $allKriterias = InstrumenPenilaian::where('id_prodi', $prodiId)->get();
        if ($allKriterias->isEmpty()) {
            $allKriterias = InstrumenPenilaian::all();
        }
        $allMetodes = MetodePenilaian::where('id_prodi', $prodiId)->get();

        return view('penjamin-mutu.asesmen.detail_asesmen_mk', compact(
            'userOtoritas',
            'mk',
            'penilaians',
            'allPenilaiansForSummary',
            'allKriterias',
            'allMetodes',
            'mappedCpmks',
            'assessedCpmkIds'
        ));
    }

    // Update bobot penilaian instrumen / kriteria per asesmen
    public function update(Request $request, $mkKode, $id)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        if (!in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit asesmen.');
        }

        $penilaian = CplMkCpmkPenilaian::where('mk_kode', $mkKode)->findOrFail($id);

        $checkedKriteriaIds = $request->input('kriteria_penilaian', []);
        $bobotInputs = $request->input('bobot_kriteria', []);

        // Pastikan PenilaianMetode terhubung (gunakan metode pertama atau buat jika belum ada)
        $penilaianMetode = $penilaian->penilaianMetode->first();
        if (!$penilaianMetode) {
            $firstMetode = MetodePenilaian::first();
            if ($firstMetode) {
                $penilaianMetode = PenilaianMetode::create([
                    'cpl_mk_cpmk_penilaian_id' => $penilaian->id,
                    'metode_id' => $firstMetode->id,
                    'bobot' => 0,
                ]);
            }
        }

        if ($penilaianMetode) {
            // Hapus instrumen yang un-checked
            PenilaianInstrumen::where('cpl_mk_cpmk_penilaian_id', $penilaian->id)
                ->whereNotIn('kriteria_id', $checkedKriteriaIds)
                ->delete();

            // Update / Insert instrumen yang checked
            foreach ($checkedKriteriaIds as $kId) {
                $bobotVal = isset($bobotInputs[$kId]) ? (float) $bobotInputs[$kId] : 0;
                PenilaianInstrumen::updateOrCreate(
                    [
                        'cpl_mk_cpmk_penilaian_id' => $penilaian->id,
                        'kriteria_id' => $kId,
                    ],
                    [
                        'penilaian_metode_id' => $penilaianMetode->id,
                        'bobot_metode' => $bobotVal,
                    ]
                );
            }

            // Recalculate PenilaianMetode total bobot
            foreach ($penilaian->penilaianMetode as $pm) {
                $totalBobotMetode = PenilaianInstrumen::where('penilaian_metode_id', $pm->id)->sum('bobot_metode');
                $pm->bobot = $totalBobotMetode;
                $pm->save();
            }
        }

        return redirect()->back()->with('success', 'Data asesmen & bobot kriteria berhasil diperbarui.');
    }

    // Update paket metode & kriteria di dalamnya
    public function updateMetode(Request $request, $mkKode, $pmId)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        if (!in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit metode.');
        }

        $pm = PenilaianMetode::findOrFail($pmId);

        $checkedKriteriaIds = $request->input('kriteria_penilaian', []);

        // Update total bobot metode jika dikirim manual dari form
        if ($request->has('bobot_metode') && $request->input('bobot_metode') !== '') {
            $pm->bobot = (float) $request->input('bobot_metode');
            $pm->save();
        }

        // Hapus instrumen di bawah metode ini yang un-checked
        PenilaianInstrumen::where('penilaian_metode_id', $pm->id)
            ->whereNotIn('kriteria_id', $checkedKriteriaIds)
            ->delete();

        $countKriteria = count($checkedKriteriaIds);
        $fallbackBobotPerKriteria = $countKriteria > 0 ? ($pm->bobot / $countKriteria) : 0;

        // Update / Insert instrumen yang checked
        foreach ($checkedKriteriaIds as $kId) {
            $userKriteriaVal = (isset($bobotInputs[$kId]) && $bobotInputs[$kId] !== '') ? (float) $bobotInputs[$kId] : null;

            $finalBobotVal = ($userKriteriaVal !== null && $userKriteriaVal > 0)
                ? $userKriteriaVal
                : round($fallbackBobotPerKriteria, 2);

            PenilaianInstrumen::updateOrCreate(
                [
                    'cpl_mk_cpmk_penilaian_id' => $pm->cpl_mk_cpmk_penilaian_id,
                    'penilaian_metode_id' => $pm->id,
                    'kriteria_id' => $kId,
                ],
                [
                    'bobot_metode' => $finalBobotVal,
                ]
            );
        }

        return redirect()->back()->with('success', 'Paket metode & bobot kriteria berhasil diperbarui.');
    }

    // Hapus satu paket metode beserta kriteria di dalamnya
    public function destroyMetode(Request $request, $mkKode, $pmId)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        if (!in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            abort(403, 'Hanya PM Prodi / Kaprodi yang dapat menghapus metode.');
        }

        $pm = PenilaianMetode::findOrFail($pmId);
        $penilaianId = $pm->cpl_mk_cpmk_penilaian_id;

        // Hapus kriteria di bawah metode ini
        $pm->instrumens()->delete();
        $pm->delete();

        // Jika tidak ada metode tersisa di penilaian ini, hapus CplMkCpmkPenilaian parent-nya
        $remainingMetodeCount = PenilaianMetode::where('cpl_mk_cpmk_penilaian_id', $penilaianId)->count();
        if ($remainingMetodeCount === 0) {
            CplMkCpmkPenilaian::where('id', $penilaianId)->delete();
        }

        return redirect()->back()->with('success', 'Paket metode & kriteria penilaian berhasil dihapus.');
    }

    // Hapus satu baris CplMkCpmkPenilaian (beserta penilaian_metode & penilaian_instrumen-nya via cascade)
    public function destroy(Request $request, $mkKode, $id)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        if ($userOtoritas !== 'Penjamin Mutu Program Studi') {
            abort(403, 'Hanya PM Prodi yang dapat menghapus asesmen.');
        }

        $penilaian = CplMkCpmkPenilaian::where('mk_kode', $mkKode)->findOrFail($id);

        // Hapus instrumen & metode terkait terlebih dulu
        foreach ($penilaian->penilaianMetode as $pm) {
            $pm->instrumens()->delete();
            $pm->delete();
        }
        $penilaian->penilaianInstrumen()->delete();
        $penilaian->delete();

        return redirect()->back()->with('success', 'Data asesmen berhasil dihapus.');
    }
}
