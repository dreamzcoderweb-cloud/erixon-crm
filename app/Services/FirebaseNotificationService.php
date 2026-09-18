<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected ?string $projectId;
    protected ?string $credentialsPath;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id', 'app1-17024');
        $this->credentialsPath = config('services.firebase.credentials');

        if ($this->credentialsPath && !file_exists($this->credentialsPath)) {
            // Check if relative path was given
            $resolvedPath = base_path($this->credentialsPath);
            if (file_exists($resolvedPath)) {
                $this->credentialsPath = $resolvedPath;
            }
        }
    }

    /**
     * Read service account credentials JSON.
     */
    protected function getCredentials(): ?array
    {
        if (!$this->credentialsPath || !file_exists($this->credentialsPath)) {
            Log::error('Firebase credentials file not found at: ' . ($this->credentialsPath ?? 'null'));
            return null;
        }

        $content = file_get_contents($this->credentialsPath);
        $data = json_decode($content, true);

        if (!is_array($data) || empty($data['private_key']) || empty($data['client_email'])) {
            Log::error('Invalid Firebase credentials file format.');
            return null;
        }

        return $data;
    }

    /**
     * Generate an OAuth2 access token for Firebase Cloud Messaging HTTP v1 API using RS256 JWT.
     */
    public function getAccessToken(): ?string
    {
        return Cache::remember('firebase_fcm_access_token', 3000, function () {
            $credentials = $this->getCredentials();
            if (!$credentials) {
                return null;
            }

            $clientEmail = $credentials['client_email'];
            $privateKey = $credentials['private_key'];
            $now = time();

            // JWT Header
            $header = [
                'alg' => 'RS256',
                'typ' => 'JWT',
            ];

            // JWT Claim set (Payload)
            $payload = [
                'iss' => $clientEmail,
                'sub' => $clientEmail,
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            ];

            $base64UrlHeader = $this->base64UrlEncode(json_encode($header));
            $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
            $dataToSign = $base64UrlHeader . '.' . $base64UrlPayload;

            $signature = '';
            $binaryPrivateKey = openssl_pkey_get_private($privateKey);
            if (!$binaryPrivateKey) {
                Log::error('Unable to parse Firebase private key with OpenSSL.');
                return null;
            }

            $success = openssl_sign($dataToSign, $signature, $binaryPrivateKey, OPENSSL_ALGO_SHA256);
            if (!$success) {
                Log::error('Failed to sign Firebase JWT assertion.');
                return null;
            }

            $base64UrlSignature = $this->base64UrlEncode($signature);
            $jwt = $dataToSign . '.' . $base64UrlSignature;

            // Exchange JWT for OAuth2 Access Token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();
                return $tokenData['access_token'] ?? null;
            }

            Log::error('Failed to retrieve Firebase OAuth access token', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        });
    }

    /**
     * Send a Push Notification to a specific device FCM token using FCM HTTP v1.
     */
    public function sendNotification(string $fcmToken, string $title, string $body, array $data = []): array
    {
        $fcmToken = trim($fcmToken);
        if (empty($fcmToken)) {
            return ['success' => false, 'message' => 'Empty FCM token provided'];
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Could not obtain Firebase access token'];
        }

        $projectId = $this->projectId;
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // FCM v1 requires all data values to be strings
        $stringData = [];
        foreach ($data as $key => $value) {
            $stringData[(string) $key] = is_null($value) ? '' : (string) $value;
        }

        $messagePayload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $stringData,
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'followup_reminders',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $messagePayload);

            if ($response->successful()) {
                Log::info('FCM notification sent successfully', [
                    'token' => substr($fcmToken, 0, 15) . '...',
                    'response' => $response->json(),
                ]);
                return [
                    'success' => true,
                    'message' => 'Notification sent successfully',
                    'response' => $response->json(),
                ];
            }

            Log::warning('FCM notification delivery failed', [
                'status' => $response->status(),
                'token' => substr($fcmToken, 0, 15) . '...',
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'FCM sending failed',
                'status' => $response->status(),
                'error' => $response->json() ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('Exception sending FCM notification: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send today's follow-up reminders notification to a staff member.
     *
     * @param User $user
     * @param iterable $followups
     * @return array
     */
    public function sendFollowupReminderNotification(User $user, $followups): array
    {
        if (empty($user->fcmtoken)) {
            return [
                'success' => false,
                'message' => 'User does not have an FCM token registered.',
            ];
        }

        $count = is_countable($followups) ? count($followups) : 0;
        if ($count === 0) {
            return [
                'success' => false,
                'message' => 'No pending follow-ups for today.',
            ];
        }

        $firstItem = is_array($followups) ? ($followups[0] ?? null) : $followups->first();
        $customerName = $firstItem->lead->customer->name ?? null;
        $leadTitle = $firstItem->lead->lead_title ?? null;

        $title = "Today's Follow-up Reminders 🔔";
        if ($count === 1) {
            $identifier = $customerName ? "for {$customerName}" : ($leadTitle ? "for {$leadTitle}" : '');
            $body = "You have 1 pending follow-up scheduled for today {$identifier}.";
        } else {
            $body = "You have {$count} pending follow-ups scheduled for today. Please review and take action.";
        }

        $data = [
            'type' => 'today_reminders',
            'count' => (string) $count,
            'user_id' => (string) $user->id,
            'date' => now()->toDateString(),
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ];

        return $this->sendNotification($user->fcmtoken, $title, $body, $data);
    }

    /**
     * Base64 URL encode utility for JWT.
     */
    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
