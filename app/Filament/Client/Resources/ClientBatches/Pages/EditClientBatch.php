<?php

namespace App\Filament\Client\Resources\ClientBatches\Pages;

use App\Filament\Client\Resources\ClientBatches\ClientBatchResource;
use Filament\Resources\Pages\EditRecord;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ParticipantsImport;
use Filament\Notifications\Notification;

class EditClientBatch extends EditRecord
{
    protected static string $resource = ClientBatchResource::class;

    /**
     * 🔒 Cek akses saat halaman dibuka
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->status !== 'standby') {

            Notification::make()
                ->title('Batch tidak dapat diedit')
                ->body('Batch sudah diproses / dibayar. Anda akan dialihkan ke halaman batch.')
                ->danger()
                ->duration(3000)
                ->send();

            $this->redirect(ClientBatchResource::getUrl());
        }
    }

    /**
     * 🔥 sebelum save ke database
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // hapus field yang tidak ada di database
        unset($data['participants_file']);
        unset($data['users']);

        // set user client
        $data['user_id'] = auth()->id();

        return $data;
    }

    /**
     * 🔥 setelah save → import excel peserta
     */
    protected function afterSave(): void
    {
        $files = $this->data['participants_file'] ?? null;

        if (!$files) {
            return;
        }

        // jika multiple upload ambil file pertama
        $file = is_array($files) ? reset($files) : $files;

        if (!$file) {
            return;
        }

        $path = storage_path('app/public/' . $file);

        if (!file_exists($path)) {

            Notification::make()
                ->title('File tidak ditemukan')
                ->body('File peserta gagal diproses.')
                ->danger()
                ->send();

            return;
        }

        try {

            Excel::import(
                new ParticipantsImport($this->record->id),
                $path
            );

        } catch (\Throwable $e) {

            Notification::make()
                ->title('Import gagal')
                ->body('Terjadi kesalahan saat mengimport peserta.')
                ->danger()
                ->send();
        }
    }

    /**
     * 🔥 redirect setelah save
     */
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('view', [
            'record' => $this->record,
        ]);
    }
}