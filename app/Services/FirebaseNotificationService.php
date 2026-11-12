<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\FcmDeviceToken;
use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected string $projectId;
    protected ?string $serverKey;
    protected ?string $credentialsPath;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id', 'vprotect-3e20f');
        $this->serverKey = config('firebase.server_key');
        $this->credentialsPath = config('firebase.credentials_path');
    }

    /**
     * Send notification to a single FCM device token.
     */
    public function sendToToken(string $token, string $title, string $body, array $data = [], array $options = []): array
    {
        return $this->sendToTokens([$token], $title, $body, $data, $options);
    }

    /**
     * Send notification to multiple FCM device tokens.
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [], array $options = []): array
    {
        $tokens = array_filter(array_unique($tokens));
        if (empty($tokens)) {
            return [
                'success' => false,
                'message' => 'No target tokens provided.',
                'sent_count' => 0,
                'failed_count' => 0,
            ];
        }

        // Try HTTP v1 API first if service account exists
        $serviceAccountFile = $this->getServiceAccountPath();
        if ($serviceAccountFile && file_exists($serviceAccountFile)) {
            return $this->sendViaHttpV1($tokens, $title, $body, $data, $options);
        }

        // Fallback to legacy server key if configured
        if (!empty($this->serverKey)) {
            return $this->sendViaLegacyHttp($tokens, $title, $body, $data, $options);
        }

        // If neither is configured yet, log simulation and save to database
        Log::warning('Firebase push notification skipped: Neither service-account.json nor FIREBASE_SERVER_KEY is configured.', [
            'tokens_count' => count($tokens),
            'title' => $title,
        ]);

        return [
            'success' => false,
            'simulated' => true,
            'message' => 'Firebase credentials pending. Please upload Google Service Account JSON to storage/app/firebase/service-account.json or set FIREBASE_SERVER_KEY in .env.',
            'sent_count' => 0,
            'failed_count' => count($tokens),
        ];
    }

    /**
     * Send push notification to a specific Employee by employee_id.
     */
    public function sendToEmployee(int $employeeId, string $title, string $body, array $data = [], array $options = []): array
    {
        $tokens = FcmDeviceToken::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();

        // Record in database
        $notification = PushNotification::create([
            'title' => $title,
            'body' => $body,
            'image_url' => $options['image_url'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'data_payload' => $data,
            'sender_type' => $options['sender_type'] ?? 'system',
            'sender_id' => $options['sender_id'] ?? null,
            'target_type' => 'employee',
            'target_id' => $employeeId,
            'employee_id' => $employeeId,
            'status' => 'pending',
        ]);

        $res = $this->sendToTokens($tokens, $title, $body, $data, $options);

        $notification->update([
            'status' => ($res['sent_count'] > 0 || empty($tokens)) ? 'sent' : 'failed',
            'delivery_response' => json_encode($res),
            'error_message' => $res['message'] ?? null,
        ]);

        $res['notification_id'] = $notification->id;
        return $res;
    }

    /**
     * Send push notification to a specific Web User (Admin / HR) by user_id.
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = [], array $options = []): array
    {
        $tokens = FcmDeviceToken::where('user_id', $userId)
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();

        $notification = PushNotification::create([
            'title' => $title,
            'body' => $body,
            'image_url' => $options['image_url'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'data_payload' => $data,
            'sender_type' => $options['sender_type'] ?? 'system',
            'sender_id' => $options['sender_id'] ?? null,
            'target_type' => 'user',
            'target_id' => $userId,
            'user_id' => $userId,
            'status' => 'pending',
        ]);

        $res = $this->sendToTokens($tokens, $title, $body, $data, $options);

        $notification->update([
            'status' => ($res['sent_count'] > 0 || empty($tokens)) ? 'sent' : 'failed',
            'delivery_response' => json_encode($res),
            'error_message' => $res['message'] ?? null,
        ]);

        $res['notification_id'] = $notification->id;
        return $res;
    }

    /**
     * Send notification to all team members under a supervisor.
     */
    public function sendToTeam(int $supervisorId, string $title, string $body, array $data = [], array $options = []): array
    {
        $subordinateIds = Employee::where('supervisor_id', $supervisorId)
            ->where('employee_id', '!=', $supervisorId)
            ->pluck('employee_id')
            ->toArray();

        if (empty($subordinateIds)) {
            return [
                'success' => true,
                'message' => 'No team members mapped under this supervisor.',
                'sent_count' => 0,
                'failed_count' => 0,
            ];
        }

        $tokens = FcmDeviceToken::whereIn('employee_id', $subordinateIds)
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();

        foreach ($subordinateIds as $empId) {
            PushNotification::create([
                'title' => $title,
                'body' => $body,
                'image_url' => $options['image_url'] ?? null,
                'action_url' => $options['action_url'] ?? null,
                'data_payload' => $data,
                'sender_type' => 'supervisor',
                'sender_id' => $supervisorId,
                'target_type' => 'team',
                'target_id' => $supervisorId,
                'employee_id' => $empId,
                'status' => 'sent',
            ]);
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $options);
    }

    /**
     * Broadcast notification to all active employees.
     */
    public function broadcastToAllEmployees(string $title, string $body, array $data = [], array $options = []): array
    {
        $tokens = FcmDeviceToken::whereNotNull('employee_id')
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();

        // Create log record
        PushNotification::create([
            'title' => $title,
            'body' => $body,
            'image_url' => $options['image_url'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'data_payload' => $data,
            'sender_type' => $options['sender_type'] ?? 'admin',
            'sender_id' => $options['sender_id'] ?? null,
            'target_type' => 'all_employees',
            'status' => 'sent',
        ]);

        return $this->sendToTokens($tokens, $title, $body, $data, $options);
    }

    /**
     * Broadcast notification to all web users.
     */
    public function broadcastToAllWebUsers(string $title, string $body, array $data = [], array $options = []): array
    {
        $tokens = FcmDeviceToken::whereNotNull('user_id')
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();

        PushNotification::create([
            'title' => $title,
            'body' => $body,
            'image_url' => $options['image_url'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'data_payload' => $data,
            'sender_type' => $options['sender_type'] ?? 'admin',
            'sender_id' => $options['sender_id'] ?? null,
            'target_type' => 'all_web_users',
            'status' => 'sent',
        ]);

        return $this->sendToTokens($tokens, $title, $body, $data, $options);
    }

    /**
     * Send via Google Firebase HTTP v1 API (Modern Standard)
     */
    protected function sendViaHttpV1(array $tokens, string $title, string $body, array $data, array $options): array
    {
        $accessToken = $this->getGoogleAccessToken();
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Unable to generate Google OAuth2 token from service account credentials.',
                'sent_count' => 0,
                'failed_count' => count($tokens),
            ];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $sentCount = 0;
        $failedCount = 0;
        $invalidTokens = [];

        // Stringify all data payload values for Firebase
        $stringData = [];
        foreach ($data as $k => $v) {
            $stringData[(string)$k] = is_scalar($v) ? (string)$v : json_encode($v);
        }

        foreach ($tokens as $token) {
            $message = [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $stringData,
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'sound' => 'default',
                        'click_action' => $options['click_action'] ?? 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                'webpush' => [
                    'headers' => [
                        'Urgency' => 'high',
                    ],
                    'notification' => [
                        'icon' => $options['icon'] ?? asset('frontent/assets/img/favicon.png'),
                        'badge' => asset('frontent/assets/img/favicon.png'),
                    ],
                ],
            ];

            if (!empty($options['image_url'])) {
                $message['notification']['image'] = $options['image_url'];
            }

            if (!empty($options['action_url'])) {
                $message['webpush']['fcm_options']['link'] = $options['action_url'];
            }

            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json; UTF-8',
                ])->timeout(8)->post($url, ['message' => $message]);

                if ($response->successful()) {
                    $sentCount++;
                } else {
                    $failedCount++;
                    $resJson = $response->json();
                    $errorCode = $resJson['error']['details'][0]['errorCode'] ?? ($resJson['error']['status'] ?? '');
                    if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                        $invalidTokens[] = $token;
                    }
                }
            } catch (\Throwable $e) {
                $failedCount++;
                Log::error("Firebase HTTP v1 push error: " . $e->getMessage());
            }
        }

        // Clean up dead/unregistered tokens
        if (!empty($invalidTokens)) {
            $this->deactivateTokens($invalidTokens);
        }

        return [
            'success' => $sentCount > 0,
            'mode' => 'http_v1',
            'message' => "Successfully delivered {$sentCount} notifications ({$failedCount} failed).",
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
        ];
    }

    /**
     * Send via Legacy FCM HTTP API (Fallback)
     */
    protected function sendViaLegacyHttp(array $tokens, string $title, string $body, array $data, array $options): array
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $chunks = array_chunk($tokens, 500);
        $sentCount = 0;
        $failedCount = 0;
        $invalidTokens = [];

        foreach ($chunks as $chunk) {
            $payload = [
                'registration_ids' => $chunk,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'icon' => $options['icon'] ?? asset('frontent/assets/img/favicon.png'),
                ],
                'data' => array_merge($data, [
                    'title' => $title,
                    'body' => $body,
                    'action_url' => $options['action_url'] ?? '',
                ]),
                'priority' => 'high',
            ];

            if (!empty($options['image_url'])) {
                $payload['notification']['image'] = $options['image_url'];
            }

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $this->serverKey,
                    'Content-Type' => 'application/json',
                ])->timeout(10)->post($url, $payload);

                if ($response->successful()) {
                    $json = $response->json();
                    $sentCount += $json['success'] ?? 0;
                    $failedCount += $json['failure'] ?? 0;

                    if (!empty($json['results'])) {
                        foreach ($json['results'] as $idx => $result) {
                            if (!empty($result['error']) && in_array($result['error'], ['NotRegistered', 'InvalidRegistration'])) {
                                if (isset($chunk[$idx])) {
                                    $invalidTokens[] = $chunk[$idx];
                                }
                            }
                        }
                    }
                } else {
                    $failedCount += count($chunk);
                }
            } catch (\Throwable $e) {
                $failedCount += count($chunk);
                Log::error("Firebase Legacy push error: " . $e->getMessage());
            }
        }

        if (!empty($invalidTokens)) {
            $this->deactivateTokens($invalidTokens);
        }

        return [
            'success' => $sentCount > 0,
            'mode' => 'legacy',
            'message' => "Delivered {$sentCount} notifications ({$failedCount} failed).",
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
        ];
    }

    /**
     * Mark invalid tokens inactive so we don't send to them again.
     */
    protected function deactivateTokens(array $tokens): void
    {
        $hashes = array_map(fn($t) => hash('sha256', $t), $tokens);
        FcmDeviceToken::whereIn('token_hash', $hashes)->update(['is_active' => false]);
    }

    /**
     * Generate Google OAuth2 Bearer token from Service Account JSON using JWT RS256.
     */
    protected function getGoogleAccessToken(): ?string
    {
        return Cache::remember('firebase_fcm_access_token', 3300, function () {
            $filePath = $this->getServiceAccountPath();
            if (!$filePath || !file_exists($filePath)) {
                return null;
            }

            $json = json_decode(file_get_contents($filePath), true);
            if (!$json || empty($json['client_email']) || empty($json['private_key'])) {
                return null;
            }

            $now = time();
            $header = ['alg' => 'RS256', 'typ' => 'JWT'];
            $claim = [
                'iss' => $json['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ];

            $base64Header = $this->base64UrlEncode(json_encode($header));
            $base64Claim = $this->base64UrlEncode(json_encode($claim));
            $signatureInput = $base64Header . '.' . $base64Claim;

            $signature = '';
            $pkey = openssl_pkey_get_private($json['private_key']);
            if (!$pkey) {
                return null;
            }

            if (!openssl_sign($signatureInput, $signature, $pkey, OPENSSL_ALGO_SHA256)) {
                return null;
            }

            $jwt = $signatureInput . '.' . $this->base64UrlEncode($signature);

            $tokenResponse = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($tokenResponse->successful()) {
                return $tokenResponse->json()['access_token'] ?? null;
            }

            Log::error('Google OAuth2 token request failed: ' . $tokenResponse->body());
            return null;
        });
    }

    protected function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    protected function getServiceAccountPath(): ?string
    {
        if (!empty($this->credentialsPath) && file_exists($this->credentialsPath)) {
            return $this->credentialsPath;
        }

        $defaultPath = storage_path('app/firebase/service-account.json');
        return file_exists($defaultPath) ? $defaultPath : null;
    }
}
