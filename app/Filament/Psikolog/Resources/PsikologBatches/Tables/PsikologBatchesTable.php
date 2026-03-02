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

                BadgeColumn::make('status') // Gunakan BadgeColumn
                    ->label('Status')
                    ->colors([
                        'warning' => 'standby',  // kuning
                        'primary' => 'open',     // biru
                        'success' => 'done',     // hijau
                        'secondary' => fn($state) => !in_array($state, ['standby', 'open', 'done']), // abu-abu
                    ]),
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