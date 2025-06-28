<?php

namespace App\Imports;

use App\Models\VerifikasiWisuda;
use App\Models\User;
use App\Models\PeriodeWisuda;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class VerifWisudaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    private $tahun;
    private $rowCount = 0;
    private $importedWithSeriIjazah = 0;
    private $importedWithoutSeriIjazah = 0;
    private $updatedCount = 0;
    private $newCount = 0;
    private $failures = [];

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function model(array $row)
    {
        $this->rowCount++;

        // Find user by NIM
        $user = User::where('nim', $row['nim'])->first();
        if (!$user) {
            return null;
        }

        // Check if already exists
        $existing = VerifikasiWisuda::where('user_id', $user->id)->first();
        if ($existing) {
            // Only update if not confirmed yet
            if (!in_array($existing->status_id, ['4', '5'])) {
                // Count for statistics
                if (!empty($row['no_seri_ijazah'])) {
                    $this->importedWithSeriIjazah++;
                } else {
                    $this->importedWithoutSeriIjazah++;
                }

                $existing->update([
                    'no_seri_ijazah' => $row['no_seri_ijazah'] ?? null,
                    'periode_wisuda' => $row['periode_wisuda'] ?? $existing->periode_wisuda,
                    'kode_akses' => $row['kode_akses'] ?? null,
                    'jadwal' => $row['jadwal'] ?? null,
                    'catatan' => $row['catatan'] ?? null,
                ]);
                $this->updatedCount++;
            }
            return null;
        }

        // Get active graduation period for this year
        $activePeriode = PeriodeWisuda::getActivePeriode($this->tahun);
        $periodeWisuda = null;

        if ($activePeriode) {
            $periodeWisuda = $this->tahun . '-' . str_pad($activePeriode->bulan, 2, '0', STR_PAD_LEFT);
        }

        // Count for statistics
        if (!empty($row['no_seri_ijazah'])) {
            $this->importedWithSeriIjazah++;
        } else {
            $this->importedWithoutSeriIjazah++;
        }

        // Determine status based on whether no_seri_ijazah is provided
        $status_id = '1'; // Default: Belum Diproses

        $this->newCount++;
        return new VerifikasiWisuda([
            'user_id' => $user->id,
            'status_id' => $status_id,
            'no_seri_ijazah' => $row['no_seri_ijazah'] ?? null,
            'periode_wisuda' => $row['periode_wisuda'] ?? $periodeWisuda,
            'kode_akses' => $row['kode_akses'] ?? null,
            'jadwal' => $row['jadwal'] ?? null,
            'catatan' => $row['catatan'] ?? null,
            'tanggal_proses' => null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nim' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nim.required' => 'NIM wajib diisi',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = $failures;
    }

    public function failures()
    {
        return $this->failures;
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getImportedWithSeriIjazah()
    {
        return $this->importedWithSeriIjazah;
    }

    public function getImportedWithoutSeriIjazah()
    {
        return $this->importedWithoutSeriIjazah;
    }

    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }

    public function getNewCount()
    {
        return $this->newCount;
    }


    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
