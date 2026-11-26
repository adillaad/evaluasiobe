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
        $headers = [
            'A1' => 'Tahun Kurikulum',
            'B1' => 'Kode Profil (Opsional)',
            'C1' => 'Jenis Profil Lulusan',
            'D1' => 'Profil Lulusan (Deskripsi)',
            'E1' => 'Status (Wajib / Pilihan)',
            'F1' => 'Acuan',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Style Header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:F1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:F1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2EFDA');

        // Sample data 1
        $sheet->setCellValue('A2', '2025');
        $sheet->setCellValue('B2', 'PL01');
        $sheet->setCellValue('C2', 'Keterampilan Khusus');
        $sheet->setCellValue('D2', 'Lulusan memiliki kemampuan mendesain, mengimplementasi dan mengevaluasi solusi berbasis computing termasuk kecerdasan buatan yang memenuhi kebutuhan pengguna.');
        $sheet->setCellValue('E2', 'Wajib');
        $sheet->setCellValue('F2', 'KKNI 02, CS 2013, SN Dikti 01');

        // Sample data 2
        $sheet->setCellValue('A3', '2025');
        $sheet->setCellValue('B3', ''); // Kosongkan agar otomatis di-generate kode oleh sistem
        $sheet->setCellValue('C3', 'Keterampilan Umum');
        $sheet->setCellValue('D3', 'Lulusan yang mampu menerapkan pemikiran logis, kritis, sistematis, dan inovatif dalam konteks pengembangan atau implementasi ilmu pengetahuan dan teknologi.');
        $sheet->setCellValue('E3', 'Pilihan');
        $sheet->setCellValue('F3', 'IABEE');

        // Alignments
        $sheet->getStyle('A2:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:E3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D3')->getAlignment()->setWrapText(true);

        // Data validation for Status (Wajib / Pilihan)
        $validation = $sheet->getCell('E2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Pilihan Tidak Valid');
        $validation->setError('Pilih Status dari dropdown: Wajib atau Pilihan.');
        $validation->setPromptTitle('Status Profil Lulusan');
        $validation->setPrompt('Pilih Wajib atau Pilihan');
        $validation->setFormula1('"Wajib,Pilihan"');

        for ($row = 3; $row <= 100; $row++) {
            $sheet->getCell('E' . $row)->setDataValidation(clone $validation);
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(24);
        $sheet->getColumnDimension('C')->setWidth(26);
        $sheet->getColumnDimension('D')->setWidth(55);
        $sheet->getColumnDimension('E')->setWidth(24);
        $sheet->getColumnDimension('F')->setWidth(30);

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

        // Header mapping secara dinamis
        $headerRow = array_map(function ($val) {
            return strtolower(trim((string)$val));
        }, $dataRows[0] ?? []);

        $colTahun = null;
        $colKode = null;
        $colJenis = null;
        $colDeskripsi = null;
        $colStatus = null;
        $colAcuan = null;

        foreach ($headerRow as $idx => $header) {
            if ($colTahun === null && (str_contains($header, 'tahun') || str_contains($header, 'kurikulum'))) {
                $colTahun = $idx;
            } elseif ($colKode === null && str_contains($header, 'kode')) {
                $colKode = $idx;
            } elseif ($colStatus === null && str_contains($header, 'status')) {
                $colStatus = $idx;
            } elseif ($colAcuan === null && (str_contains($header, 'acuan') || str_contains($header, 'referensi'))) {
                $colAcuan = $idx;
            } elseif ($colDeskripsi === null && (str_contains($header, 'deskripsi') || str_contains($header, 'graduate') || (str_contains($header, 'profil') && !str_contains($header, 'karir') && !str_contains($header, 'jenis') && !str_contains($header, 'kode') && !str_contains($header, 'nama')))) {
                $colDeskripsi = $idx;
            } elseif ($colJenis === null && (str_contains($header, 'jenis') || str_contains($header, 'karir') || str_contains($header, 'nama'))) {
                $colJenis = $idx;
            }
        }

        // Fallback jika header tidak terdeteksi via nama
        if ($colTahun === null) $colTahun = 0;
        if ($colKode === null && count($headerRow) >= 6) $colKode = 1;
        if ($colJenis === null) $colJenis = ($colKode !== null) ? 2 : 1;
        if ($colDeskripsi === null) $colDeskripsi = ($colKode !== null) ? 3 : 2;
        if ($colStatus === null && count($headerRow) >= 5) {
            $colStatus = ($colKode !== null) ? 4 : 3;
        }
        if ($colAcuan === null && count($headerRow) >= 6) {
            $colAcuan = ($colKode !== null) ? 5 : 4;
        }

        // Drop header
        unset($dataRows[0]);

        $importedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($dataRows as $row) {
                $rawTahunKurikulum = $colTahun !== null ? trim((string)($row[$colTahun] ?? '')) : '';
                $rawKode           = $colKode !== null ? str_replace('-', '', trim((string)($row[$colKode] ?? ''))) : '';
                $rawJenis          = $colJenis !== null ? trim((string)($row[$colJenis] ?? '')) : '';
                $deskripsi         = $colDeskripsi !== null ? trim((string)($row[$colDeskripsi] ?? '')) : '';
                $rawStatus         = $colStatus !== null ? trim((string)($row[$colStatus] ?? '')) : '';
                $rawAcuan          = $colAcuan !== null ? trim((string)($row[$colAcuan] ?? '')) : '';

                $namaProfil = $rawJenis;

                if (empty($rawKode) && empty($namaProfil) && empty($deskripsi)) {
                    continue;
                }

                // Normalisasi status: wajib / pilihan
                $statusLower = strtolower($rawStatus);
                if (in_array($statusLower, ['pilihan', 'p', 'opsional', 'optional'])) {
                    $status = 'pilihan';
                } else {
                    $status = 'wajib';
                }

                // Normalisasi acuan
                $acuan = !empty($rawAcuan) ? $rawAcuan : '-';

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

                // Check existing PL by prodi, kurikulum, and namaProfil / jenis / kode
                $existingPl = ProfilLulusan::where('id_prodi', $id_prodi_user)
                    ->where('kurikulum_id', $kurikulumObj->id)
                    ->where(function ($q) use ($rawKode, $namaProfil) {
                        $hasCond = false;
                        if (!empty($rawKode)) {
                            $q->where('kode', $rawKode);
                            $hasCond = true;
                        }
                        if (!empty($namaProfil)) {
                            if ($hasCond) {
                                $q->orWhere('namaProfil', $namaProfil)->orWhere('jenis', $namaProfil);
                            } else {
                                $q->where(function ($sq) use ($namaProfil) {
                                    $sq->where('namaProfil', $namaProfil)->orWhere('jenis', $namaProfil);
                                });
                            }
                        }
                    })->first();

                if ($existingPl) {
                    $updateData = [
                        'namaProfil' => !empty($namaProfil) ? $namaProfil : ($existingPl->namaProfil ?: $existingPl->jenis),
                        'jenis'      => !empty($namaProfil) ? $namaProfil : ($existingPl->jenis ?: $existingPl->namaProfil),
                        'deskripsi'  => !empty($deskripsi) ? $deskripsi : $existingPl->deskripsi,
                    ];
                    if (!empty($rawStatus)) {
                        $updateData['status'] = $status;
                    }
                    if (!empty($rawAcuan)) {
                        $updateData['acuan'] = $acuan;
                    }
                    $existingPl->update($updateData);
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
                        'status'       => $status,
                        'acuan'        => $acuan,
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

        $userProdiId = $user->id_prodiUser;
        $mks = DB::table('mks')
            ->when(
                $otoritas === 'Penjamin Mutu Universitas',
                fn($q) => $q->where(function($sub) use ($user) {
                    $sub->where('mks.id_universitas', $user->id_universitasUser)
                        ->orWhereExists(function($pivot) use ($user) {
                            $pivot->select(DB::raw(1))
                                  ->from('mk_kurikulum')
                                  ->join('prodi', 'mk_kurikulum.id_prodi', '=', 'prodi.id')
                                  ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                                  ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                                  ->where('fakultas.id_universitas', $user->id_universitasUser);
                        });
                })
            )
            ->when(
                $otoritas === 'Penjamin Mutu Fakultas',
                fn($q) => $q->where(function($sub) use ($user) {
                    $sub->whereExists(function($pivot) use ($user) {
                        $pivot->select(DB::raw(1))
                              ->from('mk_kurikulum')
                              ->join('prodi', 'mk_kurikulum.id_prodi', '=', 'prodi.id')
                              ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                              ->where('prodi.id_fakultas', $user->id_fakultasUser);
                    });
                })
            )
            ->when(
                in_array($otoritas, ['Penjamin Mutu Program Studi', 'Koordinator Program Studi', 'Kepala Program Studi']),
                fn($q) => $q->where(function($sub) use ($userProdiId) {
                    $sub->where('mks.id_prodi', $userProdiId)
                        ->orWhereExists(function($pivot) use ($userProdiId) {
                            $pivot->select(DB::raw(1))
                                  ->from('mk_kurikulum')
                                  ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                                  ->where('mk_kurikulum.id_prodi', $userProdiId);
                        });
                })
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
