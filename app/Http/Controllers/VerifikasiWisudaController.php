<?php

namespace App\Http\Controllers;

use App\Exports\VerifWisudaExport;
use App\Models\Layanan;
use App\Models\SKPI;
use App\Models\StatusWisuda;
use App\Models\Tahun;
use App\Models\Template;
use App\Models\TranskripNilai;
use App\Models\VerifikasiWisuda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class VerifikasiWisudaController extends Controller
{
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $ajuan = $this->canStore();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusWisuda::all();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        return view('pages.verifikasi_wisuda.index', compact('ajuan', 'layanan', 'tahuns', 'status', 'templates'));
    }

    private function canStore()
    {
        if (is_null(auth()->user()->skl)) return false;
        if (!in_array(auth()->user()->skl->status_id, ['5', '6'])) return false;
        if (!is_null(auth()->user()->verifikasiWisuda)) return false;
        return true;
    }

    public function list(Request $request)
    {
        $data = VerifikasiWisuda::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $data = $data->where('status_id', $request->status);
        $data = $data->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                        <i class="fa fa-eye"></i> Review
                    </button>';
                if ($row->status_id == 1) {
                    $aksi .= '<button type="button" class="btn btn-warning btn-sm btn-proses btn-block" data-nim="' . $row->user->nim . '" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-pen"></i> proses
                        </button>';
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
            ->editColumn('periode_wisuda', function ($row) {
                $periode_wisuda = $row->periode_wisuda;
                if ($periode_wisuda) {
                    $periode_wisuda = Carbon::createFromFormat('Y-m', $row->periode_wisuda)->translatedFormat('F Y');
                }
                return $periode_wisuda;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'periode_wisuda'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $data = VerifikasiWisuda::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $data = $data->where('status_id', $request->status);
        $data = $data->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                        <i class="fa fa-eye"></i> Review
                    </button>';
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
            ->editColumn('periode_wisuda', function ($row) {
                $periode_wisuda = $row->periode_wisuda;
                if ($periode_wisuda) {
                    $periode_wisuda = Carbon::createFromFormat('Y-m', $row->periode_wisuda)->translatedFormat('F Y');
                }
                return $periode_wisuda;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'periode_wisuda'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:102400']
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran :attribute tidak boleh lebih dari 100 MB',
        ], [
            'file' => 'File PDF'
        ]);

        if (!$this->canStore()) {
            return response()->json(['status' => false, 'message' => 'Anda tidak dapat mengajukan verifikasi wisuda'], 500);
        }

        try {
            $fileName = 'VERIFWISUDA_' . trim(Auth::user()->name) . '_' . Auth::user()->nim . '_' . trim(Auth::user()->prodis->name) . '_' . time() . '.pdf';
            $request->file('file')->storeAs('verifWisuda/upload/', $fileName, 'public');

            VerifikasiWisuda::create([
                'user_id' => Auth::user()->id,
                'status_id' => '1',
                'file' => $fileName,
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Ditambahkan!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
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
        $name = 'Rekap_Data_Verifikasi_Wisuda_Tahun_' . $tahun;
        return Excel::download(new VerifWisudaExport($tahun), $name . '.xlsx');
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

    public function update(Request $request, $id)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:102400']
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran :attribute tidak boleh lebih dari 100 MB',
        ], [
            'file' => 'File PDF'
        ]);

        try {
            $id = decodeId($id);
            $ajuan = VerifikasiWisuda::findOrFail($id);

            $fileName = 'VERIFWISUDA_' . trim(Auth::user()->name) . '_' . Auth::user()->nim . '_' . trim(Auth::user()->prodis->name) . '_' . time() . '.pdf';
            $request->file('file')->storeAs('verifWisuda/upload/', $fileName, 'public');
            Storage::disk('public')->delete('verifWisuda/upload/' . $ajuan->file);

            $ajuan->update([
                'file' => $fileName,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
        return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Diedit!'], 200);
    }

    public function proses(Request $request, $id)
    {
        $request->validate([
            'status_id' => ['required'],
            'no_seri_ijazah' => ['required_if:status_id,2'],
            'periode_wisuda' => ['required_if:status_id,2'],
            'kode_akses' => ['required_if:status_id,2'],
        ], [
            'required' => ':attribute wajib diisi!',
            'required_if' => ':attribute wajib diisi!',
        ], [
            'status_id' => 'Status Verifikasi',
            'no_seri_ijazah' => 'No Seri Ijazah',
            'periode_wisuda' => 'Periode Wisuda',
            'kode_akses' => 'Kode Akses Wisuda',
        ]);

        try {
            $id = decodeId($id);
            $ajuan = VerifikasiWisuda::findOrFail($id);

            if ($request->status_id == '2') {
                Storage::disk('public')->delete('verifWisuda/upload/' . $ajuan->file);
            }

            $ajuan->update([
                'status_id' => $request->status_id,
                'no_seri_ijazah' => $request->no_seri_ijazah,
                'periode_wisuda' => $request->periode_wisuda,
                'kode_akses' => $request->kode_akses,
                'catatan' => $request->catatan,
                'tanggal_proses' => new \DateTime(),
            ]);

            if ($request->status_id == '2') {
                $mahasiswa_id = $ajuan->user_id;
                TranskripNilai::create([
                    'user_id' => $mahasiswa_id,
                    'status_id' => '1',
                    'periode_wisuda' => $request->periode_wisuda,
                ]);
                SKPI::create([
                    'user_id' => $mahasiswa_id,
                    'status_id' => '1',
                    'periode_wisuda' => $request->periode_wisuda,
                ]);
            }
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Diproses!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
    }
}
