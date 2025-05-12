<?php

namespace App\Http\Controllers;

use App\Models\KategoriLayanan;
use App\Models\Layanan;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class LayananController extends Controller
{
    public function index()
    {
        $role = Role::all();
        $kategoriLayanan = KategoriLayanan::all();
        return view('pages.layanan.index', compact('role', 'kategoriLayanan'));
    }

    public function list()
    {
        $list = Layanan::with('kategoriLayanan')->get();

        return DataTables::of($list)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $aksi = '';
                    if (!is_null($row->template_surat_hasil)) {
                        $aksi .= '<a href="'.asset('storage/suratHasil/'.$row->template_surat_hasil).'" class="btn btn-primary btn-sm btn-block">
                                    <i class="fa fa-file"></i>
                                </a>';
                    }
                    $aksi .= '<button type="button" class="btn btn-warning btn-sm btn-edit btn-block" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-pen"></i>
                        </button>';
                    if ($row->is_default == '0') {
                        $aksi .= '<button type="button" class="btn btn-danger btn-sm btn-delete btn-block" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-trash"></i>
                            </button>';
                    }
                    return $aksi;
                })
                ->editColumn('is_active', function ($row) {
                    return $row->is_active == 1 ? 'Aktif' : 'Non-Aktif';
                })
                ->rawColumns(['action', 'is_active'])
                ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_layanan_id' => ['required'],
            'name' => ['required'],
            'file' => ['file', 'mimes:doc,docx', 'max:10240'],
            'url_mhs' => ['required', 'url:http,https'],
            'url_staff' => ['required', 'url:http,https'],
            'gate' => ['required'],
        ], [
            'required' => ':attribute wajib diisi',
            'url' => ':attribute tidak valid!',
        ]);

        try {
            $data = $request->input();
            // $data['gate'] = json_encode($request->gate);
            if ($request->hasFile('file')) {
                $fileName = Str::of($request->name)->replace(' ', '')->replace('/','').'.docx';
                $request->file->storeAs('suratHasil/', $fileName, 'public');
                $data['template_surat_hasil'] = $fileName;
            }
            $layanan = Layanan::create($data);
            $layanan->roles()->attach($request->gate);
            return response()->json(['status' => true, 'message' => 'Layanan Berhasil Ditambahkan'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => true, 'message' => 'Terjadi Kesalahan'], 200);
        }
    }

    public function edit($id)
    {
        try {
            // $layanan->gate = json_decode($layanan->gate, true);
            $id = decodeId($id);
            $layanan = Layanan::with('roles')->findOrFail($id);
            return response()->json(['status' => true, 'data' => $layanan], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'data' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_layanan_id' => ['required'],
            'name' => ['required'],
            'file' => ['file', 'mimes:doc,docx', 'max:10240'],
            'url_mhs' => ['required', 'url:http,https'],
            'url_staff' => ['required', 'url:http,https'],
            'gate' => ['required'],
        ], [
            'required' => ':attribute wajib diisi',
            'url' => ':attribute tidak valid!',
        ]);

        try {
            $id = decodeId($id);
            $data = $request->input();
            $data['is_active'] = $request->is_active ? $request->is_active : '0';
            $layanan = Layanan::findOrFail($id);
            if ($request->hasFile('file')) {
                $fileName = Str::of($request->name)->replace(' ', '')->replace('/','').'.docx';
                Storage::disk('public')->delete('suratHasil/'.$layanan->template_surat_hasil);
                $request->file->storeAs('suratHasil/', $fileName, 'public');
                $data['template_surat_hasil'] = $fileName;
            }
            $layanan->update($data);
            $layanan->roles()->sync($request->gate);
            return response()->json(['status' => true, 'message' => 'Layanan Berhasil Diedit'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $id = decodeId($id);
            $layanan = Layanan::findOrFail($id);
            Storage::disk('public')->delete('suratHasil/'.$layanan->template_surat_hasil);
            $layanan->delete();
            return response()->json(['status' => true, 'message' => 'Layanan Berhasil Dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }
}
