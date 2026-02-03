<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private $projectId;
    private $accessToken;

    public function __construct()
    {
        $serviceAccountPath = storage_path('firebase/handoova-firebase-adminsdk-fbsvc-4083a36f51.json');
        $this->projectId = json_decode(file_get_contents($serviceAccountPath), true)['project_id'];

        $this->accessToken = $this->getAccessToken($serviceAccountPath);
    }

    private function getAccessToken($serviceAccountPath)
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $serviceAccountPath);
        $token = $credentials->fetchAuthToken();

        return $token['access_token'];
    }

    public function sendAndroidPush($token, $data, $title = "Handova", $body = "Handova B", $priority = 'high')
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        Log::info("send android push", ['token' => $token]);
        try {
            $payload = [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "title" => $title,
                        "body" =>  $body
                    ],
                    "android" => [
                        "priority" => "high"
                    ],
                    "data" => $data // Android data-only
                ]
            ];

            return Http::withToken($this->accessToken)->post($url, $payload)->json();
        } catch (\Throwable $th) {
            //throw $th;
            Log::info("error{$th}", ['result' => $result]);
        }
    }

    public function sendIosPush($token, $data = [], $title, $body, $priority = 'high')
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $apnsPriority = $priority === 'high' ? "10" : "5";

        $payload = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $title,
                    "body" =>  $body
                ],
                "apns" => [
                    "headers" => [
                        "apns-priority" => $apnsPriority
                    ],
                    "payload" => [
                        "aps" => [
                            "alert" => [
                                "title" => $title,
                                "body" => $body,
                            ],
                            "sound" => "default",
                        ]
                    ]
                ],
                "data" => array_merge($data, [
                    "title" => $title,
                    "body" => $body
                ])
            ]
        ];

        return Http::withToken($this->accessToken)->post($url, $payload)->json();
    }
}
