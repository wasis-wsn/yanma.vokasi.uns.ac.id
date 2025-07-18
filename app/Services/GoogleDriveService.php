<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class GoogleDriveService
{
    private $client;
    private $service;

    public function __construct()
    {
        $this->initializeClient();
    }

    private function initializeClient()
    {
        $this->client = new Client();
        $this->client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('APP_URL') . '/google/callback');
        $this->client->addScope(Drive::DRIVE_FILE);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        // Get stored access token
        $accessToken = $this->getStoredAccessToken();
        if ($accessToken) {
            $this->client->setAccessToken($accessToken);

            // Check if token is expired and refresh if needed
            if ($this->client->isAccessTokenExpired()) {
                $this->refreshAccessToken();
            }
        }

        $this->service = new Drive($this->client);
    }

    private function getStoredAccessToken()
    {
        try {
            // Try to get from cache first
            $token = Cache::get('google_drive_access_token');
            if ($token) {
                return $token;
            }

            // Try to get from file storage
            if (Storage::disk('local')->exists('google_tokens.json')) {
                $tokens = json_decode(Storage::disk('local')->get('google_tokens.json'), true);
                if (isset($tokens['access_token'])) {
                    Cache::put('google_drive_access_token', $tokens['access_token'], now()->addMinutes(50));
                    return $tokens['access_token'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error reading stored access token: ' . $e->getMessage());
        }

        return null;
    }

    private function getStoredRefreshToken()
    {
        try {
            if (Storage::disk('local')->exists('google_tokens.json')) {
                $tokens = json_decode(Storage::disk('local')->get('google_tokens.json'), true);
                return $tokens['refresh_token'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Error reading stored refresh token: ' . $e->getMessage());
        }

        return env('GOOGLE_DRIVE_REFRESH_TOKEN');
    }

    private function storeTokens($accessToken)
    {
        try {
            $tokens = [
                'access_token' => $accessToken,
                'created_at' => now()->toISOString(),
                'expires_at' => now()->addHour()->toISOString()
            ];

            // Keep existing refresh token if available
            $existingRefreshToken = $this->getStoredRefreshToken();
            if ($existingRefreshToken) {
                $tokens['refresh_token'] = $existingRefreshToken;
            }

            // If new refresh token is provided, use it
            if (isset($accessToken['refresh_token'])) {
                $tokens['refresh_token'] = $accessToken['refresh_token'];
            }

            Storage::disk('local')->put('google_tokens.json', json_encode($tokens));
            Cache::put('google_drive_access_token', $accessToken, now()->addMinutes(50));

            Log::info('Tokens stored successfully');
        } catch (\Exception $e) {
            Log::error('Error storing tokens: ' . $e->getMessage());
        }
    }

    private function refreshAccessToken()
    {
        try {
            $refreshToken = $this->getStoredRefreshToken();
            if (!$refreshToken) {
                throw new \Exception('No refresh token available');
            }

            $this->client->refreshToken($refreshToken);
            $accessToken = $this->client->getAccessToken();

            if (!$accessToken) {
                throw new \Exception('Failed to get access token after refresh');
            }

            // Store the new access token
            $this->storeTokens($accessToken);

            Log::info('Access token refreshed successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to refresh access token: ' . $e->getMessage());
            return false;
        }
    }

    public function storeRefreshToken($refreshToken)
    {
        try {
            $tokens = [];

            // Get existing tokens if available
            if (Storage::disk('local')->exists('google_tokens.json')) {
                $tokens = json_decode(Storage::disk('local')->get('google_tokens.json'), true) ?: [];
            }

            // Update with new refresh token
            $tokens['refresh_token'] = $refreshToken;
            $tokens['updated_at'] = now()->toISOString();

            // Store back to file
            Storage::disk('local')->put('google_tokens.json', json_encode($tokens));

            Log::info('Refresh token stored successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('Error storing refresh token: ' . $e->getMessage());
            return false;
        }
    }

    public function createFolder($folderName, $parentFolderId = null)
    {
        try {
            // Check if folder already exists
            $existingFolder = $this->findFolder($folderName, $parentFolderId);
            if ($existingFolder) {
                return [
                    'success' => true,
                    'folder_id' => $existingFolder->getId(),
                    'name' => $existingFolder->getName()
                ];
            }

            $fileMetadata = new \Google\Service\Drive\DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder'
            ]);

            if ($parentFolderId) {
                $fileMetadata->setParents([$parentFolderId]);
            } elseif (env('GOOGLE_DRIVE_FOLDER_ID')) {
                $fileMetadata->setParents([env('GOOGLE_DRIVE_FOLDER_ID')]);
            }

            $folder = $this->service->files->create($fileMetadata, [
                'fields' => 'id,name'
            ]);

            Log::info('Folder created in Google Drive: ' . $folder->getName() . ' (ID: ' . $folder->getId() . ')');

            return [
                'success' => true,
                'folder_id' => $folder->getId(),
                'name' => $folder->getName()
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create folder in Google Drive: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function findFolder($folderName, $parentFolderId = null)
    {
        try {
            $query = "name='{$folderName}' and mimeType='application/vnd.google-apps.folder' and trashed=false";
            
            if ($parentFolderId) {
                $query .= " and '{$parentFolderId}' in parents";
            } elseif (env('GOOGLE_DRIVE_FOLDER_ID')) {
                $query .= " and '" . env('GOOGLE_DRIVE_FOLDER_ID') . "' in parents";
            }

            $response = $this->service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id,name)'
            ]);

            $files = $response->getFiles();
            return !empty($files) ? $files[0] : null;

        } catch (\Exception $e) {
            Log::error('Failed to find folder in Google Drive: ' . $e->getMessage());
            return null;
        }
    }

    public function getOrCreatePeriodeWisudaFolder($periodeWisuda)
    {
        try {
            if (!$periodeWisuda) {
                return null;
            }

            // Format periode wisuda for folder name (e.g., "2024-03" -> "Periode Wisuda Maret 2024")
            $folderName = $this->formatPeriodeWisudaFolderName($periodeWisuda);
            
            // First create/get "Verifikasi Wisuda" folder
            $verifikasiFolder = $this->createFolder('Verifikasi Wisuda');
            if (!$verifikasiFolder['success']) {
                return null;
            }

            // Then create/get periode wisuda folder inside it
            $periodeFolder = $this->createFolder($folderName, $verifikasiFolder['folder_id']);
            if (!$periodeFolder['success']) {
                return null;
            }

            return $periodeFolder['folder_id'];

        } catch (\Exception $e) {
            Log::error('Failed to get/create periode wisuda folder: ' . $e->getMessage());
            return null;
        }
    }

    private function formatPeriodeWisudaFolderName($periodeWisuda)
    {
        try {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $periodeWisuda);
            $monthName = $date->translatedFormat('F');
            $year = $date->year;
            
            return "Periode Wisuda {$monthName} {$year}";
        } catch (\Exception $e) {
            // Fallback to original format if parsing fails
            return "Periode Wisuda {$periodeWisuda}";
        }
    }

    public function uploadFile($filePath, $fileName, $folderId = null, $periodeWisuda = null)
    {
        try {
            // Check if we have a valid token
            if (!$this->client->getAccessToken()) {
                throw new \Exception('No access token available');
            }

            // If periode wisuda is provided, get/create the appropriate folder
            if ($periodeWisuda && !$folderId) {
                $folderId = $this->getOrCreatePeriodeWisudaFolder($periodeWisuda);
            }

            $fileMetadata = new \Google\Service\Drive\DriveFile([
                'name' => $fileName
            ]);

            if ($folderId) {
                $fileMetadata->setParents([$folderId]);
            } elseif (env('GOOGLE_DRIVE_FOLDER_ID')) {
                $fileMetadata->setParents([env('GOOGLE_DRIVE_FOLDER_ID')]);
            }

            $content = file_get_contents($filePath);
            $file = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => mime_content_type($filePath),
                'uploadType' => 'multipart',
                'fields' => 'id,name,webViewLink'
            ]);

            $logMessage = 'File uploaded to Google Drive successfully: ' . $file->getId();
            if ($periodeWisuda) {
                $logMessage .= ' (Periode: ' . $periodeWisuda . ')';
            }
            Log::info($logMessage);

            return [
                'success' => true,
                'file_id' => $file->getId(),
                'name' => $file->getName(),
                'link' => $file->getWebViewLink()
            ];

        } catch (\Exception $e) {
            Log::error('Google Drive upload failed: ' . $e->getMessage());

            // Try to refresh token and retry once
            if (strpos($e->getMessage(), 'invalid_grant') !== false ||
                strpos($e->getMessage(), 'unauthorized') !== false ||
                strpos($e->getMessage(), 'invalid_credentials') !== false) {

                Log::info('Attempting to refresh token and retry upload');
                if ($this->refreshAccessToken()) {
                    try {
                        return $this->uploadFile($filePath, $fileName, $folderId, $periodeWisuda);
                    } catch (\Exception $retryException) {
                        Log::error('Retry upload also failed: ' . $retryException->getMessage());
                    }
                }
            }

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function isConfigured()
    {
        $hasCredentials = !empty(env('GOOGLE_DRIVE_CLIENT_ID')) &&
                         !empty(env('GOOGLE_DRIVE_CLIENT_SECRET'));

        $hasToken = !empty($this->getStoredAccessToken()) ||
                   !empty($this->getStoredRefreshToken());

        return $hasCredentials && $hasToken;
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback($code)
    {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($accessToken['error'])) {
                Log::error('Google callback error: ' . $accessToken['error']);
                return false;
            }

            $this->storeTokens($accessToken);
            return true;
        } catch (\Exception $e) {
            Log::error('Google callback error: ' . $e->getMessage());
            return false;
        }
    }
}
