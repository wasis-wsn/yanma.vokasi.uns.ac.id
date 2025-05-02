<?php

namespace App\Http\Controllers;

use App\Models\PembinaOrmawa;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PembinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.pembina.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function list()
    {
        $list = PembinaOrmawa::with('prodi')->get();
        return DataTables::of($list)
            ->addIndexColumn()
            ->editColumn('name', function ($row) {
                return wordwrap($row->name, 25,"<br/>");
            })
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-warning btn-sm btn-edit" data-id="' . encodeId($row->id) . '">
                        <i class="fa fa-pen"></i>
                    </button>';
                $aksi .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . encodeId($row->id) . '">
                        <i class="fa fa-trash"></i>
                    </button>';
                if ($row->id == 1) {
                    $aksi = '';
                }
                return $aksi;
            })
            ->rawColumns(['name', 'action'])
            ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'nip' => ['required', 'numeric'],
            'nidn' => ['required', 'numeric'],
            'unit_id' => ['required', 'numeric'],
        ], [
            'required' => ':attribute wajib diisi',
            'string' => ':attribute tidak valid',
            'numeric' => ':attribute tidak valid',
        ], [
            'name' => 'nama',
            'nip' => 'NIP/NIK',
            'nidn' => 'NIDN',
            'unit_id' => 'Unit',
        ]);

        try {
            PembinaOrmawa::create($request->input());
            return response()->json(['status' => true, 'message' => 'Pembina berhasil ditambahkan'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $id = decodeId($id);
            $data = PembinaOrmawa::with('prodi')->findOrFail($id);
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'nip' => ['required', 'numeric'],
            'nidn' => ['required', 'numeric'],
            'unit_id' => ['required', 'numeric'],
        ], [
            'required' => ':attribute wajib diisi',
            'string' => ':attribute tidak valid',
            'numeric' => ':attribute tidak valid',
        ], [
            'name' => 'nama',
            'nip' => 'NIP/NIK',
            'nidn' => 'NIDN',
            'unit_id' => 'Unit',
        ]);

        try {
            $id = decodeId($id);
            PembinaOrmawa::findOrFail($id)->update($request->input());
            return response()->json(['status' => true, 'message' => 'Pembina berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $id = decodeId($id);
            PembinaOrmawa::findOrFail($id)->delete();
            return response()->json(['status' => true, 'message' => 'Pembina berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }
}
