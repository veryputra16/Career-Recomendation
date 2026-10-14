<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class OpenAIProvider implements AIProviderInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $model = 'gpt-4o-mini',
        protected string $baseUrl = 'https://api.openai.com/v1',
        protected int $timeout = 30
    ) {}

    /**
     * Generate text/JSON via OpenAI Chat Completion API.
     */
    public function generateText(string $prompt, array $options = []): string
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('AI service configuration error: Missing API credentials.');
        }

        $model = $options['model'] ?? $this->model;
        $maxTokens = $options['max_tokens'] ?? config('ai.max_tokens', 1500);
        $jsonMode = $options['json_mode'] ?? true;

        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert career advisory AI that extracts skills from CVs in structured JSON format.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.1,
            'max_tokens' => $maxTokens,
        ];

        if ($jsonMode) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout($this->timeout)
                ->asJson()
                ->acceptJson()
                ->post(rtrim($this->baseUrl, '/') . '/chat/completions', $payload);

            if ($response->failed()) {
                $status = $response->status();
                $errorMessage = $response->json('error.message') ?? $response->body();

                Log::error('OpenAI Provider API error', [
                    'status' => $status,
                    'error' => $errorMessage,
                ]);

                if ($status === 429) {
                    throw new RuntimeException('AI service is currently experiencing high demand or rate limits. Please try again shortly.');
                }

                if ($status >= 500) {
                    throw new RuntimeException('The AI provider encountered an upstream error. Please try again later.');
                }

                throw new RuntimeException("AI provider request failed with status [{$status}]: {$errorMessage}");
            }

            $content = $response->json('choices.0.message.content');

            if (empty($content)) {
                throw new RuntimeException('AI provider returned an empty response.');
            }

            return trim($content);
        } catch (Throwable $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }

            Log::error('OpenAI communication failure', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            throw new RuntimeException('Unable to communicate with the AI extraction service: ' . $e->getMessage(), 0, $e);
        }
    }
}
