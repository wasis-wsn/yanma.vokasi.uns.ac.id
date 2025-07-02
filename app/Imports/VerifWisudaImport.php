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
    private $updatedDetails = [];

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

        // Get active graduation period for this year - ALWAYS apply this
        $activePeriode = PeriodeWisuda::getActivePeriode($this->tahun);
        $periodeWisuda = null;

        if ($activePeriode) {
            $periodeWisuda = $this->tahun . '-' . str_pad($activePeriode->bulan, 2, '0', STR_PAD_LEFT);
        }

        // Check if already exists
        $existing = VerifikasiWisuda::where('user_id', $user->id)->first();

        if ($existing) {
            // Track what fields are being updated
            $updates = [];
            $hasChanges = false;

            // ALWAYS update periode_wisuda to active period if available
            if ($periodeWisuda && $existing->periode_wisuda != $periodeWisuda) {
                $updates['periode_wisuda'] = $periodeWisuda;
                $hasChanges = true;
            }

            // Check each field for changes
            if (isset($row['no_seri_ijazah']) && $existing->no_seri_ijazah != $row['no_seri_ijazah']) {
                $updates['no_seri_ijazah'] = $row['no_seri_ijazah'];
                $hasChanges = true;
            }

            if (isset($row['kode_akses']) && $existing->kode_akses != $row['kode_akses']) {
                $updates['kode_akses'] = $row['kode_akses'];
                $hasChanges = true;
            }

            if (isset($row['jadwal']) && $existing->jadwal != $row['jadwal']) {
                $updates['jadwal'] = $row['jadwal'];
                $hasChanges = true;
            }

            if (isset($row['catatan']) && $existing->catatan != $row['catatan']) {
                $updates['catatan'] = $row['catatan'];
                $hasChanges = true;
            }

            // Update if there are changes and not confirmed yet
            if ($hasChanges && !in_array($existing->status_id, ['4', '5'])) {
                // Update the existing record
                $existing->update($updates);

                // Count for statistics - use updated values
                $finalNoSeriIjazah = $updates['no_seri_ijazah'] ?? $existing->no_seri_ijazah;
                if (!empty($finalNoSeriIjazah)) {
                    $this->importedWithSeriIjazah++;
                } else {
                    $this->importedWithoutSeriIjazah++;
                }

                $this->updatedCount++;

                // Track what was updated for detailed feedback
                $this->updatedDetails[] = [
                    'nim' => $row['nim'],
                    'name' => $user->name,
                    'updates' => array_keys($updates)
                ];
            } else if (!$hasChanges) {
                // No changes, but still count for statistics
                if (!empty($existing->no_seri_ijazah)) {
                    $this->importedWithSeriIjazah++;
                } else {
                    $this->importedWithoutSeriIjazah++;
                }
            }

            return null;
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
            'periode_wisuda' => $periodeWisuda, // Always use active period
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

    public function getUpdatedDetails()
    {
        return $this->updatedDetails;
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
