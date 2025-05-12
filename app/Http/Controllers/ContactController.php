<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    public function landingPage()
    {
        $contact = Contact::all();
        return view('landingpage.contact.index', compact('contact'));
    }

    public function index()
    {
        return view('pages.contact.index');
    }

    public function list()
    {
        $list = Contact::all();

        return DataTables::of($list)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $aksi = '<button type="button" class="btn btn-warning btn-sm btn-edit btn-block" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-pen"></i>
                        </button>';
                    $aksi .= '<button type="button" class="btn btn-danger btn-sm btn-delete btn-block" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-trash"></i>
                        </button>';
                    return $aksi;
                })
                ->editColumn('link', function ($row) {
                    return '<a href="'.$row->link.'">'.$row->link.'</a>';
                })
                ->rawColumns(['action', 'link'])
                ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'link' => ['required', 'url:http,https'],
        ], [
            'required' => ':attribute wajib diisi!',
            'url' => ':attribute tidak valid!',
        ]);

        try {
            Contact::create($request->input());
            return response()->json(['status' => true, 'message' => 'Kontak berhasil ditambahkan'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }

    public function edit($id)
    {
        try {
            $id = decodeId($id);
            $data = Contact::findOrFail($id);
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
            'link' => ['required', 'url:http,https'],
        ], [
            'required' => ':attribute wajib diisi!',
            'url' => ':attribute tidak valid!',
        ]);

        try {
            $id = decodeId($id);
            Contact::findOrFail($id)->update($request->input());
            return response()->json(['status' => true, 'message' => 'Kontak berhasil diedit'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $id = decodeId($id);
            Contact::findOrFail($id)->delete();
            return response()->json(['status' => true, 'message' => 'Kontak berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }
}
