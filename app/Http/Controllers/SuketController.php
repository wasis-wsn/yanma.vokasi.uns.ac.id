<?php

namespace App\Http\Controllers;

use App\Models\SuratKeterangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\SuketExport;
use App\Models\Tahun;
use App\Models\Layanan;
use App\Models\Semester;
use App\Models\StatusKemahasiswaan;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Models\Template;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;

class SuketController extends Controller
{
    // public $layanan_id = 11;
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusKemahasiswaan::all();
        $prodis = Prodi::all();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_akademik', 'desc')->limit('6')->get();
        $semester = Semester::all();
        session(['layanan' => $layanan]);
        return view('pages.surat_keterangan.index', compact('tahuns', 'layanan', 'status', 'templates', 'prodis', 'tahunAkademik', 'semester'));
    }

    public function listMahasiswa()
    {
        $list = SuratKeterangan::with('user.prodis', 'status')->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i>
                        </button>';
                if (in_array($row->status_id, ['1', '3', '4'])) {
                    $aksi .= '<button type="button" class="btn btn-warning btn-sm btn-edit" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-pen"></i>
                            </button>';
                    $aksi .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . encodeId($row->id) . '">
                                <i class="fa fa-trash"></i>
                            </button>';
                } elseif ($row->status_id == '9') {
                    $aksi = '<a href="' . asset('storage/surat_keterangan/hasil/' . $row->surat_hasil) . '" class="btn btn-success btn-sm" target="_blank">
                                <i class="fa fa-download"></i> Unduh Surat
                            </a>';
                }

                return $aksi;
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y');
            })
            ->editColumn('keperluan', function ($row) {
                return wordwrap($row->keperluan, 20, '<br>'); 
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('queue_number', function ($row) {
                if ($row->queue_status === 'processed') {
                    return "Selesai";
                }
                $currentQueue = SuratKeterangan::whereDate('created_at', today())
                                ->where('queue_status', 'waiting')
                                ->orderBy('queue_number', 'asc')
                                ->first();
                
                $position = $row->queue_number;
                $current = $currentQueue ? $currentQueue->queue_number : 0;
                
                return "Antrian $position (Sekarang: $current)";
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['action', 'created_at', 'keperluan', 'tanggal_proses', 'status_id', 'catatan', 'queue_number'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $list = SuratKeterangan::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        $totalWaiting = SuratKeterangan::whereDate('created_at', today())
                ->where('queue_status', 'waiting')
                ->count();

        $list->each(function($item) use ($totalWaiting) {
            $item->total_waiting = $totalWaiting;
        });
        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                if (in_array($row->status_id, ['1', '3', '4', '5', '6'])) {
                    $aksi .= '<button type="button" class="btn btn-success btn-sm btn-proses btn-block" data-id="' . encodeId($row->id) . '" data-status="' . $row->status_id . '">
                                <i class="fa fa-file-pen"></i> Proses
                            </button>';
                }
                if (in_array($row->status_id, ['5', '6'])) {
                    $aksi .= '<a href="' . route('suket.generate', encodeId($row->id)) . '" class="btn btn-primary btn-sm btn-block">
                                <i class="fa fa-file"></i> Generate
                            </a>';
                }
                if ($row->status_id == '9') {
                    $aksi = '<a href="' . asset('storage/surat_keterangan/hasil/' . $row->surat_hasil) . '" class="btn btn-info btn-sm btn-block" target="_blank">
                            <i class="fa fa-file"></i> Lihat File
                        </a>';
                }
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('keperluan', function ($row) {
                return wordwrap($row->keperluan, 20, '<br>'); 
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('queue_number', function ($row) {
                if ($row->queue_status === 'processed') {
                    return "Selesai";
                }
                $currentQueue = SuratKeterangan::whereDate('created_at', today())
                                ->where('queue_status', 'waiting')
                                ->orderBy('queue_number', 'asc')
                                ->first();
                
                $position = $row->queue_number;
                $current = $currentQueue ? $currentQueue->queue_number : 0;
                
                return "Antrian $position (Sekarang: $current)";
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['action', 'tanggal_submit', 'keperluan', 'tanggal_proses', 'nama_prodi', 'status_id', 'catatan', 'queue_number'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $list = SuratKeterangan::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        $totalWaiting = SuratKeterangan::whereDate('created_at', today())
                ->where('queue_status', 'waiting')
                ->count();

        $list->each(function($item) use ($totalWaiting) {
            $item->total_waiting = $totalWaiting;
        });

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('keperluan', function ($row) {
                return Str::of($row->keperluan)->limit(20); 
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('queue_number', function ($row) {
                if ($row->queue_status === 'processed') {
                    return "Selesai";
                }
                $currentQueue = SuratKeterangan::whereDate('created_at', today())
                                ->where('queue_status', 'waiting')
                                ->orderBy('queue_number', 'asc')
                                ->first();
                
                $position = $row->queue_number;
                $current = $currentQueue ? $currentQueue->queue_number : 0;
                
                return "Antrian $position (Sekarang: $current)";
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['action', 'tanggal_submit', 'keperluan', 'tanggal_proses', 'status_id', 'catatan'])
            ->toJson();
    }

    public function listAdminProdi(Request $request)
    {
        $list = SuratKeterangan::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($q) use ($request) {
                $q->where('prodi', $request->prodi);
            });
        }
        $list = $list->orderBy('created_at', 'desc')->get();

        $totalWaiting = SuratKeterangan::whereDate('created_at', today())
                ->where('queue_status', 'waiting')
                ->count();

        $list->each(function($item) use ($totalWaiting) {
            $item->total_waiting = $totalWaiting;
        });

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('keperluan', function ($row) {
                return Str::of($row->keperluan)->limit(20); 
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('nama_prodi', function ($row) {
                return $row->user->prodis->name ?? '-';
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('queue_number', function ($row) {
                if ($row->queue_status === 'processed') {
                    return "Selesai";
                }
                $currentQueue = SuratKeterangan::whereDate('created_at', today())
                                ->where('queue_status', 'waiting')
                                ->orderBy('queue_number', 'asc')
                                ->first();
                
                $position = $row->queue_number;
                $current = $currentQueue ? $currentQueue->queue_number : 0;
                
                return "Antrian $position (Sekarang: $current)";
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['action', 'tanggal_submit', 'keperluan', 'tanggal_proses', 'status_id', 'catatan'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'keperluan' => ['required', 'string'],
            // 'tahun_akademik' => ['required'],
            'tahun_akademik_id' => ['required', 'numeric'],
            'semester_id' => ['required', 'numeric'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'numeric' => ':attribute tidak falid!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'tahun_akademik_id' => 'Tahun Akademik',
            'semester_id' => 'Semester',
            'keperluan' => 'Keperluan Surat',
            'file' => 'File Hasil Scan KRS',
        ]);

        try {
            $fileName = 'SUKET-' . auth()->user()->nim . '-' . auth()->user()->name . '-' . time() . '.pdf';
            $request->file->storeAs('surat_keterangan/upload/', $fileName, 'public');

            // Generate nomor antrian terlepas dari siapa yang mengajukan
            $lastQueue = SuratKeterangan::whereDate('created_at', today())
                            ->orderBy('queue_number', 'desc')
                            ->first();

            $queueNumber = $lastQueue ? $lastQueue->queue_number + 1 : 1;

            SuratKeterangan::create([
                'user_id' => Auth::user()->id,
                'status_id' => '1',
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'semester_id' => $request->semester_id,
                'keperluan' => $request->keperluan,
                'file' => $fileName,
                'queue_number' => $queueNumber,
                'queue_status' => 'waiting',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan', 'queue_number' => $queueNumber], 500);
        }
        return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Ditambahkan!'], 200);
    }

    public function show(string $id)
    {
        $id = decodeId($id);
        $ajuan = SuratKeterangan::where('id', $id)->with('user.prodis', 'status', 'tahunAkademik', 'semester')->first();
        return response()->json(['status' => true, 'data' => $ajuan], 200);
    }

    public function revisi(Request $request, string $id)
    {
        $request->validate([
            'keperluan' => ['required', 'string'],
            // 'tahun_akademik' => ['required'],
            'tahun_akademik_id' => ['required', 'numeric'],
            'semester_id' => ['required', 'numeric'],
            'file' => ['file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'numeric' => ':attribute tidak falid!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'tahun_akademik_id' => 'Tahun Akademik',
            'semester_id' => 'Semester',
            'keperluan' => 'Keperluan Surat',
            'file' => 'File Hasil Scan KRS',
        ]);

        try {
            $id = decodeId($id);
            $ajuan = SuratKeterangan::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '3', '4'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat mengedit data'], 500);
            }
            $message = ($ajuan->status_id == '3') ? 'Direvisi' : 'Diedit';
            $status_id = ($ajuan->status_id == '3') ? '4' : $ajuan->status_id;

            $file = $ajuan->file;
            if ($request->hasFile('file')) {
                $file = 'SUKET-' . Auth::user()->nim . '-' . Auth::user()->name . '-' . time() . '.pdf';
                $request->file->storeAs('surat_keterangan/upload/', $file, 'public');

                Storage::disk('public')->delete('surat_keterangan/upload/'.$ajuan->file);
            }

            $ajuan->update([
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'semester_id' => $request->semester_id,
                'keperluan' => $request->keperluan,
                'file' => $file,
                'status_id' => $status_id
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil '.$message], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $id = decodeId($id);
            $ajuan = SuratKeterangan::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '3', '4'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat membatalkan ajuan'], 500);
            }
            Storage::disk('public')->delete('surat_keterangan/upload/' . $ajuan->file);
            $ajuan->update(['status_id' => '2']);
            return response()->json(['status' => true, 'message' => 'Ajuan berhasil dibatalkan'], 200);
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
        $tahun_akademik = Str::of($tahun->tahun_akademik)->replace(' ', '')->replace('/','_')->replace('\\','_');
        $name = 'Rekap_Data_Surat_Keterangan_Tahun_Akademik_' . $tahun_akademik . '_' . $semester->semester;
        return Excel::download(new SuketExport($tahun, $semester), $name . '.xlsx');
    }

    public function proses(Request $request, $id)
    {
        $request->validate([
            'status_id' => ['required'],
            'no_surat' => Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['5', '6']);
            }),
            'file' => ['requiredif:status_id,9'],
            'catatan' => Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['3', '7', '8']);
            })
        ], [
            'required' => ':attribute harus diisi!'
        ]);
        
        $return = [
            'status' => true,
            'message' => '',
            // 'file' => '',
        ];
        $status_code = 200;

        try {
            $id = decodeId($id);
            $ajuan = SuratKeterangan::where('id', $id)->with('user.prodis', 'status')->first();
    
            $data_update = [
                'no_surat' => $ajuan->no_surat,
                'status_id' => $request->status_id,
                'catatan' => $request->catatan,
                'surat_hasil' => $ajuan->surat_hasil,
                'tanggal_proses' => new \DateTime(),
            ];
    
            // ajuan diproses
            if (in_array($request->status_id, ['5', '6'])) {
                $data_update['no_surat'] = $request->no_surat;
                $data_update['queue_status'] = 'processed'; // Update status antrian
                $return['message'] = 'Ajuan Berhasil Diproses!';
            } elseif (in_array($request->status_id, ['7', '8'])) { //status ditolak
                // Hapus file upload jika ajuan ditolak
                Storage::disk('public')->delete('surat_keterangan/upload/' . $ajuan->file);
                $return['message'] = 'Ajuan Berhasil Ditolak!';
            } elseif ($request->status_id == '9') { //status selesai
                // Hapus file upload dan surat hasil sementara
                Storage::disk('public')->delete('surat_keterangan/upload/' . $ajuan->file);
                // Upload surat hasil jika ajuan telah selesai
                $fileName = $ajuan->user->nim . '-' . $ajuan->user->name . '-Suket' . time() . '.pdf';
                $request->file->storeAs('surat_keterangan/hasil/', $fileName, 'public');
                
                $data_update['surat_hasil'] = $fileName;
                $data_update['queue_status'] = 'processed'; // Update status antrian
                $return['message'] = 'Ajuan telah selesai!';
            }
            
            $ajuan->update($data_update);

            // Hitung antrian yang tersisa
            $waitingCount = SuratKeterangan::where('queue_status', 'waiting')
            ->whereDate('created_at', today())
            ->count();

            $return['waiting_count'] = $waitingCount;
        } catch (\Throwable $th) {
            $return['status'] = false;
            $return['message'] = 'Terjadi Kesalahan!';
            $status_code = 500;
        } finally {
            return response()->json($return, $status_code);
        }
    }

    public function generateSurat($id)
    {
        $id = decodeId($id);
        $ajuan = SuratKeterangan::where('id', $id)->with('user.prodis', 'status', 'semester', 'tahunAkademik')->first();
        // $tahun_akademik = explode(' - ', $ajuan->tahun_akademik);
        $wd3 = User::where('role', '3')->first();
        $layanan = session('layanan');
        $templateProcessor = new TemplateProcessor(public_path('/storage/suratHasil/' . $layanan->template_surat_hasil));
        $templateProcessor->setValues([
            'nomor' => $ajuan->no_surat,
            'nama_wd3' => $wd3->name,
            'nip_wd3' => $wd3->nim,
            'pangkat_wd3' => $wd3->pangkat,
            'jabatan_wd3' => $wd3->jabatan,
            'name' => $ajuan->user->name,
            'nim' => $ajuan->user->nim,
            'prodi' => $ajuan->user->prodis->name,
            'keperluan' => $ajuan->keperluan,
            'semester' => $ajuan->semester->semester,
            'tahun_akademik' => $ajuan->tahunAkademik->tahun_akademik,
            'tanggal_surat' => Carbon::today()->translatedFormat('j F Y'),
        ]);

        $docFile = $ajuan->user->nim . '-' . $ajuan->user->name . '-SUKET' . time() . '.docx';
        $templateProcessor->saveAs($docFile);
        return response()->download($docFile)->deleteFileAfterSend(true);
    }

    private function generateQueueNumber()
    {
        $lastQueue = SuratKeterangan::whereDate('created_at', today())
                        ->orderBy('queue_number', 'desc')
                        ->first();
        
        return $lastQueue ? $lastQueue->queue_number + 1 : 1;
    }

    public function updateQueue()
    {
        try {
            // Ambil semua antrian hari ini yang masih waiting, diurutkan berdasarkan queue_number
            $waitingQueues = SuratKeterangan::whereDate('created_at', today())
                            ->where('queue_status', 'waiting')
                            ->orderBy('queue_number', 'asc')
                            ->get();
            
            $newQueueNumber = 1;
            
            // Update nomor antrian untuk semua yang waiting
            foreach ($waitingQueues as $queue) {
                $queue->update(['queue_number' => $newQueueNumber++]);
            }
            
            $totalWaiting = SuratKeterangan::whereDate('created_at', today())
                            ->where('queue_status', 'waiting')
                            ->count();
            
            return response()->json([
                'status' => true,
                'total_waiting' => $totalWaiting,
                'current_queue' => $waitingQueues->first()->queue_number ?? null
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate antrian'
            ], 500);
        }
    }

    public function queueStatus()
{
    $userQueue = SuratKeterangan::where('user_id', Auth::id())
                    ->whereDate('created_at', today())
                    ->where('queue_status', 'waiting')
                    ->first();
    
    // Get the current queue number (lowest waiting)
    $currentQueue = SuratKeterangan::whereDate('created_at', today())
                    ->where('queue_status', 'waiting')
                    ->orderBy('queue_number', 'asc')
                    ->first();
    
    $totalWaiting = SuratKeterangan::whereDate('created_at', today())
                    ->where('queue_status', 'waiting')
                    ->count();
    
    return response()->json([
        'status' => true,
        'user_queue' => $userQueue ? $userQueue->queue_number : null,
        'total_waiting' => $totalWaiting,
        'current_queue' => $currentQueue ? $currentQueue->queue_number : null
    ]);
}

    private function getQueueInfo($queueNumber)
    {
        $totalWaiting = SuratKeterangan::whereDate('created_at', today())
                        ->where('queue_status', 'waiting')
                        ->count();
        
        return "Antrian $queueNumber dari $totalWaiting";
    }
}
