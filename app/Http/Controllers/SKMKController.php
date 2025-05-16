<?php

namespace App\Http\Controllers;

use App\Models\SKMK;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\SKMKExport;
use App\Models\Layanan;
use App\Models\Semester;
use App\Models\StatusKemahasiswaan;
use App\Models\Tahun;
use App\Models\TahunAkademik;
use App\Models\Template;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;

class SKMKController extends Controller
{
    // public $layanan_id = 12;
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusKemahasiswaan::all();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_akademik', 'desc')->limit('6')->get();
        $semester = Semester::all();
        session(['layanan' => $layanan]);
        return view('pages.skmk.index', compact('tahuns', 'layanan', 'status', 'templates', 'tahunAkademik', 'semester'));
    }

    public function listMahasiswa()
    {
        $data = SKMK::where('user_id', Auth::user()->id)->with('user.prodis', 'status')->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
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
                    $aksi = '<a href="' . asset('storage/skmk/hasil/' . $row->surat_hasil) . '" class="btn btn-success btn-sm" target="_blank">
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
            ->editColumn('status_id', function ($row) {
                return '<button type="button" class="btn ' . $row->status->color . ' btn-sm" disabled>' . $row->status->name . '</button>';
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['action', 'created_at', 'tanggal_proses', 'status_id', 'catatan'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $list = SKMK::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

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
                    $aksi .= '<a href="' . route('skmk.generate', encodeId($row->id)) . '" class="btn btn-primary btn-sm btn-block">
                                <i class="fa fa-file"></i> Generate
                            </a>';
                }
                if ($row->status_id == '9') {
                    $aksi = '<a href="' . asset('storage/skmk/hasil/' . $row->surat_hasil) . '" class="btn btn-info btn-sm btn-block" target="_blank">
                            <i class="fa fa-file"></i> Lihat File
                        </a>';
                }
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s');
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
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_proses', 'status_id', 'catatan'])
            ->toJson();
    }

    public function listDekanat(Request $request)
    {
        $list = SKMK::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

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
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_proses', 'status_id', 'catatan'])
            ->toJson();
    }

    public function listAdminProdi(Request $request)
    {
        $list = SKMK::with('user.prodis', 'status')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
        $list = $list->orderBy('created_at', 'desc')->get();

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
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 20, '<br>');
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_proses', 'status_id', 'catatan'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester_romawi' => ['required', 'string'],
            'tahun_akademik_id' => ['required', 'numeric'],
            'semester_id' => ['required', 'numeric'],
            'nama_ortu' => ['required', 'string'],
            'nip_ortu' => ['required', 'string'],
            'pangkat_ortu' => ['required', 'string'],
            'instansi_ortu' => ['required', 'string'],
            'alamat_instansi' => ['required', 'string'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'semester_romawi' => 'Semester',
            'tahun_akademik_id' => 'Tahun Akademik',
            'semester_id' => 'Semester Akademik',
            'nama_ortu' => 'Nama Bapak / Ibu',
            'nip_ortu' => 'NIP / NRP',
            'pangkat_ortu' => 'Pangkat / Golongan',
            'instansi_ortu' => 'Nama Instansi Bekerja',
            'alamat_instansi' => 'Alamat Instansi',
            'file' => 'File',
        ]);

        try {
            $fileName = 'SKMK-' . auth()->user()->nim . '-' . auth()->user()->name . '-' . time() . '.pdf';
            $request->file->storeAs('skmk/upload/', $fileName, 'public');

            SKMK::create([
                'user_id' => Auth::user()->id,
                'status_id' => '1',
                'semester_romawi' => $request->semester_romawi,
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'semester_id' => $request->semester_id,
                'nama_ortu' => $request->nama_ortu,
                'nip_ortu' => $request->nip_ortu,
                'pangkat_ortu' => $request->pangkat_ortu,
                'instansi_ortu' => $request->instansi_ortu,
                'alamat_instansi' => $request->alamat_instansi,
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
        $ajuan = SKMK::where('id', $id)->with('user.prodis', 'status', 'tahunAkademik', 'semester')->first();
        return response()->json(['status' => true, 'data' => $ajuan], 200);
    }

    public function revisi(Request $request, string $id)
    {
        $request->validate([
            'semester_romawi' => ['required', 'string'],
            'tahun_akademik_id' => ['required', 'numeric'],
            'semester_id' => ['required', 'numeric'],
            'nama_ortu' => ['required', 'string'],
            'nip_ortu' => ['required', 'string'],
            'pangkat_ortu' => ['required', 'string'],
            'instansi_ortu' => ['required', 'string'],
            'alamat_instansi' => ['required', 'string'],
            'file' => ['file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'semester_romawi' => 'Semester',
            'tahun_akademik_id' => 'Tahun Akademik',
            'semester_id' => 'Semester Akademik',
            'nama_ortu' => 'Nama Bapak / Ibu',
            'nip_ortu' => 'NIP / NRP',
            'pangkat_ortu' => 'Pangkat / Golongan',
            'instansi_ortu' => 'Nama Instansi Bekerja',
            'alamat_instansi' => 'Alamat Instansi',
            'file' => 'File',
        ]);

        try {
            $id = decodeId($id);
            $ajuan = SKMK::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '3', '4'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat mengedit data'], 500);
            }
            $message = ($ajuan->status_id == '3') ? 'Direvisi' : 'Diedit';
            $status_id = ($ajuan->status_id == '3') ? '4' : $ajuan->status_id;

            $file = $ajuan->file;
            if ($request->hasFile('file')) {
                $fileName = 'SKMK-' . Auth::user()->nim . '-' . Auth::user()->name . '-' . time() . '.' . $request->file('file')->getClientOriginalExtension();

                $file = $fileName;
                $request->file->storeAs('skmk/upload/', $fileName, 'public');

                Storage::disk('public')->delete('skmk/upload/' . $ajuan->file);
            }

            $ajuan->update([
                'semester_romawi' => $request->semester_romawi,
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'semester_id' => $request->semester_id,
                'nama_ortu' => $request->nama_ortu,
                'nip_ortu' => $request->nip_ortu,
                'pangkat_ortu' => $request->pangkat_ortu,
                'instansi_ortu' => $request->instansi_ortu,
                'alamat_instansi' => $request->alamat_instansi,
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
            $ajuan = SKMK::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '3', '4'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat membatalkan ajuan'], 500);
            }
            Storage::disk('public')->delete('skmk/upload/' . $ajuan->file);
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
        $name = 'Rekap_Data_SKMK_Tahun_Akademik_' . $tahun_akademik . '_' . $semester->semester;
        return Excel::download(new SKMKExport($tahun, $semester), $name . '.xlsx');
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
            $ajuan = SKMK::where('id', $id)->with('user.prodis', 'status')->first();
    
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
                $return['message'] = 'Ajuan Berhasil Diproses!';
            } elseif (in_array($request->status_id, ['7', '8'])) { //status ditolak
                // Hapus file upload jika ajuan ditolak
                Storage::disk('public')->delete('skmk/upload/' . $ajuan->file);
                $return['message'] = 'Ajuan Berhasil Ditolak!';
            } elseif ($request->status_id == '9') { //status selesai
                // Hapus file upload dan surat hasil sementara
                Storage::disk('public')->delete('skmk/upload/' . $ajuan->file);
                
                // Upload surat hasil jika ajuan telah selesai
                $fileName = $ajuan->user->nim . '-' . $ajuan->user->name . '-SKMK' . time() . '.pdf';
                $request->file->storeAs('skmk/hasil/', $fileName, 'public');
                
                $data_update['surat_hasil'] = $fileName;
                $return['message'] = 'Ajuan telah selesai!';
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
        $id = decodeId($id);
        $ajuan = SKMK::where('id', $id)->with('user.prodis', 'status', 'semester', 'tahunAkademik')->first();
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
            'semester' => $ajuan->semester_romawi,
            'tahun_akademik' => $ajuan->tahunAkademik->tahun_akademik . ' - ' . $ajuan->semester->semester,
            'nama_ortu' => $ajuan->nama_ortu,
            'nip_ortu' => $ajuan->nip_ortu,
            'instansi_ortu' => $ajuan->instansi_ortu,
            'tanggal_surat' => Carbon::today()->translatedFormat('j F Y'),
        ]);

        $docFile = $ajuan->user->nim . '-' . $ajuan->user->name . '-SKMK' . time() . '.docx';
        $templateProcessor->saveAs($docFile);
        return response()->download($docFile)->deleteFileAfterSend(true);
    }
}
