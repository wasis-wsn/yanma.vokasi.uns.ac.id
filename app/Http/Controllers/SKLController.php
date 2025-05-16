<?php

namespace App\Http\Controllers;

use App\Exports\SKLExport;
use App\Models\Layanan;
use App\Models\SKL;
use App\Models\StatusPengesahanTA;
use App\Models\StatusSKL;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SKLController extends Controller
{
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusSKL::all(); // status SKL
        $prodis = Prodi::all();
        $status_ta = StatusPengesahanTA::all();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        return view('pages.skl.index', compact('tahuns', 'layanan', 'status', 'prodis', 'status_ta', 'templates'));
    }

    public function listStaff(Request $request)
    {
        $list = SKL::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') {
            $list = $list->where('status_id', $request->status);
        }
        
        // Uncomment dan perbaiki filter prodi
        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($query) use ($request) {
                $query->where('prodi', $request->prodi);
            });
        }

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                if (in_array($row->status_id, ['1', '2', '3', '4', '5'])) {
                    $aksi .= '<button type="button" class="btn btn-success btn-sm btn-block btn-proses" data-id="' . encodeId($row->id) . '" data-status="' . $row->status_id . '">
                                <i class="fa fa-check"></i> Proses
                            </button>';
                }
                if (in_array($row->status_id, ['6', '7'])) {
                    $aksi = '';
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
            ->addColumn('nama_prodi', function ($row) {
                return wordwrap($row->user->prodis->name, 20, "<br>");
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, "<br>");
            })
            ->rawColumns(['action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'tanggal_ambil', 'nama_prodi', 'catatan'])
            ->toJson();
    }

    public function listFo(Request $request)
    {
        $list = SKL::with('user.prodis', 'status')
            ->whereYear('created_at', $request->year);
            
        if ($request->status != 'all') {
            $list = $list->where('status_id', $request->status);
        }
        
        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($query) use ($request) {
                $query->where('prodi', $request->prodi);
            });
        }

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '';
                if ($row->status_id == 6) {
                    $aksi .= '<button type="button" class="btn btn-warning btn-sm btn-proses btn-block" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-hand"></i> Diambil
                            </button>';
                }
                return $aksi;
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
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->addColumn('nama_prodi', function ($row) {
                return wordwrap($row->user->prodis->name, 20, "<br>");
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, "<br>");
            })
            ->rawColumns(['action', 'created_at', 'status_id', 'tanggal_proses', 'tanggal_ambil', 'nama_prodi', 'catatan'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $list = SKL::with('user.prodis', 'status')
            ->whereYear('created_at', $request->year);
            
        if ($request->status != 'all') {
            $list = $list->where('status_id', $request->status);
        }
        
        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($query) use ($request) {
                $query->where('prodi', $request->prodi);
            });
        }

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
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
            ->addColumn('nama_prodi', function ($row) {
                return wordwrap($row->user->prodis->name, 20, "<br>");
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, "<br>");
            })
            ->rawColumns(['action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'tanggal_ambil', 'nama_prodi', 'catatan'])
            ->toJson();
    }

    public function export(Request $request)
    {
        $request->validate([
            'tahun' => ['required']
        ], [
            'required' => ':attribute wajib diisi',
        ], [
            'tahun' => 'Tahun'
        ]);

        $tahun = $request->tahun;
        $name = 'Rekap_Data_Surat_Keterangan_Lulus_Tahun_' . $tahun;
        return Excel::download(new SKLExport($tahun), $name . '.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'lembar_revisi' => ['required', 'file', 'mimes:pdf', 'max:1024'],
            'ss_ajuan_skl' => ['required', 'file', 'image', 'max:1024'],
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 1 MB!',
        ], [
            'lembar_revisi' => 'Lembar Persetujuan Revisi Tugas Akhir',
            'ss_ajuan_skl' => 'Screenshot Bukti Ajuan SKL di SIAKAD',
        ]);

        if (Auth::user()->pengajuanTTDTA == null) {
            return response()->json(['status' => false, 'message' => 'Silahkan mengajukan TTD Lembar Pengesahan TA sebelum mengajukan SKL'], 500);
        }
        if(!is_null(Auth::user()->skl)) {
            return response()->json([
                'status' => false, 
                'message' => 'Anda sudah pernah mengajukan SKL'
                ], 500);
        }
        try {
            $lembarRevisinName = 'Revisi-' . Auth::user()->nim . '-' . time() . '.pdf';
            $SSextension = $request->file('ss_ajuan_skl')->getClientOriginalExtension();
            $ssAjuanName = 'ScreenShoot-' . Auth::user()->nim . '-' . time() . '.' . $SSextension;
            $request->file('lembar_revisi')->storeAs('skl/upload/', $lembarRevisinName, 'public');
            $request->file('ss_ajuan_skl')->storeAs('skl/upload/', $ssAjuanName, 'public');

            SKL::create([
                'user_id' => Auth::user()->id,
                'status_id' => '1',
                'lembar_revisi' => $lembarRevisinName,
                'ss_ajuan_skl' => $ssAjuanName,
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Ditambahkan!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function show($id)
    {
        $id = decodeId($id);
        $data = SKL::with('user.prodis', 'status')->where('id', $id)->first();
        return response()->json(['status' => true, 'data' => $data], 200);
    }

    public function revisi(Request $request, $id)
    {
        $request->validate([
            'lembar_revisi' => ['file', 'mimes:pdf', 'max:1024'],
            'ss_ajuan_skl' => ['file', 'image', 'max:1024'],
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 1 MB!',
        ], [
            'catatan' => 'Catatan Reviewer',
            'lembar_revisi' => 'Lembar Persetujuan Revisi Tugas Akhir',
            'ss_ajuan_skl' => 'Screenshot Bukti Ajuan SKL di SIAKAD',
        ]);

        try {
            $id = decodeId($id);
            $ajuan = SKL::findOrFail($id);
            $data['status_id'] = '1';

            if ($request->has('lembar_revisi')) {
                $lembarRevisinName = 'Revisi-' . Auth::user()->nim . '-' . time() . '.pdf';
                $request->file('lembar_revisi')->storeAs('skl/upload/', $lembarRevisinName, 'public');
                Storage::disk('public')->delete('skl/upload/' . $ajuan->lembar_revisi);
                $data['lembar_revisi'] = $lembarRevisinName;
            }
            if ($request->has('ss_ajuan_skl')) {
                $SSextension = $request->file('ss_ajuan_skl')->getClientOriginalExtension();
                $ssAjuanName = 'ScreenShoot-' . Auth::user()->nim . '-' . time() . '.' . $SSextension;
                $request->file('ss_ajuan_skl')->storeAs('skl/upload/', $ssAjuanName, 'public');
                Storage::disk('public')->delete('skl/upload/' . $ajuan->ss_ajuan_skl);
                $data['ss_ajuan_skl'] = $ssAjuanName;
            }

            $ajuan->update($data);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Direvisi!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function proses(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required',
            'no_surat' => Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['3', '4', '6']);
            }),
        ], [
            'required_if' => ':attribute wajib diisi!'
        ], [
            'no_surat' => 'Nomor Surat'
        ]);

        try {
            $id = decodeId($id);
            $ajuan = SKL::findOrFail($id);
            $data['status_id'] = $request->status_id;
            $data['catatan'] = $request->catatan;
            $data['tanggal_proses'] = new \DateTime();
            if (in_array($request->status_id, ['3', '4', '5'])) {
                $data['no_surat'] = $request->no_surat;
            }
            if ($request->status_id == '6') {
                Storage::disk('public')->delete('skl/upload/' . $ajuan->lembar_revisi);
                Storage::disk('public')->delete('skl/upload/' . $ajuan->ss_ajuan_skl);
            }
            if ($request->status_id == '7') {
                $data['tanggal_ambil'] = new \DateTime();
            }
            $ajuan->update($data);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
        return response()->json(['status' => true, 'message' => 'Status Berhasil Diperbarui!'], 200);
    }
}
