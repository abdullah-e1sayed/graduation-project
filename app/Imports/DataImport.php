<?php

namespace App\Imports;

use App\Models\Vulnerability;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;


class DataImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Skip the header row
        $rows->shift();
        $user=Auth::user();

        foreach ($rows as $row) {
            // Create the vulnerability record
            $subject = Vulnerability::Create(
                [
                    'user_id' => $user->id,               
                    'site' => $row[0],
                    'title' => $row[1],
                    'type' => $row[2],
                    'poc' => $row[3],
                    'step_for_reduce' => $row[4],
                    'report' => $row[5],
                ]
            );
        }
    }
}