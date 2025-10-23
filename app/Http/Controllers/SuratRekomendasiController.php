<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\SuratRekomendasi;
use App\Models\Tahun;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;
use ZipArchive;

class SuratRekomendasiController extends Controller
{
    public function index(Request $request)
    {
        $layanan = Layanan::where('url_mhs', $request->url())->orWhere('url_staff', $request->url())->first();
        $ajuan = $this->canStore();
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        $templates = Template::where('layanan_id', $layanan->id)->get();
        $status = \App\Models\StatusAlumni::all();

        return view('pages.surat_rekomendasi.index', compact('ajuan', 'layanan', 'tahuns', 'templates', 'status'));
    }

    private function canStore()
    {
        // Check if user already has a pending or approved request
        $existingRequest = auth()->user()->suratRekomendasi()->whereIn('status_id', [1, 3, 4, 5, 6, 9])->exists();
        if ($existingRequest) return false;
        return true;
    }

    public function list(Request $request)
    {
        $query = SuratRekomendasi::with('user.prodis', 'status');

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
                return $row->user && $row->user->prodis ? ($row->user->prodis->name ?? '-') : '-';
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
                $aksi .= ' <button type="button" class="btn btn-success btn-sm btn-generate" data-id="' . encodeId($row->id) . '"><i class="fas fa-file-alt"></i></button>';

                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_lulus', function ($row) {
                return $row->tanggal_lulus ? Carbon::parse($row->tanggal_lulus)->translatedFormat('d F Y') : '';
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_lulus', 'status'])
            ->toJson();
    }

    public function listStaff(Request $request)
    {
        $query = SuratRekomendasi::with('user.prodis', 'status');

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
                return $row->user && $row->user->prodis ? ($row->user->prodis->name ?? '-') : '-';
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
                $aksi .= ' <button type="button" class="btn btn-success btn-sm btn-generate" data-id="' . encodeId($row->id) . '"><i class="fas fa-file-alt"></i></button>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_lulus', function ($row) {
                return $row->tanggal_lulus ? Carbon::parse($row->tanggal_lulus)->translatedFormat('d F Y') : '';
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_lulus', 'status'])
            ->toJson();
    }

    public function listMahasiswa(Request $request)
    {
        $userId = Auth::id();
        $query = SuratRekomendasi::with('user.prodis', 'status')
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
                return $row->user && $row->user->prodis ? ($row->user->prodis->name ?? '-') : '-';
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
                $aksi .= ' <button type="button" class="btn btn-success btn-sm btn-generate" data-id="' . encodeId($row->id) . '"><i class="fas fa-file-alt"></i></button>';
                return $aksi;
            })
            ->editColumn('tanggal_submit', function ($row) {
                return Carbon::parse($row->created_at)->translatedFormat('d F Y') . '<br/>' . Carbon::parse($row->created_at)->translatedFormat('H:i:s') . ' WIB';
            })
            ->editColumn('tanggal_lulus', function ($row) {
                return $row->tanggal_lulus ? Carbon::parse($row->tanggal_lulus)->translatedFormat('d F Y') : '';
            })
            ->rawColumns(['action', 'tanggal_submit', 'tanggal_lulus', 'status'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'permohonan' => ['required', 'string'],
            'nomor_ijazah' => ['required', 'string', 'max:100'],
            'tanggal_lulus' => ['required', 'date'],
            'file_ijazah' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'file_transkrip' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ], [
            'required' => ':attribute wajib diisi!',
            'string' => ':attribute harus berupa teks!',
            'max' => ':attribute maksimal :max karakter!',
            'date' => ':attribute harus berupa tanggal yang valid!',
            'file' => ':attribute tidak valid!',
            'mimes' => ':attribute harus berformat PDF, Word, atau gambar',
        ], [
            'permohonan' => 'Permohonan',
            'nomor_ijazah' => 'Nomor Ijazah',
            'tanggal_lulus' => 'Tanggal Lulus',
            'file_ijazah' => 'File Ijazah',
            'file_transkrip' => 'File Transkrip Nilai',
        ]);

