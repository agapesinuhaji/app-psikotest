<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Forms\Components\TextInput;
use App\Services\SPMResultService;
use App\Services\PapikostickResultService;

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
            | Actions
            |--------------------------------------------------------------------------
            */
            Section::make()
                ->schema([

                    Actions::make([

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
                                            'spm_start_at'         => null,
                                            'spm_end_at'           => null,
                                            'papikostick_start_at' => null,
                                            'papikostick_end_at'   => null,
                                        ]
                                    );
                                }

                                \App\Models\User::whereIn('id', $userIds)
                                    ->update(['is_active' => 1]);

                                $record->update([
                                    'status'     => 'open',
                                    'start_time' => now(),
                                ]);
                            }),

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
                            }),

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
                            }),

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

                            // 🔍 SEARCH INPUT (HANYA 1x)
                            TextInput::make('participantSearch')
                                ->label('Cari Peserta')
                                ->placeholder('Cari nama / username ...')
                                ->live(debounce: 500)
                                ->suffixIcon('heroicon-m-magnifying-glass')
                                ->columnSpanFull(),

                            // 📋 LIST PESERTA
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
