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
    private $skippedRows = 0; // Add this property
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
            $this->skippedRows++; // Increment skipped rows
            return null;
        }

        // Check if student already exists in VerifikasiWisuda
        $existing = VerifikasiWisuda::where('user_id', $user->id)->first();

        // Get active graduation period for this year - ALWAYS apply this
        $activePeriode = PeriodeWisuda::getActivePeriode($this->tahun);
        $periodeWisuda = null;

        if ($activePeriode) {
            $periodeWisuda = $this->tahun . '-' . str_pad($activePeriode->bulan, 2, '0', STR_PAD_LEFT);
        }

        // If no existing record, create new one
        if (!$existing) {
            return $this->createNewRecord($row, $user, $periodeWisuda);
        }

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


        if (isset($row['pin']) && $existing->pin != $row['pin']) {
            $updates['pin'] = $row['pin'];
            $hasChanges = true;
        }


        // Handle tanggal_terbit field
        if (isset($row['tanggal_terbit'])) {
            $tanggalTerbit = null;
            if (!empty($row['tanggal_terbit'])) {
                // Try to parse different date formats
                try {
                    if (is_numeric($row['tanggal_terbit'])) {
                        // Excel serial date
                        $tanggalTerbit = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_terbit'])->format('Y-m-d');
                    } else {
                        // String date
                        $tanggalTerbit = \Carbon\Carbon::parse($row['tanggal_terbit'])->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    // If parsing fails, keep the original value
                    $tanggalTerbit = $row['tanggal_terbit'];
                }
            }

            if ($existing->tanggal_terbit != $tanggalTerbit) {
                $updates['tanggal_terbit'] = $tanggalTerbit;
                $hasChanges = true;
            }
        }

        // Update if there are changes and not confirmed yet
    if ($hasChanges) {
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

        return null; // Never create new records, only update existing ones
    }

    private function createNewRecord(array $row, $user, $periodeWisuda)
    {
        // Handle tanggal_terbit field for new record
        $tanggalTerbit = null;
        if (isset($row['tanggal_terbit']) && !empty($row['tanggal_terbit'])) {
            try {
                if (is_numeric($row['tanggal_terbit'])) {
                    // Excel serial date
                    $tanggalTerbit = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_terbit'])->format('Y-m-d');
                } else {
                    // String date
                    $tanggalTerbit = \Carbon\Carbon::parse($row['tanggal_terbit'])->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // If parsing fails, keep the original value
                $tanggalTerbit = $row['tanggal_terbit'];
            }
        }

        // Create new VerifikasiWisuda record
        $newRecord = new VerifikasiWisuda();
        $newRecord->user_id = $user->id;
        $newRecord->status_id = '1'; // Default to "Belum Diproses"
        $newRecord->periode_wisuda = $periodeWisuda;
        $newRecord->no_seri_ijazah = $row['no_seri_ijazah'] ?? null;
        $newRecord->pin = $row['pin'] ?? null;
        $newRecord->tanggal_terbit = $tanggalTerbit;

        $this->newCount++;

        // Count for statistics
        if (!empty($row['no_seri_ijazah'])) {
            $this->importedWithSeriIjazah++;
        } else {
            $this->importedWithoutSeriIjazah++;
        }

        return $newRecord;
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

    public function getSkippedRows()
    {
        return $this->skippedRows;
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
