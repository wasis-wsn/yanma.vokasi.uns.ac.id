<?php

namespace App\Http\Controllers;

use App\Exports\TranskripExport;
use App\Models\Layanan;
use App\Models\StatusTranskrip;
use App\Models\Tahun;
use App\Models\Template;
use App\Models\TranskripNilai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return view('pages.transkrip.index', compact('transkrip', 'status', 'tahuns', 'templates'));
    }

    public function listStaff(Request $request)
    {
        $list = TranskripNilai::with('user.prodis', 'status')->whereRaw("SUBSTRING(periode_wisuda, 1, 4) = ?", [$request->year]);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
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
                $periode_wisuda = $row->periode_wisuda;
                if ($periode_wisuda) {
                    $periode_wisuda = Carbon::createFromFormat('Y-m', $row->periode_wisuda)->translatedFormat('F Y');
                }
                return $periode_wisuda;
            })
            ->rawColumns(['id', 'action', 'status_id', 'tanggal_ambil', 'periode_wisuda'])
            ->toJson();
    }

    public function listFo(Request $request)
    {
        $list = TranskripNilai::with('user.prodis', 'status')->whereRaw("SUBSTRING(periode_wisuda, 1, 4) = ?", [$request->year]);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
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
                $periode_wisuda = $row->periode_wisuda;
                if ($periode_wisuda) {
                    $periode_wisuda = Carbon::createFromFormat('Y-m', $row->periode_wisuda)->translatedFormat('F Y');
                }
                return $periode_wisuda;
            })
            ->rawColumns(['action', 'status_id', 'tanggal_ambil', 'periode_wisuda'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $list = TranskripNilai::with('user.prodis', 'status')->whereRaw("SUBSTRING(periode_wisuda, 1, 4) = ?", [$request->year]);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
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
                $periode_wisuda = $row->periode_wisuda;
                if ($periode_wisuda) {
                    $periode_wisuda = Carbon::createFromFormat('Y-m', $row->periode_wisuda)->translatedFormat('F Y');
                }
                return $periode_wisuda;
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
            'no_surat' => ['required_if:status,2'],
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
}
