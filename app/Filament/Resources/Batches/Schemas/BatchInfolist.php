<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use App\Services\SPMResultService;
use App\Services\PapikostickResultService;
use App\Services\ResultExportService;

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
            | Actions
            |--------------------------------------------------------------------------
            */
            Section::make()
                ->schema([

                    Actions::make([

                        // ===========================
                        // Mulai Batch
                        // ===========================
                        Action::make('startBatch')
                            ->label('Mulai Batch')
                            ->button()
                            ->color('success')
                            ->icon('heroicon-o-play')
                            ->visible(fn ($record) => $record->status === 'standby')
                            ->requiresConfirmation()
                            ->action(function ($record) {

                                $users   = $record->users;
                                $userIds = $users->pluck('id');

                                \App\Models\ClientQuestion::whereIn('user_id', $userIds)->delete();

                                $questions = \App\Models\Question::pluck('id')->toArray();

                                foreach ($users as $user) {
                                    $shuffled = $questions;
                                    shuffle($shuffled);

                                    foreach ($shuffled as $order => $questionId) {
                                        \App\Models\ClientQuestion::updateOrCreate(
                                            [
                                                'user_id'     => $user->id,
                                                'question_id' => $questionId,
                                            ],
                                            [
                                                'order' => $order + 1,
                                            ]
                                        );
                                    }
                                }

                                foreach ($users as $user) {
                                    \App\Models\ClientTest::updateOrCreate(
                                        ['user_id' => $user->id],
                                        [
                                            'spm_start_at'          => null,
                                            'spm_end_at'            => null,
                                            'papikostick_start_at'  => null,
                                            'papikostick_end_at'    => null,
                                        ]
                                    );
                                }

                                \App\Models\User::whereIn('id', $userIds)
                                    ->update(['is_active' => 1]);

                                $record->update([
                                    'status'     => 'open',
                                    'start_time' => now(),
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Batch berhasil dimulai')
                                    ->success()
                                    ->send();
                            }),

                        // ===========================
                        // Akhiri Batch
                        // ===========================
                        Action::make('endBatch')
                            ->label('Akhiri Batch')
                            ->button()
                            ->color('danger')
                            ->visible(fn ($record) => $record->status === 'open')
                            ->requiresConfirmation()
                            ->action(function ($record) {

                                $userIds = $record->users->pluck('id');

                                \App\Models\User::whereIn('id', $userIds)
                                    ->update(['is_active' => 0]);

                                $record->update([
                                    'status'   => 'closed',
                                    'end_time' => now(),
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Batch diakhiri')
                                    ->success()
                                    ->send();
                            }),

                        // ===========================
                        // Proses Hasil
                        // ===========================
                        Action::make('processResults')
                            ->label('Proses Hasil')
                            ->button()
                            ->color('primary')
                            ->icon('heroicon-o-cog')
                            ->visible(fn ($record) =>
                                $record->status === 'closed'
                                && ! $record->is_result_processed
                            )
                            ->requiresConfirmation()
                            ->action(function ($record) {

                                foreach ($record->users as $user) {
                                    SPMResultService::processUser($user->id);
                                    PapikostickResultService::processUser($user->id);
                                }

                                $record->update([
                                    'is_result_processed' => true,
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Hasil berhasil diproses')
                                    ->success()
                                    ->send();
                            }),

                        // ===========================
                        // DOWNLOAD DOCX (LANGKAH 4)
                        // ===========================
                        Action::make('downloadResults')
                            ->label('Download Hasil (DOCX)')
                            ->button()
                            ->color('info')
                            ->icon('heroicon-o-document-arrow-down')
                            ->visible(fn ($record) => $record->is_result_processed)
                            ->action(function ($record) {

                                $zipPath = storage_path(
                                    'app/temp/batch-' . $record->id . '-hasil-psikotes.zip'
                                );

                                $zip = new \ZipArchive();
                                $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

                                foreach ($record->users as $user) {
                                    $docx = \App\Services\ResultDocxService::generate($user);
                                    $zip->addFile($docx, basename($docx));
                                }

                                $zip->close();

                                return response()->download($zipPath);
                            }),


                    ])
                    ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | Users List
                    |--------------------------------------------------------------------------
                    */
                    RepeatableEntry::make('users')
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextEntry::make('name'),
                                    TextEntry::make('username'),
                                    TextEntry::make('plain_password')->label('Password'),
                                    TextEntry::make('birth')
                                        ->label('Place, Date of Birth')
                                        ->state(fn ($record) =>
                                            $record->place_of_birth . ', ' .
                                            \Carbon\Carbon::parse($record->date_of_birth)->format('d M Y')
                                        ),
                                    TextEntry::make('age'),
                                    TextEntry::make('last_education'),
                                ])
                                ->columns(4),
                        ])
                        ->columnSpanFull(),

                ])
                ->columnSpanFull(),
        ]);
    }
}
