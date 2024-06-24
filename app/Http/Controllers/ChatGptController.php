<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGptController extends Controller
{

    public function sendMessage(Request $request)
    {
        
        Log::info('Request received in sendMessage method');
        Log::info('Request data: ', $request->all());

        $message = $request->input('message');
        $apiKey = env('CHAT_GPT_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/engines/text-davinci-003/completions', [
            'prompt' => $message,
            'max_tokens' => 150,
            'temperature' => 0.7,
        ]);

        return $response->json();
    }
}
