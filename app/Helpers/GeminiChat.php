<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class GeminiChat
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $model;

    protected array $history;

    public function __construct(string $baseUrl, string $apiKey, string $model, array $history)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->history = $history;
    }

    public function chat(string $prompt, ?array $responseSchema = null): array
    {
        $this->history[] = [
            'role' => 'user',
            'parts' => [['text' => $prompt]],
        ];

        $requestPayload = [
            'contents' => $this->history,
            'generationConfig' => [
                'temperature' => 0,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 8192,
                'responseMimeType' => $responseSchema ? 'application/json' : 'text/plain',
            ],
        ];

        if ($responseSchema) {
            $requestPayload['generationConfig']['responseSchema'] = $responseSchema;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", $requestPayload);

        $responseData = $response->json();

        if (isset($responseData['contents'])) {
            $this->history[] = $responseData['contents'][0];
        }

        return $responseData;
    }
}
