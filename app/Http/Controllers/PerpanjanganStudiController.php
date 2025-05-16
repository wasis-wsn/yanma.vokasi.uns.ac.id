<?php

namespace App\Http\Controllers;

use App\Exports\PerpanjanganExport;
use App\Models\Layanan;
use App\Models\PerpanjanganStudi;
use App\Models\Semester;
use App\Models\StatusHeregistrasi;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\TahunAkademik;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PerpanjanganStudiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = Carbon::now()->toDateTimeString();
        $perpanjangan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusHeregistrasi::all();
        $prodis = Prodi::all();
        $templates = Template::where('layanan_id', $perpanjangan->id)->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_akademik', 'desc')->limit('6')->get();
        $semester = Semester::all();
        session(['layanan' => $perpanjangan]);
        return view('pages.perpanjangan.index', compact('perpanjangan', 'tanggal', 'tahuns', 'status', 'prodis', 'templates', 'tahunAkademik', 'semester'));
    }

    public function listMahasiswa()
    {
        $data = PerpanjanganStudi::with('user.prodis', 'status', 'tahunAkademik', 'semester')->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i>
                        </button>';
                if ($row->status_id == '1' || $row->status_id == '2') {
                    $aksi .= '<button type="button" class="btn btn-warning btn-sm btn-edit" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-pen"></i>
                            </button>';
                    $aksi .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-trash"></i>
                            </button>';
                }
                return $aksi;
            })
            ->addColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y');
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('tanggal_ambil', function ($row) {
                $tanggal_ambil = $row->tanggal_ambil;
                if ($tanggal_ambil) {
                    $tanggal_ambil = Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_ambil)->translatedFormat('H:i:s');
                }
                return $tanggal_ambil;
            })
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik->tahun_akademik . ' - ' . $row->semester->semester;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 30, "<br>");
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_proses', 'tahun_akademik', 'status_id', 'tanggal_ambil', 'catatan'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $list = PerpanjanganStudi::with('user.prodis', 'status', 'tahunAkademik', 'semester')
            ->whereYear('created_at', $request->year);
            
        if ($request->status != 'all') {
            $list = $list->where('status_id', $request->status);
        }
        
        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($query) use ($request) {
                $query->where('prodi', $request->prodi);
            });
        }
        
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                return encodeId($row->id);
            })
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                if (in_array($row->status_id, ['1', '2', '3', '4', '6'])) {
                    $aksi .= '<button type="button" class="btn btn-success btn-sm btn-block btn-proses" data-id="' . encodeId($row->id) . '" data-status="' . $row->status_id . '">
                                <i class="fa fa-check"></i> Proses
                            </button>';
                }
                if (in_array($row->status_id, ['5', '7'])) {
                    $aksi = '';
                }

                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik->tahun_akademik . ' - ' . $row->semester->semester;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('nama_prodi', function ($row) {
                return wordwrap($row->user->prodis->name, 20, "<br>");
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 30, "<br>");
            })
            ->rawColumns(['id', 'action', 'tanggal_submit', 'tahun_akademik', 'status_id', 'nama_prodi', 'catatan'])
            ->toJson();
    }

    public function listFo(Request $request)
    {
        $list = PerpanjanganStudi::with('user.prodis', 'status', 'tahunAkademik', 'semester')
            ->whereYear('created_at', $request->year);
            
        if ($request->status != 'all') {
            $list = $list->where('status_id', $request->status);
        }
        
        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($query) use ($request) {
                $query->where('prodi', $request->prodi);
            });
        }
        
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '';
                if ($row->status_id == 6) {
                    $aksi .= '<button type="button" class="btn btn-warning btn-sm btn-proses btn-block" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-hand"></i> Diambil
                            </button>';
                }
                return $aksi;
            })
            ->editColumn('tanggal_ambil', function ($row) {
                return ($row->tanggal_ambil) ? Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_ambil)->translatedFormat('H:i:s') : '';
            })
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik->tahun_akademik . ' - ' . $row->semester->semester;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->rawColumns(['action', 'tanggal_ambil', 'tahun_akademik', 'status_id'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $list = PerpanjanganStudi::with('user.prodis', 'status', 'tahunAkademik', 'semester')
            ->whereYear('created_at', $request->year);
            
        if ($request->status != 'all') {
            $list = $list->where('status_id', $request->status);
        }
        
        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($query) use ($request) {
                $query->where('prodi', $request->prodi);
            });
        }
        
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                return '<input class="form-check-input" type="checkbox" value="' . encodeId($row->id) . '" name="ids">';
            })
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik->tahun_akademik . ' - ' . $row->semester->semester;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('nama_prodi', function ($row) {
                return wordwrap($row->user->prodis->name, 20, "<br>");
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 30, "<br>");
            })
            ->rawColumns(['id', 'action', 'tanggal_submit', 'tahun_akademik', 'status_id', 'nama_prodi', 'catatan'])
            ->toJson();
    }

    public function listAdminProdi(Request $request) 
    {
        $list = PerpanjanganStudi::with('user.prodis', 'status', 'tahunAkademik', 'semester')
            ->whereYear('created_at', $request->year);
            
        if ($request->status != 'all') {
            $list = $list->where('status_id', $request->status);
        }

        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($query) use ($request) {
                $query->where('prodi', $request->prodi);
            });
        }
        
        $list = $list->orderBy('created_at', 'desc')->get();
    
        return DataTables::of($list)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
                return '<input class="form-check-input" type="checkbox" value="' . encodeId($row->id) . '" name="ids">';
            })
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik->tahun_akademik . ' - ' . $row->semester->semester;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('nama_prodi', function ($row) {
                return wordwrap($row->user->prodis->name, 20, "<br>");
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 30, "<br>");
            })
            ->rawColumns(['id', 'action', 'tanggal_submit', 'tahun_akademik', 'status_id', 'nama_prodi', 'catatan'])
            ->toJson();
    }

    public function setting(Request $request)
    {
        $request->validate([
            'open_datetime' => ['required'],
            'close_datetime' => ['required', 'after:open_datetime'],
            'semester_id' => ['required'],
            'tahun_akademik_id' => ['required'],
        ], [
            'required' => ':attribute wajib diisi',
        ], [
            'open_datetime' => 'Tanggal Buka',
            'close_datetime' => 'Tanggal Tutup',
            'semester_id' => 'Semester',
            'tahun_akademik_id' => 'Tahun Akademik',
        ]);

        try {
            $layanan = session('layanan');
            $layanan->update($request->input());
            // Layanan::where('name', 'Perpanjangan Studi')->update($request->input());
            return response()->json(['status' => true, 'message' => 'Pengaturan layanan berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->role == '1') {
            $tanggal = Carbon::now()->toDateTimeString();
            $layanan = session('layanan');
            $layanan = Layanan::findOrFail($layanan->id);
            if ($tanggal <= $layanan->open_datetime && $tanggal >= $layanan->close_datetime) {
                return response()->json(['status' => false, 'message' => 'Saat ini diluar jadwal pengajuan layanan'], 500);
            }
        }

        $rules = [
            'file' => ['required', 'file', 'mimes:pdf'],
            'semester_id' => ['required'],
            'tahun_akademik_id' => ['required'],
            'perpanjangan_ke' => ['required'],
        ];

        // Jika role user adalah 2, maka field 'mahasiswa' harus required
        if (Auth::user()->role === '2') {
            $rules['mahasiswa'] = 'required';
        }

        $request->validate($rules, [
            'required' => ':attribute wajib diisi!'
        ], [
            'semester_id' => 'Semester',
            'tahun_akademik_id' => 'Tahun Akademik',
            'perpanjangan_ke' => 'Perpanjangan Ke',
            'file' => 'File PDF'
        ]);

        try {
            $user = $request->mahasiswa ? User::findOrFail($request->mahasiswa) : Auth::user();
            $fileName = 'Perpanjangan_' . Str::of($user->name)->trim() . '_' . $user->nim . '_' . $request->perpanjangan_ke . '_' . time() . '.pdf';
            $request->file('file')->storeAs('perpanjangan/upload/', $fileName, 'public');

            PerpanjanganStudi::create([
                'user_id' => $user->id,
                'status_id' => '1',
                'file' => $fileName,
                'perpanjangan_ke' => $request->perpanjangan_ke,
                'semester_id' => $request->semester_id,
                'tahun_akademik_id' => $request->tahun_akademik_id,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
        return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Ditambahkan!'], 200);
    }

    public function show($id)
    {
        $id = decodeId($id);
        $data = PerpanjanganStudi::where('id', $id)->with('user.prodis', 'status', 'tahunAkademik', 'semester')->first();
        $data['tgl_proses'] = ($data->tanggal_proses) ? Carbon::parse($data->tanggal_proses)->translatedFormat('d F Y H:i:s').' WIB' : '';
        return response()->json(['status' => true, 'data' => $data], 200);
    }

    public function revisi(Request $request, string $id)
    {
        $request->validate([
            'file' => ['file', 'mimes:pdf'],
            'perpanjangan_ke' => ['required'],
        ], [
            'required' => ':attribute wajib diisi!'
        ], [
            'perpanjangan_ke' => 'Perpanjangan Ke',
            'file' => 'File PDF'
        ]);

        try {
            $id = decodeId($id);
            $ajuan = PerpanjanganStudi::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '2'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat mengedit data'], 500);
            }
            $message = ($ajuan->status_id == '2') ? 'Direvisi' : 'Diedit';

            $file = $ajuan->file;
            if ($request->hasFile('file')) {
                $fileName = 'Perpanjangan_' . Str::of(Auth::user()->name)->trim() . '_' . Auth::user()->nim . '_' . $request->perpanjangan_ke . '_' . time() . '.pdf';

                $file = $fileName;
                $request->file->storeAs('perpanjangan/upload/', $fileName, 'public');

                Storage::disk('public')->delete('perpanjangan/upload/' . $ajuan->file);
            }

            $ajuan->update([
                'perpanjangan_ke' => $request->perpanjangan_ke,
                'file' => $file,
                'status_id' => '1'
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil ' . $message], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $id = decodeId($id);
            $ajuan = PerpanjanganStudi::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '2'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat menghapus data'], 500);
            }
            Storage::disk('public')->delete('perpanjangan/upload/' . $ajuan->file);
            $ajuan->delete();
            return response()->json(['status' => true, 'message' => 'Ajuan berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'tahun_id' => ['required'],
            'semester_id' => ['required'],
        ], [
            'required' => ':attribute wajib diisi',
        ], [
            'tahun_id' => 'Tahun Akademik',
            'semester_id' => 'Semester Akademik'
        ]);

        $tahun = TahunAkademik::findOrFail($request->tahun_id);
        $semester = Semester::findOrFail($request->semester_id);
        $tahun_akademik = explode('/', $tahun->tahun_akademik);
        $name = 'Rekap_Data_Perpanjangan_Studi_Tahun_Akademik_' . $tahun_akademik[0] . '_' . $tahun_akademik[1] . '_' . $semester->semester;
        return Excel::download(new PerpanjanganExport($tahun, $semester), $name . '.xlsx');
    }

    public function proses(Request $request, $id = null)
    {
        // Validate request
        $rules = [
            'status_id' => ['required'],
            'no_surat' => Rule::requiredIf(fn () => in_array($request->status_id, ['3', '4', '6'])),
            'catatan' => Rule::requiredIf(fn () => in_array($request->status_id, ['2', '5'])),
            'file' => Rule::requiredIf(fn () => $request->status_id == '6'),
            'selected_ids' => ['nullable', 'string']
        ];

        $request->validate($rules, [
            'required' => ':attribute harus diisi!',
            'requiredif' => ':attribute harus diisi!'
        ]);

        try {
            // Determine IDs to process
            $ids = [];
            if ($id) {
                $ids[] = decodeId($id);
            } elseif ($request->selected_ids) {
                $ids = array_filter(
                    explode(',', $request->selected_ids),
                    fn($id) => is_numeric($id) && $id > 0
                );
            }

            if (empty($ids)) {
                throw new \Exception('Tidak ada data yang dipilih');
            }

            \Log::info('Processing IDs:', ['ids' => $ids]);

            // Process each ID
            foreach ($ids as $currentId) {
                $ajuan = PerpanjanganStudi::where('id', $currentId)
                    ->with('user.prodis', 'status')
                    ->firstOrFail();

                $data_update = [
                    'status_id' => $request->status_id,
                    'catatan' => $request->catatan,
                    'tanggal_proses' => now(),
                    'no_surat' => $ajuan->no_surat,
                    'tanggal_ambil' => $ajuan->tanggal_ambil
                ];

                // Update no_surat if needed
                if (in_array($request->status_id, ['3', '4', '6'])) {
                    $data_update['no_surat'] = $request->no_surat;
                }

                // Handle file upload for status "Selesai"
                if ($request->status_id == '6' && $request->hasFile('file')) {
                    Storage::disk('public')->delete('perpanjangan/upload/' . $ajuan->file);
                    
                    $fileName = 'Perpanjangan_' . Str::of($ajuan->user->name)->trim() 
                        . '_' . $ajuan->user->nim 
                        . '_' . $ajuan->perpanjangan_ke 
                        . '_hasil_' . time() . '.pdf';
                        
                    $request->file('file')->storeAs('perpanjangan/upload/', $fileName, 'public');
                    $data_update['file'] = $fileName;
                }

                // Handle taken status
                if ($request->status_id == '7') {
                    $data_update['tanggal_ambil'] = now();
                }

                $ajuan->update($data_update);
            }

            $message = count($ids) > 1 
                ? 'Semua ajuan berhasil diproses!'
                : ($request->status_id == '7' ? 'Ajuan telah diambil!' : 'Ajuan berhasil diproses!');

            return response()->json([
                'status' => true,
                'message' => $message
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Process error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkProcess(Request $request)
    {
        try {
            $request->validate([
                'status_id' => 'required',
                'selected_ids' => 'required',
                'catatan' => 'nullable',
            ]);

            $ids = explode(',', $request->selected_ids);

            // Decode setiap ID terenkripsi
            $decodedIds = array_map(function($id) {
                return decodeId($id);
            }, $ids);

            // Filter hanya ID yang valid (numeric dan > 0)
            $validIds = array_filter($decodedIds, function($id) {
                return is_numeric($id) && $id > 0;
            });

            if (empty($validIds)) {
                throw new \Exception('Tidak ada ID valid untuk diproses');
            }

            // Verify records exist
            $records = PerpanjanganStudi::whereIn('id', $validIds)->get();
            if ($records->isEmpty()) {
                throw new \Exception('Data tidak ditemukan');
            }

            $result = PerpanjanganStudi::whereIn('id', $validIds)
                ->update([
                    'status_id' => $request->status_id,
                    'catatan' => $request->catatan ?: null,
                    'tanggal_proses' => now(),
                ]);

            \Log::info('Update result:', [
                'processed_count' => $result,
                'ids' => $validIds
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diproses'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Bulk process error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
