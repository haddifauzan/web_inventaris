<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GroqService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');
        $this->apiUrl = env('GROQ_API_URL');
    }

    public function generateResponse($messages)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->apiUrl, [
            'model' => 'llama-3.3-70b-versatile', // atau model lain yang tersedia di Groq
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1000
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => $response->status(),
            'message' => $response->body()
        ];
    }
}