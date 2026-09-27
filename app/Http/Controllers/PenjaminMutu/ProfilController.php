<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Models\CPL;
use App\Models\CPMK;
use App\Models\Kurikulum;
use App\Models\profesi;
use App\Models\ProfilLulusan;
use App\Models\Prodi;
use App\Traits\UniversityFilterTrait;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;

class ProfilController extends Controller
{
    use UniversityFilterTrait;

    public function indexListProfil(Request $request)
    {
        return view('penjamin-mutu.profil.listProfilLulusan', $this->getFilterData($request));
    }

    public function indexProfilProfesi(Request $request)
    {
        $user        = auth()->user();
        $kurikulums  = Kurikulum::where('id_prodi', $user->id_prodiUser)->get();
        $listProfesi = Profesi::forUser($user)->get();

        return view('penjamin-mutu.profil.listProfilLulusanprof', array_merge(
            $this->getFilterData($request),
            compact('kurikulums', 'listProfesi')
        ));
    }

    public function indexListProfesi(Request $request)
    {
        $profesi = Profesi::forUser(auth()->user())
            ->when($request->kurikulum_id, fn($q) => $q->where('kurikulum_id', $request->kurikulum_id))
            ->get();

        return view('penjamin-mutu.profil.listProfesi', array_merge(
            $this->getFilterData($request),
            ['profesi' => $profesi]
        ));
    }

    public function indexPemetaanCPMKProf(Request $request)
    {
        $user = auth()->user();
        $query = CPMK::forUser($user);
        if ($request->filled('kurikulum_id')) {
            $query->where('id_kurikulum', $request->kurikulum_id);
        }
        $cpmks = $query->get();
        $kurikulums = $this->kurikulumsForUser();

        return view('penjamin-mutu.profil.listProfesiCpmk', compact('cpmks', 'kurikulums'));
    }

    public function indexProfilMK(Request $request)
    {
        $raw = ProfilLulusan::queryProfilMK(auth()->user(), $request)
            ->orderBy('kurikulums.tahun', 'desc')
            ->orderBy('profil_lulusan.kode')
            ->get();

        $groupedByKurikulum = $raw->groupBy(function ($item) {
            return $item->kurikulum_tahun ? 'Kurikulum ' . $item->kurikulum_tahun : 'Tanpa Kurikulum';
        })->map(function ($itemsInKurikulum) {
            return $itemsInKurikulum->groupBy('profil_kode');
        });

        return view('penjamin-mutu.profil.profil-MK', array_merge(
            $this->getFilterData($request),
            ['groupedByKurikulum' => $groupedByKurikulum]
        ));
    }
    public function readListProfil(Request $request)
    {
        $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
        $listProfil = ProfilLulusan::with(['kurikulum', 'prodi.fakultas'])
            ->filterOtoritas(auth()->user(), $request->kurikulum_id)
            ->get();

        $filterData = $this->getFilterData($request);

        return view('penjamin-mutu.profil.readListProfil', array_merge(
            compact('listProfil', 'userOtoritas'),
            $filterData
        ));
    }

    // Download Template Excel Profil Lulusan
    public function downloadTemplateProfil()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import PL');

        // Header
        $sheet->setCellValue('A1', 'Tahun Kurikulum');
        $sheet->setCellValue('B1', 'Nama Profil Karir');
        $sheet->setCellValue('C1', 'Deskripsi / Graduate Profile');

        // Style Header
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Sample data
        $sheet->setCellValue('A2', '2025');
        $sheet->setCellValue('B2', 'Software Engineer');
        $sheet->setCellValue('C2', 'Lulusan yang mampu merancang, membangun, dan menguji sistem perangkat lunak secara profesional.');

