<?php

namespace App\Http\Controllers;

use App\Models\Pemilihan;
use App\Models\PemilihanCandidate;
use App\Models\Prodi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PemilihanManageController extends Controller
{
    public function index()
    {
        $jenisOptions = Pemilihan::jenisOptions();
        $prodis = Prodi::orderBy('name')->get(['id', 'name']);

        return view('pages.pemilihan.index', compact('jenisOptions', 'prodis'));
    }

    public function list(Request $request)
    {
        $pemilihans = Pemilihan::withCount(['candidates', 'votes'])->orderByDesc('created_at');

        return DataTables::of($pemilihans)
            ->addIndexColumn()
            ->addColumn('jenis_label', function ($row) {
                $jenisOptions = Pemilihan::jenisOptions();
                return $jenisOptions[$row->jenis] ?? strtoupper($row->jenis);
            })
            ->addColumn('periode', function ($row) {
                $mulai = $row->mulai_at ? $row->mulai_at->format('d M Y H:i') : '-';
                $selesai = $row->selesai_at ? $row->selesai_at->format('d M Y H:i') : '-';
                return "{$mulai} s/d {$selesai}";
            })
            ->addColumn('status_badge', function ($row) {
                if (!$row->is_active) {
                    return '<span class="badge bg-secondary">Nonaktif</span>';
                }

                if ($row->votingWindowIsOpen()) {
                    return '<span class="badge bg-success">Voting Dibuka</span>';
                }

                return '<span class="badge bg-warning text-dark">Terjadwal</span>';
            })
            ->addColumn('action', function ($row) {
                $toggleLabel = $row->is_active ? 'Nonaktifkan Menu' : 'Aktifkan Menu';
                $buttons = '<div class="btn-group btn-group-sm" role="group">';
                $buttons .= '<button type="button" class="btn btn-info btn-manage-candidate" data-id="' . e($row->slug) . '" data-name="' . e($row->name) . '">Kelola Calon</button>';
                $buttons .= '<button type="button" class="btn btn-warning btn-edit-pemilihan" data-id="' . e($row->slug) . '"><i class="fa fa-pen"></i></button>';
                $buttons .= '<button type="button" class="btn btn-outline-secondary btn-toggle-pemilihan" data-id="' . e($row->slug) . '">' . $toggleLabel . '</button>';
                $buttons .= '<button type="button" class="btn btn-danger btn-delete-pemilihan" data-id="' . e($row->slug) . '"><i class="fa fa-trash"></i></button>';
                $buttons .= '</div>';
                return $buttons;
            })
            ->rawColumns(['status_badge', 'action'])
            ->toJson();
    }

    public function show(Pemilihan $pemilihan): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $pemilihan,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePemilihan($request);

        $data['slug'] = $this->generateUniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        $pemilihan = Pemilihan::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Pemilihan berhasil dibuat.',
            'data' => $pemilihan,
        ]);
    }

    public function update(Request $request, Pemilihan $pemilihan): JsonResponse
    {
        $data = $this->validatePemilihan($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($pemilihan->name !== $data['name']) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $pemilihan->id);
        }

        $pemilihan->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Pemilihan berhasil diperbarui.',
        ]);
    }

    public function destroy(Pemilihan $pemilihan): JsonResponse
    {
        if ($pemilihan->votes()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Pemilihan tidak dapat dihapus karena sudah memiliki suara.',
            ], 422);
        }

        $pemilihan->delete();

        return response()->json([
            'status' => true,
            'message' => 'Pemilihan berhasil dihapus.',
        ]);
    }

    public function toggleStatus(Pemilihan $pemilihan): JsonResponse
    {
        $pemilihan->is_active = !$pemilihan->is_active;
        $pemilihan->save();

        return response()->json([
            'status' => true,
            'message' => 'Status menu pemilu diperbarui.',
            'is_active' => $pemilihan->is_active,
        ]);
    }

    public function candidates(Pemilihan $pemilihan): JsonResponse
    {
        $pemilihan->load(['candidates' => function ($query) {
            $query->withCount('votes as total_votes')
                ->with(['prodi:id,name', 'dapilProdis:id,name'])
                ->orderBy('nomor_urut');
        }]);

        return response()->json([
            'status' => true,
            'pemilihan' => $pemilihan->only(['id', 'name', 'jenis', 'is_active']),
            'candidates' => $pemilihan->candidates->map(function ($candidate) {
                return [
                    'id' => $candidate->id,
                    'pemilihan_id' => $candidate->pemilihan_id,
                    'nomor_urut' => $candidate->nomor_urut,
                    'name' => $candidate->name,
                    'ketua_nama' => $candidate->ketua_nama,
                    'ketua_prodi' => $candidate->ketua_prodi,
                    'ketua_angkatan' => $candidate->ketua_angkatan,
                    'wakil_nama' => $candidate->wakil_nama,
                    'wakil_prodi' => $candidate->wakil_prodi,
                    'wakil_angkatan' => $candidate->wakil_angkatan,
                    'visi' => $candidate->visi,
                    'misi' => $candidate->misi,
                    'deskripsi' => $candidate->deskripsi,
                    'foto' => $candidate->foto,
                    'photo_url' => $candidate->photo_url,
                    'prodi' => $candidate->prodi ? [
                        'id' => $candidate->prodi->id,
                        'name' => $candidate->prodi->name,
                    ] : null,
                    'dapil_names' => $candidate->dapilProdis->pluck('name')->values(),
                    'total_votes' => (int) ($candidate->total_votes ?? 0),
                ];
            })->values(),
        ]);
    }

    public function storeCandidate(Request $request, Pemilihan $pemilihan): JsonResponse
    {
        $data = $this->validateCandidate($request, $pemilihan);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('pemilihan/candidates', 'public');
        }

        $dapilIds = $this->extractDapilProdiIds($request, $pemilihan);

        $candidate = $pemilihan->candidates()->create($data);
        $this->syncCandidateDapils($candidate, $dapilIds);

        return response()->json([
            'status' => true,
            'message' => 'Calon berhasil ditambahkan.',
        ]);
    }

    public function showCandidate(PemilihanCandidate $candidate): JsonResponse
    {
        $candidate->loadMissing(['prodi:id,name', 'dapilProdis:id,name']);

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $candidate->id,
                'pemilihan_id' => $candidate->pemilihan_id,
                'nomor_urut' => $candidate->nomor_urut,
                'name' => $candidate->name,
                'ketua_nama' => $candidate->ketua_nama,
                'ketua_prodi' => $candidate->ketua_prodi,
                'ketua_angkatan' => $candidate->ketua_angkatan,
                'wakil_nama' => $candidate->wakil_nama,
                'wakil_prodi' => $candidate->wakil_prodi,
                'wakil_angkatan' => $candidate->wakil_angkatan,
                'visi' => $candidate->visi,
                'misi' => $candidate->misi,
                'deskripsi' => $candidate->deskripsi,
                'prodi_id' => $candidate->prodi_id,
                'prodi_name' => $candidate->prodi?->name,
                'dapil_prodi_ids' => $candidate->dapilProdis->pluck('id'),
                'dapil_prodi_names' => $candidate->dapilProdis->pluck('name'),
                'foto' => $candidate->foto,
                'photo_url' => $candidate->photo_url,
            ],
        ]);
    }

    public function updateCandidate(Request $request, PemilihanCandidate $candidate): JsonResponse
    {
        $candidate->loadMissing('pemilihan');
        $data = $this->validateCandidate($request, $candidate->pemilihan, $candidate->id);

        if ($request->hasFile('foto')) {
            if ($candidate->foto) {
                Storage::disk('public')->delete($candidate->foto);
            }
            $data['foto'] = $request->file('foto')->store('pemilihan/candidates', 'public');
        } else {
            unset($data['foto']);
        }

        $dapilIds = $this->extractDapilProdiIds($request, $candidate->pemilihan);

        $candidate->update($data);
        $this->syncCandidateDapils($candidate, $dapilIds);

        return response()->json([
            'status' => true,
            'message' => 'Calon berhasil diperbarui.',
        ]);
    }

    public function destroyCandidate(PemilihanCandidate $candidate): JsonResponse
    {
        if ($candidate->votes()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Calon tidak dapat dihapus karena sudah menerima suara.',
            ], 422);
        }

        $candidate->delete();

        return response()->json([
            'status' => true,
            'message' => 'Calon berhasil dihapus.',
        ]);
    }

    private function validatePemilihan(Request $request): array
    {
        $jenisOptions = array_keys(Pemilihan::jenisOptions());

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'jenis' => ['required', Rule::in($jenisOptions)],
            'deskripsi' => ['nullable', 'string'],
            'mulai_at' => ['nullable', 'date'],
            'selesai_at' => ['nullable', 'date', 'after_or_equal:mulai_at'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute tidak valid.',
            'date' => ':attribute tidak valid.',
            'after_or_equal' => ':attribute harus setelah atau sama dengan tanggal mulai.',
            'in' => ':attribute tidak valid.',
        ], [
            'name' => 'Nama Pemilihan',
            'jenis' => 'Jenis Pemilihan',
            'deskripsi' => 'Deskripsi',
            'mulai_at' => 'Tanggal Mulai',
            'selesai_at' => 'Tanggal Selesai',
        ]);
    }

    private function validateCandidate(Request $request, Pemilihan $pemilihan, ?int $candidateId = null): array
    {
        $pemilihanId = $pemilihan->id;
        $dapilRules = [
            $pemilihan->jenis === 'caleg' ? 'required' : 'nullable',
            'array',
        ];

        if ($pemilihan->jenis === 'caleg') {
            $dapilRules[] = 'min:1';
        }

        return $request->validate([
            'nomor_urut' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('pemilihan_candidates')->where(function ($query) use ($pemilihanId) {
                    return $query->where('pemilihan_id', $pemilihanId);
                })->ignore($candidateId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'ketua_nama' => ['required', 'string', 'max:255'],
            'ketua_prodi' => ['required', 'string', 'max:255'],
            'ketua_angkatan' => ['required', 'string', 'max:50'],
            'wakil_nama' => ['required', 'string', 'max:255'],
            'wakil_prodi' => ['required', 'string', 'max:255'],
            'wakil_angkatan' => ['required', 'string', 'max:50'],
            'visi' => ['nullable', 'string'],
            'misi' => ['nullable', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'prodi_id' => ['nullable', 'exists:ref_prodi,id'],
            'dapil_prodi_ids' => $dapilRules,
            'dapil_prodi_ids.*' => ['integer', 'exists:ref_prodi,id'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ], [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute tidak valid.',
            'integer' => ':attribute tidak valid.',
            'min' => ':attribute minimal :min.',
            'unique' => ':attribute sudah digunakan.',
            'exists' => ':attribute tidak valid.',
            'image' => ':attribute harus berupa gambar.',
        ], [
            'nomor_urut' => 'Nomor urut',
            'name' => 'Nama calon',
            'ketua_nama' => 'Nama ketua',
            'ketua_prodi' => 'Prodi ketua',
            'ketua_angkatan' => 'Angkatan ketua',
            'wakil_nama' => 'Nama wakil ketua',
            'wakil_prodi' => 'Prodi wakil ketua',
            'wakil_angkatan' => 'Angkatan wakil ketua',
            'visi' => 'Visi',
            'misi' => 'Misi',
            'deskripsi' => 'Deskripsi',
            'prodi_id' => 'Prodi calon',
            'dapil_prodi_ids' => 'Daftar dapil',
            'foto' => 'Foto',
        ]);
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Pemilihan::where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function extractDapilProdiIds(Request $request, Pemilihan $pemilihan): array
    {
        if ($pemilihan->jenis !== 'caleg') {
            return [];
        }

        return collect($request->input('dapil_prodi_ids', []))
            ->filter(fn ($value) => !is_null($value) && $value !== '')
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();
    }

    private function syncCandidateDapils(PemilihanCandidate $candidate, array $prodiIds): void
    {
        $candidate->dapilProdis()->sync($prodiIds);
    }
}
