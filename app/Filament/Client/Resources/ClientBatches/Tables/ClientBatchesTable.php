<?php

namespace App\Filament\Client\Resources\ClientBatches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

use App\Models\Payment;
use App\Services\MidtransService;

class ClientBatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([

                TextColumn::make('name')
                    ->label('Batch Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->date()
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Start')
                    ->time(),

                TextColumn::make('end_time')
                    ->label('End')
                    ->time(),

                // ==============================
                // STATUS BATCH
                // ==============================
                BadgeColumn::make('status')
                    ->label('Batch Status')
                    ->colors([
                        'primary' => 'standby',
                        'success' => 'active',
                        'danger' => 'closed',
                    ]),

                // ==============================
                // STATUS PEMBAYARAN
                // ==============================
                BadgeColumn::make('payment.status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'gray' => fn ($state) => $state === null,
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Menunggu',
                        'paid' => 'Lunas',
                        'failed' => 'Gagal',
                        default => 'Belum Bayar',
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since(),
            ])

            ->recordActions([

                // 🔥 VIEW (SEKARANG SUDAH AMAN)
                ViewAction::make(),

                EditAction::make()
                    ->visible(fn ($record) => optional($record->payment)->status !== 'paid'),

                // =====================================
                // 🔥 ACTION BAYAR MIDTRANS
                // =====================================
                Action::make('pay')
                    ->label('Bayar')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->visible(fn ($record) => optional($record->payment)->status !== 'paid')

                    ->action(function ($record) {

                        // ===============================
                        // 1. Hitung jumlah peserta
                        // ===============================
                        $participants = $record->users()->count();

                        if ($participants <= 0) {
                            Notification::make()
                                ->title('Tidak ada peserta dalam batch ini')
                                ->danger()
                                ->send();
                            return;
                        }

                        // ===============================
                        // 2. Hitung biaya
                        // ===============================
                        $hargaPerPeserta = 200000;

                        $subtotal = $participants * $hargaPerPeserta;
                        $ppn = (int) ($subtotal * 0.11);
                        $uniqueCode = rand(1, 300);

                        $total = $subtotal + $ppn + $uniqueCode;

                        // ===============================
                        // 3. Order ID
                        // ===============================
                        $orderId = 'BATCH-' . $record->id . '-' . time();

                        // ===============================
                        // 4. Midtrans SNAP
                        // ===============================
                        $midtrans = app(MidtransService::class);

                        $snapToken = $midtrans->createTransaction([
                            'order_id' => $orderId,
                            'gross_amount' => $total,
                            'customer_details' => [
                                'first_name' => auth()->user()->name,
                                'email' => auth()->user()->email,
                            ],
                        ]);

                        // ===============================
                        // 5. Simpan ke DB
                        // ===============================
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

                        // ===============================
                        // 6. Redirect ke halaman bayar
                        // ===============================
                        return redirect('/payment/' . $orderId);
                    }),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}