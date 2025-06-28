<?php

namespace App\Http\Controllers;

use App\Models\PeriodeWisuda;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
            'is_active' => 'boolean'
        ]);

        try {
            $periode = PeriodeWisuda::findOrFail($id);

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

            return response()->json([
                'status' => true,
                'message' => 'Periode wisuda berhasil diperbarui!'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $th->getMessage()
            ], 500);
        }
    }
}
