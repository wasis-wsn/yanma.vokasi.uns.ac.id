<?php

namespace App\Http\Controllers;

use App\Exports\UndurDiriExport;
use App\Models\Layanan;
use App\Models\Semester;
use App\Models\StatusHeregistrasi;
use App\Models\Tahun;
use App\Models\TahunAkademik;
use App\Models\Template;
use App\Models\UndurDiri;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class UndurDiriController extends Controller
{
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusHeregistrasi::all();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_akademik', 'desc')->limit('6')->get();
        $semester = Semester::all();
        session(['layanan' => $layanan]);
        return view('pages.undur_diri.index', compact('tahuns', 'layanan', 'status', 'templates', 'tahunAkademik', 'semester'));
    }

    public function listMahasiswa()
    {
        $data = UndurDiri::with('status', 'tahunAkademik', 'semester')->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i>
                        </button>';
                if ($row->status_id == '1' || $row->status_id == '2') {
                    $aksi .= '<button type="button" class="btn btn-warning btn-sm btn-edit" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-pen"></i>
                            </button>';
                    $aksi .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-trash"></i>
                            </button>';
                }

                return $aksi;
            })
            ->addColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y');
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('tanggal_ambil', function ($row) {
                $tanggal_ambil = $row->tanggal_ambil;
                if ($tanggal_ambil) {
                    $tanggal_ambil = Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_ambil)->translatedFormat('H:i:s');
                }
                return $tanggal_ambil;
            })
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik->tahun_akademik . ' - ' . $row->semester->semester;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 30, "<br>");
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_proses', 'status_id', 'tahun_akademik', 'tanggal_ambil', 'catatan'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $list = UndurDiri::with('user.prodis', 'status', 'tahunAkademik', 'semester')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                return '<input class="form-check-input" type="checkbox" value="' . encodeId($row->id) . '" name="ids">';
            })
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                if (in_array($row->status_id, ['1', '2', '3', '4', '6'])) {
                    $aksi .= '<button type="button" class="btn btn-success btn-sm btn-block btn-proses" data-id="' . encodeId($row->id) . '" data-status="' . $row->status_id . '">
                                <i class="fa fa-check"></i> Proses
                            </button>';
                }
                if (in_array($row->status_id, ['5', '7'])) {
                    $aksi = '';
                }

                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik->tahun_akademik . ' - ' . $row->semester->semester;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('nama_prodi', function ($row) {
                return wordwrap($row->user->prodis->name, 20, "<br>");
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 30, "<br>");
            })
            ->rawColumns(['id', 'action', 'tanggal_submit', 'tahun_akademik', 'status_id', 'nama_prodi', 'catatan'])
            ->toJson();
    }

    public function listFo(Request $request)
    {
        $list = UndurDiri::with('user.prodis', 'status', 'tahunAkademik', 'semester')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

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
            ->editColumn('tanggal_ambil', function ($row) {
                return ($row->tanggal_ambil) ? Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_ambil)->translatedFormat('H:i:s') : '';
            })
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik->tahun_akademik . ' - ' . $row->semester->semester;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['action', 'tanggal_ambil', 'tahun_akademik', 'status_id'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $list = UndurDiri::with('user.prodis', 'status', 'tahunAkademik', 'semester')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                return '<input class="form-check-input" type="checkbox" value="' . encodeId($row->id) . '" name="ids">';
            })
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik->tahun_akademik . ' - ' . $row->semester->semester;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('nama_prodi', function ($row) {
                return wordwrap($row->user->prodis->name, 20, "<br>");
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 30, "<br>");
            })
            ->rawColumns(['id', 'action', 'tanggal_submit', 'tahun_akademik', 'status_id', 'nama_prodi', 'catatan'])
            ->toJson();
    }

    public function setting(Request $request)
    {
        $request->validate([
            'semester_id' => ['required'],
            'tahun_akademik_id' => ['required'],
        ], [
            'required' => ':attribute wajib diisi',
        ], [
            'semester_id' => 'Semester',
            'tahun_akademik_id' => 'Tahun Akademik',
        ]);

        try {
            $layanan = session('layanan');
            $layanan->update($request->input());
            return response()->json(['status' => true, 'message' => 'Pengaturan layanan berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester_id' => 'required',
            'tahun_akademik_id' => 'required',
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'file' => 'File PDF',
            'semester_id' => 'Semester Pengajuan Selang/Cuti',
            'tahun_akademik_id' => 'Tahun Akademik',
        ]);

        try {
            $file = 'UndurDiri' . '_' . Auth::user()->nim . '_' . Str::of(Auth::user()->name)->replace(' ','') . '_' . time() . '.pdf';
            $request->file('file')->storeAs('undur/upload/', $file, 'public');

            UndurDiri::create([
                'user_id' => Auth::user()->id,
                'status_id' => '1',
                'semester_id' => $request->semester_id,
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'file' => $file,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
        return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Ditambahkan!'], 200);
    }

    public function show($id)
    {
        $id = decodeId($id);
        $data = UndurDiri::where('id', $id)->with('user.prodis', 'status', 'tahunAkademik', 'semester')->first();
        $data['tgl_proses'] = ($data->tanggal_proses) ? Carbon::parse($data->tanggal_proses)->translatedFormat('d F Y H:i:s') . ' WIB' : '';
        return response()->json(['status' => true, 'data' => $data], 200);
    }

    public function revisi(Request $request, string $id)
    {
        $request->validate([
            'file' => ['file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'file' => 'Dokumen Persyaratan',
        ]);

        try {
            $id = decodeId($id);
            $ajuan = UndurDiri::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '2'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat mengedit data'], 500);
            }
            $message = ($ajuan->status_id == '2') ? 'Direvisi' : 'Diedit';

            $file = $ajuan->file;
            if ($request->hasFile('file')) {
                $file = 'UndurDiri' . '_' . Auth::user()->nim . '_' . Str::of(Auth::user()->name)->replace(' ','') . '_' . time() . '.pdf';
                $request->file('file')->storeAs('undur/upload/', $file, 'public');
                Storage::disk('public')->delete('undur/upload/' . $ajuan->file);
            }

            $ajuan->update([
                'status_id' => '1',
                'file' => $file,
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil ' . $message], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $id = decodeId($id);
            $ajuan = UndurDiri::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '2'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat menghapus data'], 500);
            }
            Storage::disk('public')->delete('undur/upload/' . $ajuan->file);
            $ajuan->delete();
            return response()->json(['status' => true, 'message' => 'Ajuan berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'tahun_id' => ['required'],
            'semester_id' => ['required'],
        ], [
            'required' => ':attribute wajib diisi',
        ], [
            'tahun_id' => 'Tahun Akademik',
            'semester_id' => 'Semester Akademik'
        ]);

        $tahun = TahunAkademik::findOrFail($request->tahun_id);
        $semester = Semester::findOrFail($request->semester_id);
        $tahun_akademik = explode('/', $tahun->tahun_akademik);
        $name = 'Rekap_Data_Undur_Diri_Tahun_Akademik_' . $tahun_akademik[0] . '_' . $tahun_akademik[1] . '_' . $semester->semester;
        return Excel::download(new UndurDiriExport($tahun, $semester), $name . '.xlsx');
    }

    public function proses(Request $request, $id)
    {
        $request->validate([
            'status_id' => ['required'],
            'no_surat' => Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['3', '4', '6']);
            }),
            'catatan' => Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['2', '5']);
            })
        ], [
            'required' => ':attribute harus diisi!'
        ]);

        $return = [
            'status' => true,
            'message' => 'Ajuan Berhasil Diproses!',
        ];
        $status_code = 200;

        try {
            $id = decodeId($id);
            $ajuan = UndurDiri::where('id', $id)->with('user.prodis', 'status')->first();

            $data_update = [
                'no_surat' => $ajuan->no_surat,
                'status_id' => $request->status_id,
                'catatan' => $request->catatan,
                'tanggal_proses' => new \DateTime(),
                'tanggal_ambil' => $ajuan->tanggal_ambil,
            ];

            if (in_array($request->status_id, ['3', '4', '6'])) { // ajuan diproses
                $data_update['no_surat'] = $request->no_surat;
            }
            if (in_array($request->status_id, ['5', '6'])) { // ajuan ditolak / Selesai
                Storage::disk('public')->delete('undur/upload/' . $ajuan->file);
            }
            if ($request->status_id == '7') { // surat diambil
                $data_update['tanggal_ambil'] = new \DateTime();
                $return['message'] = 'Ajuan telah diambil!';
            }

            $ajuan->update($data_update);
        } catch (\Throwable $th) {
            $return['status'] = false;
            $return['message'] = 'Terjadi Kesalahan!';
            $status_code = 500;
        }

        return response()->json($return, $status_code);
    }
}
