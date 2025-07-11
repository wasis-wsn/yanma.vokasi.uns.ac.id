<?php

namespace App\Console\Commands;

use App\Models\VerifikasiWisuda;
use App\Models\TranskripNilai;
use App\Models\SKPI;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateVerifikasiWisudaStatus extends Command
{
    protected $signature = 'wisuda:update-status';
    protected $description = 'Update verifikasi wisuda status from "Belum Diproses" to "Valid" after 6 hours';

    public function handle()
    {
        try {
            $this->info('=== Starting wisuda status update command ===');
            Log::info('Starting wisuda status update command');
            
            $sixHoursAgo = Carbon::now()->subHours(6);
            $this->info("Current time: " . Carbon::now());
            $this->info("Looking for records with status 1 older than: {$sixHoursAgo}");
            
            $records = VerifikasiWisuda::where('status_id', '1')
                ->where('tanggal_terbit', '<=', $sixHoursAgo)
                ->whereNotNull('tanggal_terbit') // Only process records that have uploaded confirmation
                ->get();

            $this->info("Found {$records->count()} records to process");
            Log::info("Found {$records->count()} records to process");
            
            if ($records->isEmpty()) {
                $this->info('No records found to update');
                Log::info('No records found to update');
                return 0;
            }
            
            $updatedCount = 0;

            foreach ($records as $verifikasi) {
                try {
                    $this->info("Processing user ID: {$verifikasi->user_id}, tanggal_terbit: {$verifikasi->tanggal_terbit}");
                    
                    // Update status to 6 (Valid)
                    $verifikasi->update([
                        'status_id' => '6',
                        'tanggal_proses' => now()
                    ]);

                    // Create transkrip and SKPI records if they don't exist
                    if ($verifikasi->periode_wisuda) {
                        $existingTranskrip = TranskripNilai::where('user_id', $verifikasi->user_id)
                            ->where('periode_wisuda', $verifikasi->periode_wisuda)
                            ->first();

                        if (!$existingTranskrip) {
                            TranskripNilai::create([
                                'user_id' => $verifikasi->user_id,
                                'status_id' => '1',
                                'periode_wisuda' => $verifikasi->periode_wisuda,
                            ]);
                            $this->info("Created TranskripNilai for user {$verifikasi->user_id}");
                        }

                        $existingSKPI = SKPI::where('user_id', $verifikasi->user_id)
                            ->where('periode_wisuda', $verifikasi->periode_wisuda)
                            ->first();

                        if (!$existingSKPI) {
                            SKPI::create([
                                'user_id' => $verifikasi->user_id,
                                'status_id' => '1',
                                'periode_wisuda' => $verifikasi->periode_wisuda,
                            ]);
                            $this->info("Created SKPI for user {$verifikasi->user_id}");
                        }
                    }

                    $updatedCount++;
                    $this->info("Updated status for user: {$verifikasi->user->name} ({$verifikasi->user->nim})");
                    Log::info("Updated status for user: {$verifikasi->user->name} ({$verifikasi->user->nim})");
                    
                } catch (\Exception $e) {
                    $this->error("Failed to update status for user ID {$verifikasi->user_id}: {$e->getMessage()}");
                    Log::error("Failed to update status for user ID {$verifikasi->user_id}: {$e->getMessage()}");
                }
            }

            $this->info("=== Command completed. Total updated records: {$updatedCount} ===");
            Log::info("Command completed. Total updated records: {$updatedCount}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Command failed with error: {$e->getMessage()}");
            Log::error("Wisuda update command failed: {$e->getMessage()}");
            return 1;
        }
    }
}