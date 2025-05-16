<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BeritaController extends Controller
{
    public function landingPage()
    {
        $berita = Berita::all();
        return view('landingpage.berita.index', compact('berita'));
    }

    public function index()
    {
        return view('pages.berita.index');
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $query = Berita::query();

            return DataTables::of($query)
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
                ->editColumn('gambar', function ($row) {
                    return '<img src="'.asset('storage/'.$row->gambar).'" class="img-thumbnail" width="100">';
                })
                ->editColumn('PDF', function ($row) {
                    if ($row->PDF) {
                        return '<a href="'.asset('storage/'.$row->PDF).'" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-file-PDF"></i> Lihat PDF</a>';
                    }
                    return '<span class="badge badge-danger">Tidak ada PDF</span>';
                })
                ->editColumn('deskripsi', function ($row) {
                    return substr($row->deskripsi, 0, 100) . '...';
                })
                ->editColumn('tanggal', function ($row) {
                    return date('d-m-Y', strtotime($row->tanggal));
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->get('search')['value']) {
                        $searchValue = $request->get('search')['value'];
                        $query->where(function ($query) use ($searchValue) {
                            $query->where('judul', 'like', "%$searchValue%")
                                ->orWhere('deskripsi', 'like', "%$searchValue%")
                                ->orWhere('tanggal', 'like', "%$searchValue%");
                        });
                    }
                })
                ->orderColumn('tanggal', function ($query, $order) {
                    $query->orderBy('tanggal', $order);
                })
                ->rawColumns(['action', 'gambar', 'PDF'])
                ->make(true);
        }

        return response()->json(['error' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'PDF' => 'nullable|mimes:pdf|max:10240', // Allow PDF files up to 10MB
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
        ], [
            'required' => ':attribute wajib diisi!',
            'image' => ':attribute harus berupa gambar!',
            'mimes' => ':attribute harus berformat sesuai ketentuan!',
            'max' => ':attribute maksimal ukuran yang ditentukan!',
        ]);

        try {
            $gambarPath = $request->file('gambar')->store('berita', 'public');

            $data = [
                'judul' => $request->judul,
                'gambar' => $gambarPath,
                'deskripsi' => $request->deskripsi,
                'tanggal' => $request->tanggal,
            ];

            // Store PDF file if uploaded
            if ($request->hasFile('PDF')) {
                $data['PDF'] = $request->file('PDF')->store('berita/PDF', 'public');
            }

            Berita::create($data);

            return response()->json(['status' => true, 'message' => 'Berita berhasil ditambahkan'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }

    public function edit($id)
    {
        try {
            $id = decodeId($id);
            $data = Berita::findOrFail($id);
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'PDF' => 'nullable|mimes:pdf|max:10240', // Allow PDF files up to 10MB
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
        ], [
            'required' => ':attribute wajib diisi!',
            'image' => ':attribute harus berupa gambar!',
            'mimes' => ':attribute harus berformat sesuai ketentuan!',
            'max' => ':attribute maksimal ukuran yang ditentukan!',
        ]);

        try {
            $id = decodeId($id);
            $berita = Berita::findOrFail($id);

            $data = $request->except(['gambar', 'PDF']);

            if ($request->hasFile('gambar')) {
                // Hapus gambar lama
                if ($berita->gambar && file_exists(storage_path('app/public/' . $berita->gambar))) {
                    unlink(storage_path('app/public/' . $berita->gambar));
                }

                // Upload gambar baru
                $data['gambar'] = $request->file('gambar')->store('berita', 'public');
            }

            // Handle PDF file update
            if ($request->hasFile('PDF')) {
                // Hapus PDF lama jika ada
                if ($berita->PDF && file_exists(storage_path('app/public/' . $berita->PDF))) {
                    unlink(storage_path('app/public/' . $berita->PDF));
                }

                // Upload PDF baru
                $data['PDF'] = $request->file('PDF')->store('berita/PDF', 'public');
            }

            $berita->update($data);
            return response()->json(['status' => true, 'message' => 'Berita berhasil diedit'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $id = decodeId($id);
            $berita = Berita::findOrFail($id);

            // Hapus gambar
            if ($berita->gambar && file_exists(storage_path('app/public/' . $berita->gambar))) {
                unlink(storage_path('app/public/' . $berita->gambar));
            }

            // Hapus PDF
            if ($berita->PDF && file_exists(storage_path('app/public/' . $berita->PDF))) {
                unlink(storage_path('app/public/' . $berita->PDF));
            }

            $berita->delete();
            return response()->json(['status' => true, 'message' => 'Berita berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }
}
