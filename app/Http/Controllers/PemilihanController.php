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
        $user = Auth::user();
        $userId = $user?->id;
        $userProdiId = $user?->prodi;

        $pemilihans = Pemilihan::with([
            'candidates' => function ($query) {
                $query->withCount('votes as total_votes')
                    ->with('dapilProdis:id,name')
                    ->orderBy('nomor_urut');
            },
            'votes' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
        ])->open()
            ->whereIn('jenis', ['presbem', 'caleg'])
            ->get()
            ->keyBy('jenis');

        $pemilihanPresbem = $pemilihans->get('presbem');
        $pemilihanCaleg = $pemilihans->get('caleg');

        $eligibility = [
            'presbem' => $this->userEligibleForPemilihan($pemilihanPresbem, $userProdiId),
            'caleg' => $this->userEligibleForPemilihan($pemilihanCaleg, $userProdiId),
        ];

        return view('pages.pemilihan.mahasiswa', compact('pemilihanPresbem', 'pemilihanCaleg', 'eligibility'));
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

        $user = Auth::user();
        $userId = $user?->id;
        $userProdiId = $user?->prodi;

        if ($pemilihan->jenis === 'caleg' && !$this->userEligibleForPemilihan($pemilihan, $userProdiId)) {
            return response()->json([
                'status' => false,
                'message' => 'Prodi kamu bukan bagian dari dapil pemilihan legislatif ini.',
            ], 403);
        }

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
        $user = Auth::user();
        $userId = $user?->id;
        $userProdiId = $user?->prodi;

        $pemilihan->load([
            'candidates' => function ($query) {
                $query->withCount('votes as total_votes')
                    ->with('dapilProdis:id,name')
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
                'eligible' => $this->userEligibleForPemilihan($pemilihan, $userProdiId),
            ],
            'candidates' => $pemilihan->candidates->map(function ($candidate) {
                return [
                    'id' => $candidate->id,
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

    private function userEligibleForPemilihan(?Pemilihan $pemilihan, ?int $userProdiId): bool
    {
        if (!$pemilihan) {
            return false;
        }

        if ($pemilihan->jenis !== 'caleg') {
            return true;
        }

        if (!$userProdiId) {
            return false;
        }

        if (!$pemilihan->relationLoaded('candidates')) {
            $pemilihan->load(['candidates' => function ($query) {
                $query->with('dapilProdis:id,name');
            }]);
        } else {
            $pemilihan->candidates->loadMissing('dapilProdis:id,name');
        }

        return $pemilihan->candidates->contains(function ($candidate) use ($userProdiId) {
            return $candidate->dapilProdis->contains('id', $userProdiId);
        });
    }
}
