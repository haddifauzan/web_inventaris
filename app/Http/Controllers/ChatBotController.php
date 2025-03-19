<?php

namespace App\Http\Controllers;

use App\Services\GroqService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation' => 'nullable|array'
        ]);

        $conversation = $request->input('conversation', []);
        $conversation[] = ['role' => 'user', 'content' => $request->input('message')];

        $response = $this->groqService->generateResponse($conversation);

        if (isset($response['error'])) {
            return response()->json([
                'success' => false,
                'error' => $response['message']
            ], 500);
        }

        $aiMessage = $response['choices'][0]['message']['content'] ?? 'Maaf, saya tidak dapat memproses pesan Anda saat ini.';
        $conversation[] = ['role' => 'assistant', 'content' => $aiMessage];

        return response()->json([
            'success' => true,
            'message' => $aiMessage,
            'conversation' => $conversation
        ]);
    }
}