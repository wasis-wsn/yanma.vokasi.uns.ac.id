<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\SuratKeteranganAlumni;
use App\Models\Tahun;
use App\Models\Template;
use App\Models\User;
use App\Models\StatusAlumni;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SuratKeteranganAlumniController extends Controller
{
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $ajuan = $this->canStore();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        $status = StatusAlumni::all();

        return view('pages.surat_keterangan_alumni.index', compact('ajuan', 'layanan', 'tahuns', 'templates', 'status'));
    }

    private function canStore()
    {
        // Check if user already has a pending or approved request
        $existingRequest = auth()->user()->suratKeteranganAlumni()->whereIn('status_id', [1, 3, 4, 5, 6, 9])->exists();
        if ($existingRequest) return false;
        return true;
    }

    public function list(Request $request)
    {
        $query = SuratKeteranganAlumni::with('user.prodis', 'status');

        // Only filter by year if provided
        if ($request->has('year') && $request->year) {
            $query->whereYear('created_at', $request->year);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                return $row->user ? $row->user->name : '-';
            })
            ->addColumn('nim', function ($row) {
                return $row->user ? $row->user->nim : '-';
            })
            ->addColumn('program_studi', function ($row) {
                return $row->user && $row->user->prodis ? $row->user->prodis->nama : '-';
            })
            ->editColumn('nomor_ijazah', function ($row) {
                return $row->nomor_ijazah ?? '-';
            })
            ->addColumn('status', function ($row) {
                if ($row->status) {
                    return '<span class="btn btn-sm ' . $row->status->color . '">' . $row->status->name . '</span>';
                }
                return '<span class="btn btn-sm btn-secondary">Belum Ada Status</span>';
            })
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-primary btn-sm btn-detail" data-id="' . encodeId($row->id) . '"><i class="fas fa-eye"></i></button>';
                                $aksi .= ' <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="' . encodeId($row->id) . '"><i class="fas fa-edit"></i></button>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_lulus', function ($row) {
                return $row->tanggal_lulus ? Carbon::parse($row->tanggal_lulus)->translatedFormat('d F Y') : '';
            })
            ->editColumn('file', function ($row) {
                if ($row->file) {
                    return '<a href="' . Storage::url($row->file) . '" target="_blank" class="btn btn-info btn-sm"><i class="fas fa-download"></i> Download</a>';
                }
                return '-';
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_lulus', 'file', 'status'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $query = SuratKeteranganAlumni::with('user.prodis', 'status');

        // Only filter by year if provided
        if ($request->has('year') && $request->year) {
            $query->whereYear('created_at', $request->year);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                return $row->user ? $row->user->name : '-';
            })
            ->addColumn('nim', function ($row) {
                return $row->user ? $row->user->nim : '-';
            })
            ->addColumn('program_studi', function ($row) {
                return $row->user && $row->user->prodis ? $row->user->prodis->nama : '-';
            })
            ->editColumn('nomor_ijazah', function ($row) {
                return $row->nomor_ijazah ?? '-';
            })
            ->addColumn('status', function ($row) {
                if ($row->status) {
                    return '<span class="btn btn-sm ' . $row->status->color . '">' . $row->status->name . '</span>';
                }
                return '<span class="btn btn-sm btn-secondary">Belum Ada Status</span>';
            })
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-primary btn-sm btn-detail" data-id="' . encodeId($row->id) . '"><i class="fas fa-eye"></i></button>';
                $aksi .= ' <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="' . encodeId($row->id) . '"><i class="fas fa-edit"></i></button>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_lulus', function ($row) {
                return $row->tanggal_lulus ? Carbon::parse($row->tanggal_lulus)->translatedFormat('d F Y') : '';
            })
            ->editColumn('file', function ($row) {
                if ($row->file) {
                    return '<a href="' . Storage::url($row->file) . '" target="_blank" class="btn btn-info btn-sm"><i class="fas fa-download"></i> Download</a>';
                }
                return '-';
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_lulus', 'file', 'status'])
            ->toJson();
    }

    public function listMahasiswa(Request $request)
    {
        $userId = Auth::id();
        $query = SuratKeteranganAlumni::with('user.prodis', 'status')
            ->whereHas('user', function ($query) use ($userId) {
                $query->where('id', $userId);
            });

        // Only filter by year if provided
        if ($request->has('year') && $request->year) {
            $query->whereYear('created_at', $request->year);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                return $row->user ? $row->user->name : '-';
            })
            ->addColumn('nim', function ($row) {
                return $row->user ? $row->user->nim : '-';
            })
            ->addColumn('program_studi', function ($row) {
                return $row->user && $row->user->prodis ? $row->user->prodis->nama : '-';
            })
            ->editColumn('nomor_ijazah', function ($row) {
                return $row->nomor_ijazah ?? '-';
            })
            ->addColumn('status', function ($row) {
                if ($row->status) {
                    return '<span class="btn btn-sm ' . $row->status->color . '">' . $row->status->name . '</span>';
                }
                return '<span class="btn btn-sm btn-secondary">Belum Ada Status</span>';
            })
            ->addColumn('action', function ($row) {
                $aksi = '<button type="button" class="btn btn-primary btn-sm btn-detail" data-id="' . encodeId($row->id) . '"><i class="fas fa-eye"></i></button>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_lulus', function ($row) {
                return $row->tanggal_lulus ? Carbon::parse($row->tanggal_lulus)->translatedFormat('d F Y') : '';
            })
            ->editColumn('file', function ($row) {
                if ($row->file) {
                    return '<a href="' . Storage::url($row->file) . '" target="_blank" class="btn btn-info btn-sm"><i class="fas fa-download"></i> Download</a>';
                }
                return '-';
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_lulus', 'file', 'status'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'permohonan' => ['required', 'string'],
            'nomor_ijazah' => ['required', 'string', 'max:100'],
            'tanggal_lulus' => ['required', 'date'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
        ], [
            'required' => ':attribute wajib diisi!',
            'string' => ':attribute harus berupa teks!',
            'max' => ':attribute maksimal :max karakter!',
            'date' => ':attribute harus berupa tanggal yang valid!',
            'mimes' => ':attribute harus berformat PDF!',
            'file.max' => 'Ukuran :attribute maksimal 2MB!',
        ], [
            'permohonan' => 'Permohonan',
            'nomor_ijazah' => 'Nomor Ijazah',
            'tanggal_lulus' => 'Tanggal Lulus',
            'file' => 'File',
        ]);

        try {
            $data = $request->only(['permohonan', 'nomor_ijazah', 'tanggal_lulus']);
            $data['user_id'] = Auth::id();

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('surat_keterangan_alumni', $filename, 'public');
                $data['file'] = $path;
            }

            SuratKeteranganAlumni::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan!'
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Store error: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data!'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $id = decodeId($id);
            $data = SuratKeteranganAlumni::with('user.prodis', 'status')->where('id', $id)->first();

            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Show error: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Validasi berbeda untuk staff dan mahasiswa
        $roleGate = optional(auth()->user()->roles)->gate_name;
        $isStaff = in_array($roleGate, ['staff', 'dekanat', 'subkoor', 'adminprodi', 'fo']);

        if ($isStaff) {
            // Validasi untuk Staff - hanya edit status
            $request->validate([
                'status_id' => ['required', 'exists:status_alumni,id'],
            ], [
                'required' => ':attribute wajib diisi!',
                'exists' => ':attribute tidak valid!',
            ], [
                'status_id' => 'Status',
            ]);
        } else {
            // Validasi untuk Mahasiswa - edit data pengajuan
            $request->validate([
                'permohonan' => ['nullable', 'string'],
                'nomor_ijazah' => ['nullable', 'string', 'max:100'],
                'tanggal_lulus' => ['nullable', 'date'],
                'file' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
            ], [
                'string' => ':attribute harus berupa teks!',
                'max' => ':attribute maksimal :max karakter!',
                'date' => ':attribute harus berupa tanggal yang valid!',
                'mimes' => ':attribute harus berformat PDF!',
                'file.max' => 'Ukuran :attribute maksimal 2MB!',
            ], [
                'permohonan' => 'Permohonan',
                'nomor_ijazah' => 'Nomor Ijazah',
                'tanggal_lulus' => 'Tanggal Lulus',
                'file' => 'File',
            ]);
        }

        try {
            $id = decodeId($id);
            $surat = SuratKeteranganAlumni::findOrFail($id);

            $surat = SuratKeteranganAlumni::findOrFail($id);

            $data_update = [];

            // Update berdasarkan role
            if ($isStaff) {
                // Staff: Update status saja
                // Log incoming request for debugging
                Log::debug('SuratKeteranganAlumni update request (staff)', [
                    'id' => $id,
                    'decoded_id' => $id,
                    'request_all' => $request->all(),
                ]);

                $data_update['status_id'] = $request->status_id;
                $data_update['tanggal_proses'] = now();

                // Log data prepared for update
                Log::debug('SuratKeteranganAlumni prepared update data (staff)', [
                    'data_update' => $data_update,
                ]);
            } else {
                // Mahasiswa: Update data pengajuan (hanya yang diisi)
                if ($request->filled('permohonan')) {
                    $data_update['permohonan'] = $request->permohonan;
                }
                if ($request->filled('nomor_ijazah')) {
                    $data_update['nomor_ijazah'] = $request->nomor_ijazah;
                }
                if ($request->filled('tanggal_lulus')) {
                    $data_update['tanggal_lulus'] = $request->tanggal_lulus;
                }

                // Handle file upload if provided
                if ($request->hasFile('file')) {
                    // Delete old file if exists
                    if ($surat->file && Storage::disk('public')->exists($surat->file)) {
                        Storage::disk('public')->delete($surat->file);
                    }

                    $file = $request->file('file');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('surat_keterangan_alumni', $filename, 'public');
                    $data_update['file'] = $path;
                }
            }

            $surat->update($data_update);

            // Log status after update
            $surat->refresh();
            Log::debug('SuratKeteranganAlumni after update', [
                'id' => $surat->id,
                'status_id' => $surat->status_id,
                'tanggal_proses' => $surat->tanggal_proses,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diperbarui!'
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Update error: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data!'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $id = decodeId($id);
            $surat = SuratKeteranganAlumni::findOrFail($id);

            // Delete file if exists
            if ($surat->file && Storage::disk('public')->exists($surat->file)) {
                Storage::disk('public')->delete($surat->file);
            }

            $surat->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus!'
            ], 200);
        } catch (\Throwable $th) {
            Log::error('Delete error: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus data!'
            ], 500);
        }
    }

    public function proses(Request $request, $id)
    {
        $request->validate([
            'status_id' => ['required'],
            'no_surat' => \Illuminate\Validation\Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['5', '6']);
            }),
            'file' => ['required_if:status_id,9'],
            'catatan' => \Illuminate\Validation\Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['3', '7', '8']);
            })
        ], [
            'required' => ':attribute harus diisi!',
            'required_if' => ':attribute harus diisi!'
        ]);

        try {
            $id = decodeId($id);
            $ajuan = SuratKeteranganAlumni::with('user.prodis', 'status')->findOrFail($id);

            $data_update = [
                'no_surat' => $ajuan->no_surat,
                'status_id' => $request->status_id,
                'catatan' => $request->catatan,
                'surat_hasil' => $ajuan->surat_hasil,
                'tanggal_proses' => new \DateTime(),
            ];

            $message = '';

            // ajuan diproses
            if (in_array($request->status_id, ['5', '6'])) {
                $data_update['no_surat'] = $request->no_surat;
                $message = 'Ajuan Berhasil Diproses!';
            } elseif (in_array($request->status_id, ['7', '8'])) { //status ditolak
                // Hapus file upload jika ajuan ditolak
                if ($ajuan->file) {
                    Storage::disk('public')->delete($ajuan->file);
                }
                $message = 'Ajuan Berhasil Ditolak!';
            } elseif ($request->status_id == '9') { //status selesai
                // Hapus file upload
                if ($ajuan->file) {
                    Storage::disk('public')->delete($ajuan->file);
                }
                // Upload surat hasil jika ajuan telah selesai
                $fileName = $ajuan->user->nim . '-' . $ajuan->user->name . '-SKA-' . time() . '.pdf';
                $request->file->storeAs('surat_keterangan_alumni/hasil/', $fileName, 'public');
                $data_update['surat_hasil'] = $fileName;
                $message = 'Ajuan telah selesai!';
            }

            $ajuan->update($data_update);

            return response()->json([
                'status' => true,
                'message' => $message,
                'status_id' => $request->status_id
            ], 200);

        } catch (\Throwable $th) {
            Log::error('Proses error: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
