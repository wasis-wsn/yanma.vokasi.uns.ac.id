<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Lpj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\StatusLPJ;
use App\Models\Tahun;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;

class LPJController extends Controller
{
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusLPJ::all();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        return view('pages.lpj.index', compact('tahuns', 'layanan', 'status', 'templates'));
    }

    public function listMahasiswa()
    {
        $user_id = Auth::user()->id;
        $data = Lpj::with('suratTugas', 'status')
                    ->whereHas('suratTugas', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                $aksi = '';
                if (in_array($row->status_id, ['1', '2', '3'])) { // status belum upload, sudah upload, revisi
                    $aksi .= '<button type="button" class="btn btn-info btn-sm btn-upload" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-upload"></i> Upload LPJ
                        </button>';
                }
                if ($row->file) {
                    $aksi .= '<a href="' . asset('storage/lpj/' . $row->file) . '" class="btn btn-success btn-sm" target="_blank">
                                <i class="fa fa-file"></i> Lihat LPJ
                            </a>';
                }
                return $aksi;
            })
            ->addColumn('no_surat', function ($row) {
                $btn = '<a href="' . asset('storage/surat_tugas/hasil/' . $row->suratTugas->surat_hasil) . '" class="btn btn-primary btn-sm" target="_blank">
                            <i class="fa fa-file"></i>
                        </a>';
                return $row->suratTugas->no_surat . $btn;
            })
            ->addColumn('nama_kegiatan', function ($row) {
                return $row->suratTugas->nama_kegiatan;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['id', 'no_surat', 'status_id'])
            ->toJson();
    }

    public function listOrmawa()
    {
        $user_id = Auth::user()->id;
        $data = Lpj::with('sik', 'status')
                    ->whereHas('sik', function ($query) use ($user_id) {
                        $query->where('ormawa_id', $user_id);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                $aksi = '';
                if (in_array($row->status_id, ['1', '2', '3'])) { // status belum upload, sudah upload, revisi
                    $aksi .= '<button type="button" class="btn btn-info btn-sm btn-upload" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-upload"></i> Upload LPJ
                        </button>';
                }
                if ($row->file) {
                    $aksi .= '<a href="' . asset('storage/lpj/' . $row->file) . '" class="btn btn-success btn-sm" target="_blank">
                                <i class="fa fa-file"></i> Lihat LPJ
                            </a>';
                }
                return $aksi;
            })
            ->addColumn('no_surat', function ($row) {
                $btn = '<a href="' . asset('storage/sik/hasil/' . $row->sik->surat_hasil) . '" class="btn btn-primary btn-sm" target="_blank">
                            <i class="fa fa-file"></i>
                        </a>';
                return $row->sik->no_surat . $btn;
            })
            ->addColumn('nama_kegiatan', function ($row) {
                return $row->sik->nama_kegiatan;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['id', 'no_surat', 'status_id'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $data = Lpj::with('suratTugas', 'sik', 'status');
        if ($request->status != 'all') $data = $data->where('status_id', $request->status);
        $data = $data->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                $aksi = '';
                if ($row->file) { // status sudah upload, revisi
                    $aksi .= '<a href="' . asset('storage/lpj/' . $row->file) . '" class="btn btn-success btn-sm" target="_blank">
                                <i class="fa fa-file"></i> Lihat LPJ
                            </a>';
                }
                if (in_array($row->status_id, ['2'])) { // status sudah upload, revisi
                    $aksi .= '<button type="button" class="btn btn-info btn-sm btn-validasi" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-check"></i> Validasi LPJ
                        </button>';
                }
                return $aksi;
            })
            ->addColumn('no_surat', function ($row) {
                if ($row->suratTugas) {
                    $btn = '<a href="' . asset('storage/surat_tugas/hasil/' . $row->suratTugas->surat_hasil) . '" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fa fa-file"></i>
                            </a>';
                    return $row->suratTugas->no_surat . $btn;
                } else {
                    $btn = '<a href="' . asset('storage/sik/hasil/' . $row->sik->surat_hasil) . '" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fa fa-file"></i>
                            </a>';
                    return $row->sik->no_surat . $btn;
                }
            })
            ->addColumn('nama_kegiatan', function ($row) {
                if ($row->suratTugas) {
                    return $row->suratTugas->nama_kegiatan;
                } else {
                    return $row->sik->nama_kegiatan;
                }
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['id', 'no_surat', 'status_id'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $data = Lpj::with('suratTugas', 'sik', 'status');
        if ($request->status != 'all') $data = $data->where('status_id', $request->status);
        $data = $data->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                $aksi = '';
                if ($row->file) { // status sudah upload, revisi
                    $aksi .= '<a href="' . asset('storage/lpj/' . $row->file) . '" class="btn btn-success btn-sm" target="_blank">
                                <i class="fa fa-file"></i> Lihat LPJ
                            </a>';
                }
                return $aksi;
            })
            ->addColumn('no_surat', function ($row) {
                if ($row->suratTugas) {
                    $btn = '<a href="' . asset('storage/surat_tugas/hasil/' . $row->suratTugas->surat_hasil) . '" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fa fa-file"></i>
                            </a>';
                    return $row->suratTugas->no_surat . $btn;
                } else {
                    $btn = '<a href="' . asset('storage/sik/hasil/' . $row->sik->surat_hasil) . '" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fa fa-file"></i>
                            </a>';
                    return $row->sik->no_surat . $btn;
                }
            })
            ->addColumn('nama_kegiatan', function ($row) {
                if ($row->suratTugas) {
                    return $row->suratTugas->nama_kegiatan;
                } else {
                    return $row->sik->nama_kegiatan;
                }
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['id', 'no_surat', 'status_id'])
            ->toJson();
    }

    public function listAdminProdi(Request $request)
    {
        $data = Lpj::with('suratTugas', 'sik', 'status');
        if ($request->status != 'all') $data = $data->where('status_id', $request->status);
        $data = $data->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                $aksi = '';
                if ($row->file) { // status sudah upload, revisi
                    $aksi .= '<a href="' . asset('storage/lpj/' . $row->file) . '" class="btn btn-success btn-sm" target="_blank">
                                <i class="fa fa-file"></i> Lihat LPJ
                            </a>';
                }
                return $aksi;
            })
            ->addColumn('no_surat', function ($row) {
                if ($row->suratTugas) {
                    $btn = '<a href="' . asset('storage/surat_tugas/hasil/' . $row->suratTugas->surat_hasil) . '" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fa fa-file"></i>
                            </a>';
                    return $row->suratTugas->no_surat . $btn;
                } else {
                    $btn = '<a href="' . asset('storage/sik/hasil/' . $row->sik->surat_hasil) . '" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fa fa-file"></i>
                            </a>';
                    return $row->sik->no_surat . $btn;
                }
            })
            ->addColumn('nama_kegiatan', function ($row) {
                if ($row->suratTugas) {
                    return $row->suratTugas->nama_kegiatan;
                } else {
                    return $row->sik->nama_kegiatan;
                }
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['id', 'no_surat', 'status_id'])
            ->toJson();
    }

    public function upload(Request $request, $id)
    {
        $request->validate([
            'file' => ['required','file','mimes:pdf','max:10240']
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran :attribute tidak boleh melebihi 10 mb!',
        ], [
            'file' => 'File LPJ'
        ]);

        try {
            $id = decodeId($id);
            $ajuan = Lpj::findOrFail($id);
            if ($ajuan->status_id == '4') {
                throw new Exception('Tidak dapat mengupload LPJ');
            }
            $fileName = $request->file->getClientOriginalName() . '.pdf';
            $request->file->storeAs('lpj/', $fileName, 'public');
            if (!is_null($ajuan->file)) { Storage::disk('public')->delete('lpj/'. $ajuan->file); }

            $ajuan->update([
                'status_id' => '2', // status sudah upload
                'file' => $fileName,
            ]);
            return response()->json(['status' => true, 'message' => 'LPJ berhasil Diupload!'], 200);
        } catch (\Throwable $th) {
            if ($th->getMessage() === 'Tidak dapat mengupload LPJ') {
                return response()->json(['status' => false, 'message' => $th->getMessage()], 400);
            }
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function proses(Request $request, $id)
    {
        $request->validate([
            'status_id' => ['required'],
        ]);

        try {
            $id = decodeId($id);
            Lpj::findOrFail($id)->update([
                'status_id' => $request->status_id,
                'catatan' => $request->catatan,
                'tanggal_proses' => new \DateTime(),
            ]);
            return response()->json(['status' => true, 'message' => 'LPJ berhasil diproses!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }
}
