<?php

namespace App\Helpers;

class Reports
{
    static public function generateReportTemplate($bug_name, $asset, $step_to_reproduce, $POC, $severity, $response)
    {
        return "# Penetration Testing Report

---

## Report Summary
Date:  
Report ID:  
Reported by:  
Target Asset: $asset

---

## 1. Vulnerability Overview

- Vulnerability Name: $bug_name
- Severity: $severity
- Affected Asset: $asset

---

## 2. Steps to Reproduce

Step-by-Step Reproduction:

$step_to_reproduce

> *Note: The steps to reproduce have been documented as provided by the user.*

---

## 3. Proof of Concept (POC)

### POC Details
$POC

> *Note: POC details are retained as provided by the user.*

---

$response

---

## 7. Summary Table

| Section              | Details                      |
|----------------------|------------------------------|
| Vulnerability Name   | $bug_name                    |
| Severity             | $severity                    |
| Asset                | $asset                       |

---

> Disclaimer: This report is intended solely for the recipient organization. Unauthorized disclosure, duplication, or distribution of this report is prohibited.
";
    }

    static public function getStructure($bugName)
    {
        return "Your task is to generate a penetration testing report. Use the provided details strictly and focus on the following:
1. Vulnerability Description:
2. Impact Assessment:
3. Mitigation Steps:
### Input Details:
- bug name: $bugName

### Report Structure:
<output>
Vulnerability Description:

Impact Assessment:

Mitigation Steps:
</output>

### Important Notes:
- The report must be detailed, specific, and based only on the provided input.
- Do not provide generic explanations or unrelated content.
- Failure to adhere to these instructions will result in an incomplete or incorrect report.
";
    }

}