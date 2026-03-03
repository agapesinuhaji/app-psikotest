<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Forms\Components\TextInput;

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
                    TextEntry::make('name')->label('Nama Batch'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('start_time')->dateTime(),
                    TextEntry::make('end_time')->dateTime(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            /*
            |--------------------------------------------------------------------------
            | Actions (ADMIN ONLY)
            |--------------------------------------------------------------------------
            */
            Section::make()
                ->schema([

                    Actions::make([

                        /*
                        |--------------------------------------------------------------------------
                        | START BATCH (Dipindahkan ke Psikolog)
                        |--------------------------------------------------------------------------
                        */
                        /*
                        Action::make('startBatch')
                            ->label('Mulai Batch')
                            ->button()
                            ->color('success')
                            ->icon('heroicon-o-play')
                            ->visible(fn ($record) => $record->status === 'standby')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                // Logic dipindah ke PsikologBatchInfolist
                            }),
                        */

                        /*
                        |--------------------------------------------------------------------------
                        | END BATCH (Dipindahkan ke Psikolog)
                        |--------------------------------------------------------------------------
                        */
                        /*
                        Action::make('endBatch')
                            ->label('Akhiri Batch')
                            ->button()
                            ->color('danger')
                            ->visible(fn ($record) => $record->status === 'open')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                // Logic dipindah ke PsikologBatchInfolist
                            }),
                        */

                        /*
                        |--------------------------------------------------------------------------
                        | PROCESS RESULTS (Dipindahkan ke Psikolog)
                        |--------------------------------------------------------------------------
                        */
                        /*
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
                                // Logic dipindah ke PsikologBatchInfolist
                            }),
                        */

                        /*
                        |--------------------------------------------------------------------------
                        | DOWNLOAD RESULTS (Admin boleh)
                        |--------------------------------------------------------------------------
                        */
                        Action::make('downloadResults')
                            ->label('Download Hasil')
                            ->button()
                            ->color('primary')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->visible(fn ($record) =>
                                $record->is_result_processed
                            )
                            ->url(fn ($record) =>
                                route('batches.download.results', $record)
                            )
                            ->openUrlInNewTab(),

                    ])
                    ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | Participants Section
                    |--------------------------------------------------------------------------
                    */
                    Section::make('Participants')
                        ->description('Daftar peserta yang mengikuti batch ini')
                        ->schema([

                            TextInput::make('participantSearch')
                                ->label('Cari Peserta')
                                ->placeholder('Cari nama / username ...')
                                ->live(debounce: 500)
                                ->suffixIcon('heroicon-m-magnifying-glass')
                                ->columnSpanFull(),

                            RepeatableEntry::make('users')
                                ->state(function ($record, $livewire) {

                                    $search = strtolower($livewire->participantSearch ?? '');

                                    return $record->users->filter(function ($user) use ($search) {

                                        if ($search === '') return true;

                                        return str_contains(strtolower($user->name), $search)
                                            || str_contains(strtolower($user->username), $search)
                                            || str_contains(strtolower($user->nik ?? ''), $search)
                                            || str_contains(strtolower($user->nama_ayah ?? ''), $search);

                                    })->values();
                                })
                                ->schema([
                                    Section::make()
                                        ->schema([
                                            TextEntry::make('name')->label('Nama'),
                                            TextEntry::make('username'),
                                            TextEntry::make('plain_password')->label('Password'),
                                            TextEntry::make('nik')->label('NIK'),
                                            TextEntry::make('birth')
                                                ->label('Tempat, Tanggal Lahir')
                                                ->state(fn ($record) =>
                                                    $record->place_of_birth . ', ' .
                                                    \Carbon\Carbon::parse($record->date_of_birth)->format('d M Y')
                                                ),
                                            TextEntry::make('age'),
                                            TextEntry::make('last_education'),
                                            TextEntry::make('nama_ayah')->label('Nama Ayah'),
                                        ])
                                        ->columns(4),
                                ])
                                ->columnSpanFull(),

                        ])
                        ->columnSpanFull(),

                ])
                ->columnSpanFull(),
        ]);
    }
}