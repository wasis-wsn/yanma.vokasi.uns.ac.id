<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TemplateController extends Controller
{
    public function index()
    {
        return view('pages.template.index');
    }

    public function list()
    {
        $list = Template::with('layanan')->orderBy('layanan_id', 'desc')->get();
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
                return '<a href="' . asset('storage/template/'. $row->file) . '" target="_blank">'. $row->file .'</a>';
            })
            ->rawColumns(['action', 'file'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'layanan_id' => ['required', 'numeric'],
            'template' => ['required', 'string'],
            'file' => ['required', 'file', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi',
            'string' => ':attribute tidak valid',
            'numeric' => ':attribute tidak valid',
            'max' => 'ukuran :attribute tidak boleh lebih dari 10MB',
        ], [
            'layanan_id' => 'Layanan',
            'template' => 'Nama Template',
            'file' => 'File',
        ]);

        try {
            $name = $request->file->getClientOriginalName();
            $fileName = Str::of($name)->replace(' ', '')->replace('/','');
            $request->file->storeAs('template/', $fileName, 'public');
            $data = [
                'layanan_id' => $request->layanan_id,
                'template' => $request->template,
                'file' => $fileName,
            ];
            Template::create($data);
            return response()->json(['status' => true, 'message' => 'Template berhasil ditambahkan'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }
    public function edit(string $id)
    {
        try {
            $id = decodeId($id);
            $data = Template::with('layanan')->findOrFail($id);
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'layanan_id' => ['required', 'numeric'],
            'template' => ['required', 'string'],
            'file' => ['file', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi',
            'string' => ':attribute tidak valid',
            'numeric' => ':attribute tidak valid',
            'max' => 'ukuran :attribute tidak boleh lebih dari 10MB',
        ], [
            'layanan_id' => 'Layanan',
            'template' => 'Nama Template',
            'file' => 'File',
        ]);

        try {
            $data = [
                'layanan_id' => $request->layanan_id,
                'template' => $request->template,
            ];
            $id = decodeId($id);
            $template = Template::findOrFail($id);
            if ($request->hasFile('file')) {
                $name = $request->file('file')->getClientOriginalName();
                $fileName = Str::of($name)->replace(' ', '')->replace('/','');
                Storage::disk('public')->delete('template/'.$template->file);
                $request->file->storeAs('template/', $fileName, 'public');
                $data['file'] = $fileName;
            }
            $template->update($data);
            return response()->json(['status' => true, 'message' => 'Template berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $id = decodeId($id);
            $template = Template::findOrFail($id);
            Storage::disk('public')->delete('template/'.$template->file);
            $template->delete();
            return response()->json(['status' => true, 'message' => 'Template berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }
}
