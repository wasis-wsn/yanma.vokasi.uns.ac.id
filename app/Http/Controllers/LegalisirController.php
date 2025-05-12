<?php

namespace App\Http\Controllers;

use App\Exports\LegalisirExport;
use App\Models\Layanan;
use App\Models\Legalisir;
use App\Models\Prodi;
use App\Models\StatusLegalisir;
use App\Models\Tahun;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LegalisirController extends Controller
{
    public function landingPage(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        return view('landingpage.legalisir.index', compact('templates'));
    }

    public function search(Request $request)
    {
        $q = $request->q;
        $data = Legalisir::with('prodi')
            ->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($q) . '%')
                    ->orWhere(DB::raw('LOWER(nim)'), 'LIKE', '%' . strtolower($q) . '%');
            })
            // ->where('status_id', '!=', '4')
            ->orderBy('nim')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Meng-hash ID setiap objek dalam koleksi data
        $hashedData = $data->map(function ($item) {
            $id = strval($item['id']);
            $item = $item->toArray();
            $item['id'] = encodeId($id);
            return $item;
        });
        return response()->json($hashedData, 200);
    }

    public function detail(Request $request)
    {
        $id = decodeId($request->id);
        $data = Legalisir::where('id', $id)->with('prodi', 'status')->first();
        return view('landingpage.legalisir.detail', compact('data'));
    }

    public function index()
    {
        $prodis = Prodi::orderBy('name')->get();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusLegalisir::all();
        return view('pages.legalisir.index', compact('tahuns', 'prodis', 'status'));
    }

    public function listFo(Request $request)
    {
        $list = Legalisir::with('prodi', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                        <i class="fa fa-eye"></i> Review
                    </button>';
                if (in_array($row->status_id, ['1', '2', '3'])) {
                    $aksi .= '<button type="button" class="btn btn-warning btn-sm btn-edit btn-block" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-pen"></i> Edit
                        </button>';
                    $aksi .= '<button type="button" class="btn btn-danger btn-sm btn-delete btn-block" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-trash"></i> Hapus
                        </button>';
                }
                $aksi .= '<button type="button" class="btn btn-success btn-sm btn-proses btn-block" data-id="' . encodeId($row->id) . '"  data-status="' . $row->status_id . '">
                            <i class="fa fa-check"></i> Proses
                        </button>';
                return $aksi;
            })
            ->addColumn('tanggal_submit', function ($row) {
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
            ->rawColumns(['id', 'action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'tanggal_ambil'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $list = Legalisir::with('prodi', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                        <i class="fa fa-eye"></i> Review
                    </button>';
                return $aksi;
            })
            ->addColumn('tanggal_submit', function ($row) {
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
            ->rawColumns(['id', 'action', 'tanggal_submit', 'status_id', 'tanggal_proses', 'tanggal_ambil'])
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
        $name = 'Rekap_Data_Legalisir_Tahun_' . $tahun;
        return Excel::download(new LegalisirExport($tahun), $name . '.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'nim' => ['required', 'string'],
            // 'prodi_id' => ['required', 'numeric'],
            'no_wa' => ['required', 'numeric'],
            'legalisir' => ['required'],
            'jumlah' => ['required', 'numeric'],
            'keperluan' => ['required', 'string'],
            'tahun_lulus' => ['required', 'numeric'],
        ], [
            'required' => ':attribute wajib diisi',
            'string' => ':attribute tidak valid',
            'numeric' => ':attribute tidak valid',
        ], [
            'name' => 'Nama Lengkap Alumni',
            'nim' => 'NIM Alumni',
            'prodi_id' => 'Program Studi Alumni',
            'no_wa' => 'Nomor WhatsApp Alumni',
            'legalisir' => 'Legalisir',
            'jumlah' => 'Jumlah Legalisir',
            'keperluan' => 'Keperluan',
            'tahun_lulus' => 'Tahun Lulus',
        ]);

        try {
            $prodi_id = '';
            if ($request->namaprodi != '' || $request->namaprodi != null) {
                $prodi = Prodi::create(['name' => $request->namaprodi]);
                $prodi_id = $prodi->id;
            } else {
                $prodi_id = $request->prodi_id;
            }
            $legalisir = '';
            foreach ($request->legalisir as $item) {
                $legalisir .= $item . ', ';
            }
            Legalisir::create([
                'status_id' => '1',
                'name' => $request->name,
                'nim' => $request->nim,
                'prodi_id' => $prodi_id,
                'no_wa' => $request->no_wa,
                'jumlah' => $request->jumlah,
                'keperluan' => $request->keperluan,
                'tahun_lulus' => $request->tahun_lulus,
                'legalisir' => $legalisir,
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan berhasil ditambahkan!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => true, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    public function show($id)
    {
        try {
            $id = decodeId($id);
            $data = Legalisir::where('id', $id)->with('prodi', 'status')->first();
            $array = explode(', ', $data['legalisir']);
            if (end($array) === '') {
                array_pop($array);
            }
            $data['legalisir'] = $array;
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'nim' => ['required', 'string'],
            // 'prodi_id' => ['required', 'numeric'],
            'no_wa' => ['required', 'numeric'],
            'legalisir' => ['required'],
            'jumlah' => ['required', 'numeric'],
            'keperluan' => ['required', 'string'],
            'tahun_lulus' => ['required', 'numeric'],
        ], [
            'required' => ':attribute wajib diisi',
            'string' => ':attribute tidak valid',
            'numeric' => ':attribute tidak valid',
        ], [
            'name' => 'Nama Lengkap Alumni',
            'nim' => 'NIM Alumni',
            'prodi_id' => 'Program Studi Alumni',
            'no_wa' => 'Nomor WhatsApp Alumni',
            'legalisir' => 'Legalisir',
            'jumlah' => 'Jumlah Legalisir',
            'keperluan' => 'Keperluan',
            'tahun_lulus' => 'Tahun Lulus',
        ]);

        try {
            $id = decodeId($id);
            $prodi_id = '';
            if ($request->namaprodi != '' || $request->namaprodi != null) {
                $prodi = Prodi::create(['name' => $request->namaprodi]);
                $prodi_id = $prodi->id;
            } else {
                $prodi_id = $request->prodi_id;
            }
            $legalisir = '';
            foreach ($request->legalisir as $item) {
                $legalisir .= $item . ', ';
            }
            $ajuan = Legalisir::findOrFail($id);
            $ajuan->update([
                'name' => $request->name,
                'nim' => $request->nim,
                'prodi_id' => $prodi_id,
                'no_wa' => $request->no_wa,
                'jumlah' => $request->jumlah,
                'keperluan' => $request->keperluan,
                'tahun_lulus' => $request->tahun_lulus,
                'legalisir' => $legalisir,
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan berhasil diedit!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $id = decodeId($id);
            Legalisir::findOrFail($id)->delete();
            return response()->json(['status' => true, 'message' => 'Ajuan berhasil dihapus!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    public function proses(Request $request, $id)
    {
        try {
            $id = decodeId($id);

            $dataUpdate = [
                'status_id' => $request->status_id,
                'catatan' => $request->catatan,
            ];

            if ($request->status_id == '4') {
                $dataUpdate['tanggal_ambil'] = new \DateTime();
            } else {
                $dataUpdate['tanggal_proses'] = new \DateTime();
            }

            Legalisir::findOrFail($id)->update($dataUpdate);

            return response()->json(['status' => true, 'message' => 'Ajuan berhasil diproses'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan ehe'], 500);
        }
    }
}
