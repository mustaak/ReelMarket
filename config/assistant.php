<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shopping Assistant AI Provider
    |--------------------------------------------------------------------------
    */

    'base_url' => env(
        'AI_BASE_URL',
        'https://generativelanguage.googleapis.com/v1beta/openai'
    ),

    'api_key' => env('AI_API_KEY', 'TEST_KEY'),

    'model' => env('AI_MODEL', 'gemini-3.1-flash-lite'),

    'timeout' => (int) env('AI_TIMEOUT', 30),

    'connect_timeout' => (int) env('AI_CONNECT_TIMEOUT', 10),

    'max_tool_rounds' => (int) env('AI_MAX_TOOL_ROUNDS', 3),

    /*
    |--------------------------------------------------------------------------
    | Shopping Assistant Limits
    |--------------------------------------------------------------------------
    */

    'max_message_length' => 1000,

    'max_products' => 5,

    'history_turns' => 12,
];
