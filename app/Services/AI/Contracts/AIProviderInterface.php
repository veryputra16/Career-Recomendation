<?php

namespace App\Services\AI\Contracts;

interface AIProviderInterface
{
    /**
     * Generate text or structured output from the AI provider.
     *
     * @param string $prompt The full prompt string.
     * @param array<string, mixed> $options Provider-specific options (model, max_tokens, json_mode, etc.)
     * @return string The raw response content.
     * @throws \Throwable When the provider request fails.
     */
    public function generateText(string $prompt, array $options = []): string;
}
