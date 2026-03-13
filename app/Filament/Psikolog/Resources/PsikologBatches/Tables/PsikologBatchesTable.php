<?php

namespace App\Filament\Psikolog\Resources\PsikologBatches\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\ViewAction;

class PsikologBatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Tampilkan nama user melalui relasi
                TextColumn::make('client.name')
                    ->label('Company')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Batch Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('date') 
                    ->label('Test Time')
                    ->dateTime('d M Y H:i') 
                    ->sortable(),

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
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}