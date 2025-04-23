<?php

namespace App\Helpers;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Rage
{
    private function callPythonFunction($functionName, $arguments = [])
    {
        $pythonScriptPath = base_path('modle/chat/chat/src/rageApi/RAGHlperFunc.py');
        $process = new Process(array_merge(['python3', $pythonScriptPath, $functionName], $arguments));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return json_decode($process->getOutput(), true);
    }

    private function processAndStoreEmbeddings($userId, $embeddingInput)
    {
        return $this->callPythonFunction("process_and_store_embeddings", [$userId, json_encode($embeddingInput)]);
    }

    private function processPage($filePath, $pageNum)
    {
        return $this->callPythonFunction("process_page", [$filePath, $pageNum]);
    }
}
