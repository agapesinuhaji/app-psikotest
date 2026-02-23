<?php

namespace App\Filament\Client\Resources\ClientBatches\Pages;

use App\Filament\Client\Resources\ClientBatches\ClientBatchResource;
use Filament\Resources\Pages\EditRecord;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ParticipantsImport;

class EditClientBatch extends EditRecord
{
    protected static string $resource = ClientBatchResource::class;

    /**
     * 🔥 sebelum save ke database
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // ❌ buang field yang bukan kolom batches
        unset($data['participants_file']);
        unset($data['users']);

        // 🔥 set owner batch
        $data['user_id'] = auth()->id();

        return $data;
    }

    /**
     * 🔥 setelah save → import excel
     */
    protected function afterSave(): void
    {
        $files = $this->data['participants_file'] ?? null;

        // jika kosong → skip
        if (!$files) {
            return;
        }

        // 🔥 FileUpload return array → ambil file pertama
        $file = is_array($files) ? reset($files) : $files;

        if (!$file) {
            return;
        }

        // path ke storage
        $path = storage_path('app/public/' . $file);

        // 🔥 jalankan import
        Excel::import(
            new ParticipantsImport($this->record->id),
            $path
        );
    }
}