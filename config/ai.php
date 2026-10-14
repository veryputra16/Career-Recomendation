<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | Supported providers: "openai", "gemini"
    | Can be extended to support Anthropic or compatible local providers.
    |
    */
    'provider' => env('AI_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Global API Key & Model Configuration
    |--------------------------------------------------------------------------
    */
    'api_key' => env('AI_API_KEY', ''),

    'model' => env('AI_MODEL', 'gpt-4o-mini'),

    'timeout' => (int) env('AI_TIMEOUT', 30),

    'max_tokens' => (int) env('AI_MAX_TOKENS', 1500),

    /*
    |--------------------------------------------------------------------------
    | Provider Specific Settings
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'openai' => [
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'model' => env('AI_MODEL', 'gpt-4o-mini'),
        ],
        'gemini' => [
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'model' => env('AI_MODEL', 'gemini-1.5-flash'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompt Versions
    |--------------------------------------------------------------------------
    */
    'prompt_versions' => [
        'skill_extraction' => 'skill-extraction-v1',
    ],

];
