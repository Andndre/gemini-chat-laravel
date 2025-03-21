<?php

namespace App\Helpers;

class GeminiAPI
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $model;

    public function __construct(string $model)
    {
        $this->baseUrl = config('services.gemini.base_url');
        $this->apiKey = config('services.gemini.api_key');
        $this->model = $model;
    }

    public function startChat(array $history = []): GeminiChat
    {
        return new GeminiChat($this->baseUrl, $this->apiKey, $this->model, $history);
    }
}
