<?php

namespace App\Http\Controllers;

use App\Models\Dapil;
use App\Models\Prodi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DapilController extends Controller
{
    public function index(): JsonResponse
    {
        $dapils = Dapil::with('prodis:id,name')->orderBy('name')->get();

        return response()->json([
            'status' => true,
            'data' => $dapils,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:dapils,name',
            'description' => 'nullable|string',
            'prodi_ids' => 'nullable|array',
            'prodi_ids.*' => 'exists:ref_prodi,id',
        ]);

        try {
            DB::transaction(function () use ($request, &$dapil) {
                $dapil = Dapil::create([
                    'name' => $request->name,
                    'description' => $request->description,
                ]);

                if ($request->filled('prodi_ids')) {
                    $dapil->prodis()->sync($request->prodi_ids);
                }
            });

            $dapil->load('prodis:id,name');

            return response()->json([
                'status' => true,
                'message' => 'Dapil berhasil ditambahkan.',
                'data' => $dapil,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan dapil: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Dapil $dapil): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:dapils,name,' . $dapil->id,
            'description' => 'nullable|string',
            'prodi_ids' => 'nullable|array',
            'prodi_ids.*' => 'exists:ref_prodi,id',
        ]);

        try {
            DB::transaction(function () use ($request, $dapil) {
                $dapil->update([
                    'name' => $request->name,
                    'description' => $request->description,
                ]);

                $dapil->prodis()->sync($request->prodi_ids ?? []);
            });

            $dapil->load('prodis:id,name');

            return response()->json([
                'status' => true,
                'message' => 'Dapil berhasil diperbarui.',
                'data' => $dapil,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui dapil: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Dapil $dapil): JsonResponse
    {
        try {
            // Check if dapil has candidates
            if ($dapil->candidates()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dapil tidak dapat dihapus karena sudah digunakan oleh calon.',
                ], 422);
            }

            $dapil->delete();

            return response()->json([
                'status' => true,
                'message' => 'Dapil berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus dapil: ' . $e->getMessage(),
            ], 500);
        }
    }
}
