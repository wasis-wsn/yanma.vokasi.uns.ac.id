<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Akreditasi;
use App\Models\Prodi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AkreditasiController extends Controller
{
    public function landingPage()
    {
        // $prodis = Prodi::where('id', '!=', '1')->whereHas('akreditasi')->orderBy('name', 'desc')->get();
        return view('landingpage.akreditasi.index');
    }

    public function getAkreditasi(Request $request)
    {
        $keyword = $request->prodi ? urldecode($request->prodi) : null;

        $prodis = Prodi::with(['akreditasi' => function ($query) {
            $query->orderByDesc('tanggal_akhir')->orderByDesc('tanggal_awal');
        }])->where('id', '!=', '1')->whereHas('akreditasi');
        if ($keyword) {
            $prodis = $prodis->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($keyword) . '%');
        }
        $prodis = $prodis->orderBy('name', 'desc')->get();
        $prodis->each(function ($prodi) {
            $prodi->encoded_id = encodeId($prodi->id);
            $prodi->akreditasi->transform(function ($item) {
                $item->file_url = $item->file ? asset('storage/akreditasi/' . $item->file) : null;
                $item->periode_label = $this->formatPeriode($item->tanggal_awal, $item->tanggal_akhir);
                return $item;
            });
        });

        return response()->json($prodis, 200);
    }

    public function index()
    {
        $prodis = Prodi::where('id', '!=', '1')->get();
        return view('pages.akreditasi.index', compact('prodis'));
    }

    public function list(Request $request)
    {
        $list = Akreditasi::with('prodi');
        if ($request->prodi != 'all') $list = $list->where('prodi_id', $request->prodi);
        $list = $list->orderBy('prodi_id')->orderByDesc('tanggal_akhir')->get();
        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-warning btn-sm btn-edit" data-id="' . encodeId($row->id) . '">
                        <i class="fa fa-pen"></i>
                    </button>';
                $aksi .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . encodeId($row->id) . '">
                        <i class="fa fa-trash"></i>
                    </button>';
                return $aksi;
            })
            ->addColumn('tanggal_awal_label', function ($row) {
                return $row->tanggal_awal ? $row->tanggal_awal->translatedFormat('d F Y') : '-';
            })
            ->addColumn('tanggal_akhir_label', function ($row) {
                return $row->tanggal_akhir ? $row->tanggal_akhir->translatedFormat('d F Y') : '-';
            })
            ->editColumn('file', function ($row) {
                return '<a href="' . asset('storage/akreditasi/' . $row->file) . '" target="_blank" rel="noopener noreferrer">' . $row->file . '</a>';
            })
            ->rawColumns(['action', 'file'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'prodi_id' => ['required', 'exists:ref_prodi,id'],
            'tanggal_awal' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_awal'],
            'file' => ['required', 'file', 'mimes:pdf'],
        ], [
            'required' => ':attribute wajib diisi',
            'exists' => ':attribute tidak valid',
            'date' => ':attribute tidak valid',
            'after_or_equal' => ':attribute harus sama atau setelah tanggal awal',
            'file' => ':attribute tidak valid',
            'mimes' => ':attribute tidak valid',
        ], [
            'prodi_id' => 'Prodi',
            'tanggal_awal' => 'Tanggal Awal',
            'tanggal_akhir' => 'Tanggal Akhir',
            'file' => 'File Akreditasi',
        ]);

        try {
            $prodi = Prodi::findOrFail($request->prodi_id);
            $start = Carbon::parse($request->tanggal_awal);
            $end = Carbon::parse($request->tanggal_akhir);

            $file = $request->file('file');
            $fileName = Str::slug($prodi->name, '_') . '_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('akreditasi/', $fileName, 'public');

            Akreditasi::create([
                'prodi_id' => $request->prodi_id,
                'tanggal_awal' => $start,
                'tanggal_akhir' => $end,
                'file' => $fileName,
            ]);
            return response()->json(['status' => true, 'message' => 'Akreditasi berhasil ditambahkan'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function edit(string $id)
    {
        try {
            $id = decodeId($id);
            $data = Akreditasi::findOrFail($id);
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'prodi_id' => ['required', 'exists:ref_prodi,id'],
            'tanggal_awal' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_awal'],
            'file' => ['file', 'mimes:pdf'],
        ], [
            'required' => ':attribute wajib diisi',
            'exists' => ':attribute tidak valid',
            'date' => ':attribute tidak valid',
            'after_or_equal' => ':attribute harus sama atau setelah tanggal awal',
            'file' => ':attribute tidak valid',
            'mimes' => ':attribute tidak valid',
        ], [
            'prodi_id' => 'Prodi',
            'tanggal_awal' => 'Tanggal Awal',
            'tanggal_akhir' => 'Tanggal Akhir',
            'file' => 'File Akreditasi',
        ]);

        try {
            $id = decodeId($id);

            $akreditasi = Akreditasi::findOrFail($id);

            $start = Carbon::parse($request->tanggal_awal);
            $end = Carbon::parse($request->tanggal_akhir);
            $fileName = $akreditasi->file;

            if ($request->hasFile('file')) {
                $prodi = Prodi::findOrFail($request->prodi_id);
                $file = $request->file('file');
                $fileName = Str::slug($prodi->name, '_') . '_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '_' . time() . '.' . $file->getClientOriginalExtension();
                if ($akreditasi->file && Storage::disk('public')->exists('akreditasi/' . $akreditasi->file)) {
                    Storage::disk('public')->delete('akreditasi/' . $akreditasi->file);
                }
                $file->storeAs('akreditasi/', $fileName, 'public');
            }

            $akreditasi->update([
                'prodi_id' => $request->prodi_id,
                'tanggal_awal' => $start,
                'tanggal_akhir' => $end,
                'file' => $fileName,
            ]);
            return response()->json(['status' => true, 'message' => 'Akreditasi berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $id = decodeId($id);
            $akreditasi = Akreditasi::findOrFail($id);
            if ($akreditasi->file && Storage::disk('public')->exists('akreditasi/' . $akreditasi->file)) {
                Storage::disk('public')->delete('akreditasi/' . $akreditasi->file);
            }
            $akreditasi->delete();
            return response()->json(['status' => true, 'message' => 'Akreditasi berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function showProdi(string $encodedId)
    {
        try {
            $id = decodeId($encodedId);
        } catch (\Throwable $th) {
            abort(404);
        }

        $prodi = Prodi::with(['akreditasi' => function ($query) {
            $query->orderByDesc('tanggal_akhir')->orderByDesc('tanggal_awal');
        }])->findOrFail($id);

        $akreditasi = $prodi->akreditasi->map(function ($item) {
            return [
                'file_url' => $item->file ? asset('storage/akreditasi/' . $item->file) : null,
                'periode_label' => $this->formatPeriode($item->tanggal_awal, $item->tanggal_akhir),
                'tanggal_awal' => $item->tanggal_awal,
                'tanggal_akhir' => $item->tanggal_akhir,
                'created_at' => $item->created_at,
            ];
        });

        return view('landingpage.akreditasi.prodi', [
            'prodi' => $prodi,
            'akreditasi' => $akreditasi,
            'otherProdi' => Prodi::withCount('akreditasi')
                ->where('id', '!=', $prodi->id)
                ->where('id', '!=', 1)
                ->whereHas('akreditasi')
                ->orderBy('name')
                ->get(),
        ]);
    }

    private function formatPeriode(?Carbon $start, ?Carbon $end): string
    {
        $startLabel = $start ? $start->translatedFormat('d F Y') : '-';
        $endLabel = $end ? $end->translatedFormat('d F Y') : '-';

        if ($startLabel === '-' && $endLabel === '-') {
            return '-';
        }

        return "{$startLabel} - {$endLabel}";
    }
}
