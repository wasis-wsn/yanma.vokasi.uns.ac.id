<?php

namespace App\Http\Controllers;

use App\Models\PeriodeWisuda;
use App\Models\Tahun;
use App\Models\VerifikasiWisuda;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PeriodeWisudaController extends Controller
{
    private const STATUS_TUNDA = "8";

    public function index()
    {
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        return view('pages.periode_wisuda.index', compact('tahuns'));
    }

    public function list(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        // Initialize year if not exists
        PeriodeWisuda::initializeYear($tahun);

        $data = PeriodeWisuda::where('tahun', $tahun)->orderBy('bulan')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                // Normalize month number: handle cases where bulan might be stored 0-11 or 1-12
                $rawBulan = (int) $row->bulan;
                if ($rawBulan >= 0 && $rawBulan <= 11) {
                    $bulanNumber = $rawBulan + 1;
                } else {
                    $bulanNumber = $rawBulan;
                }
                // Ensure valid 1-12
                if ($bulanNumber < 1 || $bulanNumber > 12) {
                    $bulanNumber = 1;
                }

                $namaBulan = \Carbon\Carbon::createFromDate($row->tahun, $bulanNumber, 1)->translatedFormat('F');

                return '<button type="button" class="btn btn-primary btn-sm btn-edit"
                            data-id="' . $row->id . '"
                            data-bulan="' . $row->bulan . '"
                            data-tahun="' . $row->tahun . '"
                            data-nama="' . $namaBulan . '"
                            data-tanggal="' . ($row->tanggal_wisuda ? $row->tanggal_wisuda->format('Y-m-d') : '') . '"
                            data-active="' . ($row->is_active ? 1 : 0) . '">
                        <i class="fa fa-edit"></i> Edit
                    </button>';
            })
            ->editColumn('tanggal_wisuda', function ($row) {
                return $row->tanggal_wisuda ? $row->tanggal_wisuda->format('d F Y') : '-';
            })
            ->editColumn('is_active', function ($row) {
                $color = $row->is_active ? 'btn-success' : 'btn-secondary';
                $text = $row->is_active ? 'Aktif' : 'Tidak Aktif';
                return '<button type="button" class="btn ' . $color . ' btn-sm" disabled>' . $text . '</button>';
            })
            ->rawColumns(['action', 'is_active'])
            ->make(true);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_wisuda' => 'nullable|date',
            'is_active' => 'boolean',
            'reset_status' => 'nullable|boolean'
        ]);

        try {
            $periode = PeriodeWisuda::findOrFail($id);
            $originalDate = $periode->tanggal_wisuda;
            $newDate = $request->tanggal_wisuda;

            \Log::info('Periode Update Debug:', [
                'periode_id' => $id,
                'original_date' => $originalDate ? $originalDate->format('Y-m-d') : 'null',
                'new_date' => $newDate,
                'reset_status' => $request->reset_status,
                'tahun' => $periode->tahun,
                'bulan' => $periode->bulan
            ]);

            // If setting this as active, deactivate others in the same year
            if ($request->is_active) {
                PeriodeWisuda::where('tahun', $periode->tahun)
                    ->where('id', '!=', $id)
                    ->update(['is_active' => false]);
            }

            $periode->update([
                'tanggal_wisuda' => $request->tanggal_wisuda,
                'is_active' => $request->is_active
            ]);

            $resetCount = 0;

            // Reset when there's a new date and reset_status is requested
            if ($newDate && $request->reset_status) {
                \Log::info("Starting reset - targeting ALL students to status 1 (kecuali status terverifikasi dan tunda)");

                // Reset all students to status 1, kecuali yang berstatus terverifikasi (2) dan tunda (8)
                $resetQuery = VerifikasiWisuda::whereNotIn('status_id', ['2', self::STATUS_TUNDA]);

                $beforeResetCount = $resetQuery->count();
                \Log::info("Found {$beforeResetCount} students to reset to status 1");

                if ($beforeResetCount > 0) {
                    $examplesReset = $resetQuery->with('user')->limit(5)->get();
                    foreach ($examplesReset as $example) {
                        \Log::info("Will reset to status 1: {$example->user->name} (NIM: {$example->user->nim}), Current Status: {$example->status_id}, Periode: '{$example->periode_wisuda}'");
                    }

                    // Reset all statuses except 2 and 8 to status 1
                    $resetCount = VerifikasiWisuda::whereNotIn('status_id', ['2', self::STATUS_TUNDA])
                        ->update([
                            'status_id' => '1', // Reset to status 1 (Diajukan)
                            'catatan' => 'Status direset ke diajukan karena perubahan periode wisuda pada ' . now()->format('d F Y H:i:s'),
                            'tanggal_update' => now(),
                            'updated_at' => now()
                        ]);

                    \Log::info("Successfully reset {$resetCount} students to status 1");
                }

                \Log::info("Total students affected: {$resetCount}");
            }

            return response()->json([
                'status' => true,
                'message' => 'Periode wisuda berhasil diperbarui!',
                'reset_count' => $resetCount,
                'total_affected' => $resetCount
            ]);
        } catch (\Throwable $th) {
            \Log::error('Error updating periode: ' . $th->getMessage());
            \Log::error('Stack trace: ' . $th->getTraceAsString());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $th->getMessage()
            ], 500);
        }
    }
}
