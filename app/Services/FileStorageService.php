<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileStorageService
{
    private $googleDriveService;

    public function __construct()
    {
        // Only initialize Google Drive service if it's configured
        if ($this->isGoogleDriveConfigured()) {
            try {
                $this->googleDriveService = new GoogleDriveService();
            } catch (\Exception $e) {
                Log::warning('Google Drive service initialization failed: ' . $e->getMessage());
                $this->googleDriveService = null;
            }
        }
    }

    private function isGoogleDriveConfigured()
    {
        return !empty(env('GOOGLE_DRIVE_CLIENT_ID')) &&
               !empty(env('GOOGLE_DRIVE_CLIENT_SECRET'));
    }

    public function store($file, $path, $fileName = null, $periodeWisuda = null)
    {
        $fileName = $fileName ?: $file->getClientOriginalName();
        $result = [
            'success' => false,
            'local_path' => null,
            'google_drive_id' => null,
            'storage_method' => 'none',
            'error' => null
        ];

        // This service is for Google Drive storage only
        // No local storage for validation files

        // Try to upload to Google Drive if configured and service is available
        if ($this->googleDriveService && $this->googleDriveService->isConfigured()) {
            try {
                // Get the temporary file path for Google Drive upload
                $tempPath = $file->getRealPath();
                $googleResult = $this->googleDriveService->uploadFile($tempPath, $fileName, null, $periodeWisuda);

                if ($googleResult['success']) {
                    $result['google_drive_id'] = $googleResult['file_id'];
                    $result['storage_method'] = 'google_drive';
                    $result['success'] = true;

                    $logMessage = "File uploaded to Google Drive: {$googleResult['file_id']}";
                    if ($periodeWisuda) {
                        $logMessage .= " (Periode: {$periodeWisuda})";
                    }
                    Log::info($logMessage);
                } else {
                    Log::error("Google Drive upload failed: " . ($googleResult['error'] ?? 'Unknown error'));
                    $result['error'] = $googleResult['error'] ?? 'Google Drive upload failed';
                }
            } catch (\Exception $e) {
                Log::error("Google Drive upload error: " . $e->getMessage());
                $result['error'] = 'Google Drive upload error: ' . $e->getMessage();
            }
        } else {
            Log::error("Google Drive not configured or service unavailable");
            $result['error'] = 'Google Drive not configured';
        }

        return $result;
    }

    public function delete($localPath = null, $googleDriveId = null)
    {
        $deleted = false;

        // Delete local file only if it exists (cleanup from previous versions)
        if ($localPath && Storage::disk('public')->exists($localPath)) {
            try {
                Storage::disk('public')->delete($localPath);
                $deleted = true;
                Log::info("Legacy local file deleted: {$localPath}");
            } catch (\Exception $e) {
                Log::error("Failed to delete local file {$localPath}: " . $e->getMessage());
            }
        }

        // Delete from Google Drive if ID is provided
        if ($googleDriveId && $this->googleDriveService && $this->googleDriveService->isConfigured()) {
            try {
                // Google Drive delete functionality can be implemented here
                Log::info("Google Drive file deletion not implemented yet: {$googleDriveId}");
            } catch (\Exception $e) {
                Log::error("Failed to delete Google Drive file {$googleDriveId}: " . $e->getMessage());
            }
        }

        return $deleted;
    }

    public function isGoogleDriveAvailable()
    {
        return $this->googleDriveService && $this->googleDriveService->isConfigured();
    }
}
