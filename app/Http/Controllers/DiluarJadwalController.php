<?php

namespace App\Http\Controllers;

use App\Models\DiluarJadwal;
use App\Models\Layanan;
use App\Models\Tahun;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\DiluarJadwalExport;
use App\Models\Semester;
use App\Models\StatusHeregistrasi;
use App\Models\TahunAkademik;
use App\Models\Template;
use App\Models\User;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class DiluarJadwalController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = Carbon::now()->toDateTimeString();
        $diluarJadwal = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $status = StatusHeregistrasi::all();
        $templates = Template::where('layanan_id', $diluarJadwal->id)->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_akademik', 'desc')->limit('6')->get();
        $semester = Semester::all();
        session(['layanan' => $diluarJadwal]);
        return view('pages.diluar_jadwal.index', compact('diluarJadwal', 'tanggal', 'tahuns', 'status', 'templates', 'tahunAkademik', 'semester'));
    }

    public function listMahasiswa()
    {
        $data = DiluarJadwal::with('status', 'tahunAkademik', 'semester')->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
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
            ->editColumn('tanggal_bayar', function ($row) {
                return Carbon::parse($row->tanggal_bayar)->translatedFormat('d F Y');
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
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_proses', 'tanggal_bayar', 'status_id', 'tahun_akademik', 'tanggal_ambil', 'catatan'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $list = DiluarJadwal::with('user.prodis', 'status', 'tahunAkademik', 'semester')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
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
            ->editColumn('tanggal_bayar', function ($row) {
                return Carbon::parse($row->tanggal_bayar)->translatedFormat('d F Y');
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
            ->rawColumns(['id', 'action', 'tanggal_submit', 'tanggal_bayar', 'tahun_akademik', 'status_id', 'nama_prodi', 'catatan'])
            ->toJson();
    }

    public function listFo(Request $request)
    {
        $list = DiluarJadwal::with('user.prodis', 'status', 'tahunAkademik', 'semester')->whereYear('created_at', $request->year);
        if ($request->status != 'all') $list = $list->where('status_id', $request->status);
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
        $list = DiluarJadwal::with('user.prodis', 'status', 'tahunAkademik', 'semester')->whereYear('created_at', $request->year);
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
            ->editColumn('tanggal_ambil', function ($row) {
                return ($row->tanggal_ambil) ? Carbon::parse($row->tanggal_ambil)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->tanggal_ambil)->translatedFormat('H:i:s') : '';
            })
            ->editColumn('created_at', function ($row) {
                return ($row->created_at) ? Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') : '';
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
            ->editColumn('nama_prodi', function ($row) {
                return wordwrap($row->user->prodis->name, 20, "<br>");
            })
            ->editColumn('catatan', function ($row) {
                return wordwrap($row->catatan, 30, "<br>");
            })
            ->rawColumns(['action', 'created_at', 'tanggal_ambil', 'tanggal_proses', 'tahun_akademik', 'status_id', 'nama_prodi', 'catatan'])
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

    public function store(Request $request)
    {
        // jika role mahasiswa cek jadwal ajuan layanan
        if (Auth::user()->role == '1') {
            $tanggal = Carbon::now()->toDateTimeString();
            $layanan = session('layanan');
            $diluarJadwal = Layanan::findOrFail($layanan->id);
            if ($tanggal <= $diluarJadwal->open_datetime && $tanggal >= $diluarJadwal->close_datetime) {
                return response()->json(['status' => false, 'message' => 'Saat ini diluar jadwal pengajuan layanan'], 500);
            }
        }

        $rules = [
            'surat_permohonan' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'semester_id' => ['required'],
            'semester_romawi' => ['required'],
            'tahun_akademik_id' => ['required'],
            'alasan' => ['required'],
            'tanggal_bayar' => ['required'],
            // 'bukti_bayar_ukt' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            // 'izin_cuti' => ['file', 'mimes:pdf', 'max:10240'],
        ];

        // Jika role user adalah 2, maka field 'mahasiswa' harus required
        if (Auth::user()->role === '2') {
            $rules['mahasiswa'] = 'required';
        }

        $request->validate($rules, [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'surat_permohonan' => 'Dokumen Persyaratan',
            'semester_romawi' => 'Semester',
            'semester_id' => 'Semester Akademik',
            'tahun_akademik_id' => 'Tahun Akademik',
            'alasan' => 'Alasan',
            'tanggal_bayar' => 'Tanggal akan Bayar',
            // 'bukti_bayar_ukt' => 'Bukti Pembayaran UKT Terakhir',
            // 'izin_cuti' => 'Izin Cuti/Selang',
        ]);

        try {
            $user = $request->mahasiswa ? User::findOrFail($request->mahasiswa) : Auth::user();
            $surat_permohonan = $user->nim . '_' . Str::of($user->name)->trim() . '_' . 'SuratPermohonan' . '_' . time() . '.pdf';
            // $bukti_bayar_ukt = $user->nim . '_' . Str::of($user->name)->trim() . '_' . 'Kuitansi_LuarJadwal' . '_' . time() . '.pdf';
            // $izin_cuti = $user->nim . '_' . Str::of($user->name)->trim() . '_' . 'IzinCuti' . '_' . time() . '.pdf';

            $request->file('surat_permohonan')->storeAs('diluar_jadwal/upload/surat_permohonan/', $surat_permohonan, 'public');
            // $request->file('bukti_bayar_ukt')->storeAs('diluar_jadwal/upload/bukti_bayar_ukt/', $bukti_bayar_ukt, 'public');

            // if ($request->hasFile('izin_cuti')) {
            //     $request->file('izin_cuti')->storeAs('diluar_jadwal/upload/izin_cuti/', $izin_cuti, 'public');
            // } else {
            //     $izin_cuti = null;
            // }

            DiluarJadwal::create([
                'user_id' => $user->id,
                'status_id' => '1',
                'semester_romawi' => $request->semester_romawi,
                'semester_id' => $request->semester_id,
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'alasan' => $request->alasan,
                'tanggal_bayar' => $request->tanggal_bayar,
                'surat_permohonan' => $surat_permohonan,
                // 'bukti_bayar_ukt' => $bukti_bayar_ukt,
                // 'izin_cuti' => $izin_cuti,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
        return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Ditambahkan!'], 200);
    }

    public function show($id)
    {
        $id = decodeId($id);
        $data = DiluarJadwal::where('id', $id)->with('user.prodis', 'status', 'tahunAkademik', 'semester')->first();
        $data['tgl_proses'] = ($data->tanggal_proses) ? Carbon::parse($data->tanggal_proses)->translatedFormat('d F Y H:i:s') . ' WIB' : '';
        $data['tgl_bayar'] = ($data->tanggal_bayar) ? Carbon::parse($data->tanggal_bayar)->translatedFormat('d F Y') : '';
        return response()->json(['status' => true, 'data' => $data], 200);
    }

    public function revisi(Request $request, string $id)
    {
        $request->validate([
            'surat_permohonan' => ['file', 'mimes:pdf', 'max:10240'],
            'semester_romawi' => ['required'],
            'alasan' => ['required'],
            'tanggal_bayar' => ['required'],
            // 'bukti_bayar_ukt' => ['file', 'mimes:pdf', 'max:10240'],
            // 'izin_cuti' => ['file', 'mimes:pdf', 'max:10240'],
        ], [
            'required' => ':attribute wajib diisi!',
            'max' => 'ukuran file :attribute tidak boleh melebihi 10 mb!',
        ], [
            'surat_permohonan' => 'Dokumen Persyaratan',
            'semester_romawi' => 'Semester',
            'alasan' => 'Alasan',
            'tanggal_bayar' => 'Tanggal akan Bayar',
            // 'bukti_bayar_ukt' => 'Bukti Pembayaran UKT Terakhir',
            // 'izin_cuti' => 'Izin Cuti/Selang',
        ]);

        try {
            $id = decodeId($id);
            $ajuan = DiluarJadwal::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '2'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat mengedit data'], 500);
            }
            $message = ($ajuan->status_id == '2') ? 'Direvisi' : 'Diedit';

            $surat_permohonan = $ajuan->surat_permohonan;
            // $bukti_bayar_ukt = $ajuan->bukti_bayar_ukt;
            // $izin_cuti = $ajuan->izin_cuti;
            if ($request->hasFile('surat_permohonan')) {
                $surat_permohonan = Auth::user()->nim . '_' . Str::of(Auth::user()->name)->trim() . '_' . 'SuratPermohonan' . '_' . time() . '.pdf';
                $request->file('surat_permohonan')->storeAs('diluar_jadwal/upload/surat_permohonan/', $surat_permohonan, 'public');
                Storage::disk('public')->delete('diluar_jadwal/upload/surat_permohonan/' . $ajuan->surat_permohonan);
            }
            // if ($request->hasFile('bukti_bayar_ukt')) {
            //     $bukti_bayar_ukt = Auth::user()->nim . '_' . Str::of(Auth::user()->name)->trim() . '_' . 'Kuitansi_LuarJadwal' . '_' . time() . '.pdf';
            //     $request->file('bukti_bayar_ukt')->storeAs('diluar_jadwal/upload/bukti_bayar_ukt/', $bukti_bayar_ukt, 'public');
            //     Storage::disk('public')->delete('diluar_jadwal/upload/bukti_bayar_ukt/' . $ajuan->bukti_bayar_ukt);
            // }
            // if ($request->hasFile('izin_cuti')) {
            //     $izin_cuti = Auth::user()->nim . '_' . Str::of(Auth::user()->name)->trim() . '_' . 'IzinCuti' . '_' . time() . '.pdf';
            //     $request->file('izin_cuti')->storeAs('diluar_jadwal/upload/izin_cuti/', $izin_cuti, 'public');
            //     Storage::disk('public')->delete('diluar_jadwal/upload/izin_cuti/' . $ajuan->izin_cuti);
            // }

            $ajuan->update([
                'status_id' => '1',
                'semester_romawi' => $request->semester_romawi,
                'alasan' => $request->alasan,
                'tanggal_bayar' => $request->tanggal_bayar,
                'surat_permohonan' => $surat_permohonan,
                // 'bukti_bayar_ukt' => $bukti_bayar_ukt,
                // 'izin_cuti' => $izin_cuti,
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
            $ajuan = DiluarJadwal::findOrFail($id);
            if (!in_array($ajuan->status_id, ['1', '2'])) {
                return response()->json(['status' => false, 'message' => 'Tidak dapat menghapus data'], 500);
            }
            Storage::disk('public')->delete('diluar_jadwal/upload/surat_permohonan/' . $ajuan->surat_permohonan);
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
        $name = 'Rekap_Data_Pembayaran_UKT_Diluar_Jadwal_Tahun_Akademik_' . $tahun_akademik[0] . '_' . $tahun_akademik[1] . '_' . $semester->semester;
        return Excel::download(new DiluarJadwalExport($tahun, $semester), $name . '.xlsx');
    }

    public function proses(Request $request, $id)
    {
        $rules = [
            'status_id' => ['required'],
            'no_surat' => Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['3', '4', '6']);
            }),
            'catatan' => Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['2', '5']);
            }),
            'file' => Rule::requiredIf(function () use ($request) {
                return $request->status_id == '6';
            })
        ];

        $request->validate($rules, [
            'required' => ':attribute harus diisi!',
            'file.required' => 'Surat hasil harus diupload!'
        ]);

        try {
            $id = decodeId($id);
            $ajuan = DiluarJadwal::where('id', $id)->with('user.prodis', 'status')->first();

            $data_update = [
                'no_surat' => $ajuan->no_surat,
                'status_id' => $request->status_id,
                'catatan' => $request->catatan,
                'tanggal_proses' => new \DateTime(),
                'tanggal_ambil' => $ajuan->tanggal_ambil,
            ];

            if (in_array($request->status_id, ['3', '4', '6'])) {
                $data_update['no_surat'] = $request->no_surat;
            }

            // Handle file upload for status "Selesai"
            if ($request->status_id == '6' && $request->hasFile('file')) {
                // Delete old file
                Storage::disk('public')->delete('diluar_jadwal/upload/surat_permohonan/' . $ajuan->surat_permohonan);
                
                // Upload new file
                $fileName = 'DiluarJadwal_' . $ajuan->user->nim . '_' . Str::of($ajuan->user->name)->replace(' ', '') . '_hasil_' . time() . '.pdf';
                $request->file('file')->storeAs('diluar_jadwal/upload/surat_permohonan/', $fileName, 'public');
                $data_update['surat_permohonan'] = $fileName;
            }

            if ($request->status_id == '7') {
                $data_update['tanggal_ambil'] = new \DateTime();
                $return['message'] = 'Ajuan telah diambil!';
            }

            $ajuan->update($data_update);
            return response()->json(['status' => true, 'message' => 'Ajuan Berhasil Diproses!'], 200);

        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan!'], 500);
        }
    }

    private function deleteFile($ajuan){
        Storage::disk('public')->delete('diluar_jadwal/upload/surat_permohonan/' . $ajuan->surat_permohonan);
        // Storage::disk('public')->delete('diluar_jadwal/upload/bukti_bayar_ukt/' . $ajuan->bukti_bayar_ukt);
        // Storage::disk('public')->delete('diluar_jadwal/upload/izin_cuti/' . $ajuan->izin_cuti);
    }
}
