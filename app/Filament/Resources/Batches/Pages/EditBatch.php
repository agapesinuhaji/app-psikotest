<?php

namespace App\Filament\Resources\Batches\Pages;

use App\Filament\Resources\Batches\BatchResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBatch extends EditRecord
{
    protected static string $resource = BatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        /*
        |--------------------------------------------------------------------------
        | Hanya jalan jika:
        | - Status batch masih 'paid'
        | - Payment juga sudah 'paid'
        |--------------------------------------------------------------------------
        */
        if ($record->status !== 'paid') {
            return;
        }

        if ($record->payment?->status !== 'paid') {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan tanggal sudah diisi
        |--------------------------------------------------------------------------
        */
        if (! $record->date) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan field date benar-benar berubah
        |--------------------------------------------------------------------------
        */
        if (! $record->wasChanged('date')) {
            return;
        }

        $users = $record->users;

        if ($users->isEmpty()) {
            return;
        }

        $userIds = $users->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | Hapus soal lama
        |--------------------------------------------------------------------------
        */
        \App\Models\ClientQuestion::whereIn('user_id', $userIds)->delete();

        /*
        |--------------------------------------------------------------------------
        | Ambil semua soal
        |--------------------------------------------------------------------------
        */
        $questions = \App\Models\Question::pluck('id')->toArray();

        /*
        |--------------------------------------------------------------------------
        | Generate soal acak per user
        |--------------------------------------------------------------------------
        */
        foreach ($users as $user) {

            $shuffled = $questions;
            shuffle($shuffled);

            foreach ($shuffled as $order => $questionId) {
                \App\Models\ClientQuestion::create([
                    'user_id'     => $user->id,
                    'question_id' => $questionId,
                    'order'       => $order + 1,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Reset waktu test
            |--------------------------------------------------------------------------
            */
            \App\Models\ClientTest::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'spm_start_at'         => null,
                    'spm_end_at'           => null,
                    'papikostick_start_at' => null,
                    'papikostick_end_at'   => null,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update status menjadi 'time set'
        |--------------------------------------------------------------------------
        */
        $record->update([
            'status' => 'time set',
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return BatchResource::getUrl('view', [
            'record' => $this->record,
        ]);
    }
}