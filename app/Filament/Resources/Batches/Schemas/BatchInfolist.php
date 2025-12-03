<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class BatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | Batch Information
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | Users List + Start Batch Button
            |--------------------------------------------------------------------------
            */
            Section::make('')
                ->schema([

                    Actions::make([
                        Action::make('startBatch')
                            ->label('Mulai Batch')
                            ->button()
                            ->color('success')
                            ->icon('heroicon-o-play')

                            // Tombol hanya muncul jika status bukan "standby"
                            ->visible(fn ($record) => $record->status == 'standby')

                            ->requiresConfirmation()
                            ->action(function ($record) {

                                // ===============================
                                // 1. Ambil semua user dalam batch
                                // ===============================
                                $users = $record->users;
                                $userIds = $users->pluck('id');

                                // ===============================
                                // 2. Hapus soal lama user
                                // ===============================
                                \App\Models\ClientQuestion::whereIn('user_id', $userIds)->delete();

                                // Ambil semua bank soal
                                $questions = \App\Models\Question::pluck('id')->toArray();

                                // ===============================
                                // 3. Generate ulang soal + random order
                                // ===============================
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

                                // ===============================
                                // 4. Generate client_tests KOSONG
                                // ===============================
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

                                // ===============================
                                // 5. Ubah status batch menjadi "open"
                                // ===============================
                                $record->update([
                                    'status' => 'open',
                                ]);

                                // ===============================
                                // 6. Notifikasi
                                // ===============================
                                \Filament\Notifications\Notification::make()
                                    ->title('Batch berhasil dimulai!')
                                    ->body('Soal berhasil digenerate dan data client_test sudah disiapkan.')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | REPEATABLE ENTRY: LIST USERS
                    |--------------------------------------------------------------------------
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