        $sheet->setCellValue('A3', '2025');
        $sheet->setCellValue('B3', 'Data Analyst');
        $sheet->setCellValue('C3', 'Lulusan yang mampu mengolah dan menganalisis data untuk mendukung pengambilan keputusan.');

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Template_Import_Profil_Lulusan.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // Import Excel Profil Lulusan
    public function importExcelProfil(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'id_kurikulum' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $id_prodi_user = $user->id_prodiUser;

        $file = $request->file('excel_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File Excel tidak valid.');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $dataRows = $worksheet->toArray();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        if (count($dataRows) <= 1) {
            return redirect()->back()->with('error', 'File Excel kosong atau hanya berisi header.');
        }

        // Cek struktur header (apakah ada kolom Kode Profil atau tidak)
        $headerRow = $dataRows[0] ?? [];
        $hasKodeColumn = isset($headerRow[1]) && str_contains(strtolower((string)$headerRow[1]), 'kode');

        // Drop header
        unset($dataRows[0]);

        $importedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($dataRows as $row) {
                if ($hasKodeColumn) {
                    $rawTahunKurikulum = trim((string)($row[0] ?? ''));
                    $rawKode           = str_replace('-', '', trim((string)($row[1] ?? '')));
                    $namaProfil        = trim((string)($row[2] ?? ''));
                    $deskripsi         = trim((string)($row[3] ?? ''));
                } else {
                    $rawTahunKurikulum = trim((string)($row[0] ?? ''));
                    $rawKode           = '';
                    $namaProfil        = trim((string)($row[1] ?? ''));
                    $deskripsi         = trim((string)($row[2] ?? ''));
                }

                if (empty($rawKode) && empty($namaProfil) && empty($deskripsi)) {
                    continue;
                }

                // 1. Tentukan Kurikulum
                $kurikulumObj = null;
                $tahunClean = preg_replace('/[^\d]/', '', $rawTahunKurikulum);

                if (!empty($tahunClean)) {
                    $kurikulumObj = Kurikulum::where('id_prodi', $id_prodi_user)
                        ->where('tahun', $tahunClean)
                        ->first();
                    if (!$kurikulumObj) {
                        $kurikulumObj = Kurikulum::where('tahun', $tahunClean)->first();
                    }
                }

                if (!$kurikulumObj && $request->id_kurikulum) {
                    $kurikulumObj = Kurikulum::find($request->id_kurikulum);
                }

                if (!$kurikulumObj) {
                    $kurikulumObj = Kurikulum::where('id_prodi', $id_prodi_user)->orderBy('tahun', 'desc')->first()
                        ?: Kurikulum::orderBy('tahun', 'desc')->first();
                }

                if (!$kurikulumObj) {
                    continue;
                }

                // Check existing PL by prodi, kurikulum, and namaProfil / kode
                $existingPl = ProfilLulusan::where('id_prodi', $id_prodi_user)
                    ->where('kurikulum_id', $kurikulumObj->id)
                    ->where(function($q) use ($rawKode, $namaProfil) {
                        if (!empty($rawKode)) {
                            $q->where('kode', $rawKode);
                        }
                        if (!empty($namaProfil)) {
                            $q->orWhere('namaProfil', $namaProfil)->orWhere('jenis', $namaProfil);
                        }
                    })->first();

                if ($existingPl) {
                    $existingPl->update([
                        'namaProfil' => !empty($namaProfil) ? $namaProfil : ($existingPl->namaProfil ?: $existingPl->jenis),
                        'jenis'      => !empty($namaProfil) ? $namaProfil : ($existingPl->jenis ?: $existingPl->namaProfil),
                        'deskripsi'  => !empty($deskripsi) ? $deskripsi : $existingPl->deskripsi,
                    ]);
                    $updatedCount++;
                } else {
                    $kode = !empty($rawKode)
                        ? $rawKode
                        : ProfilLulusan::generateKode($id_prodi_user, $kurikulumObj->id);

                    ProfilLulusan::create([
                        'kurikulum_id' => $kurikulumObj->id,
                        'id_prodi'     => $id_prodi_user,
                        'kode'         => $kode,
                        'namaProfil'   => !empty($namaProfil) ? $namaProfil : $kode,
                        'jenis'        => !empty($namaProfil) ? $namaProfil : $kode,
                        'deskripsi'    => !empty($deskripsi) ? $deskripsi : ($namaProfil ?: $kode),
                    ]);
                    $importedCount++;
                }
            }

            DB::commit();

            $msg = "Berhasil memproses impor Profil Lulusan: {$importedCount} data baru ditambahkan";
            if ($updatedCount > 0) {
                $msg .= ", {$updatedCount} data diperbarui.";
            } else {
                $msg .= ".";
            }

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengimpor data Profil Lulusan: ' . $e->getMessage());
        }
    }

    public function readListProfilProf(Request $request)
    {
        $user        = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? '';
        $kurikulums = Kurikulum::where('id_prodi', $user->id_prodiUser)->get();

        $query = ProfilLulusan::query()
            ->join('kurikulums', 'profil_lulusan.kurikulum_id', '=', 'kurikulums.id')
            ->join('prodi', 'profil_lulusan.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('profil_lulusan.*');
        $otoritas = $user->otoritas->otoritas;

        if ($otoritas === 'Penjamin Mutu Universitas') {
            $query->where('universitas.id', $user->id_universitasUser);
        } elseif ($otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', $user->id_fakultasUser);
        } else {
            $query->where('profil_lulusan.id_prodi', $user->id_prodiUser);
        }
        if ($request->filled('kurikulum_id')) {
            $query->where('profil_lulusan.kurikulum_id', $request->kurikulum_id);
        }

        $listProfil  = $query->get();
        $listProfesi = Profesi::forUser($user)
            ->when($request->filled('kurikulum_id'), fn($q) => $q->where('profesi.kurikulum_id', $request->kurikulum_id))
            ->get();

        $view = $user->prodi->is_aptikom
            ? 'penjamin-mutu.profil.readListProfilProfAptikom'
            : 'penjamin-mutu.profil.readListProfilProf';

        return view($view, compact('listProfil', 'listProfesi', 'kurikulums', 'userOtoritas'));
    }

    public function readListProfesi(Request $request)
    {
        $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
        $profesi = Profesi::with('kurikulum')
            ->forUser(auth()->user())
            ->when($request->kurikulum_id, fn($q) => $q->where('kurikulum_id', $request->kurikulum_id))
            ->get();

        return view('penjamin-mutu.profil.readListProfesi', compact('profesi', 'userOtoritas'));
    }
    public function createListProfil()
    {
        $kurikulums = $this->kurikulumsForUser();
        $view = auth()->user()->prodi->is_aptikom
            ? 'penjamin-mutu.profil.createListProfilAptikom'
            : 'penjamin-mutu.profil.createListProfil';

        return view($view, compact('kurikulums'));
    }

    public function createListProfesi(Request $request)
    {
        $kurikulums = $this->kurikulumsForUser();
        $view = $request->ajax() ? 'penjamin-mutu.profil.formProfesi' : 'penjamin-mutu.profil.createlistProfesi';

        return $request->ajax()
            ? view($view, compact('kurikulums'))->render()
            : view($view, compact('kurikulums'));
    }
    public function storeListProfil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'namaProfil'   => 'required|string|unique:profil_lulusan',
            'deskripsi'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Gagal menyimpan data', 'validation_errors' => $validator->errors()], 422);
        }

        ProfilLulusan::create(array_merge($request->all(), [
            'id_prodi' => auth()->user()->id_prodiUser,
            'kode'     => ProfilLulusan::generateKode(auth()->user()->id_prodiUser, $request->kurikulum_id),
        ]));

        return response()->json(['message' => 'Data berhasil disimpan'], 201);
    }

    public function storeListProfilAptikom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'jenis'        => 'required|string',
            'deskripsi'    => 'required|string',
            'status'       => 'required|in:wajib,pilihan',
            'acuan'        => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Gagal menyimpan data', 'validation_errors' => $validator->errors()], 422);
        }

        ProfilLulusan::create(array_merge($request->all(), [
            'id_prodi' => auth()->user()->id_prodiUser,
            'kode'     => ProfilLulusan::generateKode(auth()->user()->id_prodiUser, $request->kurikulum_id),
        ]));

        return response()->json(['message' => 'Data berhasil disimpan'], 201);
    }

    public function storeListProfesi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'nama'         => 'required|string|unique:profesi',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? response()->json(['errors' => $validator->errors()], 422)
                : redirect()->back()->withInput()->with('error', implode(', ', $validator->errors()->all()));
        }

        Profesi::create(array_merge($request->all(), [
            'id_prodi' => auth()->user()->id_prodiUser,
        ]));

        return $request->ajax()
            ? response()->json(['message' => 'Profesi berhasil ditambahkan'], 201)
            : redirect()->back()->with('success', 'Profesi berhasil ditambahkan!');
    }

    public function storeProfesiCPMK(Request $request)
    {
        $request->validate([
            'cpmk_id'    => 'required|exists:cpmks,id',
            'profesis'   => 'required|array|min:1',
            'profesis.*' => 'exists:profesi,id',
        ]);

        CPMK::findOrFail($request->cpmk_id)
            ->profesis()
            ->syncWithoutDetaching($request->profesis);

        return redirect()->back()->with('success', 'Pemetaan berhasil disimpan');
    }
    public function showProfil($id)
    {
        $profil     = ProfilLulusan::findOrFail($id);
        $kurikulums = $this->kurikulumsForUser();
        $view = auth()->user()->prodi->is_aptikom
            ? 'penjamin-mutu.profil.editProfilAptikom'
            : 'penjamin-mutu.profil.editProfil';

        return view($view, compact('profil', 'kurikulums'));
    }

    public function showProfesi($id)
    {
        $profesi    = Profesi::findOrFail($id);
        $kurikulums = $this->kurikulumsForUser();
        return view('penjamin-mutu.profil.formProfesi', compact('profesi', 'kurikulums'));
    }

    public function addProfesiCpmk(Request $request)
    {
        $user     = auth()->user();
        $profesis = Profesi::forUser($user)->get();
        $cpmks    = CPMK::forUser($user)->get();
        $selected = $request->cpmk_id
            ? DB::table('profesi_cpmk')->where('cpmk_id', $request->cpmk_id)->pluck('profesi_id')->toArray()
            : [];

        return view('penjamin-mutu.profil.add_profesi_cpmk', compact('profesis', 'cpmks', 'selected'));
    }

    public function editProfesiCpmk($id)
    {
        $cpmk     = CPMK::with('profesis')->findOrFail($id);
        $profesis = Profesi::where('id_prodi', auth()->user()->id_prodiUser)->get();
        $selected = $cpmk->profesis->pluck('id')->toArray();

        return view('penjamin-mutu.profil.edit_profesi_cpmk', [
            'cpmk'         => $cpmk,
            'profesis'     => $profesis,
            'selected'     => $selected,
            'userOtoritas' => auth()->user()->otoritas->otoritas,
        ]);
    }
    public function updateProfil(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'namaProfil' => 'required|string|unique:profil_lulusan,namaProfil,' . $id,
            'deskripsi'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Gagal menyimpan data', 'validation_errors' => $validator->errors()], 422);
        }

        ProfilLulusan::findOrFail($id)->update($request->only('namaProfil', 'deskripsi'));
        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function updateProfilAptikom(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'jenis'        => 'required|string',
            'deskripsi'    => 'required|string',
            'status'       => 'required|in:wajib,pilihan',
            'acuan'        => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Gagal menyimpan data', 'validation_errors' => $validator->errors()], 422);
        }

        ProfilLulusan::findOrFail($id)->update(
            $request->only('kurikulum_id', 'jenis', 'deskripsi', 'status', 'acuan')
        );

        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function updateProfesi(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama'         => 'required|string|unique:profesi,nama,' . $id,
            'kurikulum_id' => 'required|exists:kurikulums,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Profesi::findOrFail($id)->update($request->only('nama', 'kurikulum_id'));
        return response()->json(['message' => 'Profesi berhasil diperbarui']);
    }

    public function updateProfesiCpmk(Request $request, $id)
    {
        $request->validate([
            'profesis'   => 'required|array',
            'profesis.*' => 'exists:profesi,id',
        ]);

        CPMK::findOrFail($id)->profesis()->sync($request->profesis);
        return redirect()->back()->with('success', 'Pemetaan berhasil diupdate');
    }
    public function deleteProfil($id)
    {
        ProfilLulusan::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function deleteProfesi($id)
    {
        Profesi::findOrFail($id)->delete();
        return response()->json(['message' => 'Profesi berhasil dihapus']);
    }

    public function deleteProfesiCpmk($cpmk_id, $profesi_id)
    {
        CPMK::findOrFail($cpmk_id)->profesis()->detach($profesi_id);
        return redirect()->back()->with('success', 'Data pemetaan CPMK-Profesi berhasil dihapus');
    }
    public function evaluasiCpmk()
    {
        $user     = auth()->user();
        $otoritas = $user->otoritas->otoritas;

        $mks = DB::table('mks')
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->when(
                $otoritas === 'Penjamin Mutu Universitas',
                fn($q) => $q->where('fakultas.id_universitas', $user->id_universitasUser)
            )
            ->when(
                $otoritas === 'Penjamin Mutu Fakultas',
                fn($q) => $q->where('fakultas.id', $user->id_fakultasUser)
            )
            ->when(
                in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']),
                fn($q) => $q->where('prodi.id', $user->id_prodiUser)
            )
            ->select('mks.kode', 'mks.nama')
            ->orderBy('kode')
            ->get();

        $cplMk = DB::table('mk_cpl')
            ->join('cpls', 'mk_cpl.cpl_id', '=', 'cpls.id')
            ->whereIn('mk_kode', $mks->pluck('kode'))
            ->select('mk_kode', 'cpls.kode as cpl_kode')
            ->get()
            ->groupBy('mk_kode');

        $cpmkRelations = DB::table('cpmks')
            ->join('cpl_mk_cpmk_penilaian', 'cpmks.id', '=', 'cpl_mk_cpmk_penilaian.cpmk_id')
            ->whereIn('cpl_mk_cpmk_penilaian.mk_kode', $mks->pluck('kode'))
            ->select('cpl_mk_cpmk_penilaian.mk_kode', 'cpmks.kode as cpmk_kode', 'cpmks.cpl_id')
            ->get()
            ->groupBy('mk_kode');

        $mahasiswas = DB::table('mutus')
            ->where('id_prodi', $user->id_prodiUser)
            ->select('NPM', 'nama_mhs')
            ->distinct()
            ->orderBy('nama_mhs')
            ->get();

        $nilaiCpmk = DB::table('mutus')
            ->where('id_prodi', $user->id_prodiUser)
            ->select('NPM', 'cpmk', DB::raw('ROUND(SUM(examWeight / 100 * Nilai), 2) as nilai'))
            ->groupBy('NPM', 'cpmk')
            ->get()
            ->groupBy('NPM');

        $nilaiMkMahasiswa = [];
        foreach ($mahasiswas as $mhs) {
            foreach ($mks as $mk) {
                $total = 0;
                foreach ($cpmkRelations[$mk->kode] ?? collect() as $cpmkItem) {
                    $record = $nilaiCpmk[$mhs->NPM]?->firstWhere('cpmk', $cpmkItem->cpmk_kode);
                    $total += $record->nilai ?? 0;
                }
                $nilaiMkMahasiswa[$mhs->NPM][$mk->kode] = round($total, 2);
            }
        }

        return view('penjamin-mutu.penilaian-evaluasi-cpmk', compact(
            'mahasiswas',
            'mks',
            'cplMk',
            'cpmkRelations',
            'nilaiCpmk',
            'nilaiMkMahasiswa'
        ));
    }
    private function kurikulumsForUser()
    {
        return Kurikulum::where('id_prodi', auth()->user()->id_prodiUser)->get();
    }
}
