<?php

namespace App\Http\Controllers;

use App\Models\Pemilihan;
use App\Models\PemilihanCandidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PemilihanManageController extends Controller
{
    public function index()
    {
        $jenisOptions = Pemilihan::jenisOptions();
        return view('pages.pemilihan.index', compact('jenisOptions'));
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
            $query->withCount('votes as total_votes')->orderBy('nomor_urut');
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
                    'visi' => $candidate->visi,
                    'misi' => $candidate->misi,
                    'deskripsi' => $candidate->deskripsi,
                    'foto' => $candidate->foto,
                    'total_votes' => (int) ($candidate->total_votes ?? 0),
                ];
            })->values(),
        ]);
    }

    public function storeCandidate(Request $request, Pemilihan $pemilihan): JsonResponse
    {
        $data = $this->validateCandidate($request, $pemilihan->id);
        $pemilihan->candidates()->create($data);

        return response()->json([
            'status' => true,
            'message' => 'Calon berhasil ditambahkan.',
        ]);
    }

    public function showCandidate(PemilihanCandidate $candidate): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $candidate,
        ]);
    }

    public function updateCandidate(Request $request, PemilihanCandidate $candidate): JsonResponse
    {
        $data = $this->validateCandidate($request, $candidate->pemilihan_id, $candidate->id);
        $candidate->update($data);

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

    private function validateCandidate(Request $request, int $pemilihanId, ?int $candidateId = null): array
    {
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
            'visi' => ['nullable', 'string'],
            'misi' => ['nullable', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'string', 'max:255'],
        ], [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute tidak valid.',
            'integer' => ':attribute tidak valid.',
            'min' => ':attribute minimal :min.',
            'unique' => ':attribute sudah digunakan.',
        ], [
            'nomor_urut' => 'Nomor urut',
            'name' => 'Nama calon',
            'visi' => 'Visi',
            'misi' => 'Misi',
            'deskripsi' => 'Deskripsi',
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
}
