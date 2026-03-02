<?php

namespace App\Filament\Client\Resources\ClientBatches\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Forms\Components\TextInput;

use App\Models\Payment;
use App\Services\MidtransService;
use Filament\Notifications\Notification;

class ClientBatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |------------------------------------------------------------------
            | Batch Information
            |------------------------------------------------------------------
            */
            Section::make('Informasi Batch')
                ->schema([
                    TextEntry::make('name')->label('Nama Batch'),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge(),

                    TextEntry::make('date')
                        ->label('Tanggal')
                        ->date(),

                    TextEntry::make('start_time')
                        ->label('Mulai')
                        ->time(),

                    TextEntry::make('end_time')
                        ->label('Selesai')
                        ->time(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            /*
            |------------------------------------------------------------------
            | PAYMENT
            |------------------------------------------------------------------
            */
            Section::make('Pembayaran')
                ->schema([

                    // =========================
                    // 🔢 RINGKASAN BIAYA
                    // =========================
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

                    Actions::make([
                        Action::make('pay')
                            ->label('Bayar Sekarang')
                            ->icon('heroicon-o-credit-card')
                            ->color('success')
                            ->visible(fn ($record) => optional($record->payment)->status !== 'paid')
                            ->requiresConfirmation()
                            ->action(function ($record) {

                                $participants = $record->users()->count();

                                if ($participants <= 0) {
                                    Notification::make()
                                        ->title('Tidak ada peserta dalam batch ini')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $hargaPerPeserta = 200000;
                                $subtotal = $participants * $hargaPerPeserta;
                                $ppn = (int) ($subtotal * 0.11);
                                $uniqueCode = rand(1, 300);

                                $total = $subtotal + $ppn + $uniqueCode;

                                $orderId = 'BATCH-' . $record->id . '-' . time();

                                $midtrans = app(MidtransService::class);

                                $snapToken = $midtrans->createTransaction([
                                    'order_id' => $orderId,
                                    'gross_amount' => $total,
                                    'customer_details' => [
                                        'first_name' => auth()->user()->name,
                                        'email' => auth()->user()->email,
                                    ],
                                ]);

                                Payment::create([
                                    'user_id' => auth()->id(),
                                    'batch_id' => $record->id,
                                    'order_id' => $orderId,
                                    'amount' => $total,
                                    'participants' => $participants,
                                    'ppn' => $ppn,
                                    'unique_code' => $uniqueCode,
                                    'snap_token' => $snapToken,
                                    'status' => 'pending',
                                ]);

                                return redirect('/payment/' . $orderId);
                            }),
                    ])->columnSpanFull(),
                ])
                ->columns(2) // 🔥 biar rapi 2 kolom
                ->columnSpanFull(),

            /*
            |------------------------------------------------------------------
            | PARTICIPANTS (WITH SEARCH)
            |------------------------------------------------------------------
            */
            Section::make('Peserta')
                ->description('Cari dan lihat peserta dalam batch ini')
                ->schema([

                    TextInput::make('participantSearch')
                        ->label('Cari Peserta')
                        ->placeholder('Ketik nama / username / NIK...')
                        ->suffixIcon('heroicon-m-magnifying-glass')
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, $livewire) {
                            $livewire->participantSearch = $state;
                        })
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

        ]);
    }
}