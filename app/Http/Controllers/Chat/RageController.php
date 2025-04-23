<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;

class RageController extends Controller
{
    protected $fileName;
    protected $filePath;
    protected $userId;
    protected $chunkSize;

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('file');
        $file->storeAs('files', $file->getClientOriginalName());

        $this->fileName = $file->getClientOriginalName();
        $this->filePath = 'files/' . $file->getClientOriginalName();
        $this->userId = Auth::id();
        $this->chunkSize = 50;

        // Call processPdf after uploading the file
        return $this->processPdf($request);
    }

    public function processPdf(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('file');
        $file->storeAs('files', $file->getClientOriginalName());

        $this->fileName = $file->getClientOriginalName();
        $this->filePath = 'files/' . $file->getClientOriginalName();
        $this->userId = 6; // Replace with Auth::id() if authentication is enabled
        $this->chunkSize = 50;

        if (!Storage::exists($this->filePath)) {
            throw new \Exception("File not found: " . $this->filePath);
        }

        try {
            Log::info("Processing file: {$this->filePath} for user: {$this->userId}");
            $parser = new Parser();
            $pdf = $parser->parseFile(Storage::path($this->filePath));
            $pages = $pdf->getPages();
            $numPages = count($pages);
            $allChunks = [];

            for ($startPage = 0; $startPage < $numPages; $startPage += $this->chunkSize) {
                $endPage = min($startPage + $this->chunkSize, $numPages);
                Log::info("Processing pages " . ($startPage + 1) . " to " . $endPage);

                $chunks = [];
                foreach (range($startPage, $endPage - 2) as $pageNum) {
                    $chunks[] = $this->processPage($pages[$pageNum]->getText(), $pageNum + 1); // Pass page number
                }

                $allChunks = array_merge($allChunks, ...$chunks);
            }

            $embeddingInput = [];
            foreach ($allChunks as $i => $chunk) {
                $embeddingInput["Chunk " . ($i + 1)] = "[{$this->fileName}] {$chunk}";
            }

            $this->processAndStoreEmbeddings($this->userId, $embeddingInput);
            return response()->json(['message' => "Great Job! Training Completed for {$this->fileName}."]);
        } catch (\Exception $e) {
            Log::error("Error processing file: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    private function callPythonFunction($endpoint, $payload = [])
    {
        $flaskBaseUrl = 'http://127.0.0.1:5000'; // Flask API base URL
        $url = $flaskBaseUrl . '/' . $endpoint;

        try {
            $response = Http::post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error("Flask API error: " . $response->body());
                throw new \Exception("Flask API error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Error communicating with Flask API: " . $e->getMessage());
            throw $e;
        }
    }

    private function processAndStoreEmbeddings($userId, $embeddingInput)
    {
        $payload = [
            'user_id' => $userId,
            'embedding_input' => $embeddingInput,
        ];

        return $this->callPythonFunction('process_and_store_embeddings', $payload);
    }

    private function processPage($pageText, $pageNum)
    {
        $payload = [
            'file_path' => 'F:/learn_html/learn_laravel/HUNTER-GPT/storage/app/'.$this->filePath, // Ensure file_path is included
            'page_text' => $pageText,      // Correctly pass page_text
            'page_num' => $pageNum,        // Include page_num in the payload
        ];

        return $this->callPythonFunction('process_page', $payload);
    }

    public function rageContent($request){
        $numberDataRetrav = $request->number_data_retrav;
        $apiKey = $request->api_key;
        $query = $request->query;
        $user = Profile::where('api_key', $apiKey)->first();

        if($user){
           $userId = $user->id;
           

        } else {
            return response()->json(['error' => 'Invalid API key'], 401);
        }
    }
}
