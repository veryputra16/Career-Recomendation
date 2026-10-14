<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class GeminiProvider implements AIProviderInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $model = 'gemini-1.5-flash',
        protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta',
        protected int $timeout = 30
    ) {}

    /**
     * Generate text/JSON via Google Gemini generateContent API.
     */
    public function generateText(string $prompt, array $options = []): string
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('AI service configuration error: Missing Gemini API credentials.');
        }

        $model = $options['model'] ?? $this->model;
        $maxTokens = $options['max_tokens'] ?? config('ai.max_tokens', 1500);
        $jsonMode = $options['json_mode'] ?? true;

        $generationConfig = [
            'temperature' => 0.1,
            'maxOutputTokens' => $maxTokens,
        ];

        if ($jsonMode) {
            $generationConfig['responseMimeType'] = 'application/json';
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => $generationConfig,
        ];

        $url = rtrim($this->baseUrl, '/') . "/models/{$model}:generateContent?key=" . urlencode($this->apiKey);

        try {
            $response = Http::timeout($this->timeout)
                ->asJson()
                ->acceptJson()
                ->post($url, $payload);

            if ($response->failed()) {
                $status = $response->status();
                $errorMessage = $response->json('error.message') ?? $response->body();

                Log::error('Gemini Provider API error', [
                    'status' => $status,
                    'error' => $errorMessage,
                ]);

                if ($status === 429) {
                    throw new RuntimeException('AI service is currently rate limited. Please try again shortly.');
                }

                if ($status >= 500) {
                    throw new RuntimeException('The Gemini service encountered an error. Please try again later.');
                }

                throw new RuntimeException("Gemini provider request failed with status [{$status}]: {$errorMessage}");
            }

            $content = $response->json('candidates.0.content.parts.0.text');

            if (empty($content)) {
                throw new RuntimeException('Gemini provider returned an empty response.');
            }

            return trim($content);
        } catch (Throwable $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }

            Log::error('Gemini communication failure', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            throw new RuntimeException('Unable to communicate with the Gemini AI service: ' . $e->getMessage(), 0, $e);
        }
    }
}
