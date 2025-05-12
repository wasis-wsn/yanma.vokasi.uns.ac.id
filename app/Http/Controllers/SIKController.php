<?php

namespace App\Http\Controllers;

use App\Models\Ormawa;
use App\Models\SIK;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\SIKExport;
use App\Models\Layanan;
use App\Models\Lpj;
use App\Models\StatusKemahasiswaan;
use App\Models\Tahun;
use App\Models\Template;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;

class SIKController extends Controller
{
    // public $layanan_id = 14;
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusKemahasiswaan::all();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        session(['layanan' => $layanan]);
        return view('pages.sik.index', compact('tahuns', 'layanan', 'status', 'templates'));
    }

    public function listOrmawa()
    {
        $data = SIK::where('ormawa_id', Auth::user()->id)->with('ketua.prodis', 'status', 'ormawa.pembina')->orderBy('created_at', 'desc')->get();

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
                    $aksi = '<a href="' . asset('storage/sik/hasil/' . $row->surat_hasil) . '" class="btn btn-success btn-sm" target="_blank">
                                <i class="fa fa-download"></i> Unduh Surat
                            </a>';
                }
                return $aksi;
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' 
                . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' 
                    . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s');
                }
                return $tanggal_proses;
            })
            ->editColumn('mulai_kegiatan', function ($row) {
                return Carbon::parse($row->mulai_kegiatan)->translatedFormat('d F Y H:i') . ' -<br/>' . Carbon::parse($row->selesai_kegiatan)->translatedFormat('d F Y H:i');
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('is_dana', function ($row) {
                $jenis = $row->is_dana == '0' ? 'Non Dana' : 'Dana';
                return $jenis;
            })
            ->editColumn('nama_kegiatan', function ($row) {
                return wordwrap($row->nama_kegiatan, 20, '<br>');
            })
            ->editColumn('tempat', function ($row) {
                return wordwrap($row->tempat, 20, '<br>');
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['id', 'created_at', 'tanggal_proses', 'status_id', 'mulai_kegiatan', 'is_dana', 'nama_kegiatan', 'tempat', 'catatan'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $list = SIK::with('ketua.prodis', 'status', 'ormawa.pembina')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('id', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                if (in_array($row->status_id, ['1', '3', '4', '5', '6'])) {
                    $aksi .= '<button type="button" class="btn btn-success btn-sm btn-block btn-proses" data-id="' . encodeId($row->id) . '" data-status="' . $row->status_id . '">
                                <i class="fa fa-check"></i> Proses
                            </button>';
                }
                if (in_array($row->status_id, ['5', '6'])) {
                    $aksi .= '<a href="' . route('sik.generate', encodeId($row->id)) . '" class="btn btn-primary btn-sm btn-block">
                                <i class="fa fa-file"></i> Generate
                            </a>';
                }
                if (in_array($row->status_id, ['9'])) {
                    $aksi = '<a href="' . asset('storage/sik/hasil/' . $row->surat_hasil) . '" class="btn btn-info btn-sm btn-block" target="_blank">
                            <i class="fa fa-file"></i> Lihat File
                        </a>';
                }
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' 
                . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' 
                    . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_proses;
            })
            ->editColumn('mulai_kegiatan', function ($row) {
                return Carbon::parse($row->mulai_kegiatan)->translatedFormat('d F Y H:i') . ' -<br/>' . Carbon::parse($row->selesai_kegiatan)->translatedFormat('d F Y H:i') . ' WIB';
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('is_dana', function ($row) {
                $jenis = $row->is_dana == '0' ? 'Non Dana' : 'Dana';
                return $jenis;
            })
            ->editColumn('nama_kegiatan', function ($row) {
                return wordwrap($row->nama_kegiatan, 20, '<br>');
            })
            ->editColumn('tempat', function ($row) {
                return wordwrap($row->tempat, 20, '<br>');
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['id', 'tanggal_submit', 'tanggal_proses', 'status_id', 'mulai_kegiatan', 'is_dana', 'nama_kegiatan', 'tempat', 'catatan'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $list = SIK::with('ketua.prodis', 'status', 'ormawa.pembina')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

        return DataTables::of($list)
            ->addIndexColumn()
            ->addColumn('id', function ($row) {
                $aksi = '<button type="button" class="btn btn-info btn-sm btn-detail" data-id="' . encodeId($row->id) . '">
                            <i class="fa fa-eye"></i> Review
                        </button>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
            })
            ->editColumn('tanggal_proses', function ($row) {
                $tanggal_proses = $row->tanggal_proses;
                if ($tanggal_proses) {
                    $tanggal_proses = Carbon::parse($row->tanggal_proses)->translatedFormat('d F Y') . '<br/>' 
                    . Carbon::parse($row->tanggal_proses)->translatedFormat('H:i:s') . ' WIB';
                }
                return $tanggal_proses;
            })
            ->editColumn('mulai_kegiatan', function ($row) {
                return Carbon::parse($row->mulai_kegiatan)->translatedFormat('d F Y H:i') . ' -<br/>' . Carbon::parse($row->selesai_kegiatan)->translatedFormat('d F Y H:i') . ' WIB';
            })
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('is_dana', function ($row) {
                $jenis = $row->is_dana == '0' ? 'Non Dana' : 'Dana';
                return $jenis;
            })
            ->editColumn('nama_kegiatan', function ($row) {
                return wordwrap($row->nama_kegiatan, 20, '<br>');
            })
            ->editColumn('tempat', function ($row) {
                return wordwrap($row->tempat, 20, '<br>');
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['id', 'tanggal_submit', 'tanggal_proses', 'status_id', 'mulai_kegiatan', 'is_dana', 'nama_kegiatan', 'tempat', 'catatan'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => ['required', 'string'],
            'ketua_id' => ['required'],
            'no_surat_ormawa' => ['required'],
            'tanggal_surat' => ['required'],
            'is_dana' => ['required'],
            'mulai_kegiatan' => ['required', 'date'],
            'selesai_kegiatan' => ['required', 'date', 'after:mulai_kegiatan'],
            'tanggal_lpj' => ['required', 'date', 'after_or_equal:selesai_kegiatan', 
                                'before_or_equal:'.date('Y-m-d', strtotime('+14 days', strtotime($request->selesai_kegiatan)))
                            ],
            'tempat' => ['required'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'after' => ':attribute tidak valid!',
            'after_or_equal' => ':attribute tidak valid!',
            'before_or_equal' => ':attribute tidak valid!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'nama_kegiatan' => 'Nama Kegitan',
            'ketua_id' => 'Ketua',
            'no_surat_ormawa' => 'Nomor Surat Ormawa',
            'tanggal_surat' => 'Tanggal Surat',
            'is_dana' => 'Apakah Mengajukan Dana',
            'mulai_kegiatan' => 'Tanggal Kegiatan (Mulai)',
            'selesai_kegiatan' => 'Tanggal Kegiatan (Selesai)',
            'tanggal_lpj' => 'Tanggal Pernyataan LPJ dan SPJ',
            'tempat' => 'Tempat Kegiatan',
            'file' => 'Proposal dan Lampiran',
        ]);

        try {
            // $sik_terakhir = SIK::where('status_id', '9')->where('ormawa_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
            // if ($sik_terakhir && $sik_terakhir->lpj && is_null($sik_terakhir->lpj->file)) {
            //     return response()->json([
            //         'status' => false, 
            //         'message' => 'Silahkan Upload LPJ untuk Surat Izin Kegiatan dengan No Surat ' . $sik_terakhir->no_surat . 
            //                     ' agar dapat menambah ajuan.'
            //     ], 500);
            // }
            
            $ajuan_sik = SIK::where('status_id', '9')->where('ormawa_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
            $today = Carbon::now();
            foreach ($ajuan_sik as $sik) {
                $targetDate = Carbon::parse($sik->tanggal_lpj);
                if ($sik && $sik->lpj && is_null($sik->lpj->file) && $today->greaterThan($targetDate)) {
                    return response()->json([
                        'status' => false, 
                        'message' => 'Silahkan Upload LPJ untuk Surat Izin Kegiatan dengan No Surat ' . $sik->no_surat . 
                                    ' agar dapat menambah ajuan.'
                    ], 500);
                }
            }
            $ormawa = str_replace(' ', '', Auth::user()->name);
            $namaKegiatan = str_replace(' ', '', $request->nama_kegiatan);
            $fileName = 'SIK-' . $ormawa . '-' . $namaKegiatan . '-' . time() . '.pdf';
            $request->file->storeAs('sik/upload/', $fileName, 'public');

            SIK::create([
                'ormawa_id' => Auth::user()->id,
                'status_id' => '1',
                'ketua_id' => $request->ketua_id,
                'nama_kegiatan' => $request->nama_kegiatan,
                'no_surat_ormawa' => $request->no_surat_ormawa,
                'tanggal_surat' => $request->tanggal_surat,
                'is_dana' => $request->is_dana,
                'tanggal_lpj' => $request->tanggal_lpj,
                'mulai_kegiatan' => $request->mulai_kegiatan,
                'selesai_kegiatan' => $request->selesai_kegiatan,
                'tempat' => $request->tempat,
                'file' => $fileName,
            ]);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Ditambahkan!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function show(string $id)
    {
        $id = decodeId($id);
        $ajuan = SIK::with('ketua.prodis', 'status', 'ormawa.pembina')->findOrFail($id);
        $ajuan['tanggal_surat2'] = Carbon::parse($ajuan->tanggal_surat)->translatedFormat('d F Y');
        $ajuan['tanggal_lpj2'] = Carbon::parse($ajuan->tanggal_lpj)->translatedFormat('d F Y');
        $ajuan['tanggal_kegiatan'] = Carbon::parse($ajuan->mulai_kegiatan)->translatedFormat('d F Y H:i') . ' - ' . Carbon::parse($ajuan->selesai_kegiatan)->translatedFormat('d F Y H:i');
        return response()->json(['status' => true, 'data' => $ajuan], 200);
    }

    public function revisi(Request $request, string $id)
    {
        $request->validate([
            'nama_kegiatan' => ['required', 'string'],
            'ketua_id' => ['required'],
            'no_surat_ormawa' => ['required'],
            'tanggal_surat' => ['required'],
            'is_dana' => ['required'],
            'mulai_kegiatan' => ['required', 'date'],
            'selesai_kegiatan' => ['required', 'date', 'after:mulai_kegiatan'],
            'tanggal_lpj' => ['required', 'date', 'after_or_equal:selesai_kegiatan', 
                                'before_or_equal:'.date('Y-m-d', strtotime('+14 days', strtotime($request->selesai_kegiatan)))
                            ],
            'tempat' => ['required'],
            'file' => ['file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'after' => ':attribute tidak valid!',
            'after_or_equal' => ':attribute tidak valid!',
            'before_or_equal' => ':attribute tidak valid!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'nama_kegiatan' => 'Nama Kegitan',
            'ketua_id' => 'Ketua',
            'no_surat_ormawa' => 'Nomor Surat Ormawa',
            'tanggal_surat' => 'Tanggal Surat',
            'is_dana' => 'Apakah Mengajukan Dana',
            'mulai_kegiatan' => 'Tanggal Kegiatan (Mulai)',
            'selesai_kegiatan' => 'Tanggal Kegiatan (Selesai)',
            'tanggal_lpj' => 'Tanggal Pernyataan LPJ dan SPJ',
            'tempat' => 'Tempat Kegiatan',
            'file' => 'Proposal dan Lampiran',
        ]);

        try {
            $id = decodeId($id);
            $ajuan = SIK::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '3', '4'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat mengedit data'], 500);
            }
            $message = ($ajuan->status_id == '3') ? 'Direvisi' : 'Diedit';
            $status_id = ($ajuan->status_id == '3') ? '4' : $ajuan->status_id;

            $file = $ajuan->file;
            if ($request->hasFile('file')) {
                $namaOrmawa = str_replace(' ', '', Auth::user()->name);
                $namaKegiatan = str_replace(' ', '', $request->nama_kegiatan);
                $fileName = 'SIK-' . $namaOrmawa . '-' . $namaKegiatan . '-' . time() . '.pdf';

                $file = $fileName;
                $request->file->storeAs('sik/upload/', $fileName, 'public');

                Storage::disk('public')->delete('sik/upload/' . $ajuan->file);
            }

            $ajuan->update([
                'ketua_id' => $request->ketua_id,
                'nama_kegiatan' => $request->nama_kegiatan,
                'no_surat_ormawa' => $request->no_surat_ormawa,
                'tanggal_surat' => $request->tanggal_surat,
                'is_dana' => $request->is_dana,
                'tanggal_lpj' => $request->tanggal_lpj,
                'mulai_kegiatan' => $request->mulai_kegiatan,
                'selesai_kegiatan' => $request->selesai_kegiatan,
                'tempat' => $request->tempat,
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
            $ajuan = SIK::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '3', '4'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat membatalkan ajuan'], 500);
            }
            Storage::disk('public')->delete('sik/upload/' . $ajuan->file);
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
        $name = 'Rekap_Data_Surat_Izin_Kegiatan_Tahun_' . $tahun;
        return Excel::download(new SIKExport($tahun), $name . '.xlsx');
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
            $ajuan = SIK::where('id', $id)->with('ketua.prodis', 'status', 'ormawa.pembina')->first();

            $data_update = [
                'no_surat' => $ajuan->no_surat,
                'status_id' => $request->status_id,
                'catatan' => $request->catatan,
                'surat_hasil' => $ajuan->surat_hasil,
                'tanggal_proses' => new \DateTime(),
            ];

            if (in_array($request->status_id, ['5', '6'])) { // ajuan diproses
                $data_update['no_surat'] = $request->no_surat;
                $return['message'] = 'Ajuan Berhasil Diproses!';
            } elseif (in_array($request->status_id, ['7', '8'])) { //status ditolak
                // Hapus file upload jika ajuan ditolak
                Storage::disk('public')->delete('sik/upload/' . $ajuan->file);
                $return['message'] = 'Ajuan Berhasil Ditolak!';
            } elseif ($request->status_id == '9') { //status selesai
                // Hapus file upload dan surat hasil sementara
                Storage::disk('public')->delete('sik/upload/' . $ajuan->file);
                // Upload surat hasil jika ajuan telah selesai
                $ormawa = $ajuan->ormawa->name;
                $namaKegiatan = str_replace(' ', '', $ajuan->nama_kegiatan);
                $fileName = $ormawa . '-' . $namaKegiatan . '-SIK' . time() . '.pdf';

                $request->file->storeAs('sik/hasil/', $fileName, 'public');

                $data_update['surat_hasil'] = $fileName;
                $return['message'] = 'Ajuan telah selesai!';

                Lpj::create([
                    'sik_id' => $ajuan->id,
                    'status_id' => '1', // status belum upload
                ]);
            }

            $ajuan->update($data_update);
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
        try {
            $id = decodeId($id);
            $ajuan = SIK::where('id', $id)->with('ketua.prodis', 'status', 'ormawa.pembina')->first();
            $wd3 = User::where('role', '3')->first();
            $layanan = session('layanan');
            $templateProcessor = new TemplateProcessor(public_path('/storage/suratHasil/' . $layanan->template_surat_hasil));
            $templateProcessor->setValues([
                'nomor' => $ajuan->no_surat,
                'nama_kegiatan' => $ajuan->nama_kegiatan,
                'ormawa' => $ajuan->ormawa->name,
                'nama_ketua' => $ajuan->ketua->name,
                'nim_ketua' => $ajuan->ketua->nim,
                'no_wa_ketua' => $ajuan->ketua->no_wa,
                'pembina' => $ajuan->ormawa->pembina->name,
                'no_surat_ormawa' => $ajuan->no_surat_ormawa,
                'tanggal_surat_ormawa' => Carbon::parse($ajuan->tanggal_surat)->translatedFormat('j F Y'),
                'is_dana' => $ajuan->is_dana == '1' ? 'dan Surat Pertanggungjawaban (SPJ)' : '',
                'nama_wd3' => $wd3->name,
                'nip_wd3' => $wd3->nim,
                'jabatan_wd3' => $wd3->jabatan,
                'tanggal_lpj' => Carbon::parse($ajuan->tanggal_lpj)->translatedFormat('j F Y'),
                'tanggal_mulai' => Carbon::parse($ajuan->mulai_kegiatan)->translatedFormat('j F Y'),
                'tanggal_selesai' => Carbon::parse($ajuan->selesai_kegiatan)->translatedFormat('j F Y'),
                'jam_mulai' => Carbon::parse($ajuan->mulai_kegiatan)->translatedFormat('H:i'),
                'jam_selesai' => Carbon::parse($ajuan->selesai_kegiatan)->translatedFormat('H:i'),
                'tempat' => $ajuan->tempat,
                'tanggal_surat' => Carbon::today()->translatedFormat('j F Y'),
            ]);
    
            $ormawa = str_replace(' ', '', $ajuan->ormawa->name);
            $namaKegiatan = str_replace(' ', '', $ajuan->nama_kegiatan);
            $docFile = $ormawa . '-' . $namaKegiatan . '-SIK' . time() . '.docx';
            $templateProcessor->saveAs($docFile);
            return response()->download($docFile)->deleteFileAfterSend(true);
        } catch (\Throwable $th) {
            return redirect()->route('sik.index')->with('error', 'Terjadi Kesalahan');
        }
    }
}
