<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\DataImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImportController extends Controller
{
    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        // Import the data
        Excel::import(new DataImport, $request->file('file'));

        return response()->json(['Message'=>'Vulnerabilities added successfully']);        
    }
}
