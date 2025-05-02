<?php

namespace App\Http\Controllers;

use App\Models\SuratTugas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\SuratTugasExport;
use App\Models\Layanan;
use App\Models\Lpj;
use App\Models\StatusKemahasiswaan;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\Template;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SuratTugasController extends Controller
{
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusKemahasiswaan::all();
        $prodis = Prodi::all();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        session(['layanan' => $layanan]);
        return view('pages.surat_tugas.index', compact('tahuns', 'layanan', 'status', 'prodis', 'templates'));
    }

    public function listMahasiswa()
    {
        $data = SuratTugas::where('user_id', Auth::user()->id)->with('user.prodis', 'status')->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('id', function ($row) {
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
                    $aksi = '<a href="' . asset('storage/surat_tugas/hasil/' . $row->surat_hasil) . '" class="btn btn-success btn-sm" target="_blank">
                                <i class="fa fa-download"></i> Unduh Surat
                            </a>';
                }
                return $aksi;
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y');
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('mulai_kegiatan', function ($row) {
                return Carbon::parse($row->mulai_kegiatan)->translatedFormat('d F Y') . ' -<br/>' . Carbon::parse($row->selesai_kegiatan)->translatedFormat('d F Y');
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            // ->editColumn('queue_number', function ($row) {
            //     if ($row->queue_status === 'processed') {
            //         return "Selesai";
            //     }
            //     $currentQueue = SuratTugas::whereDate('created_at', today())
            //                     ->where('queue_status', 'waiting')
            //                     ->orderBy('queue_number', 'asc')
            //                     ->first();
                
            //     $position = $row->queue_number;
            //     $current = $currentQueue ? $currentQueue->queue_number : 0;
                
            //     return "Antrian $position (Sekarang: $current)";
            // })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->editColumn('nama_kegiatan', function ($row) {
                return wordwrap($row->nama_kegiatan, 20, '<br>');
            })
            ->rawColumns(['id', 'created_at', 'tanggal_proses', 'status_id', 'mulai_kegiatan', 'catatan', 'nama_kegiatan', 'queue_number'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $list = SuratTugas::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        // $totalWaiting = SuratTugas::whereDate('created_at', today())
        //         ->where('queue_status', 'waiting')
        //         ->count();

        // $list->each(function($item) use ($totalWaiting) {
        //     $item->total_waiting = $totalWaiting;
        // });

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
                    $aksi .= '<a href="' . route('st.generate', encodeId($row->id)) . '" class="btn btn-primary btn-sm btn-block">
                                <i class="fa fa-file"></i> Generate
                            </a>';
                }
                if ($row->status_id == '9') {
                    $aksi = '<a href="' . asset('storage/surat_tugas/hasil/' . $row->surat_hasil) . '" class="btn btn-info btn-sm btn-block" target="_blank">
                            <i class="fa fa-file"></i> Lihat File
                        </a>';
                }
                return $aksi;
            })
            ->addColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('mulai_kegiatan', function ($row) {
                return Carbon::parse($row->mulai_kegiatan)->translatedFormat('d F Y') . ' -<br/>' . Carbon::parse($row->selesai_kegiatan)->translatedFormat('d F Y');
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            // ->editColumn('queue_number', function ($row) {
            //     if ($row->queue_status === 'processed') {
            //         return "Selesai";
            //     }
            //     $currentQueue = SuratTugas::whereDate('created_at', today())
            //                     ->where('queue_status', 'waiting')
            //                     ->orderBy('queue_number', 'asc')
            //                     ->first();
                
            //     $position = $row->queue_number;
            //     $current = $currentQueue ? $currentQueue->queue_number : 0;
                
            //     return "Antrian $position (Sekarang: $current)";
            // })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->editColumn('nama_kegiatan', function ($row) {
                return wordwrap($row->nama_kegiatan, 20, '<br>');
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_proses', 'mulai_kegiatan', 'status_id', 'catatan', 'nama_kegiatan'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $list = SuratTugas::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        // $totalWaiting = SuratTugas::whereDate('created_at', today())
        //         ->where('queue_status', 'waiting')
        //         ->count();

        // $list->each(function($item) use ($totalWaiting) {
        //     $item->total_waiting = $totalWaiting;
        // });

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                return $aksi;
            })
            ->addColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('mulai_kegiatan', function ($row) {
                return Carbon::parse($row->mulai_kegiatan)->translatedFormat('d F Y') . ' -<br/>' . Carbon::parse($row->selesai_kegiatan)->translatedFormat('d F Y');
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            // ->editColumn('queue_number', function ($row) {
            //     if ($row->queue_status === 'processed') {
            //         return "Selesai";
            //     }
            //     $currentQueue = SuratTugas::whereDate('created_at', today())
            //                     ->where('queue_status', 'waiting')
            //                     ->orderBy('queue_number', 'asc')
            //                     ->first();
                
            //     $position = $row->queue_number;
            //     $current = $currentQueue ? $currentQueue->queue_number : 0;
                
            //     return "Antrian $position (Sekarang: $current)";
            // })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->editColumn('nama_kegiatan', function ($row) {
                return wordwrap($row->nama_kegiatan, 20, '<br>');
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_proses', 'mulai_kegiatan', 'status_id', 'catatan', 'nama_kegiatan'])
            ->toJson();
    }

    public function listAdminProdi(Request $request)
    {
        $list = SuratTugas::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        if ($request->prodi != 'all') {
            $list = $list->whereHas('user', function($q) use ($request) {
                $q->where('prodi', $request->prodi);
            });
        }
        $list = $list->orderBy('created_at', 'desc')->get();

        // $totalWaiting = SuratTugas::whereDate('created_at', today())
        //         ->where('queue_status', 'waiting')
        //         ->count();

        // $list->each(function($item) use ($totalWaiting) {
        //     $item->total_waiting = $totalWaiting;
        // });

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                return $aksi;
            })
            ->addColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('mulai_kegiatan', function ($row) {
                return Carbon::parse($row->mulai_kegiatan)->translatedFormat('d F Y') . ' -<br/>' . Carbon::parse($row->selesai_kegiatan)->translatedFormat('d F Y');
            })
            ->editColumn('nama_prodi', function ($row) {
                return $row->user->prodis->name ?? '-';
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            // ->editColumn('queue_number', function ($row) {
            //     if ($row->queue_status === 'processed') {
            //         return "Selesai";
            //     }
            //     $currentQueue = SuratTugas::whereDate('created_at', today())
            //                     ->where('queue_status', 'waiting')
            //                     ->orderBy('queue_number', 'asc')
            //                     ->first();
                
            //     $position = $row->queue_number;
            //     $current = $currentQueue ? $currentQueue->queue_number : 0;
                
            //     return "Antrian $position (Sekarang: $current)";
            // })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->editColumn('nama_kegiatan', function ($row) {
                return wordwrap($row->nama_kegiatan, 20, '<br>');
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_proses', 'mulai_kegiatan', 'status_id', 'catatan', 'nama_kegiatan'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => ['required', 'string'],
            'mulai_kegiatan' => ['required', 'date'],
            'selesai_kegiatan' => ['required', 'date', 'after_or_equal:mulai_kegiatan'],
            'penyelenggara' => ['required'],
            'tempat' => ['required'],
            'delegasi' => ['required'],
            'jumlah_peserta' => ['required'],
            'dospem' => ['required'],
            'nip_dospem' => ['required'],
            'nidn_dospem' => ['required'],
            'unit_dospem' => ['required'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'after_or_equal' => ':attribute tidak valid!',
            'max' => 'ukuran :attribute tidak boleh melebihi 10 mb!',
        ], [
            'nama_kegiatan' => 'Nama Kegiatan',
            'mulai_kegiatan' => 'Tanggal Mulai Kegiatan',
            'selesai_kegiatan' => 'Tanggal Selesai Kegiatan',
            'penyelenggara' => 'Penyelenggara Kegiatan',
            'tempat' => 'Tempat Pelaksanaan',
            'delegasi' => 'Delegasi sebagai',
            'jumlah_peserta' => 'Jumlah Peserta Delegasi',
            'dospem' => 'Dosen Pembimbing',
            'nip_dospem' => 'NIP/NIK Dosen Pembimbing',
            'nidn_dospem' => 'NIDN Dosen Pembimbing',
            'unit_dospem' => 'Unit Kerja Dosen Pembimbing',
            'file' => 'file PDF',
        ]);

        try {
            // $st_terakhir = SuratTugas::orderBy('created_at', 'desc')->find(Auth::user()->id);
            // if ($st_terakhir && $st_terakhir->lpj && is_null($st_terakhir->lpj->file)) {
            //     return response()->json([
            //         'status' => false, 
            //         'message' => 'Silahkan Upload LPJ untuk Surat Tugas Delegasi dengan No Surat ' . $st_terakhir->no_surat . 
            //                     ' agar dapat menambah ajuan.'
            //     ], 500);
            // }
            $ajuan_st = SuratTugas::where('status_id', '9')->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
            foreach ($ajuan_st as $st) {
                if ($st && $st->lpj && is_null($st->lpj->file)) {
                    return response()->json([
                        'status' => false, 
                        'message' => 'Silahkan Upload LPJ untuk Surat Tugas Delegasi dengan No Surat ' . $st->no_surat . 
                                    ' agar dapat menambah ajuan.'
                    ], 500);
                }
            }

            $fileName = 'ST-' . Auth::user()->nim . '-' . Str::of(Auth::user()->name)->replace(' ','') . '-' . time() . '.pdf';
            $request->file->storeAs('surat_tugas/upload/', $fileName, 'public');

            // Generate nomor antrian terlepas dari siapa yang mengajukan
            // $lastQueue = SuratTugas::whereDate('created_at', today())
            //                 ->orderBy('queue_number', 'desc')
            //                 ->first();

            // $queueNumber = $lastQueue ? $lastQueue->queue_number + 1 : 1;

            SuratTugas::create([
                'user_id' => Auth::user()->id,
                'status_id' => '1',
                'nama_kegiatan' => $request->nama_kegiatan,
                'mulai_kegiatan' => $request->mulai_kegiatan,
                'selesai_kegiatan' => $request->selesai_kegiatan,
                'penyelenggara' => $request->penyelenggara,
                'tempat' => $request->tempat,
                'delegasi' => $request->delegasi,
                'jumlah_peserta' => $request->jumlah_peserta,
                'dospem' => $request->dospem,
                'nip_dospem' => $request->nip_dospem,
                'nidn_dospem' => $request->nidn_dospem,
                'unit_dospem' => $request->unit_dospem,
                'file' => $fileName,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
        return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Ditambahkan!'], 200);
    }

    public function show(string $id)
    {
        $id = decodeId($id);
        $ajuan = SuratTugas::where('id', $id)->with('user.prodis', 'status')->first();
        $ajuan['tanggal_kegiatan'] = Carbon::parse($ajuan->mulai_kegiatan)->translatedFormat('d F Y') . '<br/>Sampai<br/>' . Carbon::parse($ajuan->selesai_kegiatan)->translatedFormat('d F Y');
        return response()->json(['status' => true, 'data' => $ajuan], 200);
    }

    public function revisi(Request $request, string $id)
    {
        $request->validate([
            'nama_kegiatan' => ['required', 'string'],
            'mulai_kegiatan' => ['required', 'date'],
            'selesai_kegiatan' => ['required', 'date', 'after_or_equal:mulai_kegiatan'],
            'penyelenggara' => ['required'],
            'tempat' => ['required'],
            'delegasi' => ['required'],
            'jumlah_peserta' => ['required'],
            'dospem' => ['required'],
            'nip_dospem' => ['required'],
            'nidn_dospem' => ['required'],
            'unit_dospem' => ['required'],
            'file' => ['file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'after_or_equal' => ':attribute tidak valid!',
            'max' => 'ukuran :attribute tidak boleh melebihi 10 mb!',
        ], [
            'nama_kegiatan' => 'Nama Kegiatan',
            'mulai_kegiatan' => 'Tanggal Mulai Kegiatan',
            'selesai_kegiatan' => 'Tanggal Selesai Kegiatan',
            'penyelenggara' => 'Penyelenggara Kegiatan',
            'tempat' => 'Tempat Pelaksanaan',
            'delegasi' => 'Delegasi sebagai',
            'jumlah_peserta' => 'Jumlah Peserta Delegasi',
            'dospem' => 'Dosen Pembimbing',
            'nip_dospem' => 'NIP/NIK Dosen Pembimbing',
            'nidn_dospem' => 'NIDN Dosen Pembimbing',
            'unit_dospem' => 'Unit Kerja Dosen Pembimbing',
            'file' => 'file PDF',
        ]);

        try {
            $id = decodeId($id);
            $ajuan = SuratTugas::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '3', '4'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat mengedit data'], 500);
            }
            $message = ($ajuan->status_id == '3') ? 'Direvisi' : 'Diedit';
            $status_id = ($ajuan->status_id == '3') ? '4' : $ajuan->status_id;

            $file = $ajuan->file;
            if ($request->hasFile('file')) {
                $file = 'ST-' . Auth::user()->nim . '-' . Str::of(Auth::user()->name)->replace(' ','') . '-' . time() . '.pdf';
                $request->file->storeAs('surat_tugas/upload/', $file, 'public');

                Storage::disk('public')->delete('surat_tugas/upload/' . $ajuan->file);
            }

            $ajuan->update([
                'nama_kegiatan' => $request->nama_kegiatan,
                'mulai_kegiatan' => $request->mulai_kegiatan,
                'selesai_kegiatan' => $request->selesai_kegiatan,
                'penyelenggara' => $request->penyelenggara,
                'tempat' => $request->tempat,
                'delegasi' => $request->delegasi,
                'jumlah_peserta' => $request->jumlah_peserta,
                'dospem' => $request->dospem,
                'nip_dospem' => $request->nip_dospem,
                'nidn_dospem' => $request->nidn_dospem,
                'unit_dospem' => $request->unit_dospem,
                'file' => $file,
                'status_id' => $status_id
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil ' . $message], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $id = decodeId($id);
            $ajuan = SuratTugas::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '3', '4'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat membatalkan ajuan'], 500);
            }
            Storage::disk('public')->delete('surat_tugas/upload/' . $ajuan->file);
            $ajuan->update(['status_id' => '2']);
            return response()->json(['status' => true, 'message' => 'Ajuan berhasil dibatalkan'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'tahun' => ['required']
        ], [
            'required' => ':attribute wajib diisi',
        ], [
            'tahun' => 'Tahun'
        ]);

        $tahun = $request->tahun;
        $name = 'Rekap_Data_Surat_Tugas_Delegasi_Tahun_' . $tahun;
        return Excel::download(new SuratTugasExport($tahun), $name . '.xlsx');
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
            'file' => ''
        ];
        $status_code = 200;

        try {
            $id = decodeId($id);
            $ajuan = SuratTugas::where('id', $id)->with('user.prodis', 'status')->first();
    
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
                // $data_update['queue_status'] = 'processed'; // Update status antrian
                $return['message'] = 'Ajuan Berhasil Diproses!';
            } elseif (in_array($request->status_id, ['7', '8'])) { //status ditolak
                // Hapus file upload jika ajuan ditolak
                Storage::disk('public')->delete('surat_tugas/upload/' . $ajuan->file);
                $return['message'] = 'Ajuan Berhasil Ditolak!';
            } elseif ($request->status_id == '9') { //status selesai
                // Hapus file upload dan surat hasil sementara
                Storage::disk('public')->delete('surat_tugas/upload/' . $ajuan->file);
                // Upload surat hasil jika ajuan telah selesai
                $fileName = $ajuan->user->nim . '-' . $ajuan->user->name . '-ST' . time() . '.pdf';
                $request->file->storeAs('surat_tugas/hasil/', $fileName, 'public');
                $data_update['surat_hasil'] = $fileName;
                // $data_update['queue_status'] = 'processed'; // Update status antrian
                $return['message'] = 'Ajuan telah selesai!';

                Lpj::create([
                    'surat_tugas_id' => $ajuan->id,
                    'status_id' => '1', // status belum upload
                ]);
            }
            
            $ajuan->update($data_update);

            // Hitung antrian yang tersisa
            // $waitingCount = SuratTugas::where('queue_status', 'waiting')
            // ->whereDate('created_at', today())
            // ->count();

            // $return['waiting_count'] = $waitingCount;
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
        $ajuan = SuratTugas::where('id', $id)->with('user.prodis', 'status')->first();
        $wd3 = User::where('role', '3')->first();
        $layanan = session('layanan');
        $templateProcessor = new TemplateProcessor(public_path('/storage/suratHasil/' . $layanan->template_surat_hasil));
        $templateProcessor->setValues([
            'nomor' => $ajuan->no_surat,
            'nama_wd3' => $wd3->name,
            'nip_wd3' => $wd3->nim,
            'jabatan_wd3' => $wd3->jabatan,
            'name' => $ajuan->user->name,
            'nim' => $ajuan->user->nim,
            'prodi' => $ajuan->user->prodis->name,
            'kegiatan' => $ajuan->nama_kegiatan,
            'tanggal_pelaksanaan' => 
                $ajuan->mulai_kegiatan == $ajuan->selesai_kegiatan ? 
                Carbon::parse($ajuan->mulai_kegiatan)->translatedFormat('j F Y') : 
                Carbon::parse($ajuan->mulai_kegiatan)->translatedFormat('j F Y') . ' - ' . 
                Carbon::parse($ajuan->selesai_kegiatan)->translatedFormat('j F Y'),
            'tempat_pelaksanaan' => $ajuan->tempat,
            'penyelenggara' => $ajuan->penyelenggara,
            'nama_dospem' => $ajuan->dospem,
            'nip_dospem' => $ajuan->nip_dospem,
            'nidn_dospem' => $ajuan->nidn_dospem,
            'unit_dospem' => $ajuan->unit_dospem,
            'tanggal_surat' => Carbon::today()->translatedFormat('j F Y'),
        ]);

        $docFile = $ajuan->user->nim . '-' . $ajuan->user->name . '-ST' . time() . '.docx';
        $templateProcessor->saveAs($docFile);
        return response()->download($docFile)->deleteFileAfterSend(true);
    }

    // private function generateQueueNumber()
    // {
    //     $lastQueue = SuratTugas::whereDate('created_at', today())
    //                     ->orderBy('queue_number', 'desc')
    //                     ->first();
        
    //     return $lastQueue ? $lastQueue->queue_number + 1 : 1;
    // }

    // public function updateQueue()
    // {
    //     try {
    //         // Ambil semua antrian hari ini yang masih waiting, diurutkan berdasarkan queue_number
    //         $waitingQueues = SuratTugas::whereDate('created_at', today())
    //                         ->where('queue_status', 'waiting')
    //                         ->orderBy('queue_number', 'asc')
    //                         ->get();
            
    //         $newQueueNumber = 1;
            
    //         // Update nomor antrian untuk semua yang waiting
    //         foreach ($waitingQueues as $queue) {
    //             $queue->update(['queue_number' => $newQueueNumber++]);
    //         }
            
    //         $totalWaiting = SuratTugas::whereDate('created_at', today())
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

    // public function queueStatus()
    // {
    //     $userQueue = SuratTugas::where('user_id', Auth::id())
    //                     ->whereDate('created_at', today())
    //                     ->where('queue_status', 'waiting')
    //                     ->first();
        
    //     // Get the current queue number (lowest waiting)
    //     $currentQueue = SuratTugas::whereDate('created_at', today())
    //                     ->where('queue_status', 'waiting')
    //                     ->orderBy('queue_number', 'asc')
    //                     ->first();
        
    //     $totalWaiting = SuratTugas::whereDate('created_at', today())
    //                     ->where('queue_status', 'waiting')
    //                     ->count();
        
    //     return response()->json([
    //         'status' => true,
    //         'user_queue' => $userQueue ? $userQueue->queue_number : null,
    //         'total_waiting' => $totalWaiting,
    //         'current_queue' => $currentQueue ? $currentQueue->queue_number : null
    //     ]);
    // }

    // private function getQueueInfo($queueNumber)
    // {
    //     $totalWaiting = SuratTugas::whereDate('created_at', today())
    //                     ->where('queue_status', 'waiting')
    //                     ->count();
        
    //     return "Antrian $queueNumber dari $totalWaiting";
    // }
}
