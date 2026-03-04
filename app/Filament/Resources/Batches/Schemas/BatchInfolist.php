<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Forms\Components\TextInput;
use Carbon\Carbon;

class BatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // -------------------------
            // Batch Information
            // -------------------------
            Section::make('Batch Information')
                ->schema([
                    TextEntry::make('name')->label('Nama Batch'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('start_time')->dateTime(),
                    TextEntry::make('end_time')->dateTime(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            // -------------------------
            // Actions (Admin)
            // -------------------------
            Section::make()
                ->schema([
                    Action::make('downloadResults')
                        ->label('Download Hasil (PDF)')
                        ->button()
                        ->color('info')
                        ->icon('heroicon-o-document-arrow-down')
                        ->visible(fn ($record) => $record->is_result_processed)
                        ->action(function ($record) {
                            $zipPath = storage_path('app/temp/batch-' . $record->id . '-hasil-psikotes.zip');
                            $zip = new \ZipArchive();
                            $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

                            foreach ($record->users as $user) {
                                $pdf = \App\Services\ResultPdfService::generate($user);
                                $zip->addFile($pdf, basename($pdf));
                            }

                            $zip->close();
                            return response()->download($zipPath);
                        }),
                ])
                ->columnSpanFull(),

            // -------------------------
            // Participants Section
            // -------------------------
            Section::make('Participants')
                ->description('Daftar peserta yang mengikuti batch ini')
                ->schema([

                    TextInput::make('participantSearch')
                        ->label('Cari Peserta')
                        ->placeholder('Cari nama / username ...')
                        ->live(500)
                        ->suffixIcon('heroicon-m-magnifying-glass')
                        ->columnSpanFull(),

                    RepeatableEntry::make('users')
                        ->state(function ($record, $livewire) {

                            $search = strtolower($livewire->participantSearch ?? '');

                            return $record->users->filter(function ($user) use ($search) {

                                if ($search === '') {
                                    return true;
                                }

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
                                            Carbon::parse($record->date_of_birth)->format('d M Y')
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

        ]);
    }
}