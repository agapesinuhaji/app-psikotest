<?php

namespace App\Filament\Resources\CorporateIdentities\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class CorporateIdentitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),   
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->square()
                    ->size(60),
                TextColumn::make('psikolog'),
                TextColumn::make('strk_sik'),
                TextColumn::make('sipp_sippk'),
                TextColumn::make('address'),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([])
            ->paginated(false); // ← ini yang menghapus per-page;
    }
}
