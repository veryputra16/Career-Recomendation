<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OpenAIProvider;
use InvalidArgumentException;

class AIService
{
    protected AIProviderInterface $provider;
    protected string $providerName;
    protected string $modelName;

    public function __construct(?AIProviderInterface $provider = null)
    {
        if ($provider) {
            $this->provider = $provider;
            $this->providerName = 'custom';
            $this->modelName = config('ai.model', 'gpt-4o-mini');
            return;
        }

        $this->providerName = (string) config('ai.provider', 'openai');
        $apiKey = (string) config('ai.api_key', '');
        $timeout = (int) config('ai.timeout', 30);

        switch (strtolower($this->providerName)) {
            case 'openai':
                $this->modelName = (string) (config('ai.providers.openai.model') ?: config('ai.model', 'gpt-4o-mini'));
                $baseUrl = (string) (config('ai.providers.openai.base_url') ?: 'https://api.openai.com/v1');
                $this->provider = new OpenAIProvider($apiKey, $this->modelName, $baseUrl, $timeout);
                break;

            case 'gemini':
                $this->modelName = (string) (config('ai.providers.gemini.model') ?: config('ai.model', 'gemini-1.5-flash'));
                $baseUrl = (string) (config('ai.providers.gemini.base_url') ?: 'https://generativelanguage.googleapis.com/v1beta');
                $this->provider = new GeminiProvider($apiKey, $this->modelName, $baseUrl, $timeout);
                break;

            default:
                throw new InvalidArgumentException("Unsupported AI provider configured: [{$this->providerName}]");
        }
    }

    /**
     * Execute a prompt using the configured AI provider.
     */
    public function executePrompt(string $prompt, array $options = []): string
    {
        return $this->provider->generateText($prompt, $options);
    }

    /**
     * Get the active provider name.
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * Get the active model name.
     */
    public function getModelName(): string
    {
        return $this->modelName;
    }
}
