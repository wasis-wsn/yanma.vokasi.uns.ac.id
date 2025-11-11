<?php

namespace App\Http\Controllers;

use App\Models\Pemilihan;
use App\Models\PemilihanVote;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PemilihanController extends Controller
{
    public function indexMahasiswa()
    {
        $userId = Auth::id();

        $pemilihans = Pemilihan::with([
            'candidates' => function ($query) {
                $query->withCount('votes as total_votes')
                    ->orderBy('nomor_urut');
            },
            'votes' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
        ])->open()
            ->whereIn('jenis', ['presmben', 'caleg'])
            ->get()
            ->keyBy('jenis');

        $pemilihanPresmben = $pemilihans->get('presmben');
        $pemilihanCaleg = $pemilihans->get('caleg');

        return view('pages.pemilihan.mahasiswa', compact('pemilihanPresmben', 'pemilihanCaleg'));
    }

    public function summary(Pemilihan $pemilihan): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $this->buildPayload($pemilihan),
        ]);
    }

    public function vote(Request $request, Pemilihan $pemilihan): JsonResponse
    {
        $request->validate([
            'candidate_id' => ['required', 'exists:pemilihan_candidates,id'],
        ], [
            'required' => ':attribute wajib dipilih',
            'exists' => ':attribute tidak ditemukan',
        ], [
            'candidate_id' => 'kandidat',
        ]);

        if (!$pemilihan->votingWindowIsOpen()) {
            return response()->json([
                'status' => false,
                'message' => 'Periode pemilihan belum dibuka atau sudah ditutup.',
            ], 422);
        }

        $userId = Auth::id();

        $candidate = $pemilihan->candidates()
            ->where('pemilihan_candidates.id', $request->input('candidate_id'))
            ->firstOrFail();

        try {
            DB::transaction(function () use ($pemilihan, $candidate, $userId) {
                $alreadyVote = PemilihanVote::where('pemilihan_id', $pemilihan->id)
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyVote) {
                    throw new RuntimeException('Anda sudah memberikan suara untuk pemilihan ini.', 409);
                }

                PemilihanVote::create([
                    'pemilihan_id' => $pemilihan->id,
                    'candidate_id' => $candidate->id,
                    'user_id' => $userId,
                    'voted_at' => now(),
                ]);
            });
        } catch (QueryException $exception) {
            if ((int) $exception->getCode() === 23000) {
                return response()->json([
                    'status' => false,
                    'message' => 'Suara Anda sudah tercatat sebelumnya.',
                ], 409);
            }

            throw $exception;
        } catch (\Throwable $th) {
            if ($th instanceof RuntimeException && $th->getCode() === 409) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 409);
            }

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan, silakan coba lagi.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Terima kasih, suara Anda sudah tercatat.',
            'data' => $this->buildPayload($pemilihan->fresh()),
        ]);
    }

    private function buildPayload(Pemilihan $pemilihan): array
    {
        $userId = Auth::id();

        $pemilihan->load([
            'candidates' => function ($query) {
                $query->withCount('votes as total_votes')
                    ->orderBy('nomor_urut');
            },
            'votes' => function ($query) use ($userId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            },
        ]);

        return [
            'pemilihan' => [
                'id' => $pemilihan->id,
                'name' => $pemilihan->name,
                'jenis' => $pemilihan->jenis,
                'deskripsi' => $pemilihan->deskripsi,
                'mulai_at' => optional($pemilihan->mulai_at)->toDateTimeString(),
                'selesai_at' => optional($pemilihan->selesai_at)->toDateTimeString(),
                'is_active' => (bool) $pemilihan->is_active,
                'is_open' => $pemilihan->votingWindowIsOpen(),
            ],
            'candidates' => $pemilihan->candidates->map(function ($candidate) {
                return [
                    'id' => $candidate->id,
                    'nomor_urut' => $candidate->nomor_urut,
                    'name' => $candidate->name,
                    'visi' => $candidate->visi,
                    'misi' => $candidate->misi,
                    'deskripsi' => $candidate->deskripsi,
                    'foto' => $candidate->foto,
                    'total_votes' => (int) ($candidate->total_votes ?? 0),
                ];
            })->values(),
            'user_vote' => optional($pemilihan->votes->first(), function ($vote) {
                return [
                    'candidate_id' => $vote->candidate_id,
                    'voted_at' => optional($vote->voted_at)->toDateTimeString(),
                ];
            }),
        ];
    }
}
