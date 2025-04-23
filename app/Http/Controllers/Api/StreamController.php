<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cloudstudio\Ollama\Facades\Ollama;

class StreamController extends Controller
{
    public $sys = "Dont answer the question directly and instead Before answering, analyze the task thoroughly using <thinking>. Outline each step in a structured approach, and verify each step's accuracy and application. Present your answer only after this careful process. Use Markdown formatting (headers, lists, code blocks) where appropriate to ensure clarity.";



    public function AgentTOImproveSearchQuery(Request $request)
    {
        $prompt = <<<EOT
        You are an AI Agent. Your task is to deeply analyze the user's query and refine it into a more specific and relevant search query. Follow these steps:

        ### Steps:
        1. **Analyze the Query**: Break down the query into its components and understand the intent.
        2. **Extract Key Points**: Identify the main elements and intent of the query.
        3. **Refine the Query**: Create a more specific and relevant query without altering the original meaning or adding unnecessary words.
        4. **Output in JSON**: Provide the refined query in the following JSON format:

            {
                "improved_query": "Refined query here"
            }

        ### Guidelines:
        - Do not change any words in the query.
        - Do not add extra words or phrases.
        - Ensure the refined query is concise and directly usable for web searches.
        - Think critically and deeply to ensure the query is optimized for accurate search results.

        ### User's Query:
        {$request->input('prompt')}
        EOT;

        return response()->stream(function () use ($prompt) {
            $response = Ollama::agent($this->sys)
            ->prompt($prompt)
            ->model('X')
            ->stream(true)
            ->ask();

            $body = $response->getBody();

            // Send initial SSE headers
            echo "event: connected\ndata: connected\n\n";
            ob_flush();
            flush();

            // Stream each line as an SSE message
            while (!$body->eof()) {
            $line = trim($body->read(1024));

            // If the chunk is not empty, send it as an SSE message
            if (!empty($line)) {
                $lines = explode("\n", $line);
                foreach ($lines as $json) {
                $data = json_decode($json, true);
                if (!empty($data['response'])) {
                    echo "event: message\ndata: {$data['response']}\n\n";
                    ob_flush();
                    flush();
                }
                }
            }
            }


            // Send event end
            echo "event: end\ndata: done\n\n";
            ob_flush();
            flush();

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // For Nginx buffering
        ]);
    }
}
