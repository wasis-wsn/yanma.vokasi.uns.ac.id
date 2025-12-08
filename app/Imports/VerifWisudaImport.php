<?php

namespace App\Imports;

use App\Models\VerifikasiWisuda;
use App\Models\User;
use App\Services\PeriodeWisudaFormatter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
    private $headingRow;

    public function __construct($tahun = null, $headingRow = 1)
    {
        $this->tahun = $tahun;
        $this->headingRow = is_numeric($headingRow) && (int) $headingRow > 0 ? (int) $headingRow : 1;
    }

    public function headingRow(): int
    {
        return $this->headingRow;
    }

    public function model(array $row)
    {
        $this->rowCount++;
        $periodeColumnKey = $this->findPeriodeColumnKey($row);
        $periodeColumnValue = $periodeColumnKey ? ($row[$periodeColumnKey] ?? null) : null;

        Log::debug('VerifWisudaImport: processing row', [
            'row_number' => $this->rowCount,
            'nim' => $row['nim'] ?? null,
            'periode_input' => $periodeColumnValue
        ]);

        // Find user by NIM
        $user = User::where('nim', $row['nim'])->first();
        if (!$user) {
            Log::warning('VerifWisudaImport: user not found, skipping row', [
                'row_number' => $this->rowCount,
                'nim' => $row['nim'] ?? null
            ]);
            $this->skippedRows++; // Increment skipped rows
            return null;
        }

        // Check if student already exists in VerifikasiWisuda
        $existing = VerifikasiWisuda::where('user_id', $user->id)->first();

        // Read periode wisuda directly from the import file (if provided)
        $periodeWisuda = $this->extractPeriodeWisuda($row, $periodeColumnKey);

        // If no existing record, create new one
        if (!$existing) {
            Log::info('VerifWisudaImport: creating new verifikasi wisuda record', [
                'row_number' => $this->rowCount,
                'nim' => $user->nim,
                'periode_wisuda' => $periodeWisuda
            ]);
            return $this->createNewRecord($row, $user, $periodeWisuda);
        }

        // Track what fields are being updated
        $updates = [];
        $hasChanges = false;

        // Update periode_wisuda only when provided by the import file
        if (!is_null($periodeWisuda) && $existing->periode_wisuda != $periodeWisuda) {
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
            Log::info('VerifWisudaImport: updating existing record', [
                'row_number' => $this->rowCount,
                'nim' => $user->nim,
                'updates' => $updates
            ]);

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

        Log::debug('VerifWisudaImport: new record constructed', [
            'nim' => $user->nim,
            'periode_wisuda' => $newRecord->periode_wisuda,
            'no_seri_ijazah' => $newRecord->no_seri_ijazah,
            'pin' => $newRecord->pin
        ]);

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

    private function extractPeriodeWisuda(array $row, ?string $cachedKey = null): ?string
    {
        $key = $cachedKey ?? $this->findPeriodeColumnKey($row);

        if (is_null($key)) {
            return null;
        }

        return $this->normalizePeriodeValue($row[$key]);
    }

    private function normalizePeriodeValue($value): ?string
    {
        return PeriodeWisudaFormatter::normalize($value, true);
    }

    private function findPeriodeColumnKey(array $row): ?string
    {
        foreach ($row as $key => $value) {
            $normalizedKey = Str::of($key)
                ->lower()
                ->replace([' ', '-', '_'], '')
                ->toString();

            if (in_array($normalizedKey, ['periodewisuda', 'periode'])) {
                return $key;
            }
        }

        return null;
    }
}
