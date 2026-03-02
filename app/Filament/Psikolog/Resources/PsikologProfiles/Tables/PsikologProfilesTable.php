<?php

namespace App\Filament\Psikolog\Resources\PsikologProfiles\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class PsikologProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                // Kolom Foto Profil
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular() // Membuat foto berbentuk lingkaran
                    ->disk('public'), // Pastikan disk sesuai filesystem kamu

                // Kolom Nama
                TextColumn::make('name')
                    ->label('Nama Lengkap'),

                // Kolom Nomor Telepon
                TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->copyable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
