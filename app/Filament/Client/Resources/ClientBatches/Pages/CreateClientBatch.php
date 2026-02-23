<?php

namespace App\Filament\Client\Resources\ClientBatches\Pages;

use App\Filament\Client\Resources\ClientBatches\ClientBatchResource;
use Filament\Resources\Pages\CreateRecord;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ParticipantsImport;

class CreateClientBatch extends CreateRecord
{
    protected static string $resource = ClientBatchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $file = $this->data['participants_file'] ?? null;

        // kalau kosong → skip
        if (!$file) {
            return;
        }

        // ambil array upload
        if (is_array($file)) {
            $file = $file[0] ?? null;
        }

        if (!$file) {
            return;
        }

        // path file
        $path = storage_path('app/public/' . $file);

        if (!file_exists($path)) {
            return;
        }

        // import Excel
        Excel::import(
            new ParticipantsImport($this->record->id),
            $path
        );
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Buat Batch Baru')
                ->submit('create'),
        ];
    }
}