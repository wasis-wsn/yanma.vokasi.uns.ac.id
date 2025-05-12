<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProdiController extends Controller
{
    public function index()
    {
        return view('pages.prodi.index');
    }

    public function list()
    {
        $prodi = Prodi::orderBy('name', 'desc')->get();
        return DataTables::of($prodi)
            ->addIndexColumn()
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
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'required' => ':attribute wajib diisi',
        ], [
            'name' => 'Nama Program Studi'
        ]);

        Prodi::create($request->input());
        return response()->json(['status' => true, 'message' => 'Program Studi berhasil ditambahkan'], 200);
    }

    public function edit($id)
    {
        $id = decodeId($id);
        $prodi = Prodi::findOrFail($id);
        return response()->json(['status' => true, 'data' => $prodi], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'required' => ':attribute wajib diisi',
        ], [
            'name' => 'Nama Program Studi'
        ]);

        $id = decodeId($id);
        try {
            Prodi::findOrFail($id)->update($request->input());
            return response()->json(['status' => true, 'message' => 'Program Studi berhasil diedit'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }

    public function destroy($id)
    {
        $id = decodeId($id);
        try {
            Prodi::findOrFail($id)->delete();
            return response()->json(['status' => true, 'message' => 'Program Studi berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }
}
