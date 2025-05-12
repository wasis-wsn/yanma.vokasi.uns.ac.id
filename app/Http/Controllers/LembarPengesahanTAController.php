<?php

namespace App\Http\Controllers;

use App\Models\PengajuanTTDTA;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LembarPengesahanTAController extends Controller
{
    public function listFo(Request $request)
    {
        $list = PengajuanTTDTA::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '';
                if (in_array($row->status_id, ['1', '2', '3', '4'])) {
                    $aksi .= '<button type="button" class="btn btn-success btn-sm btn-block btn-proses" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-check"></i> Proses
                            </button>';
                }
                if (in_array($row->status_id, ['1'])) {
                    $aksi .= '<button type="button" class="btn btn-danger btn-sm btn-block btn-delete" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-trash"></i> Hapus
                            </button>';
                }
                if (in_array($row->status_id, ['5'])) {
                    $aksi = '';
                }
                return $aksi;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_proses;
            })
            ->editColumn('tanggal_ambil', function ($row) {
                $tanggal_ambil = $row->tanggal_ambil;
                if ($tanggal_ambil) {
                    $tanggal_ambil = Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_ambil)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_ambil;
            })
            ->rawColumns(['action', 'created_at', 'status_id', 'tanggal_proses', 'tanggal_ambil'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $list = PengajuanTTDTA::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_proses;
            })
            ->editColumn('tanggal_ambil', function ($row) {
                $tanggal_ambil = $row->tanggal_ambil;
                if ($tanggal_ambil) {
                    $tanggal_ambil = Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_ambil)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_ambil;
            })
            ->rawColumns(['created_at', 'status_id', 'tanggal_proses', 'tanggal_ambil'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric'
        ]);
        try {
            $user_id = $request->user_id;
            $mahasiswa = User::findOrFail($user_id);
            if(!is_null($mahasiswa->pengajuanTTDTA)) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Tidak dapat mengajukan karena mahasiswa sudah pernah mengajukan TTD Lembar Pengesahan TA'
                    ], 500);
            }
            PengajuanTTDTA::create([
                'user_id' => $user_id,
                'status_id' => '1',
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Ditambahkan!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function show($id)
    {
        $id = decodeId($id);
        $data = PengajuanTTDTA::with('user', 'status')->where('id', $id)->first();
        return response()->json(['data' => $data], 200);
    }

    public function proses(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|numeric'
        ]);
        try {
            $id = decodeId($id);
            $data['status_id'] = $request->status_id;
            $data['catatan'] = $request->catatan;
            if ($request->status_id == '5') {
                $data['tanggal_ambil'] = new \DateTime();
            } else {
                $data['tanggal_proses'] = new \DateTime();
            }
            PengajuanTTDTA::findOrFail($id)->update($data);
            return response()->json(['status' => true, 'message' => 'Status Berhasil Diperbarui!'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $id = decodeId($id);
            PengajuanTTDTA::findOrFail($id)->delete();
            return response()->json(['status' => true, 'message' => 'Ajuan berhasil dihapus!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }
}
