<?php

namespace App\Filament\Resources\CorporateIdentities\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class CorporateIdentityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('psikolog'),
                TextEntry::make('strk_sik'),
                TextEntry::make('sipp_sippk'),
                TextEntry::make('address'),
                TextEntry::make('price')
                    ->label('Harga per Peserta')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                ImageEntry::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->height(150)
                    ->circular(), // opsional
                
            ]);
    }
}
