<?php

namespace App\Filament\Psikolog\Resources\PsikologBatches\Schemas;

use App\Services\PapikostickResultService;
use App\Services\SPMResultService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PsikologBatchInfolist
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
                    TextEntry::make('name')
                        ->label('Nama Batch'),

                    TextEntry::make('status')
                        ->badge(),

                    TextEntry::make('date')
                        ->label('Jadwal Test')
                        ->dateTime('d F Y H:i')
                        ->placeholder('Belum diatur'),

                    TextEntry::make('start_time')
                        ->label('Waktu Mulai')
                        ->dateTime('d F Y H:i')
                        ->placeholder('-'),

                    TextEntry::make('end_time')
                        ->label('Waktu Selesai')
                        ->dateTime('d F Y H:i')
                        ->placeholder('-'),
                ])
                ->columns(2)
                ->columnSpanFull(),

            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */
            Section::make('Pembayaran')
                ->schema([

                    TextEntry::make('participants')
                        ->label('Jumlah Peserta')
                        ->state(fn ($record) => $record->users()->count()),

                    TextEntry::make('subtotal')
                        ->label('Total Harga')
                        ->state(function ($record) {
                            $total = $record->users()->count() * 200000;
                            return 'Rp ' . number_format($total, 0, ',', '.');
                        }),

                    TextEntry::make('ppn')
                        ->label('PPN 11%')
                        ->state(function ($record) {
                            $subtotal = $record->users()->count() * 200000;
                            $ppn = (int) ($subtotal * 0.11);
                            return 'Rp ' . number_format($ppn, 0, ',', '.');
                        }),

                    TextEntry::make('payment.status')
                        ->label('Status Pembayaran')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'pending' => 'Menunggu Pembayaran',
                            'paid' => 'Lunas',
                            'failed' => 'Gagal',
                            default => 'Belum Bayar',
                        }),

                ])
                ->columns(2)
                ->columnSpanFull(),

            /*
            |--------------------------------------------------------------------------
            | ACTIONS (PSIKOLOG)
            |--------------------------------------------------------------------------
            */
            Section::make()
                ->schema([

                    Actions::make([

                        /*
                        |--------------------------------------------------------------------------
                        | START BATCH
                        |--------------------------------------------------------------------------
                        */
                        Action::make('startBatch')
                            ->label('Mulai Batch')
                            ->button()
                            ->color('success')
                            ->icon('heroicon-o-play')
                            ->visible(fn ($record) =>
                                $record->status === 'time set'
                                && $record->payment?->status === 'paid'
                            )
                            ->requiresConfirmation()
                            ->form([
                                \Filament\Forms\Components\TextInput::make('password')
                                    ->label('Konfirmasi Code')
                                    ->password()
                                    ->required()
                                    ->revealable(),
                            ])
                            ->action(function ($record, array $data) {

                                /*
                                |--------------------------------------------------------------------------
                                | 1️⃣ VALIDASI TANGGAL
                                |--------------------------------------------------------------------------
                                */

                                if (!$record->date) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Tanggal batch belum diatur')
                                        ->send();

                                    return;
                                }

                                if (now()->lt(\Carbon\Carbon::parse($record->date))) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Batch belum bisa dimulai sebelum tanggal yang ditentukan')
                                        ->send();

                                    return;
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | 2️⃣ VALIDASI PASSWORD PSIKOLOG LOGIN
                                |--------------------------------------------------------------------------
                                */

                                if (!\Illuminate\Support\Facades\Hash::check(
                                    $data['password'],
                                    auth()->user()->password
                                )) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Password salah')
                                        ->send();

                                    return;
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | 3️⃣ JALANKAN START BATCH
                                |--------------------------------------------------------------------------
                                */

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

                                Notification::make()
                                    ->success()
                                    ->title('Batch berhasil dimulai')
                                    ->send();
                            }),
                            /*
                        |--------------------------------------------------------------------------
                        | END BATCH
                        |--------------------------------------------------------------------------
                        */
                        Action::make('endBatch')
                            ->label('Akhiri Batch')
                            ->button()
                            ->color('danger')
                            ->visible(fn ($record) => $record->status === 'open')
                            ->requiresConfirmation()
                            ->form([
                                \Filament\Forms\Components\TextInput::make('password')
                                    ->label('Konfirmasi Code')
                                    ->password()
                                    ->required()
                                    ->revealable(),
                            ])
                            ->action(function ($record, array $data) {

                                /*
                                |--------------------------------------------------------------------------
                                | 1️⃣ VALIDASI PASSWORD PSIKOLOG LOGIN
                                |--------------------------------------------------------------------------
                                */

                                if (!\Illuminate\Support\Facades\Hash::check(
                                    $data['password'],
                                    auth()->user()->password
                                )) {
                                    \Filament\Notifications\Notification::make()
                                        ->danger()
                                        ->title('Password salah')
                                        ->send();

                                    return;
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | 2️⃣ PROSES AKHIRI BATCH
                                |--------------------------------------------------------------------------
                                */

                                $userIds = $record->users->pluck('id');

                                \App\Models\User::whereIn('id', $userIds)
                                    ->update(['is_active' => 0]);

                                $record->update([
                                    'status'   => 'closed',
                                    'end_time' => now(),
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->success()
                                    ->title('Batch berhasil diakhiri')
                                    ->send();
                            }),
                        /*
                        |--------------------------------------------------------------------------
                        | PROCESS RESULTS
                        |--------------------------------------------------------------------------
                        */
                        Action::make('processResults')
                            ->label('Hitung Hasil')
                            ->button()
                            ->color('primary')
                            ->icon('heroicon-o-cog')
                            ->visible(fn ($record) =>
                                $record->status === 'closed'
                                && ! $record->is_result_processed
                            )
                            ->requiresConfirmation()
                            ->form([
                                \Filament\Forms\Components\TextInput::make('password')
                                    ->label('Konfirmasi Code')
                                    ->password()
                                    ->required()
                                    ->revealable(),
                            ])
                            ->action(function ($record, array $data) {

                                /*
                                |------------------------------------------------------------------
                                | 1️⃣ VALIDASI PASSWORD PSIKOLOG LOGIN
                                |------------------------------------------------------------------
                                */
                                if (!\Illuminate\Support\Facades\Hash::check(
                                    $data['password'],
                                    auth()->user()->password
                                )) {
                                    \Filament\Notifications\Notification::make()
                                        ->danger()
                                        ->title('Password salah')
                                        ->send();

                                    return;
                                }

                                /*
                                |------------------------------------------------------------------
                                | 2️⃣ PROSES HITUNG HASIL
                                |------------------------------------------------------------------
                                */
                                foreach ($record->users as $user) {
                                    SPMResultService::processUser($user->id);
                                    PapikostickResultService::processUser($user->id);
                                }

                                $record->update([
                                    'is_result_processed' => true,
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->success()
                                    ->title('Hasil berhasil diproses')
                                    ->send();
                            }),

                        /*
                        |--------------------------------------------------------------------------
                        | DOWNLOAD RESULTS
                        |--------------------------------------------------------------------------
                        */
                        Action::make('downloadResults')
                            ->label('Download Hasil')
                            ->button()
                            ->color('gray')
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
                    | PARTICIPANTS
                    |--------------------------------------------------------------------------
                    */
                    Section::make('Participants')
                        ->description('Daftar peserta batch ini')
                        ->schema([

                            TextInput::make('participantSearch')
                                ->label('Cari Peserta')
                                ->placeholder('Cari nama / NIK ...')
                                ->live(debounce: 500)
                                ->suffixIcon('heroicon-m-magnifying-glass')
                                ->columnSpanFull(),

                            RepeatableEntry::make('users')
                                ->state(function ($record, $livewire) {

                                    $search = strtolower($livewire->participantSearch ?? '');

                                    return $record->users->filter(function ($user) use ($search) {

                                        if ($search === '') return true;

                                        return str_contains(strtolower($user->name), $search)
                                            || str_contains(strtolower($user->nik ?? ''), $search);

                                    })->values();
                                })
                                ->schema([
                                    Section::make()
                                        ->schema([
                                            TextEntry::make('name')->label('Nama'),
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