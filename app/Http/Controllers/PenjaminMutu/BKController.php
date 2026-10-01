<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\BK;
use App\Models\Kurikulum;
use App\Models\MK;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BKController extends Controller
{
    public function index(Request $request)
    {
        $idProdi = auth()->user()->id_prodiUser;
        $kurikulums = Kurikulum::where('id_prodi', $idProdi)->get();

        $query = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('bks.*')
            ->with('kurikulum');

        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', $idProdi);
        }

        if ($request->filled('kurikulum_id')) {
            $query->where('kurikulum_id', $request->kurikulum_id);
        }

        $allBkItems = $query->get();
        $bks = $allBkItems->groupBy('rumpun');

        $existingRumpuns = BK::where('id_prodi', $idProdi)
            ->whereNotNull('rumpun')
            ->where('rumpun', '!=', '')
            ->distinct()
            ->pluck('rumpun');

        return view('penjamin-mutu.bk.index', compact('bks', 'allBkItems', 'kurikulums', 'existingRumpuns'));
    }
    
    public function addBK()
    {
        $kurikulums = Kurikulum::where('id_prodi', auth()->user()->id_prodiUser)->get();

        return view('penjamin-mutu.bk.addBK', compact('kurikulums'));
    }

    public function storeBK(Request $request)
    {
        $rumpunValue = $request->input('rumpun_select') === '__NEW__'
            ? trim($request->input('rumpun_new'))
            : trim($request->input('rumpun_select') ?: $request->input('rumpun'));

        $request->merge(['rumpun' => $rumpunValue]);

        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'nama' => 'required|string',
            'rumpun' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('failed', 'Gagal menyimpan: ' . $validator->errors()->first());
        }
        try {
            $idProdi = auth()->user()->id_prodiUser;
            $jumlahBk = BK::where('id_prodi', $idProdi)->where('kurikulum_id', $request->kurikulum_id)->count();
            $kodeBKforInput = 'BK0' . ($jumlahBk + 1);

            BK::create([
                'id_prodi' => $idProdi,
                'kurikulum_id' => $request->kurikulum_id,
                'nama' => $request->nama,
                'rumpun' => $rumpunValue,
                'kode' => $kodeBKforInput,
            ]);
            return redirect()->back()->with('success', 'Data Bahan Kajian berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('failed', 'Gagal menyimpan data Bahan Kajian.');
        }
    }

    public function updateBK(Request $request, $id)
    {
        $rumpunValue = $request->input('rumpun_select') === '__NEW__'
            ? trim($request->input('rumpun_new'))
            : trim($request->input('rumpun_select') ?: $request->input('rumpun'));

        if ($rumpunValue) {
            $request->merge(['rumpun' => $rumpunValue]);
        }

        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'nama' => 'required|string',
            'rumpun' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('failed', 'Gagal memperbarui: ' . $validator->errors()->first());
        }

        try {
            $idProdi = auth()->user()->id_prodiUser;
            $bk = BK::where('id_prodi', $idProdi)->findOrFail($id);

            $bk->update([
                'nama' => $request->nama,
                'kurikulum_id' => $request->kurikulum_id,
                'rumpun' => $rumpunValue ?: $bk->rumpun,
            ]);

            return redirect()->back()->with('success', 'Bahan Kajian berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('failed', 'Gagal memperbarui Bahan Kajian.');
        }
    }

    public function destroyBK($id)
    {
        try {
            $idProdi = auth()->user()->id_prodiUser;
            $bk = BK::where('id_prodi', $idProdi)->findOrFail($id);
            $bk->delete();

            return redirect()->back()->with('success', 'Bahan Kajian berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Gagal menghapus Bahan Kajian.');
        }
    }

    public function indexBKMK(Request $request)
    {
        $queryBks = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('bks.*');

        $user = auth()->user();
        $userProdiId = $user->id_prodiUser;

        $queryMks = MK::query();

        if ($user->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryBks->where('fakultas.id_universitas', $user->id_universitasUser);
            $univId = $user->id_universitasUser;
            $queryMks->where(function($q) use ($univId) {
                $q->where('mks.id_universitas', $univId)
                  ->orWhereHas('prodi.fakultas', function($f) use ($univId) {
                      $f->where('id_universitas', $univId);
                  })->orWhereExists(function($sub) use ($univId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->join('prodi', 'mk_kurikulum.id_prodi', '=', 'prodi.id')
                          ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('fakultas.id_universitas', $univId);
                  });
            });
        } else if ($user->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryBks->where('fakultas.id', $user->id_fakultasUser);
            $fakId = $user->id_fakultasUser;
            $queryMks->where(function($q) use ($fakId) {
                $q->whereHas('prodi', function($p) use ($fakId) {
                    $p->where('id_fakultas', $fakId);
                })->orWhereExists(function($sub) use ($fakId) {
                    $sub->select(DB::raw(1))
                        ->from('mk_kurikulum')
                        ->join('prodi', 'mk_kurikulum.id_prodi', '=', 'prodi.id')
                        ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                        ->where('prodi.id_fakultas', $fakId);
                });
            });
        } else if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryBks->where('prodi.id', $userProdiId);
            $queryMks->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            });
        }

        if ($request->filled('kurikulum_id')) {
            $kurId = $request->kurikulum_id;
            $queryBks->where('bks.kurikulum_id', $kurId);
            $queryMks->where(function($q) use ($kurId) {
                $q->where('mks.id_kurikulum', $kurId)
                  ->orWhere('mks.kurikulum', $kurId)
                  ->orWhereExists(function($sub) use ($kurId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_kurikulum', $kurId);
                  });
            });
        }

        $bks = $queryBks->with('kurikulum')->get();
        $mks = $queryMks->with(['bk', 'kurikulum'])->get();

        $queryKur = Kurikulum::query()
            ->join('prodi', 'kurikulums.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('kurikulums.*', 'prodi.nama as nama_prodi')
            ->orderBy('kurikulums.tahun', 'desc');

        if ($user->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryKur->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($user->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryKur->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $queryKur->where('prodi.id', $user->id_prodiUser);
        }
        $kurikulums = $queryKur->get();

        return view('penjamin-mutu.bk.pemetaan_bk_mk', compact('bks', 'mks', 'kurikulums'));
    }

    public function updateMatrixBKMK(Request $request)
    {
        $matrix = $request->input('matrix', []); // Key: mk_kode, Value: array of bk_ids
        $kurikulumId = $request->input('kurikulum_id');
        $user = auth()->user();
        $userProdiId = $user->id_prodiUser;

        $queryMks = MK::query();
        if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMks->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            });
        }
        if ($kurikulumId) {
            $queryMks->where(function($q) use ($kurikulumId) {
                $q->where('mks.id_kurikulum', $kurikulumId)
                  ->orWhere('mks.kurikulum', $kurikulumId)
                  ->orWhereExists(function($sub) use ($kurikulumId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_kurikulum', $kurikulumId);
                  });
            });
        }
        $allMks = $queryMks->get();

        DB::transaction(function () use ($allMks, $matrix) {
            foreach ($allMks as $mk) {
                $selectedBkIds = isset($matrix[$mk->kode]) ? (array) $matrix[$mk->kode] : [];
                $mk->bk()->sync($selectedBkIds);
            }
        });

        return redirect()->back()->with('success', 'Matriks pemetaan BK - MK berhasil diperbarui.');
    }

    public function addBKMK()
    {
        $bks = BK::where('id_prodi', auth()->user()->id_prodiUser)
            ->select('bks.*')->get();

        return view('penjamin-mutu.bk.add_bk_mk', compact('bks'));
    }

    public function getMkByBk($bkId)
    {
        // Temukan BK yang dipilih
        $bk = BK::where('id_prodi', auth()->user()->id_prodiUser)
            ->with('cpl.mk')->findOrFail($bkId);

        // Ambil semua MK yang terkait melalui CPL
        $mkKodes = $bk->cpl->flatMap(function ($cpl) {
            return $cpl->mk->pluck('kode');
        })->unique();

        // Temukan MK berdasarkan ID yang dikumpulkan
        $mks = MK::where('id_prodi', auth()->user()->id_prodiUser)
            ->whereIn('kode', $mkKodes)->get();

        return response()->json(['mks' => $mks]);
    }

    public function storeBKMK(Request $request)
    {
        $validated = $request->validate([
            'bk_id' => 'required|exists:bks,id',
            'mk_kodes' => 'required|array',
            'mk_kodes.*' => 'exists:mks,kode',
        ]);

        $bk = BK::with('cpl.mk')->findOrFail($validated['bk_id']);
        $mkKodes = $validated['mk_kodes'];

        $validMkKodes = $bk->cpl->flatMap(function ($cpl) {
            return $cpl->mk->pluck('kode');
        })->unique();

        foreach ($mkKodes as $mkKode) {
            if (!$validMkKodes->contains($mkKode)) {
                return redirect()->back()->with('failed', 'MK yang dipilih tidak valid untuk BK yang dipilih.');
            }
        }

        $bk->mk()->syncWithoutDetaching($validated['mk_kodes']);

        return redirect()->back()->with('success', 'Pemetaan berhasil disimpan.');
    }

    // Download Template Excel Bahan Kajian
    public function downloadTemplateBK()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import BK');

        // Header (3 Kolom: Tanpa Kode BK karena auto-generate oleh sistem)
        $headers = [
            'A1' => 'Tahun Kurikulum',
            'B1' => 'Nama Bahan Kajian',
            'C1' => 'Rumpun BK',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style Header
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Sample Data dengan Rumpun BK yang sesuai (Kompetensi Utama, Soft Skill, Umum)
        $sheet->setCellValue('A2', '2025');
        $sheet->setCellValue('B2', 'Rekayasa Perangkat Lunak');
        $sheet->setCellValue('C2', 'Kompetensi Utama');

        $sheet->setCellValue('A3', '2025');
        $sheet->setCellValue('B3', 'Etika Profesi & Komunikasi');
        $sheet->setCellValue('C3', 'Soft Skill');

        $sheet->setCellValue('A4', '2025');
        $sheet->setCellValue('B4', 'Pancasila dan Kewarganegaraan');
        $sheet->setCellValue('C4', 'Umum');

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Template_Import_Bahan_Kajian.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // Import Excel Bahan Kajian
    public function importExcelBK(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'id_kurikulum' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $idProdi = $user->id_prodiUser;

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

        // Drop header
        unset($dataRows[0]);

        $importedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($dataRows as $row) {
                $col0 = trim((string)($row[0] ?? ''));
                $col1 = trim((string)($row[1] ?? ''));
                $col2 = trim((string)($row[2] ?? ''));
                $col3 = trim((string)($row[3] ?? ''));

                // Deteksi apakah format Excel memuat 4 kolom (Tahun, Kode, Nama, Rumpun) atau 3 kolom (Tahun, Nama, Rumpun)
                if (!empty($col3) || (preg_match('/^BK\d+/i', $col1) && !empty($col2))) {
                    // Format 4 kolom (dengan Kode BK)
                    $rawTahunKur = $col0;
                    $rawKode = $col1;
                    $nama = $col2;
                    $rumpun = !empty($col3) ? $col3 : 'Lainnya';
                } else {
                    // Format 3 kolom (tanpa Kode BK)
                    $rawTahunKur = $col0;
                    $rawKode = '';
                    $nama = $col1;
                    $rumpun = !empty($col2) ? $col2 : 'Lainnya';
                }

                if (empty($nama)) {
                    continue;
                }

                // Tentukan Kurikulum
                $idKurikulum = $request->id_kurikulum;
                $tahunClean = preg_replace('/[^\d]/', '', $rawTahunKur);

                if (!empty($tahunClean) && $idProdi) {
                    $kurObj = Kurikulum::where('id_prodi', $idProdi)
                        ->where('tahun', $tahunClean)
                        ->first();
                    if ($kurObj) {
                        $idKurikulum = $kurObj->id;
                    }
                }

                if (!$idKurikulum && $idProdi) {
                    $kurObj = Kurikulum::where('id_prodi', $idProdi)
                        ->orderBy('tahun', 'desc')
                        ->first();
                    if ($kurObj) {
                        $idKurikulum = $kurObj->id;
                    }
                }

                if (!$idKurikulum) {
                    continue;
                }

                // Cek apakah data BK dengan nama sama (atau kode yang sama) sudah ada
                $existingBk = BK::where('id_prodi', $idProdi)
                    ->where('kurikulum_id', $idKurikulum)
                    ->where(function($q) use ($rawKode, $nama) {
                        $q->where('nama', $nama);
                        if (!empty($rawKode)) {
                            $q->orWhere('kode', strtoupper($rawKode));
                        }
                    })->first();

                if ($existingBk) {
                    $existingBk->update([
                        'nama' => $nama,
                        'rumpun' => !empty($rumpun) ? $rumpun : $existingBk->rumpun,
                        'updated_at' => now(),
                    ]);
                    $updatedCount++;
                } else {
                    // Auto generate Kode BK jika tidak diisi
                    if (empty($rawKode)) {
                        $countExisting = BK::where('id_prodi', $idProdi)
                            ->where('kurikulum_id', $idKurikulum)
                            ->count();
                        $kode = 'BK' . sprintf('%02d', $countExisting + 1);
                    } else {
                        $kode = strtoupper($rawKode);
                    }

                    BK::create([
                        'id_prodi' => $idProdi,
                        'kurikulum_id' => $idKurikulum,
                        'kode' => $kode,
                        'nama' => $nama,
                        'rumpun' => !empty($rumpun) ? $rumpun : 'Lainnya',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $importedCount++;
                }
            }

            DB::commit();

            $msg = "Berhasil memproses impor Bahan Kajian: {$importedCount} data baru ditambahkan";
            if ($updatedCount > 0) {
                $msg .= ", {$updatedCount} data diperbarui.";
            } else {
                $msg .= ".";
            }

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengimpor data Bahan Kajian: ' . $e->getMessage());
        }
    }
}
