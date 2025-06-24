<?php

namespace App\Imports;

use App\Models\User;
use App\Models\VerifikasiWisuda;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class VerifWisudaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use SkipsFailures;
    
    private $tahun;
    private $rowCount = 0;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function model(array $row)
    {
        // Clean and normalize the row data
        $row = array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $row);

        // Skip empty rows
        if (empty($row['nim']) || empty($row['no_seri_ijazah'])) {
            return null;
        }

        // Find user by NIM
        $user = User::where('nim', $row['nim'])->first();
        
        if (!$user) {
            \Log::warning("Mahasiswa dengan NIM {$row['nim']} tidak ditemukan");
            return null; // Skip instead of throwing exception
        }

        // Check if verification already exists
        $existing = VerifikasiWisuda::where('user_id', $user->id)->first();
        
        if ($existing) {
            // Update existing record
            $existing->update([
                'no_seri_ijazah' => $row['no_seri_ijazah'] ?? $existing->no_seri_ijazah,
                'periode_wisuda' => $row['periode_wisuda'] ?? $existing->periode_wisuda,
                'kode_akses' => $row['kode_akses'] ?? $existing->kode_akses,
                'status_id' => 2, // Approved status
                'tanggal_proses' => now(),
            ]);
            $this->rowCount++;
            return null;
        } else {
            // Create new record
            $this->rowCount++;
            return new VerifikasiWisuda([
                'user_id' => $user->id,
                'no_seri_ijazah' => $row['no_seri_ijazah'],
                'periode_wisuda' => $row['periode_wisuda'],
                'kode_akses' => $row['kode_akses'] ?? 'AUTO_' . time(),
                'status_id' => 2, // Approved status
                'tanggal_proses' => now(),
                'file' => null, // No file for imported data
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nim' => 'required',
            'no_seri_ijazah' => 'required',
            'periode_wisuda' => 'required|regex:/^\d{4}-\d{2}$/',
            // Make kode_akses optional since it's empty in your CSV
            'kode_akses' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nim.required' => 'NIM wajib diisi',
            'no_seri_ijazah.required' => 'No Seri Ijazah wajib diisi',
            'periode_wisuda.required' => 'Periode Wisuda wajib diisi',
            'periode_wisuda.regex' => 'Periode Wisuda harus dalam format YYYY-MM',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }
}
