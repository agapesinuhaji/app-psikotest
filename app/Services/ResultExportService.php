<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResultExportService
{
    public function exportBatchDocx($batch): string
    {
        $filename = 'hasil-batch-' . Str::slug($batch->name) . '.docx';
        $path     = 'exports/' . $filename;

        // Ambil data batch lengkap
        $batch->load([
            'users',
            'users.clientTest',
            'users.spmResult',
            'users.papikostickResult',
        ]);

        // Render HTML dari Blade
        $html = view('exports.batch-report', [
            'batch' => $batch,
        ])->render();

        // Bungkus HTML agar dikenali Word
        $docxContent = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body {
        font-family: Times New Roman, serif;
        font-size: 12pt;
    }
    h1, h2 {
        text-align: center;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    table, th, td {
        border: 1px solid #000;
    }
    th, td {
        padding: 6px;
    }
</style>
</head>
<body>
$html
</body>
</html>
HTML;

        Storage::disk('local')->put($path, $docxContent);

        return Storage::disk('local')->url($path);
    }
}