        try {
            $data = $request->only(['permohonan', 'nomor_ijazah', 'tanggal_lulus']);
            $data['user_id'] = Auth::id();

            if ($request->hasFile('file_ijazah')) {
                $file = $request->file('file_ijazah');
                $stored = $file->storeAs('surat_rekomendasi/ijazah', 'ijazah-' . time() . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension(), 'public');
                $data['file_ijazah'] = $stored;
            }

            if ($request->hasFile('file_transkrip')) {
                $file = $request->file('file_transkrip');
                $stored = $file->storeAs('surat_rekomendasi/transkrip', 'transkrip-' . time() . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension(), 'public');
                $data['file_transkrip'] = $stored;
            }

            $surat = SuratRekomendasi::create($data);

            $this->generateLetterDocument($surat);

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
            $data = SuratRekomendasi::with('user.prodis', 'status')->where('id', $id)->first();

            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $data->file_url = $data->file ? Storage::url($data->file) : null;
            $data->surat_hasil_url = $data->surat_hasil ? Storage::url($data->surat_hasil) : null;
            $data->file_ijazah_url = $data->file_ijazah ? Storage::url($data->file_ijazah) : null;
            $data->file_transkrip_url = $data->file_transkrip ? Storage::url($data->file_transkrip) : null;

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
                'no_surat' => [Rule::requiredIf(function () use ($request) {
                    return $request->status_id == '6';
                })],
                'file' => [
                    Rule::requiredIf(function () use ($request) {
                        return $request->status_id == '9';
                    }),
                    'file',
                    'mimes:pdf,doc,docx'
                ],
                'file_ijazah' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
                'file_transkrip' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            ], [
                'required' => ':attribute wajib diisi!',
                'exists' => ':attribute tidak valid!',
                'file' => ':attribute tidak valid!',
                'file.mimes' => 'File Final harus berformat PDF atau Word',
                'file_ijazah.mimes' => 'File Ijazah harus berformat PDF, Word, atau gambar',
                'file_transkrip.mimes' => 'File Transkrip Nilai harus berformat PDF, Word, atau gambar',
            ], [
                'status_id' => 'Status',
                'no_surat' => 'Nomor Surat',
                'file' => 'File Final',
                'file_ijazah' => 'File Ijazah',
                'file_transkrip' => 'File Transkrip Nilai',
            ]);
        } else {
            // Validasi untuk Mahasiswa - edit data pengajuan
            $request->validate([
                'permohonan' => ['nullable', 'string'],
                'nomor_ijazah' => ['nullable', 'string', 'max:100'],
                'tanggal_lulus' => ['nullable', 'date'],
                'file_ijazah' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
                'file_transkrip' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            ], [
                'string' => ':attribute harus berupa teks!',
                'max' => ':attribute maksimal :max karakter!',
                'date' => ':attribute harus berupa tanggal yang valid!',
                'file' => ':attribute tidak valid!',
                'mimes' => ':attribute harus berformat PDF, Word, atau gambar',
            ], [
                'permohonan' => 'Permohonan',
                'nomor_ijazah' => 'Nomor Ijazah',
                'tanggal_lulus' => 'Tanggal Lulus',
                'file_ijazah' => 'File Ijazah',
                'file_transkrip' => 'File Transkrip Nilai',
            ]);
        }

        try {
            $id = decodeId($id);
            $surat = SuratRekomendasi::findOrFail($id);

            $data_update = [];

            // Update berdasarkan role
            if ($isStaff) {
                // Staff: Update status saja
                $data_update['status_id'] = $request->status_id;
                $data_update['tanggal_proses'] = now();
                if ($request->status_id == '6' && $request->filled('no_surat')) {
                    $data_update['no_surat'] = $request->no_surat;
                }
                if ($request->status_id == '9' && $request->hasFile('file')) {
                    $file = $request->file('file');
                    $filename = Str::slug(optional($surat->user)->nim ?? 'sr', '_') . '-final-' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('surat_rekomendasi/uploaded', $filename, 'public');
                    if ($surat->file && Storage::disk('public')->exists($surat->file)) {
                        Storage::disk('public')->delete($surat->file);
                    }
                    $data_update['file'] = $path;
                }
                if ($request->hasFile('file_ijazah')) {
                    $file = $request->file('file_ijazah');
                    $stored = $file->storeAs('surat_rekomendasi/ijazah', 'ijazah-' . time() . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension(), 'public');
                    if ($surat->file_ijazah && Storage::disk('public')->exists($surat->file_ijazah)) {
                        Storage::disk('public')->delete($surat->file_ijazah);
                    }
                    $data_update['file_ijazah'] = $stored;
                }

                if ($request->hasFile('file_transkrip')) {
                    $file = $request->file('file_transkrip');
                    $stored = $file->storeAs('surat_rekomendasi/transkrip', 'transkrip-' . time() . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension(), 'public');
                    if ($surat->file_transkrip && Storage::disk('public')->exists($surat->file_transkrip)) {
                        Storage::disk('public')->delete($surat->file_transkrip);
                    }
                    $data_update['file_transkrip'] = $stored;
                }
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

                if ($request->hasFile('file_ijazah')) {
                    $file = $request->file('file_ijazah');
                    $stored = $file->storeAs('surat_rekomendasi/ijazah', 'ijazah-' . time() . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension(), 'public');
                    if ($surat->file_ijazah && Storage::disk('public')->exists($surat->file_ijazah)) {
                        Storage::disk('public')->delete($surat->file_ijazah);
                    }
                    $data_update['file_ijazah'] = $stored;
                }

                if ($request->hasFile('file_transkrip')) {
                    $file = $request->file('file_transkrip');
                    $stored = $file->storeAs('surat_rekomendasi/transkrip', 'transkrip-' . time() . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension(), 'public');
                    if ($surat->file_transkrip && Storage::disk('public')->exists($surat->file_transkrip)) {
                        Storage::disk('public')->delete($surat->file_transkrip);
                    }
                    $data_update['file_transkrip'] = $stored;
                }
            }

            $surat->update($data_update);

            $surat->refresh();

            if (!($isStaff && $surat->status_id == '9' && $request->hasFile('file'))) {
                $this->generateLetterDocument($surat);
            }

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
            $surat = SuratRekomendasi::findOrFail($id);

            // Delete file if exists
            if ($surat->file && Storage::disk('public')->exists($surat->file)) {
                Storage::disk('public')->delete($surat->file);
            }

            if ($surat->surat_hasil && Storage::disk('public')->exists($surat->surat_hasil)) {
                Storage::disk('public')->delete($surat->surat_hasil);
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
            'no_surat' => Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['5', '6']);
            }),
            'catatan' => Rule::requiredIf(function () use ($request) {
                return in_array($request->status_id, ['3', '7', '8']);
            })
        ], [
            'required' => ':attribute harus diisi!',
            'required_if' => ':attribute harus diisi!'
        ]);

        try {
            $id = decodeId($id);
            $ajuan = SuratRekomendasi::with('user.prodis', 'status')->findOrFail($id);

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
                    $data_update['file'] = null;
                }
                if ($ajuan->surat_hasil) {
                    Storage::disk('public')->delete($ajuan->surat_hasil);
                    $data_update['surat_hasil'] = null;
                }
                $message = 'Ajuan Berhasil Ditolak!';
            } elseif ($request->status_id == '9') { //status selesai
                $message = 'Ajuan telah selesai!';
            }

            $ajuan->update($data_update);

            $ajuan->refresh();

            if (!in_array($request->status_id, ['7', '8'])) {
                $this->generateLetterDocument($ajuan);
            }

            if ($request->status_id == '9') {
                $this->generateLetterDocument($ajuan, 'surat_hasil');
            }

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

    private function generateLetterDocument(SuratRekomendasi $surat, string $targetColumn = 'file'): void
    {
        $templateCandidates = [
            public_path('storage/template/TemplateSuratRekomendasiAlumni.docx'),
            public_path('storage/template/TemplateSuratRekomendasi.docx'),
            public_path('storage/template/TemplateSuratKeteranganAlumni.docx'),
        ];

        $templatePath = null;
        foreach ($templateCandidates as $candidate) {
            if (file_exists($candidate)) {
                $templatePath = $candidate;
                break;
            }
        }

        if (!$templatePath) {
            Log::error('Template surat rekomendasi tidak ditemukan.', ['candidates' => $templateCandidates]);
            return;
        }

        [$tempTemplatePath, $templateProcessor] = $this->prepareTemplateProcessor($templatePath);

        $surat->loadMissing('user.prodis');
        $user = $surat->user;
        $prodi = optional($user)->prodis;

        $createdDate = $surat->created_at ?? Carbon::now();

        $templateProcessor->setValues([
            'nomor_surat' => $surat->no_surat ?: '-',
            'nama_alumni' => optional($user)->name ?: '-',
            'nim_alumni' => optional($user)->nim ?: '-',
            'program_studi' => optional($prodi)->name ?: '-',
            'nomor_ijazah' => $surat->nomor_ijazah ?: '-',
            'tanggal_lulus' => $surat->tanggal_lulus
                ? Carbon::parse($surat->tanggal_lulus)->translatedFormat('d F Y')
                : '-',
            'permohonan' => $surat->permohonan ?: '-',
            'tanggal_terbit' => $surat->tanggal_proses
                ? Carbon::parse($surat->tanggal_proses)->translatedFormat('d F Y')
                : Carbon::parse($createdDate)->translatedFormat('d F Y'),
        ]);

        $tempOutput = storage_path('app/tmp/sr_' . Str::uuid() . '.docx');
        if (!is_dir(dirname($tempOutput))) {
            mkdir(dirname($tempOutput), 0775, true);
        }

        $templateProcessor->saveAs($tempOutput);

        $directory = $targetColumn === 'surat_hasil'
            ? 'surat_rekomendasi/hasil'
            : 'surat_rekomendasi/generated';

        $fileName = Str::slug((optional($user)->nim ?: 'sr') . '-' . (optional($user)->name ?: 'pemohon'), '_')
            . '-SR-' . time() . '.docx';
        $storagePath = $directory . '/' . $fileName;

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        Storage::disk('public')->put($storagePath, file_get_contents($tempOutput));

        if ($targetColumn === 'file' && $surat->file) {
            Storage::disk('public')->delete($surat->file);
        }

        if ($targetColumn === 'surat_hasil' && $surat->surat_hasil) {
            Storage::disk('public')->delete($surat->surat_hasil);
        }

        $surat->{$targetColumn} = $storagePath;
        $surat->save();

        @unlink($tempOutput);
        @unlink($tempTemplatePath);
    }

    private function prepareTemplateProcessor(string $templatePath): array
    {
        $tempPath = storage_path('app/tmp/template_sr_' . Str::uuid() . '.docx');
        if (!is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0775, true);
        }

        copy($templatePath, $tempPath);

        $zip = new ZipArchive();
        if ($zip->open($tempPath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            $xml = $this->injectPlaceholders($xml);
            $zip->addFromString('word/document.xml', $xml);
            $zip->close();
        } else {
            throw new \RuntimeException('Tidak dapat membuka template surat rekomendasi.');
        }

        return [$tempPath, new TemplateProcessor($tempPath)];
    }

    private function injectPlaceholders(string $xml): string
    {
        $doc = new \DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($xml);

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $labelPlaceholders = [
            'Nama' => 'nama_alumni',
            'NIM' => 'nim_alumni',
            'Program Studi' => 'program_studi',
            'Nomor Ijazah' => 'nomor_ijazah',
            'Tanggal Lulus' => 'tanggal_lulus',
        ];

        foreach ($labelPlaceholders as $label => $placeholder) {
            foreach ($xpath->query("//w:p[w:r/w:t='{$label}']") as $paragraph) {
                $hasPlaceholder = false;
                foreach ($xpath->query('.//w:t', $paragraph) as $textNode) {
                    if (str_contains($textNode->textContent, '${')) {
                        $hasPlaceholder = true;
                        break;
                    }
                }

                if ($hasPlaceholder) {
                    continue;
                }

                $paragraphText = '';
                foreach ($xpath->query('.//w:t', $paragraph) as $textNode) {
                    $paragraphText .= $textNode->textContent;
                }

                $colonRun = null;
                foreach ($xpath->query('.//w:r', $paragraph) as $runNode) {
                    $textContent = '';
                    foreach ($xpath->query('.//w:t', $runNode) as $tNode) {
                        $textContent .= $tNode->textContent;
                    }
                    if (str_contains($textContent, ':')) {
                        $colonRun = $runNode;
                        break;
                    }
                }

                $colonPos = strpos($paragraphText, ':');
                if ($colonPos !== false) {
                    $afterColon = trim(substr($paragraphText, $colonPos + 1));
                    if ($afterColon !== '') {
                        continue;
                    }
                }

                $newRun = $doc->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:r');
                $text = $doc->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:t', ' ${' . $placeholder . '}');
                $text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
                $newRun->appendChild($text);

                if ($colonRun && $colonRun->parentNode) {
                    if ($colonRun->nextSibling) {
                        $colonRun->parentNode->insertBefore($newRun, $colonRun->nextSibling);
                    } else {
                        $colonRun->parentNode->appendChild($newRun);
                    }
                } else {
                    $paragraph->appendChild($newRun);
                }
            }
        }

        foreach ($xpath->query("//w:p[w:r/w:t='Nomor:']") as $paragraph) {
            $hasPlaceholder = false;
            foreach ($xpath->query('.//w:t', $paragraph) as $textNode) {
                if (str_contains($textNode->textContent, '${')) {
                    $hasPlaceholder = true;
                    break;
                }
            }

            if (!$hasPlaceholder) {
                $run = $doc->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:r');
                $text = $doc->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:t', ' ${nomor_surat}');
                $text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
                $run->appendChild($text);
                $paragraph->appendChild($run);
            }
        }

        foreach ($xpath->query("//w:t[contains(.,'isi keperluan permohonan')]") as $textNode) {
            $textNode->nodeValue = '${permohonan}';
        }

        foreach ($xpath->query("//w:t[contains(.,'Tanggal Terbit Surat')]") as $textNode) {
            $textNode->nodeValue = '${tanggal_terbit}';
        }

        return $doc->saveXML();
    }

    public function generate(string $encodedId)
    {
        try {
            $id = decodeId($encodedId);
        } catch (\Throwable $th) {
            abort(404);
        }

        $surat = SuratRekomendasi::with('user.prodis')->findOrFail($id);

        if ($surat->status_id == '9' && $surat->file && Storage::disk('public')->exists($surat->file)) {
            return response()->json([
                'status' => true,
                'url' => Storage::url($surat->file),
            ]);
        }

        $this->generateLetterDocument($surat);
        $surat->refresh();

        if ($surat->file && Storage::disk('public')->exists($surat->file)) {
            return response()->json([
                'status' => true,
                'url' => Storage::url($surat->file),
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Dokumen tidak tersedia',
        ], 500);
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
        $name = 'Rekap_Surat_Rekomendasi_Tahun_' . $tahun;

        try {
            $export = new \App\Exports\SuratRekomendasiExport($tahun);

            return Excel::download($export, $name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX, [
                'Content-Disposition' => 'attachment; filename="' . $name . '.xlsx"'
            ]);

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal export data: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }
}
