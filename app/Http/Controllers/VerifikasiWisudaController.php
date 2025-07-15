<?php

namespace App\Http\Controllers;

use App\Exports\VerifWisudaExport;
use App\Models\Layanan;
use App\Models\PeriodeWisuda;
use App\Models\SKPI;
use App\Models\StatusWisuda;
use App\Models\Tahun;
use App\Models\Template;
use App\Models\TranskripNilai;
use App\Models\VerifikasiWisuda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class VerifikasiWisudaController extends Controller
{
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $ajuan = false; // Mahasiswa tidak perlu mengajukan lagi
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusWisuda::all();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        return view('pages.verifikasi_wisuda.index', compact('ajuan', 'layanan', 'tahuns', 'status', 'templates'));
    }

    private function canStore()
    {
        if (is_null(auth()->user()->skl)) return false;
        if (!in_array(auth()->user()->skl->status_id, ['7', '6'])) return false;
        if (!is_null(auth()->user()->verifikasiWisuda)) return false;
        return true;
    }

    public function list(Request $request)
    {
        $data = VerifikasiWisuda::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $data = $data->where('status_id', $request->status);

        // Exclude verified (status 2) and confirmed (status 4) students from verification table
        // They should appear in the wisudawan table instead
        $data = $data->whereNotIn('status_id', [2, 4]);

        $data = $data->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-primary btn-sm btn-proses me-1" data-nim="' . $row->user->nim . '" data-id="' . encodeId($row->id) . '">
                        <i class="fa fa-edit"></i> Edit
                    </button>';

                if ($row->status_id == 1) {
                    // Show proses button only if no_seri_ijazah is empty
                    if (empty($row->no_seri_ijazah)) {
                        $aksi .= '<button type="button" class="btn btn-warning btn-sm btn-proses" data-nim="' . $row->user->nim . '" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-pen"></i> Proses
                            </button>';
                    }
                }
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_proses;
            })
            ->editColumn('periode_wisuda', function ($row) {
                $periode_wisuda = $row->periode_wisuda;
                if ($periode_wisuda) {
                    $periode_wisuda = Carbon::createFromFormat('Y-m', $row->periode_wisuda)->translatedFormat('F Y');

                    // Get graduation date from PeriodeWisuda
                    $periodeData = PeriodeWisuda::where('tahun', Carbon::createFromFormat('Y-m', $row->periode_wisuda)->year)
                        ->where('bulan', Carbon::createFromFormat('Y-m', $row->periode_wisuda)->month)
                        ->first();

                    if ($periodeData && $periodeData->tanggal_wisuda) {
                        $periode_wisuda .= '<br/><small class="text-muted">' . Carbon::parse($periodeData->tanggal_wisuda)->translatedFormat('d F Y') . '</small>';
                    }
                }
                return $periode_wisuda;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'periode_wisuda'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $data = VerifikasiWisuda::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $data = $data->where('status_id', $request->status);

        // Exclude verified (status 2) and confirmed (status 4) students from verification table
        // They should appear in the wisudawan table instead
        $data = $data->whereNotIn('status_id', [2, 4]);

        $data = $data->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                // No action buttons for dekanat - they can only view
                return '';
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_proses;
            })
            ->editColumn('periode_wisuda', function ($row) {
                $periode_wisuda = $row->periode_wisuda;
                if ($periode_wisuda) {
                    $periode_wisuda = Carbon::createFromFormat('Y-m', $row->periode_wisuda)->translatedFormat('F Y');

                    // Get graduation date from PeriodeWisuda
                    $periodeData = PeriodeWisuda::where('tahun', Carbon::createFromFormat('Y-m', $row->periode_wisuda)->year)
                        ->where('bulan', Carbon::createFromFormat('Y-m', $row->periode_wisuda)->month)
                        ->first();

                    if ($periodeData && $periodeData->tanggal_wisuda) {
                        $periode_wisuda .= '<br/><small class="text-muted">' . Carbon::parse($periodeData->tanggal_wisuda)->translatedFormat('d F Y') . '</small>';
                    }
                }
                return $periode_wisuda;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'periode_wisuda'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:102400']
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran :attribute tidak boleh lebih dari 100 MB',
        ], [
            'file' => 'File PDF'
        ]);

        try {
            $fileName = 'VERIFWISUDA_' . trim(Auth::user()->name) . '_' . Auth::user()->nim . '_' . trim(Auth::user()->prodis->name) . '_' . time() . '.pdf';
            $request->file('file')->storeAs('verifWisuda/upload/', $fileName, 'public');

            // Check if user already has verifikasi wisuda record
            $existingVerifikasi = VerifikasiWisuda::where('user_id', Auth::user()->id)->first();
            
            if ($existingVerifikasi) {
                // Delete old file if exists
                if ($existingVerifikasi->file) {
                    Storage::disk('public')->delete('verifWisuda/upload/' . $existingVerifikasi->file);
                }
                
                // Update existing record with new file and change status to 7 if currently 1
                $updateData = ['file' => $fileName];
                
                if ($existingVerifikasi->status_id == '1') {
                    $updateData['status_id'] = '7'; // Change status from 1 to 7
                    $updateData['tanggal_terbit'] = now();
                }
                
                $existingVerifikasi->update($updateData);
                $message = 'File validasi berhasil diupload! Status Anda telah berubah menjadi "Menunggu Validasi Admin".';
            } else {
                // Create new record if doesn't exist
                VerifikasiWisuda::create([
                    'user_id' => Auth::user()->id,
                    'status_id' => '7', // Set status to 7 instead of 1
                    'file' => $fileName,
                    'tanggal_terbit' => now(),
                ]);
                $message = 'File validasi berhasil diupload! Status Anda: "Menunggu Validasi Admin".';
            }
            
            return response()->json(['status' => true, 'message' => $message], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan: ' . $th->getMessage()], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
            'tahun' => ['required']
        ], [
            'required' => ':attribute wajib diisi!',
            'mimes' => ':attribute harus berformat Excel (.xlsx, .xls) atau CSV (.csv)',
            'max' => 'ukuran :attribute tidak boleh lebih dari 10 MB',
        ], [
            'file' => 'File',
            'tahun' => 'Tahun'
        ]);

        try {
            $file = $request->file('file');
            $tahun = $request->tahun;

            // Read the Excel/CSV file
            $import = new \App\Imports\VerifWisudaImport($tahun);
            Excel::import($import, $file);

            // Get any failures
            $failures = $import->failures();
            $failureMessages = [];

            foreach ($failures as $failure) {
                $failureMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            // Get import statistics
            $totalRows = $import->getRowCount();
            $newRecords = $import->getNewCount();
            $updatedRecords = $import->getUpdatedCount();
            $skippedRows = $import->getSkippedRows();
            $importedWithSeriIjazah = $import->getImportedWithSeriIjazah();
            $importedWithoutSeriIjazah = $import->getImportedWithoutSeriIjazah();
            $updatedDetails = $import->getUpdatedDetails();

            // Build success message with details
            $successMessage = "Data berhasil diimport untuk tahun {$tahun}. ";
            $successMessage .= "Total baris diproses: {$totalRows}. ";

            if ($skippedRows > 0) {
                $successMessage .= "Data dilewati (tidak ada di tabel verifikasi): {$skippedRows}. ";
            }

            if ($updatedRecords > 0) {
                $successMessage .= "Data diperbarui: {$updatedRecords}. ";

                // Add details about what was updated
                if (!empty($updatedDetails)) {
                    $updateSummary = [];
                    foreach ($updatedDetails as $detail) {
                        $fields = implode(', ', $detail['updates']);
                        $updateSummary[] = "{$detail['name']} ({$detail['nim']}): {$fields}";
                    }

                    if (count($updateSummary) <= 5) {
                        $successMessage .= "Detail perubahan: " . implode('; ', $updateSummary) . ". ";
                    } else {
                        $firstFive = array_slice($updateSummary, 0, 5);
                        $remaining = count($updateSummary) - 5;
                        $successMessage .= "Detail perubahan: " . implode('; ', $firstFive) . " dan {$remaining} lainnya. ";
                    }
                }
            }

            if ($importedWithSeriIjazah > 0) {
                $successMessage .= "{$importedWithSeriIjazah} mahasiswa memiliki nomor seri ijazah (siap dikonfirmasi). ";
            }

            if ($importedWithoutSeriIjazah > 0) {
                $successMessage .= "{$importedWithoutSeriIjazah} mahasiswa tidak memiliki nomor seri ijazah (perlu diproses staff).";
            }

            if (!empty($failureMessages)) {
                $successMessage .= ' Peringatan: ' . implode('; ', $failureMessages);
            }

            return response()->json([
                'status' => true,
                'message' => $successMessage
            ], 200);
        } catch (\Throwable $th) {
            \Log::error('Import error: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengimport data: ' . $th->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
{
    $request->validate([
        'tahun' => ['required'],
        'type' => ['nullable', 'in:verifikasi,wisudawan']
    ], [
        'required' => ':attribute wajib diisi',
    ], [
        'tahun' => 'Tahun',
        'type' => 'Tipe Export'
    ]);

    $tahun = $request->tahun;
    $type = $request->type ?? 'verifikasi';

    if ($type === 'wisudawan') {
        $name = 'Rekap_Data_Wisudawan_Tahun_' . $tahun;
    } else {
        $name = 'Rekap_Data_Verifikasi_Wisuda_Tahun_' . $tahun;
    }

    try {
        // Buat instance export dengan flag delete
        $export = new VerifWisudaExport($tahun, $type, true); // true = auto delete after export

        // Download file dengan proper headers
        return Excel::download($export, $name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Disposition' => 'attachment; filename="' . $name . '.xlsx"'
        ]);

    } catch (\Exception $e) {
        // Return JSON error response for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal export data: ' . $e->getMessage()
            ], 500);
        }

        // For non-AJAX requests, redirect back with error
        return back()->with('error', 'Gagal export data: ' . $e->getMessage());
    }
}

    public function show($id)
    {
        try {
            $id = decodeId($id);
            $data = VerifikasiWisuda::with('user.prodis', 'status')->where('id', $id)->first();
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:102400']
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran :attribute tidak boleh lebih dari 100 MB',
        ], [
            'file' => 'File PDF'
        ]);

        try {
            $id = decodeId($id);
            $ajuan = VerifikasiWisuda::findOrFail($id);

            $fileName = 'VERIFWISUDA_' . trim(Auth::user()->name) . '_' . Auth::user()->nim . '_' . trim(Auth::user()->prodis->name) . '_' . time() . '.pdf';
            $request->file('file')->storeAs('verifWisuda/upload/', $fileName, 'public');
            Storage::disk('public')->delete('verifWisuda/upload/' . $ajuan->file);

            $ajuan->update([
                'file' => $fileName,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
        return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Diedit!'], 200);
    }

    public function proses(Request $request, $id)
    {
        $request->validate([
            'status_id' => ['required'],
            'catatan' => ['nullable', 'string']
        ], [
            'required' => ':attribute wajib diisi!',
        ], [
            'status_id' => 'Status',
            'catatan' => 'Catatan'
        ]);

        try {
            $id = decodeId($id);
            $ajuan = VerifikasiWisuda::findOrFail($id);

            // Only update status and catatan - don't change existing verification data
            $updateData = [
                'status_id' => $request->status_id,
                'catatan' => $request->catatan,
                'tanggal_proses' => new \DateTime(),
            ];

            // If status is changed to 1 (Belum Diproses), clear file validation data
            // This forces student to re-upload validation file
            if ($request->status_id == '1') {
                $updateData['tanggal_terbit'] = null;
                $updateData['file_validasi_uploaded'] = false;
                // Note: We keep the existing 'file' field (original upload) but clear validation file data
            }

            $ajuan->update($updateData);

            // Only create transkrip and skpi if status is 2 (Terverifikasi) and data is complete
            if ($request->status_id == '2' && !empty($ajuan->periode_wisuda)) {
                $mahasiswa_id = $ajuan->user_id;

                // Check if records don't already exist
                $existingTranskrip = TranskripNilai::where('user_id', $mahasiswa_id)
                    ->where('periode_wisuda', $ajuan->periode_wisuda)
                    ->first();

                $existingSKPI = SKPI::where('user_id', $mahasiswa_id)
                    ->where('periode_wisuda', $ajuan->periode_wisuda)
                    ->first();

                if (!$existingTranskrip) {
                    TranskripNilai::create([
                        'user_id' => $mahasiswa_id,
                        'status_id' => '1',
                        'periode_wisuda' => $ajuan->periode_wisuda,
                    ]);
                }

                if (!$existingSKPI) {
                    SKPI::create([
                        'user_id' => $mahasiswa_id,
                        'status_id' => '1',
                        'periode_wisuda' => $ajuan->periode_wisuda,
                    ]);
                }
            }

            $message = 'Status berhasil diperbarui!';
            if ($request->status_id == '1') {
                $message .= ' Mahasiswa harus mengupload ulang file validasi.';
            }

            return response()->json(['status' => true, 'message' => $message], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
    }
    public function listWisudawan(Request $request)
    {
        $year = $request->year ?? date('Y');

        $data = VerifikasiWisuda::with(['user.prodis', 'status'])
            ->whereYear('created_at', $year)
            ->whereIn('status_id', [2, 4]) // Include verified (2) and confirmed (4) students
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                $aksi = '<button type="button" class="btn btn-primary btn-sm btn-proses me-1" data-nim="' . $row->user->nim . '" data-id="' . encodeId($row->id) . '" data-type="wisudawan">
                        <i class="fa fa-edit"></i> Edit
                    </button>';

                if($row->status_id != 2) {
                    $aksi .= '<button type="button" class="btn btn-warning btn-sm btn-proses" data-nim="' . $row->user->nim . '" data-id="' . encodeId($row->id) . '" data-type="wisudawan">
                            <i class="fa fa-pen"></i> Proses
                        </button>';
                }
                return $aksi;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('periode_wisuda', function ($row) {
                $periode_wisuda = $row->periode_wisuda;
                if ($periode_wisuda) {
                    $periode_wisuda = Carbon::createFromFormat('Y-m', $row->periode_wisuda)->translatedFormat('F Y');

                    // Get graduation date from PeriodeWisuda
                    $periodeData = PeriodeWisuda::where('tahun', Carbon::createFromFormat('Y-m', $row->periode_wisuda)->year)
                        ->where('bulan', Carbon::createFromFormat('Y-m', $row->periode_wisuda)->month)
                        ->first();

                    if ($periodeData && $periodeData->tanggal_wisuda) {
                        $periode_wisuda .= '<br/><small class="text-muted">' . Carbon::parse($periodeData->tanggal_wisuda)->translatedFormat('d F Y') . '</small>';
                    }
                }
                return $periode_wisuda;
            })
            ->rawColumns(['action', 'status_id', 'periode_wisuda'])
            ->make(true);
}

public function terima($id)
{
    try {
        $verifikasi = VerifikasiWisuda::findOrFail(decodeId($id));
        $verifikasi->update(['status_id' => 2]); // 2 = Diterima

        return response()->json([
            'status' => true,
            'message' => 'Wisudawan berhasil diterima'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Gagal menerima wisudawan: ' . $e->getMessage()
        ], 500);
    }
}

public function tolak($id)
{
    try {
        $verifikasi = VerifikasiWisuda::findOrFail(decodeId($id));
        $verifikasi->update(['status_id' => 3]); // 3 = Ditolak

        return response()->json([
            'status' => true,
            'message' => 'Wisudawan berhasil ditolak'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Gagal menolak wisudawan: ' . $e->getMessage()
        ], 500);
    }
}

public function konfirmasi(Request $request, $id)
{
    $request->validate([
        'konfirmasi' => ['required', 'in:setuju,tidak_setuju'],
        'catatan' => ['nullable', 'string']
    ]);

    try {
        $id = decodeId($id);
        $verifikasi = VerifikasiWisuda::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        // Check if file is already uploaded
        if (empty($verifikasi->file)) {
            return response()->json([
                'status' => false, 
                'message' => 'Silakan upload file validasi terlebih dahulu.'
            ], 400);
        }

        $updateData = [
            'tanggal_terbit' => now(),
            'catatan' => $request->catatan,
        ];

        if ($request->konfirmasi === 'setuju') {
            $updateData['status_id'] = '4'; // Status: Bersedia
        } else {
            $updateData['status_id'] = '5'; // Status: Tidak bersedia
        }

        $verifikasi->update($updateData);

        $message = $request->konfirmasi === 'setuju'
            ? 'Terima kasih! Anda telah mengkonfirmasi keikutsertaan wisuda dengan status Bersedia.'
            : 'Konfirmasi berhasil. Anda tidak bersedia mengikuti wisuda periode ini.';

        return response()->json(['status' => true, 'message' => $message], 200);
    } catch (\Throwable $th) {
        return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $th->getMessage()], 500);
    }
}


public function bulkProcess(Request $request)
{
    $request->validate([
        'status_id' => ['required'],
        'catatan' => ['nullable', 'string'],
        'selected_ids' => ['required', 'string'],
        'periode_wisuda' => ['nullable', 'string']
    ], [
        'required' => ':attribute wajib diisi!',
    ], [
        'status_id' => 'Status',
        'catatan' => 'Catatan',
        'selected_ids' => 'Data yang dipilih',
        'periode_wisuda' => 'Periode Wisuda'
    ]);

    try {
        $ids = explode(',', $request->selected_ids);
        $validIds = [];

        // Use raw IDs directly like in perpanjangan system
        foreach ($ids as $id) {
            $trimmedId = trim($id);
            if (empty($trimmedId)) continue;

            // Verify the record exists using raw ID
            $exists = VerifikasiWisuda::where('id', $trimmedId)->exists();
            if ($exists) {
                $validIds[] = $trimmedId;
            }
        }

        if (empty($validIds)) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data valid yang dipilih'
            ], 400);
        }

        $updateData = [
            'status_id' => $request->status_id,
            'catatan' => $request->catatan,
            'tanggal_proses' => now(),
        ];

        if ($request->periode_wisuda) {
            $updateData['periode_wisuda'] = $request->periode_wisuda;
        }

        // If status is changed to 1 (Belum Diproses), clear file validation data
        if ($request->status_id == '1') {
            $updateData['tanggal_terbit'] = null;
            $updateData['file_validasi_uploaded'] = false;
        }

        // Update all selected records using raw IDs
        $updated = VerifikasiWisuda::whereIn('id', $validIds)->update($updateData);

        // If status is verified (2), create related records
        if ($request->status_id == '2') {
            $verifikasiData = VerifikasiWisuda::whereIn('id', $validIds)
                ->where('status_id', '2')
                ->whereNotNull('periode_wisuda')
                ->get();

            foreach ($verifikasiData as $verifikasi) {
                // Create transkrip if not exists
                $existingTranskrip = TranskripNilai::where('user_id', $verifikasi->user_id)
                    ->where('periode_wisuda', $verifikasi->periode_wisuda)
                    ->first();

                if (!$existingTranskrip) {
                    TranskripNilai::create([
                        'user_id' => $verifikasi->user_id,
                        'status_id' => '1',
                        'periode_wisuda' => $verifikasi->periode_wisuda,
                    ]);
                }

                // Create SKPI if not exists
                $existingSKPI = SKPI::where('user_id', $verifikasi->user_id)
                    ->where('periode_wisuda', $verifikasi->periode_wisuda)
                    ->first();

                if (!$existingSKPI) {
                    SKPI::create([
                        'user_id' => $verifikasi->user_id,
                        'status_id' => '1',
                        'periode_wisuda' => $verifikasi->periode_wisuda,
                    ]);
                }
            }
        }

        $message = "Data berhasil diproses ({$updated} data berhasil diupdate)";
        if ($request->status_id == '1') {
            $message .= ". Mahasiswa yang diubah statusnya harus mengupload ulang file validasi.";
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Bulk process error: ' . $e->getMessage());
        \Log::error('Request data: ' . json_encode($request->all()));

        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
}