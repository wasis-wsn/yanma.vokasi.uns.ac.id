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
                return '<button type="button" class="btn btn-primary btn-sm btn-edit"
                            data-id="' . $row->id . '"
                            data-bulan="' . $row->bulan . '"
                            data-tahun="' . $row->tahun . '"
                            data-nama="' . $row->nama_bulan . '"
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
                \Log::info("Starting reset - targeting ALL students");

                // Reset all students regardless of periode
                $resetQuery = VerifikasiWisuda::whereNotIn('status_id', ['1']); // Only reset students who are not in "Belum Diproses" status

                // Get count before reset for logging
                $beforeCount = $resetQuery->count();
                \Log::info("Found {$beforeCount} students to reset");

                if ($beforeCount > 0) {
                    // Show some examples of students that will be reset
                    $examples = $resetQuery->with('user')->limit(5)->get();
                    foreach ($examples as $example) {
                        \Log::info("Will reset: {$example->user->name} (NIM: {$example->user->nim}), Current Status: {$example->status_id}, Periode: '{$example->periode_wisuda}'");
                    }

                    // Perform the reset for ALL students
                    $resetCount = VerifikasiWisuda::whereNotIn('status_id', ['1'])
                        ->update([
                            'status_id' => '1', // Belum Diproses
                            'catatan' => 'Status direset karena perubahan periode wisuda pada ' . now()->format('d F Y H:i:s'),
                            'tanggal_proses' => null
                        ]);

                    \Log::info("Successfully reset {$resetCount} students (ALL students)");
                } else {
                    \Log::info("No students found to reset");
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Periode wisuda berhasil diperbarui!',
                'reset_count' => $resetCount
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
