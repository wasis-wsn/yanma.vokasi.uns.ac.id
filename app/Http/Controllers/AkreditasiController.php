<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Akreditasi;
use App\Models\Prodi;
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
        $prodis = Prodi::with('akreditasi')->where('id', '!=', '1')->whereHas('akreditasi');
        if ($request->prodi != null || $request->prodi != '') {
            $q = $request->prodi;
            $prodis = $prodis->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($q) . '%');
        }
        $prodis = $prodis->orderBy('name', 'desc')->get();
        return response()->json($prodis, 200);;
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
        $list = $list->orderBy('prodi_id')->get();
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
            ->editColumn('file', function ($row) {
                return '<a href="' . asset('storage/akreditasi/' . $row->file) . '" target="_blank" rel="noopener noreferrer">' . $row->file . '</a>';
            })
            ->rawColumns(['action', 'file'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'prodi_id' => ['required'],
            'tahun' => ['required'],
            'file' => ['required', 'file', 'mimes:pdf'],
        ], [
            'required' => ':attribute wajib diisi',
            'file' => ':attribute tidak valid',
            'mimes' => ':attribute tidak valid',
        ], [
            'prodi_id' => 'Prodi',
            'tahun' => 'Tahun',
            'file' => 'File Akreditasi',
        ]);

        try {
            $prodi = Prodi::findOrFail($request->prodi_id);
            $tahun = $request->tahun;
            $fileName = Str::of($prodi->name)->replace(' ', '') . '_' . Str::of($tahun)->replace(' ', '') . '.pdf';
            $request->file->storeAs('akreditasi/', $fileName, 'public');
            Akreditasi::create([
                'prodi_id' => $request->prodi_id,
                'tahun' => $tahun,
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
            'prodi_id' => ['required'],
            'tahun' => ['required'],
            'file' => ['file', 'mimes:pdf'],
        ], [
            'required' => ':attribute wajib diisi',
            'file' => ':attribute tidak valid',
            'mimes' => ':attribute tidak valid',
        ], [
            'prodi_id' => 'Prodi',
            'tahun' => 'Tahun',
            'file' => 'File Akreditasi',
        ]);

        try {
            $id = decodeId($id);

            $akreditasi = Akreditasi::findOrFail($id);

            $tahun = $request->tahun;
            $fileName = $akreditasi->file;

            if ($request->hasFile('file')) {
                $prodi = Prodi::findOrFail($request->prodi_id);
                $fileName = Str::of($prodi->name)->replace(' ', '') . '_' . Str::of($tahun)->replace(' ', '') . '.pdf';
                Storage::disk('public')->delete('akreditasi/' . $akreditasi->file);
                $request->file->storeAs('akreditasi/', $fileName, 'public');
            }

            $akreditasi->update([
                'prodi_id' => $request->prodi_id,
                'tahun' => $tahun,
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
            Storage::disk('public')->delete('akreditasi/' . $akreditasi->file);
            $akreditasi->delete();
            return response()->json(['status' => true, 'message' => 'Akreditasi berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }
}
