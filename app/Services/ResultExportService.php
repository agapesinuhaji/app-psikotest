<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Models\User;
use App\Models\Batch;
use Exception;

class ResultExportService
{
    /**
     * Export semua hasil dalam batch ke ZIP.
     */
    public function exportBatch(int $batchId): string
    {
        $batch = Batch::with('users')->findOrFail($batchId);

        // Folder temporary PDF
        $folder = "exports/batch_{$batchId}_" . time();
        Storage::makeDirectory($folder);

        // 1️⃣ Generate PDF per user — kirim $batch
        foreach ($batch->users as $user) {
            $this->generateUserPDF($user, $folder, $batch);
        }

        // 2️⃣ Buat ZIP
        $zipPath = "exports/batch_{$batchId}_results.zip";
        $this->createZip($folder, $zipPath);

        // 3️⃣ Bersihkan PDF sementara
        Storage::deleteDirectory($folder);

        return storage_path("app/{$zipPath}");
    }

    /**
     * Generate PDF per user.
     */
    private function generateUserPDF(User $user, string $folder, Batch $batch): void
    {
        $corporate_identity = \App\Models\CorporateIdentity::first();

        $data = [
            'user'  => $user,
            'spm'   => $user->spmResult ?? null,
            'papi'  => $user->papikostickResult ?? null,
            'batch' => $batch, // ⬅️ Tambahkan batch
            'corporate_identity' => $corporate_identity,
        ];

        $pdf = Pdf::loadView('exports.user_result', $data)
            ->setPaper('A4');

        $pdfName = "{$user->username}_result.pdf";

        Storage::put("$folder/$pdfName", $pdf->output());
    }

    /**
     * Buat ZIP dari semua PDF dalam folder.
     */
    private function createZip(string $sourceFolder, string $zipPath): void
    {
        Storage::makeDirectory(dirname($zipPath));

        $zip = new ZipArchive;
        $absoluteZipPath = storage_path("app/$zipPath");

        if ($zip->open($absoluteZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Tidak dapat membuka atau membuat ZIP: $absoluteZipPath");
        }

        $files = Storage::files($sourceFolder);

        foreach ($files as $file) {
            $realPath = realpath(Storage::path($file));

            if (!$realPath || !is_file($realPath)) {
                continue;
            }

            $zip->addFile($realPath, basename($file));
        }

        $zip->close();
    }
}
