<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\CPMK;
use App\Models\ProfilLulusan;
use App\Models\Prodi;
use App\Traits\UniversityFilterTrait;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfilPdfController extends Controller
{
    use UniversityFilterTrait;

    public function printProfesiCpmk()
    {
        $cpmks = CPMK::forUser(auth()->user())->get();
        $prodi = Prodi::find(auth()->user()->id_prodiUser);
        return view('penjamin-mutu.profil.print-profesi-cpmk', compact('cpmks', 'prodi'));
    }

    public function generatePDFProfesiCpmk()
    {
        $cpmks = CPMK::forUser(auth()->user())->get();
        $prodi = Prodi::find(auth()->user()->id_prodiUser);

        return SnappyPdf::loadView('penjamin-mutu.profil.pdf-profesi-cpmk', compact('cpmks', 'prodi'))
            ->setPaper('A4', 'portrait')
            ->download('Laporan_Pemetaan_Profesi_CPMK.pdf');
    }

    public function printCplCpmkMkProfesi()
    {
        [$cpls, $prodi] = $this->cplCpmkMkData();
        return view('penjamin-mutu.profil.print-cpl-cpmk-mk-profesi', compact('cpls', 'prodi'));
    }

    public function generatePdfCplCpmkMkProfesi()
    {
        [$cpls] = $this->cplCpmkMkData();
        return SnappyPdf::loadView('penjamin-mutu.profil.cpl-cpmk-mk-profesi-pdf', compact('cpls'))
            ->setPaper('A4', 'landscape')
            ->download('Pemetaan_CPL_CPMK_MK_Profesi.pdf');
    }

    public function cplCpmkMkProfesi()
    {
        [$cpls] = $this->cplCpmkMkData();
        return view('penjamin-mutu.profil.cpl-cpmk-mk-profesi', compact('cpls'));
    }

    public function generatePDFProfilMK(Request $request)
    {
        try {
            $grouped = ProfilLulusan::queryProfilMK(auth()->user(), $request)
                ->orderBy('profil_lulusan.kode')
                ->get()
                ->groupBy('profil_kode');

            $meta = $this->resolveEntityNames(auth()->user(), $request);

            $pdf = SnappyPdf::loadView('pdf.pdf-profil-mk', array_merge($meta, [
                'grouped'      => $grouped,
                'generated_at' => now()->format('d F Y H:i:s'),
            ]));

            $pdf->setOption('footer-right', '[page]')
                ->setOption('footer-font-size', 8)
                ->setOption('margin-bottom', '15mm')
                ->setOption('orientation', 'Portrait')
                ->setOption('page-size', 'A4');

            return $pdf->download('Profil-Lulusan-MK-' . now()->format('YmdHis') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Snappy PDF Error generatePDFProfilMK: ' . $e->getMessage());
            return redirect()->back()->with('failed', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    public function printProfilMK(Request $request)
    {
        $grouped = ProfilLulusan::queryProfilMK(auth()->user(), $request)
            ->orderBy('profil_lulusan.kode')
            ->get()
            ->groupBy('profil_kode');

        return view('pdf.print-profil-mk', array_merge(
            $this->resolveEntityNames(auth()->user(), $request),
            ['grouped' => $grouped]
        ));
    }

    private function cplCpmkMkData(): array
    {
        $prodiId = auth()->user()->id_prodiUser;
        $cpls    = CPL::with(['cpmk.profesis', 'cpmk.mks', 'mk'])
            ->where('id_prodi', $prodiId)
            ->get();
        $prodi   = Prodi::find($prodiId);

        return [$cpls, $prodi];
    }

    private function resolveEntityNames($user, Request $request): array
    {
        $otoritas = $user->otoritas->otoritas ?? '';

        $namaUniversitas = $request->universitas_id
            ? DB::table('universitas')->where('id', $request->universitas_id)->value('nama')
            : null;

        $namaFakultas = $request->fakultas_id
            ? DB::table('fakultas')->where('id', $request->fakultas_id)->value('nama')
            : null;

        $namaProdi = $request->prodi_id
            ? DB::table('prodi')->where('id', $request->prodi_id)->value('nama')
            : null;

        $namaKurikulum = $request->kurikulum_id
            ? DB::table('kurikulums')->where('id', $request->kurikulum_id)->value('tahun')
            : null;

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $namaUniversitas ??= DB::table('universitas')->where('id', $user->id_universitasUser)->value('nama');
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $namaFakultas ??= DB::table('fakultas')->where('id', $user->id_fakultasUser)->value('nama');
        } else {
            $namaProdi ??= DB::table('prodi')->where('id', $user->id_prodiUser)->value('nama');
        }

        return compact('otoritas', 'namaUniversitas', 'namaFakultas', 'namaProdi', 'namaKurikulum');
    }
}
