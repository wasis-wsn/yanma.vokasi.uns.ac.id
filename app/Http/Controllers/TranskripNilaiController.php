<?php

namespace App\Http\Controllers;

use App\Exports\TranskripExport;
use App\Models\Layanan;
use App\Models\StatusTranskrip;
use App\Models\Tahun;
use App\Models\Template;
use App\Models\TranskripNilai;
use App\Models\PeriodeWisuda; // added
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // added
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TranskripNilaiController extends Controller
{
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        $transkrip = Auth::user()->transkripNilai;
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusTranskrip::all();
        return view('pages.transkrip.index', compact('transkrip', 'status', 'tahuns', 'templates', 'layanan'));
    }

    public function listStaff(Request $request)
    {
        // Filter berdasarkan tahun dari periode_wisuda dan status transkrip itu sendiri
        $list = TranskripNilai::with('user.prodis', 'status')
            ->whereRaw("SUBSTRING(periode_wisuda, 1, 4) = ?", [$request->year]);

        if ($request->status != 'all') {
            $list = $list->where('status_id', $request->status);
        }

        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                return '<input class="form-check-input" type="checkbox" value="' . encodeId($row->id) . '" name="ids">';
            })
            ->addColumn('action', function ($row) {
                $aksi = '';
                if (in_array($row->status_id, ['1', '2', '3'])) {
                    $aksi = '<button type="button" class="btn btn-primary btn-sm btn-block btn-update" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-pen"></i> Update
                            </button>';
                }
                return $aksi;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('tanggal_ambil', function ($row) {
                $tanggal_ambil = $row->tanggal_ambil;
                if ($tanggal_ambil) {
                    $tanggal_ambil = Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_ambil)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_ambil;
            })
            ->editColumn('periode_wisuda', function ($row) {
                return $this->formatPeriodeDisplay($row->periode_wisuda);
            })
            ->rawColumns(['id', 'action', 'status_id', 'tanggal_ambil', 'periode_wisuda'])
            ->toJson();
    }

    public function listFo(Request $request)
    {
        // Filter berdasarkan tahun dari periode_wisuda dan status transkrip itu sendiri
        $list = TranskripNilai::with('user.prodis', 'status')
            ->whereRaw("SUBSTRING(periode_wisuda, 1, 4) = ?", [$request->year]);

        if ($request->status != 'all') {
            $list = $list->where('status_id', $request->status);
        }

        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '';
                if ($row->status_id == 4) {
                    $aksi = '<button type="button" class="btn btn-primary btn-sm btn-block btn-update" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-hand"></i> Diambil
                            </button>';
                }
                return $aksi;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('tanggal_ambil', function ($row) {
                $tanggal_ambil = $row->tanggal_ambil;
                if ($tanggal_ambil) {
                    $tanggal_ambil = Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_ambil)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_ambil;
            })
            ->editColumn('periode_wisuda', function ($row) {
                return $this->formatPeriodeDisplay($row->periode_wisuda);
            })
            ->rawColumns(['action', 'status_id', 'tanggal_ambil', 'periode_wisuda'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        // Filter berdasarkan tahun dari periode_wisuda dan status transkrip itu sendiri
        $list = TranskripNilai::with('user.prodis', 'status')
            ->whereRaw("SUBSTRING(periode_wisuda, 1, 4) = ?", [$request->year]);

        if ($request->status != 'all') {
            $list = $list->where('status_id', $request->status);
        }

        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                return '<input class="form-check-input" type="checkbox" value="' . encodeId($row->id) . '" name="ids">';
            })
            ->addColumn('action', function () {
                return '';
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('tanggal_ambil', function ($row) {
                $tanggal_ambil = $row->tanggal_ambil;
                if ($tanggal_ambil) {
                    $tanggal_ambil = Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_ambil)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_ambil;
            })
            ->editColumn('periode_wisuda', function ($row) {
                return $this->formatPeriodeDisplay($row->periode_wisuda);
            })
            ->rawColumns(['id', 'action', 'status_id', 'tanggal_ambil', 'periode_wisuda'])
            ->toJson();
    }

    public function export(Request $request)
    {
        $request->validate([
            'tahun' => ['required']
        ], [
            'required' => ':attribute wajib diisi',
        ], [
            'tahun' => 'Periode Wisuda'
        ]);

        $tahun = $request->tahun;
        $name = 'Rekap_Data_Transkrip_Nilai_Tahun_' . $tahun;
        return Excel::download(new TranskripExport($tahun), $name . '.xlsx');
    }

    public function show($id)
    {
        try {
            $id = decodeId($id);
            $data = TranskripNilai::where('id', $id)->with('user.prodis', 'status')->first();
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan!'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_surat' => ['required'],
            'status_id' => ['required'],
        ], [
            'required' => ':attribute wajib diisi!',
            'in' => 'Nilai :attribute tidak valid!'
        ], [
            'no_surat' => 'Nomor Transkrip',
            'status_id' => 'Status',
        ]);

        try {
            $id = decodeId($id);
            $data = TranskripNilai::findOrFail($id);
            $update = [
                'no_transkrip' => $request->no_surat,
                'status_id' => $request->status_id,
                'catatan' => $request->catatan,
            ];
            if ($request->status_id == '5') $update['tanggal_ambil'] = new \DateTime();
            $data->update($update);
            return response()->json(['status' => true, 'message' => 'Data berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan!'], 500);
        }
    }

    public function updateMany(Request $request)
    {
        try {
            $ids = $request->ids;
            $status = $request->status;
            $catatan = '';
            if ($status == 4) {
                $catatan = "Silahkan mengambil berkas di Front Office SV. Pengambilan transkrip wajib membawa ktp asli atau
                surat kuasa jika diwakilkan";
            }
            $id_ajuan = [];
            foreach ($ids as $id) {
                $id_ajuan[] = (int)decodeId($id);
            }
            TranskripNilai::whereIn('id', $id_ajuan)->update([
                'status_id' => $status,
                'catatan' => $catatan,
            ]);
            return response()->json(['status' => true, 'message' => 'Status ajuan berhasil diupdate!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    // Add format helper similar to VerifikasiWisudaController
    private function formatPeriodeDisplay($periode_wisuda)
    {
        if (!$periode_wisuda) return '';

        try {
            // ekspektasi format "YYYY-MM"
            $parts = explode('-', $periode_wisuda);
            $year = intval($parts[0] ?? 0);
            $monthParsed = intval($parts[1] ?? 0);

            // Tentukan bulan yang akan ditampilkan (1..12) berdasarkan nilai pada kolom mahasiswa
            if ($monthParsed >= 0 && $monthParsed <= 11) {
                // kemungkinan disimpan 0-based di record mahasiswa
                $displayMonth = $monthParsed + 1;
            } else {
                // asumsi sudah 1..12
                $displayMonth = $monthParsed;
            }
            if ($displayMonth < 1 || $displayMonth > 12) $displayMonth = 1;

            // Prioritaskan pencocokan yang sesuai dengan data mahasiswa (exact/fallback)
            $periodeData = null;
            if ($monthParsed >= 1 && $monthParsed <= 12) {
                $periodeData = PeriodeWisuda::where('tahun', $year)->where('bulan', $monthParsed)->first();
                if (!$periodeData && $monthParsed - 1 >= 0) {
                    $periodeData = PeriodeWisuda::where('tahun', $year)->where('bulan', $monthParsed - 1)->first();
                }
            } else {
                $periodeData = PeriodeWisuda::where('tahun', $year)->where('bulan', $monthParsed)->first();
                if (!$periodeData) {
                    $periodeData = PeriodeWisuda::where('tahun', $year)->where('bulan', $displayMonth)->first();
                }
            }

            // Jika ditemukan periode dengan tanggal_wisuda: tampilkan hanya keterangan bawah (tanggal wisuda)
            if ($periodeData && $periodeData->tanggal_wisuda) {
                return '<small class="text-muted">' . Carbon::parse($periodeData->tanggal_wisuda)->translatedFormat('d F Y') . '</small>';
            }

            // Fallback: tampilkan nama bulan/tahun berdasarkan nilai mahasiswa
            return Carbon::createFromDate($year ?: now()->year, $displayMonth, 1)->translatedFormat('F Y');
        } catch (\Throwable $e) {
            Log::warning('formatPeriodeDisplay error (TranskripNilaiController): ' . $e->getMessage());
            return $periode_wisuda;
        }
    }
}
