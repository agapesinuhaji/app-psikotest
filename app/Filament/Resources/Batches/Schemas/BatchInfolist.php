<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Services\SPMResultService;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use App\Services\PapikostickResultService;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class BatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |----------------------------------------------------------------------
            | Batch Information
            |----------------------------------------------------------------------
            */
            Section::make('Batch Information')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('status'),
                    TextEntry::make('start_time')->dateTime(),
                    TextEntry::make('end_time')->dateTime(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            /*
            |----------------------------------------------------------------------
            | Users List + Start / End Batch / Proses Hasil Buttons
            |----------------------------------------------------------------------
            */
            Section::make('')
                ->schema([

                    Actions::make([
                        // ===========================
                        // Tombol Mulai Batch
                        // ===========================
                        Action::make('startBatch')
                            ->label('Mulai Batch')
                            ->button()
                            ->color('success')
                            ->icon('heroicon-o-play')
                            ->visible(fn ($record) => $record->status == 'standby')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $users = $record->users;
                                $userIds = $users->pluck('id');

                                // Hapus soal lama
                                \App\Models\ClientQuestion::whereIn('user_id', $userIds)->delete();

                                // Ambil semua bank soal
                                $questions = \App\Models\Question::pluck('id')->toArray();

                                // Generate ulang soal untuk setiap user
                                foreach ($users as $user) {
                                    $shuffled = $questions;
                                    shuffle($shuffled);

                                    foreach ($shuffled as $order => $questionId) {
                                        \App\Models\ClientQuestion::updateOrCreate(
                                            [
                                                'user_id' => $user->id,
                                                'question_id' => $questionId,
                                            ],
                                            [
                                                'order' => $order + 1,
                                            ]
                                        );
                                    }
                                }

                                // Generate client_tests kosong
                                foreach ($users as $user) {
                                    \App\Models\ClientTest::updateOrCreate(
                                        [
                                            'user_id' => $user->id,
                                        ],
                                        [
                                            'spm_start_at' => null,
                                            'spm_end_at' => null,
                                            'papikostick_start_at' => null,
                                            'papikostick_end_at' => null,
                                        ]
                                    );
                                }

                                // Aktifkan semua user
                                \App\Models\User::whereIn('id', $userIds)->update(['is_active' => 1]);

                                // Ubah status batch
                                $record->update(['status' => 'open']);

                                // Notifikasi
                                \Filament\Notifications\Notification::make()
                                    ->title('Batch berhasil dimulai!')
                                    ->body('Soal berhasil digenerate dan data client_test sudah disiapkan.')
                                    ->success()
                                    ->send();
                            }),

                        // ===========================
                        // Tombol Akhiri Batch
                        // ===========================
                        Action::make('endBatch')
                            ->label('Akhiri Batch')
                            ->button()
                            ->color('danger')
                            ->visible(fn ($record) => $record->status == 'open')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $userIds = $record->users->pluck('id');

                                // Nonaktifkan semua user
                                \App\Models\User::whereIn('id', $userIds)->update(['is_active' => 0]);

                                // Ubah status batch
                                $record->update(['status' => 'closed']);

                                // Notifikasi
                                \Filament\Notifications\Notification::make()
                                    ->title('Batch berhasil diakhiri!')
                                    ->body('Semua user dinonaktifkan dan batch ditutup.')
                                    ->success()
                                    ->send();
                            }),

                        // ===========================
                        // Tombol Proses Hasil
                        // ===========================
                        Action::make('processResults')
                            ->label('Proses Hasil')
                            ->button()
                            ->color('primary')
                            ->icon('heroicon-o-cog')
                            ->visible(fn ($record) => $record->status == 'closed')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $userIds = $record->users->pluck('id');

                                // Contoh logika proses hasil
                                // Misal: hitung skor atau generate report
                                foreach ($userIds as $id) {
                                    SPMResultService::processUser($id);
                                    PapikostickResultService::processUser($id);
                                }

                                \Filament\Notifications\Notification::make()
                                    ->title('Hasil batch diproses!')
                                    ->body('Semua hasil peserta batch sudah diproses.')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->columnSpanFull(),

                    /*
                    |------------------------------------------------------------------
                    | REPEATABLE ENTRY: LIST USERS
                    |------------------------------------------------------------------
                    */
                    RepeatableEntry::make('users')
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextEntry::make('name')->label('Name'),
                                    TextEntry::make('username')->label('Username'),
                                    TextEntry::make('plain_password')->label('Password'),
                                    TextEntry::make('birth')
                                        ->label('Place, Date of Birth')
                                        ->state(fn ($record) =>
                                            $record->place_of_birth . ', ' .
                                            \Carbon\Carbon::parse($record['date_of_birth'])->format('d M Y')
                                        ),
                                    TextEntry::make('age')->label('Age'),
                                    TextEntry::make('last_education')->label('Last Education'),
                                ])
                                ->columns(4),
                        ])
                        ->columnSpanFull(),

                ])
                ->columnSpanFull(),
        ]);
    }
}
