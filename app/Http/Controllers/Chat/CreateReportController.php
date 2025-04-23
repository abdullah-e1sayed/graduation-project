<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Reports;
use Cloudstudio\Ollama\Facades\Ollama;
use App\Models\Vulnerability;
use Illuminate\Support\Facades\Response;
use Exception;

class CreateReportController extends Controller
{
    public function runPythonScript(Request $request)
    {
        $bugName = $request->bug_name;
        $asset = $request->asset;
        $stepToPreuce = $request->step_to_preuce;
        $POC = $request->poc;
        $severity = $request->severity;
        $model = $request->input('model', 'X');


        $reportStructure = Reports::getStructure($bugName);

        $bugInfo = "bug name: $bugName, asset: $asset";

        $response = $this->getAssistantResponse($bugInfo,$reportStructure,$model);

        $reportStructure = Reports::generateReportTemplate($bugName, $asset, $stepToPreuce, $POC, $severity, $response);

        $request -> validate([
            'site'=>'sometimes|required|string|max:255',
            'title'=>'sometimes|required|string|max:255',
            'severity'=> 'in:critical,high,medium,low',
        ]);
        $vulnerability = array_merge([
            'user_id' => $request->user()->id,
            'site' => $asset,
            'title' => $bugName,
            'report' => $reportStructure,
        ], $request->all());
        $report = Vulnerability::create($vulnerability);

        return Response::json(["report"=>"{$report->report}"],201);

    }


    function getAssistantResponse($bugInfo, $reportStructure, $model = 'X') {
        $sys = "Before providing an answer, analyze the task deeply, ensure you understand each part using the <thinking> format. Develop a structured approach for your response, detailing each step involved. It’s crucial to validate that every step of your solution is accurate and correctly applied using <thinking> framework. Only after thorough verification and accurate application of your steps should you present your final answer.";

        $q = "Create a detailed pentesting report based on the following information: {$bugInfo}, Do not modify any of the provided information; include it exactly as it is in the report, the report must following this structure: {$reportStructure}";
        $question = "system message: {$sys}, user task is: {$q}";

        if ($model === "X-mini") {
            $q = $reportStructure;
        }

        $message = $question;
        try {
            $response = Ollama::prompt($question)
                ->model($model)
                ->stream(false)
                ->ask();

            return $this->cleanReportContent($response['response']);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    function cleanReportContent($response) {

        if (preg_match('/<output>(.*?)<\/output>/s', $response, $matches)) {
            $content = trim($matches[1]);

            $content = preg_replace('/^(Here.*?:|This is.*?:|Below is.*?:|.*?report for.*?:)/i', '', $content);

            $content = preg_replace('/Related concepts.*/s', '', $content);

            return trim($content);
        }

        return trim($response);
    }

}
