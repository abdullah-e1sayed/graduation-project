<?php

namespace App\Services;

use Cloudstudio\Ollama\Facades\Ollama;

class StreamService
{
    public function streamResponse(string $prompt, string $model, float $temperature)
    {
        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = Ollama::agent('You are a snarky friend with one-line responses')
            ->prompt($prompt)
            ->model($model)
            ->options(['temperature' => $temperature])
            ->stream(true)
            ->ask();

        $stream = $response->getBody();

        if (is_resource($stream)) {
            return response()->stream(function () use ($stream) {
                while (!feof($stream)) {
                    echo fgets($stream);
                    ob_flush();
                    flush();
                }
                fclose($stream);
            }, 200, [
                'Content-Type' => 'text/plain',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ]);
        } elseif (is_string($stream)) {
            return response($stream, 200, [
                'Content-Type' => 'text/plain',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ]);
        }

        return response()->json([
            'error' => 'Response body is not a valid stream or string.',
        ], 500);
    }
}
