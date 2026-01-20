<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GroqService
{
    public static function chat(string $prompt)
    {
        $response = Http::withToken(config('services.groq.key'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama3-8b-8192',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
            ]);

        return data_get($response->json(), 'choices.0.message.content', 'No response');
    }
}

