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
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
// use App\Services\GoogleDriveService;


class VerifikasiWisudaController extends Controller
{
    public function landingPage(Request $request)
    {
        return view('landingpage.verifikasi_wisuda.index');
    }
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

        // Exclude verified students and records that already have PIN & ijazah number
        $data = $data->whereNotIn('status_id', ['2'])
            ->where(function ($query) {
                $query->whereNull('no_seri_ijazah')
                    ->orWhere('no_seri_ijazah', '')
                    ->orWhereNull('pin')
                    ->orWhere('pin', '')
                    ->orWhere('status_id', '3');
            });

        $data = $data->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<div class="d-flex justify-content-center align-items-center">';
                $aksi .= '<button type="button" class="btn btn-primary btn-sm btn-proses" data-nim="' . $row->user->nim . '" data-id="' . encodeId($row->id) . '" style="white-space: nowrap;">';
                $aksi .= '<i class="fa fa-edit"></i> Proses';
                $aksi .= '</button>';
                $aksi .= '</div>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_terbit', function ($row) {
                return $row->tanggal_terbit ? Carbon::parse($row->tanggal_terbit)->translatedFormat('d F Y') : '';
            })
            ->editColumn('tanggal_update', function ($row) {
                return $row->tanggal_update ? Carbon::parse($row->tanggal_update)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_update)->translatedFormat('H:i:s') . ' WIB' : '';
            })
            ->editColumn('tanggal_proses', function ($row) {
                return $row->tanggal_proses ? Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s') . ' WIB' : '';
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
            ->editColumn('pin', function ($row) {
                return $row->pin ?? '';
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'periode_wisuda', 'tanggal_update', 'tanggal_terbit'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $data = VerifikasiWisuda::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $data = $data->where('status_id', $request->status);

        // Exclude verified students and records that already have PIN & ijazah number
        $data = $data->whereNotIn('status_id', ['2'])
            ->where(function ($query) {
                $query->whereNull('no_seri_ijazah')
                    ->orWhere('no_seri_ijazah', '')
                    ->orWhereNull('pin')
                    ->orWhere('pin', '')
                    ->orWhere('status_id', '3');
            });

        $data = $data->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                // No action buttons for dekanat, subkoor, fo, and admin - they can only view
                return '';
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_proses', function ($row) {
                return $row->tanggal_proses ? Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s') . ' WIB' : '';
            })
            ->editColumn('tanggal_terbit', function ($row) {
                return $row->tanggal_terbit ? Carbon::parse($row->tanggal_terbit)->translatedFormat('d F Y') : '';
            })
            ->editColumn('tanggal_update', function ($row) {
                return $row->tanggal_update ? Carbon::parse($row->tanggal_update)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_update)->translatedFormat('H:i:s') . ' WIB' : '';
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
            ->editColumn('pin', function ($row) {
                return $row->pin ?? '';
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'periode_wisuda', 'tanggal_terbit', 'tanggal_update'])
            ->toJson();
    }

    public function listAdminFo(Request $request)
    {
        $data = VerifikasiWisuda::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $data = $data->where('status_id', $request->status);

        // Show all data for admin and fo roles (including verified and confirmed students)
        $data = $data->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                // No action buttons for admin and fo - they can only view
                return '';
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_proses', function ($row) {
                return $row->tanggal_proses ? Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s') . ' WIB' : '';
            })
            ->editColumn('tanggal_terbit', function ($row) {
                return $row->tanggal_terbit ? Carbon::parse($row->tanggal_terbit)->translatedFormat('d F Y') : '';
            })
            ->editColumn('tanggal_update', function ($row) {
                return $row->tanggal_update ? Carbon::parse($row->tanggal_update)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_update)->translatedFormat('H:i:s') . ' WIB' : '';
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
            ->editColumn('pin', function ($row) {
                return $row->pin ?? '';
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'periode_wisuda', 'tanggal_terbit', 'tanggal_update'])
            ->toJson();
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
    public function proses(Request $request, $id)
    {
        $request->validate([
            'status_id' => ['required', 'in:1,2,3'],
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
            ];

            // Update tanggal_update when status changes to 2 (Sudah Terverifikasi) or 3 (Tidak Terverifikasi)
            if (in_array($request->status_id, ['2', '3'])) {
                $updateData['tanggal_update'] = now();
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

            return response()->json(['status' => true, 'message' => 'Status berhasil diperbarui!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
    }
    public function listWisudawan(Request $request)
    {
        $year = $request->year ?? date('Y');

        $data = VerifikasiWisuda::with(['user.prodis', 'status'])
            ->whereYear('created_at', $year)
            ->where('status_id', '2')
            ->whereNotNull('no_seri_ijazah')
            ->where('no_seri_ijazah', '<>', '')
            ->whereNotNull('pin')
            ->where('pin', '<>', '')
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                $aksi = '<div class="d-flex justify-content-center align-items-center">';
                $aksi .= '<button type="button" class="btn btn-primary btn-sm btn-proses" data-nim="' . $row->user->nim . '" data-id="' . encodeId($row->id) . '" data-type="wisudawan" style="white-space: nowrap;">';
                $aksi .= '<i class="fa fa-edit"></i> Proses';
                $aksi .= '</button>';
                $aksi .= '</div>';
                return $aksi;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('tanggal_terbit', function ($row) {
                return $row->tanggal_terbit ? Carbon::parse($row->tanggal_terbit)->translatedFormat('d F Y') : '';
            })
            ->editColumn('tanggal_update', function ($row) {
                return $row->tanggal_update ? Carbon::parse($row->tanggal_update)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_update)->translatedFormat('H:i:s') . ' WIB' : '';
            })
            ->editColumn('tanggal_proses', function ($row) {
                return $row->tanggal_proses ? Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s') . ' WIB' : '';
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
            ->editColumn('pin', function ($row) {
                return $row->pin ?? '';
            })
            ->rawColumns(['action', 'status_id', 'periode_wisuda', 'tanggal_terbit', 'tanggal_update', 'tanggal_proses'])
            ->make(true);
}
public function bulkProcess(Request $request)
{
    $request->validate([
    'status_id' => ['required', 'in:1,2,3'],
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
        ];

        if ($request->periode_wisuda) {
            $updateData['periode_wisuda'] = $request->periode_wisuda;
        }

        // Update tanggal_update when status changes to 2 (Sudah Terverifikasi) or 3 (Tidak Terverifikasi)
        if (in_array($request->status_id, ['2', '3'])) {
            $updateData['tanggal_update'] = now();
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

        return response()->json([
            'status' => true,
            'message' => "Data berhasil diproses ({$updated} data berhasil diupdate)"
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
