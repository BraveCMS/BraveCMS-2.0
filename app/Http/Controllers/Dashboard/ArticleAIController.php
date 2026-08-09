<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArticleAIController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        $prompt = $request->input('prompt');
        $apiKey = config('services.openai.key');

        if (!$apiKey) {
            Log::error('OpenAI API key missing!');
            return response()->json(['error' => 'Server misconfiguration: missing API key'], 500);
        }

        $maxRetries = 2;
        $attempt = 0;

        do {
            $attempt++;
            try {
                // Chat completion request
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

                // Retry on rate limit
                if ($response->status() === 429 && $attempt <= $maxRetries) {
                    sleep(1);
                    continue;
                }

                // Handle unsuccessful response
                if (!$response->successful()) {
                    $status = $response->status();
                    $body = $response->body();
                    Log::error("AI API failed (status $status): $body");

                    $json = json_decode($body, true);
                    $msg = $json['error']['message'] ?? 'AI service failed. Check logs';

                    return response()->json(['error' => $msg], $status);
                }

                // Parse the response
                $data = $response->json();
                if (!isset($data['choices'][0]['message']['content']) || empty($data['choices'][0]['message']['content'])) {
                    Log::error('AI returned no usable text: ' . json_encode($data));
                    return response()->json(['error' => 'AI returned no usable text'], 500);
                }

                $text = trim($data['choices'][0]['message']['content']);

                // Return structured response
                return response()->json([
                    'title' => $text,
                    'short_description' => $text,
                    'content' => $text,
                ]);

            } catch (\Exception $e) {
                Log::error('AI generation exception: ' . $e->getMessage());
                if ($attempt <= $maxRetries) {
                    sleep(1);
                    continue;
                }
                return response()->json(['error' => 'AI service failed: ' . $e->getMessage()], 500);
            }
        } while ($attempt <= $maxRetries);
    }
}
