<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Cache;

class FcmClient
{
    /** @var string */
    private $projectId;
    /** @var array */
    private $config;
    /** @var HttpClient */
    private $http;

    public function __construct(array $config = null)
    {
        $this->config = $config ?? config('fcm');
        $this->projectId = $this->config['project_id'] ?? null;
        $this->http = new HttpClient(['timeout' => 5.0]);
    }

    public function enabled(): bool
    {
        return (bool)($this->config['enabled'] ?? false);
    }

    public function topicName($raw): string
    {
        $prefix = $this->config['topic_prefix'] ?? 'tag-';
        return $prefix . $raw;
    }

    /**
     * Send a message to an FCM topic
     * @param string $topic the topic id
     * @param array $data key-value string pairs for payload
     */
    public function sendToTopic(string $topic, array $data = []): void
    {
        if (!$this->enabled()) {
            return;
        }

        if (!$this->projectId) {
            return;
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return;
        }

        $endpoint = sprintf('https://fcm.googleapis.com/v1/projects/%s/messages:send', $this->projectId);
        $payload = [
            'message' => [
                'topic' => $topic,
                'data' => array_map('strval', $data),
            ],
        ];

        $this->http->post($endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

    }

    private function getAccessToken(): ?string
    {
        return Cache::remember('fcm_token', 3300, function () {
            $serviceAccount = $this->config['service_account'] ?? '';

            if (!$serviceAccount) {
                return null;
            }
            if (!is_string($serviceAccount) || !file_exists($serviceAccount)) {
                return null;
            }

            $json = file_get_contents($serviceAccount) ?: '';
            $credsArray = json_decode($json, true);

            if (!is_array($credsArray)) {
                return null;
            }

            $credentials = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                $credsArray
            );

            $token = $credentials->fetchAuthToken();
            return $token['access_token'] ?? null;
        });
    }
}
