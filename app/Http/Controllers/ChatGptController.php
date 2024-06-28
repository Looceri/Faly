<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
use Illuminate\Support\Facades\Date; // Import the Date class

class ChatGptController extends Controller
{

    public function sendMessage(Request $request)
    {
        Log::info('Request received in sendMessage method');
        Log::info('Request data: ', $request->all());

        $message = $request->input('message');
        $apiKey = env('GEMINI_KEY');
        $client = new Client($apiKey);
        $response = $client->geminiPro()->generateContent(
            new TextPart($message)
        );

        // Assuming the correct method to retrieve the generated text is 'content'
        $generatedText = $response->text();

        // Create the message object
        $geminiMessage = [
            'id' => Date::now(), // Use the imported Date class
            'user' => 'Gemini',
            'text' => $generatedText,
        ];

        Log::info('Response generated sucessfully');
        Log::info('Response data: ', $geminiMessage);
        Log::info('');

        // Return the message object (you can use JSON response or modify your frontend logic)
        return response()->json($geminiMessage);
    }
}
