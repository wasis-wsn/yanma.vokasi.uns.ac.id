<?php

namespace App\Http\Controllers;

use App\Exports\PenundaanExport;
use App\Models\Layanan;
use App\Models\Penundaan;
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

class PenundaanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = Carbon::now()->toDateTimeString();
        $penundaan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusHeregistrasi::all();
        $prodis = Prodi::all();
        $templates = Template::where('layanan_id', $penundaan->id)->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_akademik', 'desc')->limit('6')->get();
        $semester = Semester::all();
        session(['layanan' => $penundaan]);
        return view('pages.penundaan.index', compact('penundaan', 'tanggal', 'tahuns', 'status', 'prodis', 'templates', 'tahunAkademik', 'semester'));
    }

    public function listMahasiswa()
    {
        $data = Penundaan::with('status', 'tahunAkademik', 'semester')->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
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
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik->tahun_akademik . ' - ' . $row->semester->semester;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 30, "<br>");
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_proses', 'status_id', 'tahun_akademik', 'tanggal_ambil', 'catatan'])
            ->toJson();
    }

    public function listStaff(Request $request)
{
    $list = Penundaan::with('user.prodis', 'status', 'tahunAkademik', 'semester')
        ->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($q) use ($request) {
                $q->where('prodi', $request->prodi);
            });
        }

        return DataTables::of($list)
        ->addIndexColumn()
        ->addColumn('checkbox', function ($row) {
            return '<input type="checkbox" class="form-check-input" name="ids[]" value="' . $row->id . '">';
        })
        ->editColumn('id', function ($row) {
            // Keep the raw ID value instead of converting to HTML
            return encodeId($row->id);
        })
        ->addColumn('action', function ($row) {
            // Untuk status final (5,7), tidak tampilkan tombol apapun
            if (in_array($row->status_id, ['5', '7'])) {
                return '';
            }

            // Selalu tampilkan tombol Review
            $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                        <i class="fa fa-eye"></i> Review
                    </button>';

            // Jika queue_status bukan processed, tambahkan tombol Proses untuk status tertentu
            if ($row->queue_status !== 'processed' && in_array($row->status_id, ['1', '2', '3', '4', '6'])) {
                $aksi .= '<button type="button" class="btn btn-success btn-sm btn-block btn-proses" data-id="' . encodeId($row->id) . '" data-status="' . $row->status_id . '">
                            <i class="fa fa-check"></i> Proses
                        </button>';
            }

            return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tahun_akademik', function ($row) {
                return $row->tahunAkademik ? 
                    ($row->tahunAkademik->tahun_akademik . ' - ' . ($row->semester ? $row->semester->semester : '')) 
                    : '-';
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


    public function listDekanat(Request $request)
    {
        $list = Penundaan::with('user.prodis', 'status', 'tahunAkademik', 'semester')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($q) use ($request) {
                $q->where('prodi', $request->prodi);
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
                return $row->tahunAkademik ?
                    ($row->tahunAkademik->tahun_akademik . ' - ' . ($row->semester ? $row->semester->semester : ''))
                    : '-';
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
            return response()->json(['status' => true, 'message' => 'Pengaturan layanan berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan'], 500);
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
        $name = 'Rekap_Data_Penundaan_Pembayaran_UKT_Tahun_Akademik_' . $tahun_akademik[0] . '_' . $tahun_akademik[1] . '_' . $semester->semester;
        return Excel::download(new PenundaanExport($tahun, $semester), $name . '.xlsx');
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
            'semester_id' => ['required'],
            'tahun_akademik_id' => ['required'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'alasan' => ['required'],
        ];

        // Jika role user adalah 2, maka field 'mahasiswa' harus required
        if (Auth::user()->role === '2') {
            $rules['mahasiswa'] = 'required';
        }

        $request->validate($rules, [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'semester_id' => 'Semester',
            'tahun_akademik_id' => 'Tahun Akademik',
            'file' => 'File PDF',
            'alasan' => 'Alasan',
        ]);

        try {
            $user = $request->mahasiswa ? User::findOrFail($request->mahasiswa) : Auth::user();
            $file = 'Penundaan' . '_' . $user->nim . '_' . Str::of($user->name)->trim() . '_' . time() . '.pdf';
            $request->file('file')->storeAs('penundaan/upload/', $file, 'public');

            Penundaan::create([
                'user_id' => $user->id,
                'status_id' => '1',
                'semester_id' => $request->semester_id,
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'alasan' => $request->alasan,
                'file' => $file,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
        return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Ditambahkan!'], 200);
    }

    public function show($id)
    {
        $id = decodeId($id);
        $data = Penundaan::where('id', $id)->with('user.prodis', 'status', 'tahunAkademik', 'semester')->first();
        $data['tgl_proses'] = ($data->tanggal_proses) ? Carbon::parse($data->tanggal_proses)->translatedFormat('d F Y H:i:s') . ' WIB' : '';
        return response()->json(['status' => true, 'data' => $data], 200);
    }

    public function revisi(Request $request, string $id)
    {
        $request->validate([
            'file' => ['file', 'mimes:pdf', 'max:10240'],
            'alasan' => ['required'],
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'file' => 'File PDF',
            'alasan' => 'Alasan',
        ]);

        try {
            $id = decodeId($id);
            $ajuan = Penundaan::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '2'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat mengedit data'], 500);
            }
            $message = ($ajuan->status_id == '2') ? 'Direvisi' : 'Diedit';

            $file = $ajuan->file;
            if ($request->hasFile('file')) {
                $file = 'Penundaan' . '_' . Auth::user()->nim . '_' . Str::of(Auth::user()->name)->replace(' ','') . '_' . time() . '.pdf';
                $request->file('file')->storeAs('penundaan/upload/', $file, 'public');
                Storage::disk('public')->delete('penundaan/upload/' . $ajuan->file);
            }

            $ajuan->update([
                'status_id' => '1',
                'alasan' => $request->alasan,
                'file' => $file,
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil ' . $message], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $id = decodeId($id);
            $ajuan = Penundaan::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '2'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat menghapus data'], 500);
            }
            Storage::disk('public')->delete('penundaan/upload/' . $ajuan->file);
            $ajuan->delete();
            return response()->json(['status' => true, 'message' => 'Ajuan berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function proses(Request $request, $id = null)
{
    // Ambil ID dari route parameter
    $routeId = $id ? decodeId($id) : null;

    // Ambil ID dari form (jika ada)
    $formId = $request->has('id') ? decodeId($request->id) : null;

    // Gunakan ID utama
    $mainId = $routeId ?? $formId;

    // Validasi input
    $request->validate([
        'status_id' => ['required'],
        'catatan' => Rule::requiredIf(fn () => in_array($request->status_id, ['2', '5'])),
        'ids' => ['nullable', 'array'],
    ], [
        'required' => ':attribute harus diisi!',
    ]);

    // Siapkan semua ID yang akan diproses
    $allIds = $request->has('ids')
        ? array_map('decodeId', $request->ids)
        : [$mainId];

    \Log::info('IDs diproses:', [
        'routeId' => $routeId,
        'formId' => $formId,
        'mainId' => $mainId,
        'allIds' => $allIds
    ]);

    if (empty($allIds)) {
        return response()->json([
            'status' => false,
            'message' => 'Tidak ada data yang diproses',
        ], 422);
    }

    try {
        foreach ($allIds as $id) {
            try {
                $ajuan = Penundaan::with('user.prodis', 'status')->findOrFail($id);
    
                $data_update = [
                    'status_id' => $request->status_id,
                    'catatan' => $request->catatan,
                    'tanggal_proses' => now(),
                ];
    
                // Upload ke GDrive jika status = 6 dan file tersedia
                if ($request->status_id == '6' && $ajuan->file) {
                    $localPath = public_path('storage/penundaan/upload/' . $ajuan->file);
    
                    \Log::info('Cek file lokal:', ['path' => $localPath]);
    
                    if (file_exists($localPath)) {
                        try {
                            $fileContents = file_get_contents($localPath);
                            $drivePath = 'Penundaan Studi/' . $ajuan->file;
    
                            Storage::disk('google')->put($drivePath, $fileContents);
    
                            \Log::info('Upload ke GDrive berhasil:', [
                                'file' => $ajuan->file,
                                'drivePath' => $drivePath,
                            ]);
    
                            // Hapus file setelah upload
                            unlink($localPath);
                        } catch (\Exception $e) {
                            \Log::error('Gagal upload ke Google Drive:', [
                                'error' => $e->getMessage(),
                                'file' => $ajuan->file
                            ]);
                        }
                    } else {
                        \Log::warning('File tidak ditemukan untuk upload GDrive:', [
                            'file' => $ajuan->file,
                            'checked_path' => $localPath,
                        ]);
                    }
                }
    
                // Jika status 5/6, hapus file via Laravel storage (opsional jika pakai unlink)
                if (in_array($request->status_id, ['5', '6'])) {
                    Storage::disk('public')->delete('penundaan/upload/' . $ajuan->file);
                }
    
                $ajuan->update($data_update);
    
                // Hitung antrian yang tersisa
                // $waitingCount = Penundaan::where('queue_status', 'waiting')
                //     ->whereDate('created_at', today())
                //     ->count();
    
                // $return['waiting_count'] = $waitingCount;
            } catch (\Throwable $th) {
                $return['status'] = false;
                $return['message'] = 'Terjadi Kesalahan!';
                $status_code = 500;
                continue; // Continue to next ID even if one fails
            }
        }
    
        return response()->json([
            'status' => true,
            'message' => count($allIds) > 1
                ? 'Semua ajuan berhasil diproses!'
                : 'Ajuan berhasil diproses!',
            'waiting_count' => $return['waiting_count'] ?? 0,
        ]);
    } catch (\Throwable $th) {
        \Log::error('Error saat memproses data:', [
            'error' => $th->getMessage(),
            'trace' => $th->getTraceAsString()
        ]);
    
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan saat memproses data: ' . $th->getMessage(),
        ], 500);
    }
}


//     private function generateQueueNumber()
//     {
//         $lastQueue = Penundaan::whereDate('created_at', today())
//                         ->orderBy('queue_number', 'desc')
//                         ->first();
        
//         return $lastQueue ? $lastQueue->queue_number + 1 : 1;
//     }

//     public function updateQueue()
// {
//     try {
//         $waitingQueues = Penundaan::whereDate('created_at', today())
//                         ->where('queue_status', 'waiting')
//                         ->orderBy('queue_number', 'asc')
//                         ->get();
        
//         $newQueueNumber = 1;
        
//         foreach ($waitingQueues as $queue) {
//             $queue->update(['queue_number' => $newQueueNumber++]);
//         }
        
//         $totalWaiting = Penundaan::whereDate('created_at', today())
//                         ->where('queue_status', 'waiting')
//                         ->count();
        
//         return response()->json([
//             'status' => true,
//             'total_waiting' => $totalWaiting,
//             'current_queue' => $waitingQueues->first()->queue_number ?? null
//         ]);
        
//     } catch (\Throwable $th) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Gagal mengupdate antrian'
//         ], 500);
//     }
// }

//     public function queueStatus()
// {
//     $userQueue = Penundaan::where('user_id', Auth::id())
//                     ->whereDate('created_at', today())
//                     ->where('queue_status', 'waiting')
//                     ->first();
    
//     // Get the current queue number (lowest waiting)
//     $currentQueue = Penundaan::whereDate('created_at', today())
//                     ->where('queue_status', 'waiting')
//                     ->orderBy('queue_number', 'asc')
//                     ->first();
    
//     $totalWaiting = Penundaan::whereDate('created_at', today())
//                     ->where('queue_status', 'waiting')
//                     ->count();
    
//     return response()->json([
//         'status' => true,
//         'user_queue' => $userQueue ? $userQueue->queue_number : null,
//         'total_waiting' => $totalWaiting,
//         'current_queue' => $currentQueue ? $currentQueue->queue_number : null
//     ]);
// }

//     private function getQueueInfo($queueNumber)
//     {
//         $totalWaiting = Penundaan::whereDate('created_at', today())
//                         ->where('queue_status', 'waiting')
//                         ->count();
        
//         return "Antrian $queueNumber dari $totalWaiting";
//     }
}
