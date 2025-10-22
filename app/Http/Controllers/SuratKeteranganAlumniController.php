<?php

namespace App\Http\Controllers;

use App\Models\SuratKeteranganAlumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class SuratKeteranganAlumniController extends Controller
{
    public function index()
    {
        // Return the blade view. Data (if needed) can be fetched by the page's JS via the existing API endpoints.
        return view('pages.surat_keterangan_alumni.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string'],
            'nim' => ['required', 'string'],
            'program_studi' => ['required', 'string'],
            'nomor_ijazah' => ['nullable', 'string'],
            'tanggal_lulus' => ['required', 'date'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        try {
            $fileName = null;
            if ($request->hasFile('file')) {
                $fileName = 'SKAL-' . time() . '-' . uniqid() . '.pdf';
                $request->file->storeAs('surat_keterangan_alumni/', $fileName, 'public');
            }

            $record = SuratKeteranganAlumni::create([
                'nama' => $request->nama,
                'nim' => $request->nim,
                'program_studi' => $request->program_studi,
                'nomor_ijazah' => $request->nomor_ijazah,
                'tanggal_lulus' => Carbon::parse($request->tanggal_lulus)->toDateString(),
                'file' => $fileName,
            ]);

            return response()->json(['status' => true, 'data' => $record], 201);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function show($id)
    {
        $record = SuratKeteranganAlumni::findOrFail($id);
        return response()->json(['status' => true, 'data' => $record], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => ['required', 'string'],
            'nim' => ['required', 'string'],
            'program_studi' => ['required', 'string'],
            'nomor_ijazah' => ['nullable', 'string'],
            'tanggal_lulus' => ['required', 'date'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        try {
            $record = SuratKeteranganAlumni::findOrFail($id);

            if ($request->hasFile('file')) {
                $fileName = 'SKAL-' . time() . '-' . uniqid() . '.pdf';
                $request->file->storeAs('surat_keterangan_alumni/', $fileName, 'public');
                if ($record->file) Storage::disk('public')->delete('surat_keterangan_alumni/' . $record->file);
                $record->file = $fileName;
            }

            $record->update([
                'nama' => $request->nama,
                'nim' => $request->nim,
                'program_studi' => $request->program_studi,
                'nomor_ijazah' => $request->nomor_ijazah,
                'tanggal_lulus' => Carbon::parse($request->tanggal_lulus)->toDateString(),
            ]);

            return response()->json(['status' => true, 'data' => $record], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $record = SuratKeteranganAlumni::findOrFail($id);
            if ($record->file) Storage::disk('public')->delete('surat_keterangan_alumni/' . $record->file);
            $record->delete();
            return response()->json(['status' => true, 'message' => 'Data dihapus'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
    }

    public function downloadFile($id)
    {
        $record = SuratKeteranganAlumni::findOrFail($id);
        if (!$record->file) return response()->json(['status' => false, 'message' => 'File tidak ditemukan'], 404);
        return response()->download(storage_path('app/public/surat_keterangan_alumni/' . $record->file));
    }
}
